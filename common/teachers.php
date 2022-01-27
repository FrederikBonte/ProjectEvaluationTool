<?php
require_once "common/config.inc.php";
require_once "common/debug.php";
require_once "common/form_gen.php";

// Functions to edit teachers
// Add/edit new teachers.
// Assign classes to teachers.
// Generate new login (when not yet present or something.) 

function print_edit_teachers_form()
{
	global $database;
	
	// Read the record for this teacher.
	$sql = "SELECT * FROM docent";	
	debug_log($sql);
	
	$code = $_SESSION["docent"];
	try {		
		// Prepare a query...
		$stmt = $database->prepare($sql);
		// Activate the query...
		if ($stmt->execute()) 
		{
?>
			<h2>Docenten</h2>
			<table>
			<tr>
				<th>Code</th>			
				<th>Voornaam</th>
				<th>Achternaam</th>
				<th>Aanspreekvorm</th>
				<th>Inlognaam</th>
				<th>Actie</th>
			</tr>
<?php
			foreach ($stmt as $record) 	{
				print_edit_teacher_tr_form($record);
			}
			print_add_teacher_tr_form();
?>
			</table>
<?php
		} 
		else 
		{
			debug_warning("Database refused to read students.");
		}
	}
	catch (Exception $ex)
	{
		debug_error("Failed to read teacher information because ", $ex);
	}		
}

function print_edit_teacher_tr_form($record)
{
?>
			<tr>
			<form>
				<td><?php print_hidden_input("code", $record["code"], true); ?></td>
				<td><?php print_text_input("firstname", $record["voornaam"]); ?></td>
				<td><?php print_text_input("lastname", $record["achternaam"]); ?></td>
				<td><?php print_text_input("title", $record["aanspreekvorm"]); ?></td>
				<td><?=$record["username"]?></td>
				<td><?php print_submit_button("update_teacher", "<span class=\"fa fa-pencil\"></span>");?></td>
			</form>
			</tr>
<?php
}

function print_add_teacher_tr_form()
{
?>
			<tr>
			<form>
				<td><?php print_text_input("code"); ?></td>
				<td><?php print_text_input("firstname"); ?></td>
				<td><?php print_text_input("lastname"); ?></td>
				<td><?php print_text_input("title"); ?></td>
				<td></td>
				<td><?php print_submit_button("add_teacher", "<span class=\"fa fa-plus\"></span>");?></td>
			</form>
			</tr>
<?php
}

function print_teacher_information()
{
	global $database;
	
	// Read the record for this teacher.
	$sql = 
	"SELECT * ".
	"FROM docent ".
	"WHERE code=:field1";
	
	debug_log($sql);
	
	$code = $_SESSION["docent"];
	try {		
		// Prepare a query...
		$stmt = $database->prepare($sql);
		// Additional database
		$data = [
			"field1" => $code,
		];
		
		// Activate the query...
		$stmt->execute($data);
		// Retrieve one record.
		$row=$stmt->fetch(PDO::FETCH_ASSOC);
		
		//debug_dump($row);
		$firstname = $row["voornaam"];
		$lastname = $row["achternaam"];
		$title = $row["aanspreekvorm"];
		$username = $row["username"];
?>
	<h3>Gegevens voor '<?=$code?>'</h3>
	<ul>
		<li>Voornaam : <?=$firstname?></li>
		<li>Achternaam : <?=$lastname?></li>
		<li>Aanspreekvorm : <?=$title?></li>
		<li>Inlognaam : <?=$username?></li>
		<li>Kan inzien : <?=can_view()?"ja":"<i>nee</i>"?></li>
		<li>Kan wijzigen : <?=can_edit()?"ja":"<i>nee</i>"?></li>
		<li>Kan aanmaken : <?=can_create()?"ja":"<i>nee</i>"?></li>
	</ul>
<?php
		
	}
	catch (Exception $ex)
	{
		debug_error("Failed to read teacher information because ", $ex);
	}		
}

function get_username()
{
	global $database;
	
	// Read the record for this teacher.
	$sql = 
	"SELECT username ".
	"FROM docent ".
	"WHERE code=:field1";
	
	debug_log($sql);
	
	$code = $_SESSION["docent"];
	try {		
		// Prepare a query...
		$stmt = $database->prepare($sql);
		// Additional database
		$data = [
			"field1" => $code,
		];
		
		// Activate the query...
		$stmt->execute($data);
		// Retrieve one record.
		$row=$stmt->fetch(PDO::FETCH_ASSOC);
		return $row["username"];
	}
	catch (Exception $ex)
	{
		debug_error("Failed to read teacher information because ", $ex);
	}
}

function print_change_passwd_form()
{
	$code = $_SESSION["docent"];
?>
	<h3>Inlog gegevens wijzigen</h3>
	<p>
		Uw inlog naam moet uniek zijn. 
		Als u dus een naam kiest die al in gebruik is,
		dan wordt de wijziging niet geaccepteerd.
	</p>
	<form method="POST">
<?php
	print_rand_check();
	print_text_input("username", get_username(), "Inlog naam : " );
	echo "<br />";
	print_password_input("passwd1", null, "Wachtwoord 1 : ", "passwd1", "check_passwd()");
	echo "<br />";
	print_password_input("passwd2", null, "Wachtwoord 2 : ", "passwd2", "check_passwd()");
	echo "<br />";
	print_submit_button("change_passwd", "Wijzigen", "chpasswd");
?>
	</form>
<?php
}

/// BELOW ARE THE ACTUAL DATABASE MANIPULATION FUNCTIONS

function add_teacher($code, $firstname, $lastname, $title)
{
	global $database;
	
	$query  = "INSERT INTO docent (code, voornaam, achternaam, aanspreekvorm) ";
	$query .= "VALUES (:veld0, :veld1, :veld2, :veld3)";	
	
	debug_log($query);

	$data = [
		"veld0" => $code,
		"veld1" => $firstname,
		"veld2" => $lastname,
		"veld3" => $title
	];
	
	try 
	{
		debug_log("About to add new teacher.");
		$stmt = $database->prepare($query);
		if ($stmt->execute($data)) 
		{
			debug_log("Teacher successfully added.");
		} 
		else 
		{
			debug_warning("Database refused to add new teacher.");
		}
	} 
	catch (Exception $ex) 
	{
		debug_error("ERROR: Failed to add teacher : ", $ex);
	}
}

function update_teacher($code, $firstname, $lastname, $title)
{
	global $database;
	
	$query  = "UPDATE docent ";
	$query .= "SET voornaam = :veld1, ";
	$query .= "    achternaam = :veld2, ";
	$query .= "    aanspreekvorm = :veld3 ";
	$query .= "WHERE code = :veld0";
		
	debug_log($query);

	$data = [	
		"veld0" => $code,
		"veld1" => $firstname,
		"veld2" => $lastname,
		"veld3" => $title
	];
	
	try {
		debug_log("About to change teacher $code.");
		$stmt = $database->prepare($query);
		if ($stmt->execute($data)) 
		{
			debug_log("Teacher successfully updated.");
		} 
		else 
		{
			debug_warning("Database refused to update this teacher.");
		}
	} catch (Exception $ex) {
		debug_error("ERROR: Failed to update teacher : ", $ex);
	}
}

function update_username_password($username, $password)
{
	global $database;
	
	// Read the record for this teacher.
	$sql = 
	"UPDATE docent ".
	"SET username=:field1, password=:field2 ".
	"WHERE code=:field0";
	
	debug_log($sql);
	
	try {		
		// Prepare a query...
		$stmt = $database->prepare($sql);
		// Additional database
		$data = [
			"field0" => $_SESSION["docent"],
			"field1" => $username,
			"field2" => $password
		];
		
		// Activate the query...
		if ($stmt->execute($data))
		{
			debug_warning("Username en wachtwoord zijn aangepast. De volgende keer dat u inlogt moet u deze gebruiken!");
		}
		else
		{
			debug_warning("Uw inlognaam en wachtwoord zijn niet gewijzigd.");
		}
	}
	catch (Exception $ex)
	{
		debug_error("Failed to change username and password because ", $ex);
	}	
}
?>