<?php
require_once "config.inc.php";
require_once "debug.php";

function print_select_method($selected_id = null, $label = null) 
{
	global $database;
	$query = "SELECT id, naam FROM beoordeling_methode";	
	debug_log($query);
?>
		<?=$label?><select name="method">
		<option value="-1" disabled selected>Kies een methode</option>
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

function print_select_criterium($selected_id = null, $label = null) 
{
	global $database;
	$query = "SELECT id, naam FROM criterium";	
	debug_log($query);
?>
		<?=$label?><select name="criterium">
		<option value="-1" disabled selected>Kies een criterium</option>
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

function print_edit_project_criteria($project_id) 
{
	global $database;
	$query = "SELECT groepid, criteriumid, gewicht, ROUND(m.max*gewicht,2) as max, c.naam as crit_naam, c.omschrijving as crit_omschrijving, pc.autocalc, methodeid, m.naam as methode_naam, m.omschrijving as methode_omschrijving FROM `project_criterium` pc, criterium c, beoordeling_methode m WHERE pc.criteriumid = c.id AND c.methodeid = m.id AND groepid = :id";	
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
				<th>Methode</th>
				<th>Gewicht</th>
				<th>Maximaal</th>
				<th>Meerekenen</th>
				<!--<th>Actief</th>-->
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
				print_edit_project_criterium($record);
			}
			print_add_project_criterium($project_id);
			// Quickly create a new criterium here? (without a description)
			print_create_project_criterium($project_id);
?>
			</table>
			<p>Maximum point available : <?=$total?></p>
<?php
		} 
		else 
		{
			debug_warning("Database refused to read criteria.");
		}
	} catch (Exception $ex) {
		debug_error("ERROR: Failed to load criteria : ", $ex);
	}
}

function print_edit_project_criterium($record)
{
	$autocalc_checked = ($record["autocalc"]==1)?"checked":"";
?>
			<tr>
			<form>
				<input type="hidden" name="group_id" value="<?=$record["groepid"]?>" />
				<input type="hidden" name="crit_id" value="<?=$record["criteriumid"]?>" />
				<td><?=$record["crit_naam"]?></td>
				<td><?=$record["methode_naam"]?></td>
				<td><input type="number" name="weight" step="0.1" value="<?=$record["gewicht"]?>" /></td>
				<td><?=$record["max"]?></td>
				<td><input type="checkbox" name="autocalc" <?=$autocalc_checked?> /></td>
				<td>
					<button type="submit" name="update_crit">Wijzigen</button>
					<button type="submit" name="remove_crit">Verwijderen</button>
				</td>
			</form>
			</tr>
<?php
}

function print_add_project_criterium($project_id)
{
?>
			<tr>
			<form>
				<input type="hidden" name="group_id" value="<?=$project_id?>" />
				<td><?php print_select_criterium(); ?></td>
				<td>...</td>
				<td><input type="number" name="weight" step="0.1" value="1" /></td>
				<td>...</td>
				<td><input type="checkbox" name="autocalc" checked /></td>
				<td>
					<button type="submit" name="add_crit">Toevoegen</button>
				</td>
			</form>
			</tr>
<?php
}

function print_create_project_criterium($project_id)
{
?>
			<tr>
			<form>
				<input type="hidden" name="group_id" value="<?=$project_id?>" />
				<td><input type="text" name="crit_name" required /></td>
				<td><?php print_select_method(); ?></td>
				<td><input type="number" name="weight" step="0.1" value="1" /></td>
				<td>...</td>
				<td><input type="checkbox" name="autocalc" checked /></td>
				<td>
					<button type="submit" name="create_crit">Nieuw criterium</button>
				</td>
			</form>
			</tr>
<?php
}


/// BELOW are the actual database manipulation functions for criteria and measurement methods.

function add_project_criterium($group_id, $crit_id, $weight, $autocalc)
{
	global $database;
	
	$query  = "INSERT INTO project_criterium (groepid, criteriumid, gewicht, autocalc) ";
	$query .= "VALUES (:veld1, :veld2, :veld3, :veld4)";	
	
	debug_log($query);

	$data = [
		"veld1" => $group_id,
		"veld2" => $crit_id,
		"veld3" => $weight,
		"veld4" => $autocalc
	];
	
	try 
	{
		debug_log("About to add new criterium to project.");
		$stmt = $database->prepare($query);
		if ($stmt->execute($data)) 
		{
			debug_log("Criterium successfully added.");
		} 
		else 
		{
			print_warning("Database refused to add criterium to project.");
		}
	} 
	catch (Exception $ex) 
	{
		debug_error("Failed to add criterium to project because ", $ex);
	}
}

function update_project_criterium($group_id, $crit_id, $weight, $autocalc)
{
	global $database;
	
	$query  = "UPDATE project_criterium SET gewicht=:veld3, autocalc=:veld4 ";
	$query .= "WHERE groepid=:veld1 AND criteriumid=:veld2";	
	
	debug_log($query);

	$data = [
		"veld1" => $group_id,
		"veld2" => $crit_id,
		"veld3" => $weight,
		"veld4" => $autocalc
	];
	
	try 
	{
		debug_log("About to update criterium for project.");
		$stmt = $database->prepare($query);
		if ($stmt->execute($data)) 
		{
			debug_log("Criterium successfully updated.");
		} 
		else 
		{
			print_warning("Database refused to update criterium for project.");
		}
	} 
	catch (Exception $ex) 
	{
		debug_error("Failed to update criterium for project because ", $ex);
	}
}
?>