<?php
require_once "common/config.inc.php";
require_once "common/debug.php";
require_once "common/form_gen.php";

function print_select_klas($selected_id = null, $label = null) 
{
	$query = "SELECT code as id, omschrijving as value FROM klas WHERE actief=1 ORDER BY jaar";	
	print_select($query, "klas", "Kies een klas", $selected_id, $label);
}

function print_select_any_klas($selected_id = null, $label = null) 
{
	global $database;
	$query = "SELECT code as id, omschrijving as value FROM klas ORDER BY jaar";	
	print_select($query, "klas", "Kies een klas", $selected_id, $label);
}

function print_select_student($klas_id = null, $selected_id = null, $label = null) 
{
	$query = "SELECT nummer as id, concat_ws(\" \", voornaam, tussenvoegsel, achternaam) as value ".
			 "FROM leerling ".
			 (($klas_id==null)?"":"WHERE klas='$klas_id' ").
			 "ORDER BY achternaam, voornaam";	
	print_select($query, "student", "Kies een leerling", $selected_id, $label);
}

function print_edit_students($klas_id = null)
{
	global $database;
	$query = "SELECT leerling.*, ((MAX(beoordeling.datum) IS NOT NULL) OR (MAX(evaluatie.datum) IS NOT NULL)) as beoordeeld ".
			"FROM leerling ".
			"LEFT JOIN beoordeling ON beoordeling.leerlingnummer = leerling.nummer ".
			"LEFT JOIN evaluatie ON evaluatie.leerlingnummer = leerling.nummer ".
			(($klas_id==null)?"":"WHERE klas='$klas_id' ").
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
				<th>Naam</th>
				<th>Tussenvoegsel</th>
				<th>Achternaam</th>
				<th>Klas</th>
				<th>Actief</th>
				<th>Actie</th>
			</tr>
<?php
			foreach ($stmt as $record) 	{
				// For instance, we don't assume the student will get the maximum of negative points!
				print_edit_student($record);
			}
			//print_add_student();
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

function print_edit_student($record)
{
	$deletable = $record["beoordeeld"]==0;
	//debug_dump($record);
?>
			<tr>
			<form>
<?php
	print_hidden_input("student_id", $record["nummer"]);
?>
				<td><?php print_text_input("firstname", mb_convert_encoding($record["voornaam"], "utf8")); ?></td>
				<td><?php print_text_input("middlename", mb_convert_encoding($record["tussenvoegsel"], "utf8")); ?></td>
				<td><?php print_text_input("lastname", mb_convert_encoding($record["achternaam"], "utf8")); ?></td>
				<td><?php print_select_klas($record["klas"]); ?></td>
				<td><?php print_checkbox("actief", $record["actief"]); ?></td>
				<td>
<?php
	print_submit_button("update_student", "Wijzigen");
	// Only methods that are not used by a criterium can be deleted.
	if ($deletable) 
	{
		print_submit_button("remove_student", "Verwijderen");
	}
?>
				</td>
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
		
		<achternaam><?=$firstname?></achternaam>
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

function add_student($number, $firstname, $middle, $lastname, $klas)
{
	global $database;
	
	$query  = "INSERT INTO leerling (nummer, voornaam, tussenvoegsel, achternaam, klas) ";
	$query .= "VALUES (:veld0, :veld1, :veld2a, :veld2b, :veld3)";	
	
	debug_log($query);

	$data = [
		"veld0" => $number,
		"veld1" => $firstname,
		"veld2a" => $middle,
		"veld2b" => $lastname,
		"veld3" => $klas
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

function update_student($number, $firstname, $middle, $lastname, $klas, $active = 1)
{
	global $database;
	
	$query  = "UPDATE leerling ";
	$query .= "SET voornaam = :veld1, ";
	$query .= "    tussenvoegsel = :veld2a, ";
	$query .= "    achternaam = :veld2b, ";
	$query .= "    klas = :veld3, ";
	$query .= "    actief = :veld4 ";
	$query .= "WHERE nummer = :veld0";
		
	debug_log($query);

	$data = [	
		"veld0" => $number,
		"veld1" => $firstname,
		"veld2a" => $middle,
		"veld2b" => $lastname,
		"veld3" => $klas,
		"veld4" => $active
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


?>