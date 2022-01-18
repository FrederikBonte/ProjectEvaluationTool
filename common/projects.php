<?php
require_once "common/config.inc.php";
require_once "common/debug.php";
require_once "common/form_gen.php";
require_once "common/criteria.php";

function print_select_project($selected_id = null, $label = null) 
{
	global $database;
	$query = "SELECT id, naam FROM project WHERE actief=1";	
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
	
	$query  = 	"SELECT project.id, project.naam, project.omschrijving, project.semester, project.sterren, project.actief, project.blauwdruk, (MAX(leerlingnummer) IS NOT NULL) as gebruikt ".
				"FROM project LEFT JOIN beoordeling ON groepid=project.id ".
				"WHERE project.actief=1 ".
				"GROUP BY project.id, project.naam, project.omschrijving, project.semester, project.sterren, project.actief, project.blauwdruk";
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
	$edit = $record["gebruikt"]?"":"&edit";
	$active = ($record["actief"]==1)?"checked":"";
?>
	<input type="hidden" name="project_id" value="<?=$id?>" />
	<tr>
		<td><a href="project.php?id=<?=$id?><?=$edit?>"><?=$name?></a></td>
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

function print_copy_project_form($project_id)
{
?>
	<h3>Maak een nieuwe bewerkbare versie van dit project</h3>
	<form>
<?php
	print_rand_check();
	print_hidden_input("project_id", $project_id);
	print_submit_button("copy_project", "Kopieren");
?>	
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
			<p>
				<a href="beoordelen.php?id=<?=$project_id?>&example">Toon voorbeeld pagina</a>
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
			<p>
				<a href="beoordelen.php?id=<?=$project_id?>&example">Toon voorbeeld pagina</a>
			</p>
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

function copy_project($original_id)
{
	$new_id = copy_project_name($original_id);
	if (isset($new_id)) {
		set_project_active($original_id, false);	
		copy_project_criteria($original_id, $new_id);
		return $new_id;
	}
}

function copy_project_name($original_id)
{
	global $database;
	
	$query  = "INSERT INTO project (naam, semester, sterren, omschrijving) ";
	$query .= "(SELECT concat('Kopie van ',naam) as naam, semester, sterren, omschrijving FROM project WHERE id=:veld0)";
		
	debug_log($query);

	$data = [	
		"veld0" => $original_id
	];
	
	try {
		debug_log("About to copy project $original_id...");
		$stmt = $database->prepare($query);
		if ($stmt->execute($data)) 
		{
			$id = $database->lastInsertId();
			add_group($id);
			debug_log("Project successfully copied.");
			return $id;			
		} 
		else 
		{
			debug_warning("Database refused to copy project.");
		}
	} catch (Exception $ex) {
		debug_error("Failed to copy project because ", $ex);
	}	
}

function copy_project_criteria($source_id, $target_id)
{
	global $database;
	
	$query  = "INSERT INTO project_criterium ";
	$query .= "(SELECT :veld1 as groepid, criteriumid, actief, gewicht, autocalc FROM project_criterium WHERE groepid=:veld0)";
		
	debug_log($query);

	$data = [	
		"veld0" => $source_id,
		"veld1" => $target_id
	];
	
	try {
		debug_log("About to fill project $target_id with the criteria of project $source_id...");
		$stmt = $database->prepare($query);
		if ($stmt->execute($data)) 
		{
			debug_log($stmt->rowCount()." project criteria successfully copied.");
		} 
		else 
		{
			debug_warning("Database refused to copy project criteria");
		}
	} catch (Exception $ex) {
		debug_error("Failed to copy project criteria because ", $ex);
	}	
}

function set_project_active($id, $active = false)
{
	global $database;
	
	$active_value = ($active)?1:0;
	$query  = "UPDATE project ";
	$query .= "SET actief = :veld1 ";
	$query .= "WHERE id = :veld0";
		
	debug_log($query);

	$data = [	
		"veld0" => $id,
		"veld1" => $active_value
	];
	
	try {
		debug_log("About to change active value for project $id.");
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