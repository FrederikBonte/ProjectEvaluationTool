<?php

function add_project($name, $description, $semester, $stars)
{
	global $database;
	
	$query  = "INSERT INTO project (naam, semester, sterren, omschrijving) ";
	$query .= "VALUES (:veld1, :veld2, :veld3, :veld4)";	
	
	print "<!-- $query -->\n";

	$data = [
		"veld1" => $name,
		"veld2" => $semester,
		"veld3" => $stars,
		"veld4" => $description
	];
	
	try {
		print "About to add new project.";
		$stmt = $database->prepare($query);
		if ($stmt->execute($data)) 
		{
			print "Project successfully added.";
			$id = $stmt->lastInsertId();
			add_group($id);
		} 
		else 
		{
			print "Database refused to add new project.";
		}
	} catch (Exception $ex) {
		print "ERROR: Failed to add project : ".$ex->getMessage();
	}
}

function add_group($id) {
	global $database;
	
	$query  = "INSERT INTO criterium_groep (id, parent_project, methode, naam) ";
	$query .= "VALUES (:veld1, null, :veld1, 1, 'Hoofdgroep')";	
	
	print "<!-- $query -->\n";

	$data = [
		"veld1" => $id
	];
	
	try {
		print "About to add new group.";
		$stmt = $database->prepare($query);
		if ($stmt->execute($data)) 
		{
			print "Group successfully added.";
			$id = PDO::lastInsertId();
			add_group($id);
		} 
		else 
		{
			print "Database refused to add new group.";
		}
	} catch (Exception $ex) {
		print "ERROR: Failed to add group : ".$ex->getMessage();
	}
}

function update_project($id, $name, $description, $stars)
{
	global $database;
	
	$query  = "UPDATE leerlingen ";
	$query .= "SET voornaam = :veld1, ";
	$query .= "    achternaam = :veld2, ";
	$query .= "    klas = :veld3 ";
	$query .= "WHERE llnr = :veld0";
		
	print "<!-- $query -->\n";

	$data = [	
		"veld0" => $number,
		"veld1" => $firstname,
		"veld2" => $lastname,
		"veld3" => $klas
	];
	
	try {
		print "About to change student $number.";
		$stmt = $database->prepare($query);
		if ($stmt->execute($data)) 
		{
			print "Student successfully updated.";
		} 
		else 
		{
			print "Database refused to update this student.";
		}
	} catch (Exception $ex) {
		print "ERROR: Failed to update student : ".$ex->getMessage();
	}
}

function add_criterium($id, $criterium_id, $weight)
{
	
	global $database;
	
	$autocalc = get_autocalc($criterium_id);
	
	$query  = "INSERT project_criterium ";
	$query .= "(groepid, criteriumid, gewicht, autocalc)";
	$query .= "VALUES (:veld1, :veld2, :veld3, :veld4)";
		
	print "<!-- $query -->\n";

	$data = [	
		"veld1" => $id,
		"veld2" => $criterium_id,
		"veld3" => $weight,
		"veld4" => $autocalc
	];
	
	try {
		print "About to add criterium to project $id.";
		$stmt = $database->prepare($query);
		if ($stmt->execute($data)) 
		{
			print "Criterium successfully added to project.";
		} 
		else 
		{
			print "Database refused to add this criterium to the project.";
		}
	} catch (Exception $ex) {
		print "ERROR: Failed to add criterium to the project : ".$ex->getMessage();
	}
}

function update_criterium($id, $criterium_id, $weight)
{	
	global $database;
		
	$query  = "UPDATE  project_criterium ";
	$query .= "SET gewicht=:veld3 ";
	$query .= "WHERE groepid = :veld1 AND criteriumid = :veld2";
		
	print "<!-- $query -->\n";

	$data = [	
		"veld1" => $id,
		"veld2" => $criterium_id,
		"veld3" => $weight
	];
	
	try {
		$stmt = $database->prepare($query);
		if ($stmt->execute($data)) 
		{
			print "Criterium successfully updated.";
		} 
		else 
		{
			print "Database refused to update this criterium for the project.";
		}
	} catch (Exception $ex) {
		print "ERROR: Failed to update criterium for the project : ".$ex->getMessage();
	}
}

function remove_criterium($id, $criterium_id)
{	
	global $database;
	
	$query  = "DELETE FROM project_criterium ";
	$query .= "WHERE groepid = :veld1 AND criteriumid = :veld2";
		
	print "<!-- $query -->\n";

	$data = [	
		"veld1" => $id,
		"veld2" => $criterium_id
	];
	
	try {
		$stmt = $database->prepare($query);
		if ($stmt->execute($data)) 
		{
			print "Criterium successfully deleted.";
		} 
		else 
		{
			print "Database refused to delete this criterium from the project.";
		}
	} catch (Exception $ex) {
		print "ERROR: Failed to delete criterium from the project : ".$ex->getMessage();
	}
}


function get_autocalc($criterium_id) 
{
	global $database;
	$query = "SELECT autocalc FROM criterium WHERE id = :id";	
	print "<!-- $query -->\n";
	$data = [
		"id" => $criterium_id
	];
	$result = 1;
	
	try {
		$stmt = $database->prepare($query);
		if ($stmt->execute($data)) 
		{
			$record = $stmt->fetch(PDO::FETCH_ASSOC);
			$result = $record["autocalc"];
		} 
		else 
		{
			print "Database refused to supply autocalc, assuming default.";
		}
	} 
	catch (Exception $ex) 
	{
		print "ERROR: Failed to read autocalc for criterium $criterium_id : ".$ex->getMessage();
	}
	return $result;
}
?>