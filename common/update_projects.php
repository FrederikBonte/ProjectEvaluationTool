<?php
require_once "config.inc.php";
require_once "debug.php";

function print_active_projects()
{
	global $database;
	
	$query  = "SELECT * FROM project";
	debug_log($query);
?>
	<h2>Actieve projecten</h2>
	<table>
		<tr><th>Naam</th><th>Semstr</th><th width="120px">Sterren</th><th>Omschrijving</th></tr>		
<?php
	try {
		$stmt = $database->query($query);	
		while ($record = $stmt->fetch(PDO::FETCH_ASSOC))
		{
			print_project($record);
		}	
	}
	catch (Exception $ex) 
	{
		debug_error("Failed to read projects because ", $ex);
	}
	print "</table>";
}

function print_project($record)
{
		//debug_dump($record);
	
	$id = $record["id"];
	$name = $record["naam"];
	$semester = $record["semester"];
	$stars = $record["sterren"];
	$description = $record["omschrijving"];
?>
	<input type="hidden" name="project_id" value="<?=$id?>" />
	<tr>
		<td><a href="project.php?id=<?=$id?>"><?=$name?></a></td>
		<td><?=$semester?></td>
		<td>
<?php
	print_stars($stars);
?></td>
		<td><?=$description?></td>
	</tr>
	</form>
<?php
}

function add_project($name, $description, $semester, $stars)
{
	global $database;
	
	$query  = "INSERT INTO project (naam, semester, sterren, omschrijving) ";
	$query .= "VALUES (:veld1, :veld2, :veld3, :veld4)";	
	
	debug_log($query);

	$data = [
		"veld1" => $naam,
		"veld2" => $semester,
		"veld3" => $sterren,
		"veld4" => $omschrijving
	];
	
	try {
		debug_log("About to add new project.");
		$stmt = $database->prepare($query);
		if ($stmt->execute($data)) 
		{
			debug_log("Project successfully added.");
			$id = PDO::lastInsertId();
			add_group($id);
		} 
		else 
		{
			debug_warning("Database refused to add new project.");
		}
	} catch (Exception $ex) {
		debug_error("Failed to read projects because ", $ex);
	}
}

function add_group($id) {
	global $database;
	
	$query  = "INSERT INTO criterium_groep (id, parent_project, methode, naam) ";
	$query .= "VALUES (:veld1, null, :veld1, 1, 'Hoofdgroep')";	
	
	debug_log($query);

	$data = [
		"veld1" => $id
	];
	
	try {
		debug_log("About to add new group.");
		$stmt = $database->prepare($query);
		if ($stmt->execute($data)) 
		{
			debug_log("Group successfully added.");
			$id = PDO::lastInsertId();
			add_group($id);
		} 
		else 
		{
			print_warning("Database refused to add new group.");
		}
	} catch (Exception $ex) {
		debug_error("Failed to read projects because ", $ex);
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
		
	debug_log($query);

	$data = [	
		"veld0" => $number,
		"veld1" => $firstname,
		"veld2" => $lastname,
		"veld3" => $klas
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
		debug_error("Failed to read projects because ", $ex);
	}
}
?>