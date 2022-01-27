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
				<td><?php print_submit_button("update_teacher", "Wijzigen");?></td>
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
				<td><?php print_submit_button("add_teacher", "Toevoegen");?></td>
			</form>
			</tr>
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

?>