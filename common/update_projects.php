<?php

function add_project($name, $description, $semester, $stars)
{
	global $database;
	
	$query  = "INSERT INTO project (naam, semester, sterren, omschrijving) ";
	$query .= "VALUES (:veld1, :veld2, :veld3, :veld4)";	
	
	print "<!-- $query -->\n";

	$data = [
		"veld1" => $naam,
		"veld2" => $semester,
		"veld3" => $sterren,
		"veld4" => $omschrijving
	];
	
	try {
		print "About to add new project.";
		$stmt = $database->prepare($query);
		if ($stmt->execute($data)) 
		{
			print "Project successfully added.";
			$id = PDO::lastInsertId();
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


?>