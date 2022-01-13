<?php
require_once "config.inc.php";
require_once "debug.php";
require_once "criteria.php";

function print_select_project($selected_id = null, $label = null) 
{
	global $database;
	$query = "SELECT id, naam FROM project";	
	debug_log($query);
?>
		<?=$label?><select name="project">
		<option value="-1" disabled selected>Kies een project</option>
<?php
	// Send the query to the database server.
	$stmt = $database->query($query, PDO::FETCH_ASSOC);
	// Loop through all the records.
	foreach ($stmt as $record) 	
	{
		$id = $record["id"];
		$value = $record["naam"];
		
		// Check if this option should be pre-selected.
		$selected_yn="";
		if ($id==$selected_id) {
			$selected_yn = "selected";
		}
		
		// Generate an option for each item in the table.
?>
		<option value="<?=$id?>" <?=$selected_yn?>><?=$value?></option>
<?php
	}
	print "</select>";
}

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
			print_project_short($record);
		}	
	}
	catch (Exception $ex) 
	{
		debug_error("Failed to read projects because ", $ex);
	}
	print "</table>";
}

function print_project_short($record)
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

function print_project_long($project_id)
{
	global $database;
	$query = "SELECT * FROM project WHERE id = :id";	
	print "<!-- $query -->\n";
	$data = [
		"id" => $project_id
	];
	
	try {
		$stmt = $database->prepare($query);
		if ($stmt->execute($data)) 
		{
			// Actually read the record.
			$row = $stmt->fetch(PDO::FETCH_ASSOC);
?>
			<h2>Project &quot;<?=$row["naam"]?>&quot;</h2>
			<ul>
				<li>Semester : <?=$row["semester"]?></li>
				<li>Level : 
<?php
			print_stars($row["sterren"]);
?>
				</li>
			</ul>
			<p>
				<?=$row["omschrijving"]?>
			</p>
<?php
			print_project_criteria($project_id);
		} 
		else 
		{
			debug_warning("Database refused to read project information.");
		}
	} catch (Exception $ex) {
		debug_error("ERROR: Failed to load project : ", $ex);
	}
}

function print_project_criteria($project_id) 
{
	global $database;
	$query = "SELECT criteriumid, gewicht, ROUND(m.max*gewicht,2) as max, c.naam as crit_naam, c.omschrijving as crit_omschrijvind, pc.autocalc, methodeid, m.naam as methode_naam, m.omschrijving as methode_omschrijving FROM `project_criterium` pc, criterium c, beoordeling_methode m WHERE pc.criteriumid = c.id AND c.methodeid = m.id AND groepid = :id";	
	debug_log($query);
	$data = [
		"id" => $project_id
	];
	
	try {
		$stmt = $database->prepare($query);
		if ($stmt->execute($data)) 
		{
			$total = 0;
?>
			<table>
			<tr>
				<th>Criterium</th>
				<th>Gewicht</th>
				<th>Maximaal</th>
				<th>Methode</th>
				<th>Actie</th>
			</tr>
<?php
			foreach ($stmt as $record) 	{
				// Should the max value be taken into account?
				if ($record["autocalc"]==1)
				{				
					// Add to the maximum score for this project.
					$total += $record["max"];
				}
				// For instance, we don't assume the student will get the maximum of negative points!
?>
			<tr>
				<td><?=$record["crit_naam"]?></td>
				<td><?=$record["gewicht"]?></td>
				<td><?=$record["max"]?></td>
				<td><?=$record["methode_naam"]?></td>
			</tr>
<?php
			}
?>
			</table>
			<p>Maximum point available : <?=$total?></p>
<?php
		} 
		else 
		{
			debug_warning("Database refused to read criteria.");
		}
	} 
	catch (Exception $ex) 
	{
		debug_error("ERROR: Failed to load criteria : ", $ex);
	}
}

function print_stars($checked, $field = null)
{
	$js = "";
	echo "<span onmouseleave=\"update_star_size(0)\">";
	for ($i=0;$i<5;$i++) 
	{
		if ($field) {
			$value = $i+1;
			$js = "onmouseover=\"update_star_size($value)\" onclick=\"update_star('$field', $value)\" name=\"sterretje_$value\"";
		}
		$yellow = "";
		if ($i<$checked) 
		{
			$yellow = "checked";
		}
		
		echo "<span class=\"fa fa-star $yellow\" $js></span>\n\r";			
	}
	echo "</span>";
}

function print_add_project_form()
{
?>
	<h2>Ontwerp nieuw project</h2>
	<form>
		Naam : <input type="text" name="name" /><br />
		Semester : <input type="number" name="semester" min="1" max="8" /><br />
		Difficulty : <input type="hidden" name="stars" value="1" min="1" max="5" />
<?php
	print_stars(1, "stars");
?>
		<br />Omschrijving : <textarea name="description" rows="4" cols="50" placeholder="Beschrijf hier het doel van het project."></textarea><br />
		<input type="submit" name="add_project" value="Toevoegen" />
	</form>
<?php
}

function print_edit_project_form($project_id)
{
	global $database;
	$query = "SELECT * FROM project WHERE id = :id";	
	debug_log($query);
	$data = [
		"id" => $project_id
	];
	
	try {
		$stmt = $database->prepare($query);
		if ($stmt->execute($data)) 
		{
			// Actually read the record.
			$row = $stmt->fetch(PDO::FETCH_ASSOC);
?>
			<h2>Project &quot;<?=$row["naam"]?>&quot;</h2>
			<form>
			<input type="hidden" name="project_id" value="<?=$project_id?>"/>
			<ul>
				<li>Naam : <input type="text" name="name" value="<?=$row["naam"]?>" /></li>
				<li>Semester : <input type="number" name="semester" min="1" max="8" value="<?=$row["semester"]?>" /></li>
				<li>Level : <input type="hidden" name="stars" value="<?=$row["sterren"]?>" />
<?php
			print_stars($row["sterren"], "stars");
?>
				</li>
			</ul>
			<textarea name="description" rows="4" cols="50"><?=$row["omschrijving"]?></textarea><br />
			<button type="submit" name="update_project">Wijzigen</button>
			</form>
<?php
			print_edit_project_criteria($project_id);
		} 
		else 
		{
			debug_warning("Database refused to read project information.");
		}
	} catch (Exception $ex) {
		debug_error("ERROR: Failed to load project : ", $ex);
	}
}

/// BELOW are the actual database manipulation functions for projects.

function add_project($name, $description, $semester, $stars)
{
	global $database;
	
	$query  = "INSERT INTO project (naam, semester, sterren, omschrijving) ";
	$query .= "VALUES (:veld1, :veld2, :veld3, :veld4)";	
	
	debug_log($query);

	$data = [
		"veld1" => $name,
		"veld2" => $semester,
		"veld3" => $stars,
		"veld4" => $description
	];
	
	try {
		debug_log("About to add new project.");
		$stmt = $database->prepare($query);
		if ($stmt->execute($data)) 
		{
			debug_log("Project successfully added.");
			$id = $database->lastInsertId();
			add_group($id);
			return $id;
		} 
		else 
		{
			debug_warning("Database refused to add new project.");
		}
	} catch (Exception $ex) {
		debug_error("Failed to create a new project because ", $ex);
	}
}

function add_group($id) {
	global $database;
	
	$query  = "INSERT INTO criterium_groep (id, parent_project, methode, naam) ";
	$query .= "VALUES (:veld1, :veld1, 1, 'Hoofdgroep')";	
	
	debug_log($query);

	$data = [
		"veld1" => $id
	];
	
	try 
	{
		debug_log("About to add new group.");
		$stmt = $database->prepare($query);
		if ($stmt->execute($data)) 
		{
			debug_log("Group successfully added.");
		} 
		else 
		{
			print_warning("Database refused to add new group.");
		}
	} 
	catch (Exception $ex) 
	{
		debug_error("Failed to add new group because ", $ex);
	}
}

function update_project($id, $name, $description, $semester, $stars)
{
	global $database;
	
	$query  = "UPDATE project ";
	$query .= "SET naam = :veld1, ";
	$query .= "    omschrijving = :veld2, ";
	$query .= "    semester = :veld3, ";
	$query .= "    sterren = :veld4 ";
	$query .= "WHERE id = :veld0";
		
	debug_log($query);

	$data = [	
		"veld0" => $id,
		"veld1" => $name,
		"veld2" => $description,
		"veld3" => $semester,
		"veld4" => $stars
	];
	
	try {
		debug_log("About to change project $id.");
		$stmt = $database->prepare($query);
		if ($stmt->execute($data)) 
		{
			debug_log("Project successfully updated.");
		} 
		else 
		{
			debug_warning("Database refused to update this project.");
		}
	} catch (Exception $ex) {
		debug_error("Failed to update project because ", $ex);
	}
}
?>