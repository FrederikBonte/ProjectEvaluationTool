<?php
require_once "common/config.inc.php";
require_once "common/debug.php";
require_once "common/form_gen.php";

// Functions to edit location
// Add/edit new locations.
// Assign locations to teachers.

function print_edit_locations_form()
{
	global $database;
	
	// Read the record for this teacher.
	$sql  = "SELECT locatie.*, ((MIN(docentcode) IS NOT NULL) OR (MIN(nummer) IS NOT NULL)) as gebruikt ";
	$sql .= "FROM locatie ";	
	$sql .= "LEFT JOIN docent_locatie ON docent_locatie.locatiecode=code ";	
	$sql .= "LEFT JOIN leerling ON leerling.locatiecode=code ";	
	$sql .= "GROUP BY locatie.code ";	
	debug_log($sql);
	
	try {		
		// Prepare a query...
		$stmt = $database->prepare($sql);
		// Activate the query...
		if ($stmt->execute()) 
		{
?>
			<h2>Locaties</h2>
			<table>
			<tr>
				<th>Code</th>			
				<th>Naam</th>
				<th>Omschrijving</th>
				<th>Actie</th>
			</tr>
<?php
			foreach ($stmt as $record) 	
			{
				print_edit_location_tr_form($record);
			}
			print_add_location_tr_form();
?>
			</table>
<?php
		} 
		else 
		{
			debug_warning("Database refused to read locations.");
		}
	}
	catch (Exception $ex)
	{
		debug_error("Failed to read location information because ", $ex);
	}		
}

function print_edit_location_tr_form($record)
{
?>
			<tr>
			<form method="POST">
				<td><?php print_hidden_input("code", $record["code"], true); ?></td>
				<td><?php print_text_input("name", $record["naam"]); ?></td>
				<td><?php print_text_input("description", $record["omschrijving"]); ?></td>
				<td>
<?php 
	print_submit_button("update_location", "<span class=\"fa fa-pencil\"></span>");
	if ($record["gebruikt"]==0) {
		print_submit_button("remove_location", "<span class=\"fa fa-trash\"></span>");
	}	
?>
				</td>
			</form>
			</tr>
<?php
}

function print_add_location_tr_form()
{
?>
			<tr>
			<form method="POST">
				<td><?php print_text_input("code"); ?></td>
				<td><?php print_text_input("name"); ?></td>
				<td><?php print_text_input("description"); ?></td>
				<td><?php print_submit_button("add_location", "<span class=\"fa fa-plus\"></span>");?></td>
			</form>
			</tr>
<?php
}

/// BELOW ARE THE ACTUAL DATABASE MANIPULATION FUNCTIONS

function add_location($code, $name, $description)
{
	global $database;
	
	$query  = "INSERT INTO locatie (code, naam, omschrijving) ";
	$query .= "VALUES (:veld0, :veld1, :veld2)";	
	
	debug_log($query);

	$data = [
		"veld0" => $code,
		"veld1" => $name,
		"veld2" => $description
	];
	
	try 
	{
		debug_log("About to add new location.");
		$stmt = $database->prepare($query);
		if ($stmt->execute($data)) 
		{
			debug_log("Location successfully added.");
		} 
		else 
		{
			debug_warning("Database refused to add new location.");
		}
	} 
	catch (Exception $ex) 
	{
		debug_error("ERROR: Failed to add location : ", $ex);
	}
}

function update_location($code, $name, $description)
{
	global $database;
	
	$query  = "UPDATE locatie ";
	$query .= "SET naam = :veld1, ";
	$query .= "    omschrijving = :veld2 ";
	$query .= "WHERE code = :veld0";
		
	debug_log($query);

	$data = [	
		"veld0" => $code,
		"veld1" => $name,
		"veld2" => $description
	];
	
	try {
		debug_log("About to change location $code.");
		$stmt = $database->prepare($query);
		if ($stmt->execute($data)) 
		{
			debug_log("Location successfully updated.");
		} 
		else 
		{
			debug_warning("Database refused to update this location.");
		}
	} catch (Exception $ex) {
		debug_error("ERROR: Failed to update location : ", $ex);
	}
}

function link_teacher_and_location($teacher_code, $location_code)
{
	global $database;
	
	$query  = "INSERT INTO docent_location (docentcode, locatiecode) ";
	$query .= "VALUES (:veld0, :veld1)";	
	
	debug_log($query);

	$data = [
		"veld0" => $teacher_code,
		"veld1" => $location_code
	];
	
	try 
	{
		debug_log("About to add location $location_code to teacher $teacher_code.");
		$stmt = $database->prepare($query);
		if ($stmt->execute($data)) 
		{
			debug_log("Location successfully added.");
		} 
		else 
		{
			debug_warning("Database refused to add location.");
		}
	} 
	catch (Exception $ex) 
	{
		debug_error("ERROR: Failed to add location : ", $ex);
	}	
}

function unlink_teacher_and_location($teacher_code, $location_code)
{
	global $database;
	
	$query  = "DELETE FROM docent_locatie ";
	$query .= "WHERE docentcode=:veld0 ";
	$query .= "AND locatiecode=:veld1";	
	
	debug_log($query);

	$data = [
		"veld0" => $teacher_code,
		"veld1" => $location_code
	];
	
	try 
	{
		debug_log("About to remove link between $location_code and $teacher_code.");
		$stmt = $database->prepare($query);
		if ($stmt->execute($data)) 
		{
			debug_log("Location successfully unlinked.");
		} 
		else 
		{
			debug_warning("Database refused to unlink location.");
		}
	} 
	catch (Exception $ex) 
	{
		debug_error("ERROR: Failed to unlink location : ", $ex);
	}	
}

function remove_location($code)
{
	global $database;
	
	$query  = "DELETE FROM locatie ";
	$query .= "WHERE code=:veld1";	
	
	debug_log($query);

	$data = [
		"veld1" => $code
	];
	
	try 
	{
		debug_log("About to remove location.");
		$stmt = $database->prepare($query);
		if ($stmt->execute($data)) 
		{
			debug_log("Location successfully removed.");
		} 
		else 
		{
			print_warning("Database refused to remove location.");
		}
	} 
	catch (Exception $ex) 
	{
		debug_error("Failed to remove location because ", $ex);
	}
}
?>