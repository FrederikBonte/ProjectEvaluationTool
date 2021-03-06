<?php
require_once "common/config.inc.php";
require_once "common/debug.php";
require_once "common/form_gen.php";

function print_select_klas($selected_id = null, $label = null) 
{
	$code = substr($_SESSION["docent"],0,5);
	$query = "SELECT code as id, omschrijving as value FROM klas WHERE actief=1 ".
	         "AND locatiecode IN (SELECT locatiecode FROM docent_locatie WHERE docentcode='$code') ".
			 "ORDER BY jaar";	
	print_select($query, "klas", "Kies een klas", $selected_id, $label);
}

function print_select_any_klas($selected_id = null, $label = null) 
{
	global $database;
	$query = "SELECT code as id, omschrijving as value FROM klas ORDER BY jaar";	
	print_select($query, "klas", "Kies een klas", $selected_id, $label);
}

function print_select_unassigned_klas($teacher, $label = null)
{
	global $database;
	$sqlteach = substr($teacher, 0, 5);
	$query  = "SELECT code as id, omschrijving as value FROM klas ";
	$query .= "WHERE actief=1 ";
	$query .= "AND code NOT IN (SELECT klascode FROM docent_klas WHERE docentcode='$sqlteach') ";
	$query .= "AND locatiecode IN (SELECT locatiecode FROM docent_locatie WHERE docentcode='$sqlteach') ";
	$query .= "ORDER BY jaar ";	
	print_select($query, "klas", "Kies een bestaande klas", null, $label);
}

function print_select_student($klas_id = null, $selected_id = null, $label = null) 
{
	$code = substr($_SESSION["docent"],0,5);
	$query = "SELECT nummer as id, concat_ws(\" \", voornaam, tussenvoegsel, achternaam) as value ".
			 "FROM leerling WHERE actief=1 ".
	         "AND locatiecode IN (SELECT locatiecode FROM docent_locatie WHERE docentcode='$code') ".
			 (($klas_id==null)?"":"AND klas='$klas_id' ").
			 "ORDER BY achternaam, voornaam";	
	print_select($query, "student", "Kies een leerling", $selected_id, $label);
}

function print_select_location($selected_id = null, $label = null)
{
	$code = substr($_SESSION["docent"],0,5);
	$query  = "SELECT code as id, naam as value ";
	$query .= "FROM locatie ";
	$query .= "WHERE code IN (SELECT locatiecode FROM docent_locatie WHERE docentcode='$code') ";
	$query .= "ORDER BY naam";
	print_select($query, "location", "Kies een locatie", $selected_id, $label);
}

function print_add_klas_form()
{
?>
	<h3>Voeg een nieuwe klas toe</h3>
	<p>
		Deze nieuwe klas wordt meteen toegevoegd aan uw lijst met klassen.
	</p>
	<form method="POST">
<?php
	print_text_input("code", null, "Klas code : ");
	echo "<br />";
	print_text_input("description", null, "Omschrijving : ");
	echo "<br />";
	print_number_input("year", 2022, 2040, null, "Jaar (cohort) : ");
	echo "<br />";
	print_number_input("semester", 1,8,null, "Semester : ");
	echo "<br />";
	print_select_location(null, "Locatie : ");
	echo "<br />";
	print_submit_button("add_klas", "Toevoegen");
?>		
	</form>
<?php
}

function print_add_klas_tr_form()
{
?>
	<tr>
	<form method="POST">
	<td><?php print_text_input("code");?></td>
	<td><?php print_text_input("description"); ?></td>
	<td><?php print_number_input("year", 2022, 2040, 2022); ?></td>
	<td><?php print_number_input("semester", 1,8, 1); ?></td>
	<td><?php print_select_location(); ?></td>
	<td></td>
	<td><?php print_submit_button("add_klas", "<span class=\"fa fa-plus\"></span>"); ?></td>
	</form>
	</tr>
<?php
}

function print_edit_klas_tr_form($record)
{
?>
	<tr>
	<form method="POST">
	<td><?php print_hidden_input("code", $record["code"], true);?></td>
	<td><?php print_text_input("description", $record["omschrijving"]); ?></td>
	<td><?php print_number_input("year", 2018, 2040, $record["jaar"]); ?></td>
	<td><?php print_number_input("semester", 1,8, $record["semester"]); ?></td>
	<td><?php print_select_location($record["locatiecode"]); ?></td>
	<td><?php print_checkbox("active", $record["actief"]); ?></td>
	<td><?php print_submit_button("update_klas", "<span class=\"fa fa-pencil\"></span>"); ?></td>
	<td><?php print_submit_button("unassign_klas", "<span class=\"fa fa-chain-broken\"></span>"); ?></td>
	</form>
	</tr>
<?php
}

function print_edit_docent_klas_form($teacher)
{
	global $database;
	$sqlteach = substr($teacher, 0, 5);
	$query = "SELECT * FROM klas, docent_klas WHERE klas.code = klascode AND docentcode='$sqlteach'";
	debug_log($query);
	
	try {
		$stmt = $database->prepare($query);
		if ($stmt->execute()) 
		{
?>
			<h3>Klassen</h3>
			<table>
			<tr>
				<th>Code</th>
				<th>Omschrijving</th>
				<th>Jaar</th>
				<th>Semester</th>
				<th>Locatie</th>
				<th>Actief</th>
				<th>Actie</th>
			</tr>
<?php
			foreach ($stmt as $record) 	{
				print_edit_klas_tr_form($record);
			}
			print_add_klas_tr_form();
?>
			<tr>
			<form method="POST">
				<td><?php print_select_unassigned_klas($teacher); ?></td>
				<td></td><td></td><td></td><td></td>
				<td><?php print_submit_button("assign_klas", "<span class=\"fa fa-chain\"></span>"); ?></td>
			</form>
			</tr>
			</table>
<?php
		} 
		else 
		{
			debug_warning("Database refused to read students.");
		}
	} catch (Exception $ex) {
		debug_error("ERROR: Failed to load students : ", $ex);
	}	
}

function print_edit_students($klas_id = null)
{
	global $database;
	$code = substr($_SESSION["docent"],0,5);
	$query = "SELECT leerling.*, ((MAX(beoordeling.datum) IS NOT NULL) OR (MAX(evaluatie.datum) IS NOT NULL)) as beoordeeld ".
			"FROM leerling ".
			"LEFT JOIN beoordeling ON beoordeling.leerlingnummer = leerling.nummer ".
			"LEFT JOIN evaluatie ON evaluatie.leerlingnummer = leerling.nummer ".
			"WHERE locatiecode in (SELECT locatiecode FROM docent_locatie WHERE docentcode='$code') ".
			(($klas_id==null)?"":"AND klas='$klas_id' ").
			"GROUP BY leerling.nummer, leerling.voornaam, leerling.tussenvoegsel, leerling.achternaam, leerling.klas, leerling.actief ".
			"ORDER BY achternaam, voornaam";
	debug_log($query);
	
	try {
		$stmt = $database->prepare($query);
		if ($stmt->execute()) 
		{
?>
			<h2>Studenten</h2>
			<table>
			<tr>
				<th>Nummer</th>			
				<th>Naam</th>
				<th>Tussenvoegsel</th>
				<th>Achternaam</th>
				<th>Klas</th>
				<th>Locatie</th>
				<th>Actief</th>
				<th>Actie</th>
			</tr>
<?php
			foreach ($stmt as $record) 	{
				print_edit_student_tr_form($record);
			}
			if ($klas_id)
			{
				print_add_student_tr_form($klas_id);
			}
?>
			</table>
<?php
		} 
		else 
		{
			debug_warning("Database refused to read students.");
		}
	} catch (Exception $ex) {
		debug_error("ERROR: Failed to load students : ", $ex);
	}	
}

function print_list_students($klas_id = null)
{
	global $database;
	$code = substr($_SESSION["docent"],0,5);
	$query = "SELECT leerling.*, ((MAX(beoordeling.datum) IS NOT NULL) OR (MAX(evaluatie.datum) IS NOT NULL)) as beoordeeld ".
			"FROM leerling ".
			"LEFT JOIN beoordeling ON beoordeling.leerlingnummer = leerling.nummer ".
			"LEFT JOIN evaluatie ON evaluatie.leerlingnummer = leerling.nummer ".
			"WHERE locatiecode in (SELECT locatiecode FROM docent_locatie WHERE docentcode='$code') ".
			(($klas_id==null)?"":"AND klas='$klas_id' ").
			"GROUP BY leerling.nummer, leerling.voornaam, leerling.tussenvoegsel, leerling.achternaam, leerling.klas, leerling.actief ".
			"ORDER BY achternaam, voornaam";
	debug_log($query);
	
	try {
		$stmt = $database->prepare($query);
		if ($stmt->execute()) 
		{
?>
			<h2>Studenten</h2>
			<table>
			<tr>
				<th>Nummer</th>
				<th>Naam</th>
				<th>Tussenvoegsel</th>
				<th>Achternaam</th>
				<th>Klas</th>
				<th>Locatie</th>
				<th>Actief</th>
			</tr>
<?php
			foreach ($stmt as $record) 	{
?>
			<tr>
				<td><?=$record["nummer"]?></td>
				<td><?=$record["voornaam"]?></td>
				<td><?=$record["tussenvoegsel"]?></td>
				<td><?=$record["achternaam"]?></td>
				<td><?=$record["klas"]?></td>
				<td><?=$record["locatiecode"]?></td>
				<td>&nbsp;<?=($record["actief"]==1)?"ja":"<i>nee</i>"?></td>
			</tr>
<?php
			}
?>
			</table>
<?php
		} 
		else 
		{
			debug_warning("Database refused to read students.");
		}
	} catch (Exception $ex) {
		debug_error("ERROR: Failed to load students : ", $ex);
	}	
}

function print_edit_student_tr_form($record)
{
	$deletable = $record["beoordeeld"]==0;
	//debug_dump($record);
?>
			<tr>
			<form>
				<td><?php print_hidden_input("student_id", $record["nummer"], true); ?></td>
				<td><?php print_text_input("firstname", $record["voornaam"]); ?></td>
				<td><?php print_text_input("middlename", $record["tussenvoegsel"]); ?></td>
				<td><?php print_text_input("lastname", $record["achternaam"]); ?></td>
				<td><?php print_select_klas($record["klas"]); ?></td>
				<td><?php print_select_location($record["locatiecode"]); ?></td>
				<td><?php print_checkbox("actief", $record["actief"]); ?></td>
				<td>
<?php
	print_submit_button("update_student", "<span class=\"fa fa-pencil\"></span>");
	// Only methods that are not used by a criterium can be deleted.
	if ($deletable) 
	{
		print_submit_button("remove_student", "<span class=\"fa fa-trash\"></span>");
	}
?>
				</td>
			</form>
			</tr>
<?php
}

function print_add_student_tr_form($klas)
{
?>
			<tr>
			<form>
				<td><?php print_number_input("student_id", 100000, 99999999); ?></td>
				<td><?php print_text_input("firstname"); ?></td>
				<td><?php print_text_input("middlename"); ?></td>
				<td><?php print_text_input("lastname"); ?></td>
				<td><?php print_hidden_input("klas", $klas, true); ?></td>
				<td><?php print_select_location(); ?></td>
				<td></td>
				<td><?php print_submit_button("add_student", "<span class=\"fa fa-plus\"></span>");?></td>
			</form>
			</tr>
<?php
}

function export_students_csv($klas_id)
{
global $database;
	$query = "SELECT * ".
			"FROM leerling ".
			"WHERE klas=? and actief=1 ".
			"ORDER BY achternaam, voornaam";
	try {
		$stmt = $database->prepare($query);
		if ($stmt->execute([$klas_id])) 
		{
			print "# leerlingen $klas_id\n";
			foreach ($stmt as $record) 	{
				$number = $record["nummer"];
				$firstname = $record["voornaam"];
				$middle = $record["tussenvoegsel"];
				$lastname = $record["achternaam"];
				print "$number;$firstname;$middle;$lastname\n";
			}
		} 
		else 
		{
			debug_warning("Database refused to read students.");
		}
	} catch (Exception $ex) {
		debug_error("ERROR: Failed to load students : ", $ex);
	}	
}

function export_students_xml($klas_id)
{
global $database;
	$query = "SELECT * ".
			"FROM leerling ".
			"WHERE klas=? and actief=1 ".
			"ORDER BY achternaam, voornaam";
	try {
		$stmt = $database->prepare($query);
		if ($stmt->execute([$klas_id])) 
		{
			print "<?xml version=\"1.0\"?>\n";
			print "<!-- Leerlingen $klas_id -->\n";
			print "<leerlingen>\n";
			foreach ($stmt as $record) 	{
				$number = $record["nummer"];
				$firstname = $record["voornaam"];
				$middle = $record["tussenvoegsel"];
				$lastname = $record["achternaam"];
?>
	<leerling>
		<nummer><?=$number?></nummer>
		<voornaam><?=$firstname?></voornaam>
<?php
				if ($middle && strlen(trim($middle))>0)
				{
?>		<tussenvoegsel><?=$middle?></tussenvoegsel><?php
				}
				else
				{
?>		<tussenvoegsel /><?php
				}
?>
		
		<achternaam><?=$lastname?></achternaam>
	</leerling>
<?php
			}
			print "</leerlingen>";
		} 
		else 
		{
			debug_warning("Database refused to read students.");
		}
	} catch (Exception $ex) {
		debug_error("ERROR: Failed to load students : ", $ex);
	}	
}

function export_students_json($klas_id)
{
global $database;
	$query = "SELECT * ".
			"FROM leerling ".
			"WHERE klas=? and actief=1 ".
			"ORDER BY achternaam, voornaam";
	try {
		$stmt = $database->prepare($query);
		if ($stmt->execute([$klas_id])) 
		{
			$first = true;
?>
{
	"leerlingen" : [
<?php
			foreach ($stmt as $record) 	{
				$number = $record["nummer"];
				$firstname = $record["voornaam"];
				$middle = $record["tussenvoegsel"];
				$lastname = $record["achternaam"];
				if ($first)
				{
					$first = false;
				}
				else
				{
					echo ",\n";
				}
?>
		{
			"nummer" : "<?=$number?>",
			"voornaam" : "<?=$firstname?>",
<?php
				if ($middle && strlen(trim($middle))>0)
				{
?>
			"tussenvoegsel" : "<?=$middle?>",
<?php
				}
?>
			"achternaam" : "<?=$lastname?>"
		}<?php
			}
			echo "\n";
?>
	]
}
<?php
		} 
		else 
		{
			debug_warning("Database refused to read students.");
		}
	} catch (Exception $ex) {
		debug_error("ERROR: Failed to load students : ", $ex);
	}	
}
// BELOW ARE ALL THE FUNCTIONS THAT manipulate the database.

function import_students_csv($klas_id, $filename)
{
	// Open the file to READ.
	if ($file = fopen($filename, "r")) 
	{
		$added = 0;
		$updated = 0;
		// While there are still more lines...
		while(!feof($file)) 
		{
			$line = fgets($file);
			// Skip empty lines
			if (strlen(trim($line))==0) 
			{
				continue;
			}
			else if (substr($line, 0, 1)=="#")
			{
				// Ignore lines that start with a '#' (comments in the file.)
				continue;
			}
			$parts = explode(";", $line);
			$number = trim($parts[0]);
			$firstname = trim($parts[1]);
			$middle = trim($parts[2]);
			$lastname = trim($parts[3]);
			if (strlen($middle)==0)
			{
				$middle = null;
			}
			
			if (student_exists($number))
			{
				update_student($number, $firstname, $middle, $lastname, $klas_id);
				$updated++;
			}
			else
			{
				add_student($number, $firstname, $middle, $lastname, $klas_id);
				$added++;
			}	
		}
		fclose($file);
		debug_info("File was processed. $added new student(s) were added, $updated student(s) were updated.");
	}
	else
	{
		debug_warning("Failed to open the file '$filename' for reading.");
	}
}

function student_exists($number)
{
	global $database;
	
	$query  = "SELECT * FROM leerling WHERE nummer=:veld0";	
	debug_log($query);

	$data = [
		"veld0" => $number
	];
	
	try 
	{
		$stmt = $database->prepare($query);
		if ($stmt->execute($data)) 
		{
			// Return (effectively) true when a matching student has been found.
			return $stmt->fetch(PDO::FETCH_ASSOC);
		} 
		else 
		{
			debug_warning("Database refused to search for student.");
		}
	} 
	catch (Exception $ex) 
	{
		debug_error("ERROR: Failed to read student table : ", $ex);
	}	
}

function add_student($number, $firstname, $middle, $lastname, $klas, $location)
{
	global $database;
	
	$query  = "INSERT INTO leerling (nummer, voornaam, tussenvoegsel, achternaam, klas, locatiecode) ";
	$query .= "VALUES (:veld0, :veld1, :veld2a, :veld2b, :veld3, :veld4)";	
	
	debug_log($query);

	$data = [
		"veld0" => $number,
		"veld1" => $firstname,
		"veld2a" => $middle,
		"veld2b" => $lastname,
		"veld3" => $klas,
		"veld4" => $location
	];
	
	try 
	{
		debug_log("About to add new student.");
		$stmt = $database->prepare($query);
		if ($stmt->execute($data)) 
		{
			debug_log("Student successfully added.");
		} 
		else 
		{
			debug_warning("Database refused to add new student.");
		}
	} 
	catch (Exception $ex) 
	{
		debug_error("ERROR: Failed to add student : ", $ex);
	}
}

function update_student($number, $firstname, $middle, $lastname, $klas, $location, $active = 1)
{
	global $database;
	
	$query  = "UPDATE leerling ";
	$query .= "SET voornaam = :veld1, ";
	$query .= "    tussenvoegsel = :veld2a, ";
	$query .= "    achternaam = :veld2b, ";
	$query .= "    klas = :veld3, ";
	$query .= "    locatiecode = :veld4, ";
	$query .= "    actief = :veld5 ";
	$query .= "WHERE nummer = :veld0";
		
	debug_log($query);

	$data = [	
		"veld0" => $number,
		"veld1" => $firstname,
		"veld2a" => $middle,
		"veld2b" => $lastname,
		"veld3" => $klas,
		"veld4" => $location,
		"veld5" => $active
	];
	
	try {
		debug_log("About to change student $number.");
		$stmt = $database->prepare($query);
		if ($stmt->execute($data)) 
		{
			debug_log("Student successfully updated.");
		} 
		else 
		{
			debug_warning("Database refused to update this student.");
		}
	} catch (Exception $ex) {
		debug_error("ERROR: Failed to update student : ", $ex);
	}
}

function add_klas($code, $description, $year, $semester, $location)
{
	global $database;
	
	$query  = "INSERT INTO klas (code, omschrijving, jaar, semester, locatiecode) ";
	$query .= "VALUES (:veld0, :veld1, :veld2, :veld3, :veld4)";	
	
	debug_log($query);

	$data = [
		"veld0" => $code,
		"veld1" => $description,
		"veld2" => $year,
		"veld3" => $semester,
		"veld4" => $location
	];
	
	try 
	{
		debug_log("About to add new klas.");
		$stmt = $database->prepare($query);
		if ($stmt->execute($data)) 
		{
			debug_log("Klas successfully added.");
			assign_klas_to_teacher($code, $_SESSION["docent"]);
		} 
		else 
		{
			debug_warning("Database refused to add new klas.");
		}
	} 
	catch (Exception $ex) 
	{
		debug_error("ERROR: Failed to add klas : ", $ex);
	}
}

function update_klas($code, $description, $year, $semester, $location, $active)
{
	global $database;
	
	$query  = "UPDATE klas SET omschrijving=:veld1, jaar=:veld2, semester=:veld3, locatiecode=:veld4, actief=:veld5 ";
	$query .= "WHERE code=:veld0";	
	
	debug_log($query);

	$data = [
		"veld0" => $code,
		"veld1" => $description,
		"veld2" => $year,
		"veld3" => $semester,
		"veld4" => $location,
		"veld5" => $active
	];
	
	try 
	{
		debug_log("About to update klas $code.");
		$stmt = $database->prepare($query);
		if ($stmt->execute($data)) 
		{
			debug_log("Klas successfully updated.");
		} 
		else 
		{
			debug_warning("Database refused to update klas.");
		}
	} 
	catch (Exception $ex) 
	{
		debug_error("ERROR: Failed to update klas : ", $ex);
	}
}

?>