<?php
require_once "common/config.inc.php";
require_once "common/debug.php";
require_once "common/form_gen.php";

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

function print_all_criteria()
{
	global $database;
	$query = 	"SELECT criterium.*, beoordeling_methode.naam as methode_naam, (MAX(leerlingnummer) IS NOT NULL) as in_gebruik ".
				"FROM criterium ".
				"INNER JOIN beoordeling_methode ON criterium.methodeid=beoordeling_methode.id ".
				"LEFT JOIN beoordeling ON criteriumid=criterium.id ".
				"GROUP BY  criterium.id, criterium.naam, criterium.omschrijving, criterium.methodeid, criterium.autocalc, beoordeling_methode.naam";	
	debug_log($query);
	
	try {
		$stmt = $database->prepare($query);
		if ($stmt->execute()) 
		{
?>
			<h2>Beoordelings criteria</h2>
			<p>
				Merk op dat voor gebruikte criteria de beoordelings methode niet meer veranderd mag worden
				omdat dan het cijfer van de student ook zou veranderen.
			</p>
			<table>
			<tr>
				<th>Naam</th>
				<th>Omschrijving</th>
				<th>Methode</th>
				<th>Meerekenen</th>
				<th>Actie</th>
			</tr>
<?php
			foreach ($stmt as $record) 	{
				// For instance, we don't assume the student will get the maximum of negative points!
				print_edit_criterium($record);
			}
			print_add_criterium();
?>
			</table>
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

function print_edit_criterium($record)
{
	$autocalc_checked = ($record["autocalc"]==1)?"checked":"";
	$editable = $record["in_gebruik"]==0;
	//debug_dump($record);
?>
			<tr>
			<form>
				<input type="hidden" name="crit_id" value="<?=$record["id"]?>" />
				<td><input type="text" name="name" value="<?=$record["naam"]?>" /></td>
				<td><input type="text" name="description" value="<?=$record["omschrijving"]?>" /></td>
<?php
	// Only criteria that are not in use may change their rewarding method.
	// Otherwise points that are already awarded to students change their meaning.
	if ($editable) 
	{
?>
		<td><?php print_select_method($record["methodeid"]); ?></td>
<?php
	}
	else
	{
?>
		<td><input type="hidden" name="method" value="<?=$record["methodeid"]?>" /><?=$record["methode_naam"]?></td>
<?php
	}
?>
				
				<td><input type="checkbox" name="autocalc" <?=$autocalc_checked?> /></td>
				<td>
					<button type="submit" name="update_crit">Wijzigen</button>
<?php
	// Only criteria that are not in use may be removed.
	if ($editable) 
	{
?>
					<button type="submit" name="remove_crit">Verwijderen</button>
<?php
	}
?>
				</td>
			</form>
			</tr>
<?php
}

function print_add_criterium()
{
?>
			<tr>
			<form>
				<td><input type="text" name="name" /></td>
				<td><input type="text" name="description" /></td>
				<td><?php print_select_method(); ?></td>
				<td><input type="checkbox" name="autocalc" checked /></td>
				<td>
					<button type="submit" name="add_crit">Toevoegen</button>
				</td>
			</form>
			</tr>
<?php
}

function print_all_methods()
{
	global $database;
	$query = 	"SELECT beoordeling_methode.*, (MAX(leerlingnummer) IS NOT NULL) as in_gebruik, (MAX(criterium.id) IS NOT NULL) as verbonden ".
				"FROM `beoordeling_methode` ".
				"LEFT JOIN criterium ON criterium.methodeid = beoordeling_methode.id ".
				"LEFT JOIN beoordeling ON beoordeling.criteriumid = criterium.id ".
				"GROUP BY beoordeling_methode.id, beoordeling_methode.naam, beoordeling_methode.omschrijving, beoordeling_methode.min, beoordeling_methode.max";	
	debug_log($query);
	
	try {
		$stmt = $database->prepare($query);
		if ($stmt->execute()) 
		{
?>
			<h2>Beoordelings methoden</h2>
			<p>
				Merk op dat voor gebruikte methoden de minimale en maximale waarden niet meer veranderd mogen worden
				omdat dan het cijfer van de student ook zou veranderen.
			</p>
			<table>
			<tr>
				<th>Naam</th>
				<th>Omschrijving</th>
				<th>Min</th>
				<th>Max</th>
				<th>Actie</th>
			</tr>
<?php
			foreach ($stmt as $record) 	{
				// For instance, we don't assume the student will get the maximum of negative points!
				print_edit_method($record);
			}
			print_add_method();
?>
			</table>
<?php
		} 
		else 
		{
			debug_warning("Database refused to read methods.");
		}
	} catch (Exception $ex) {
		debug_error("ERROR: Failed to load methods : ", $ex);
	}
}

function print_edit_method($record)
{
	$editable = $record["in_gebruik"]==0;
	$deletable = $record["verbonden"]==0;
	//debug_dump($record);
?>
			<tr>
			<form>
<?php
	print_hidden_input("method_id", $record["id"]);
?>
				<td><?php print_text_input("name", $record["naam"]); ?></td>
				<td><?php print_text_input("description", $record["omschrijving"]); ?></td>
<?php
	// Only methods that are not in use may change their rewarding method.
	// Otherwise points that are already awarded to students change their meaning.
	if ($editable) 
	{
?>
		<td><input type="number" name="min" value="<?=$record["min"]?>" /></td>
		<td><input type="number" name="max" value="<?=$record["max"]?>" /></td>
<?php
	}
	else
	{
?>
		<td><input type="hidden" name="min" value="<?=$record["min"]?>" /><?=$record["min"]?></td>
		<td><input type="hidden" name="max" value="<?=$record["max"]?>" /><?=$record["max"]?></td>
<?php
	}
?>
				
				<td>
<?php
	print_submit_button("update_method", "Wijzigen");
	// Only methods that are not used by a criterium can be deleted.
	if ($deletable) 
	{
		print_submit_button("remove_method", "Verwijderen");
	}
?>
				</td>
			</form>
			</tr>
<?php
}

function print_add_method()
{
?>
			<tr>
			<form>
				<td><input type="text" name="name" /></td>
				<td><input type="text" name="description" /></td>
				<td><input type="number" name="min" min="0" value="<?=$record["min"]?>" /></td>
				<td><input type="number" name="max" min="1" value="<?=$record["max"]?>" /></td>
				<td>
					<button type="submit" name="add_method">Toevoegen</button>
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

function remove_project_criterium($group_id, $crit_id)
{
	global $database;
	
	$query  = "DELETE FROM project_criterium ";
	$query .= "WHERE groepid=:veld1 AND criteriumid=:veld2";	
	
	debug_log($query);

	$data = [
		"veld1" => $group_id,
		"veld2" => $crit_id
	];
	
	try 
	{
		debug_log("About to remove criterium from project.");
		$stmt = $database->prepare($query);
		if ($stmt->execute($data)) 
		{
			debug_log("Criterium successfully removed.");
		} 
		else 
		{
			print_warning("Database refused to remove criterium from project.");
		}
	} 
	catch (Exception $ex) 
	{
		debug_error("Failed to remove criterium from project because ", $ex);
	}
}

function add_criterium($name, $description, $method_id, $autocalc)
{
	global $database;
	
	$query  = "INSERT INTO criterium (naam, omschrijving, methodeid, autocalc) ";
	$query .= "VALUES (:veld1, :veld2, :veld3, :veld4)";	
	
	debug_log($query);

	$data = [
		"veld1" => $name,
		"veld2" => $description,
		"veld3" => $method_id,
		"veld4" => $autocalc
	];
	
	try {
		debug_log("About to add new criterium.");
		$stmt = $database->prepare($query);
		if ($stmt->execute($data)) 
		{
			debug_log("Criterium successfully added.");
			$id = $database->lastInsertId();
			return $id;
		} 
		else 
		{
			debug_warning("Database refused to add new criterium.");
		}
	} catch (Exception $ex) {
		debug_error("Failed to create a new criterium because ", $ex);
	}
}

function update_criterium($crit_id, $name, $description, $method_id, $autocalc)
{
	global $database;
	
	$query  = "UPDATE criterium SET naam=:veld1, omschrijving=:veld2, methodeid=:veld3, autocalc=:veld4 ";
	$query .= "WHERE id = :veld0";	
	
	debug_log($query);

	$data = [
		"veld0" => $crit_id,
		"veld1" => $name,
		"veld2" => $description,
		"veld3" => $method_id,
		"veld4" => $autocalc
	];
	
	try {
		debug_log("About to update new criterium.");
		$stmt = $database->prepare($query);
		if ($stmt->execute($data)) 
		{
			debug_log("Criterium successfully updated.");
			$id = $database->lastInsertId();
			return $id;
		} 
		else 
		{
			debug_warning("Database refused to update criterium.");
		}
	} catch (Exception $ex) {
		debug_error("Failed to update the criterium because ", $ex);
	}
}

function remove_criterium($crit_id)
{
	global $database;
	
	$query  = "DELETE FROM criterium ";
	$query .= "WHERE id=:veld1";	
	
	debug_log($query);

	$data = [
		"veld1" => $crit_id
	];
	
	try 
	{
		debug_log("About to remove criterium.");
		$stmt = $database->prepare($query);
		if ($stmt->execute($data)) 
		{
			debug_log("Criterium successfully removed.");
		} 
		else 
		{
			print_warning("Database refused to remove criterium.");
		}
	} 
	catch (Exception $ex) 
	{
		debug_error("Failed to remove criterium because ", $ex);
	}
}

function add_method($name, $description, $min, $max)
{
	global $database;
	
	$query  = "INSERT INTO beoordeling_methode (naam, omschrijving, min, max) ";
	$query .= "VALUES (:veld1, :veld2, :veld3, :veld4)";	
	
	debug_log($query);

	$data = [
		"veld1" => $name,
		"veld2" => $description,
		"veld3" => $min,
		"veld4" => $max
	];
	
	try {
		debug_log("About to add new method.");
		$stmt = $database->prepare($query);
		if ($stmt->execute($data)) 
		{
			debug_log("Method successfully added.");
			$id = $database->lastInsertId();
			return $id;
		} 
		else 
		{
			debug_warning("Database refused to add new method.");
		}
	} catch (Exception $ex) {
		debug_error("Failed to create a new method because ", $ex);
	}
}

function update_method($method_id, $name, $description, $min, $max)
{
	global $database;
	
	$query  = "UPDATE beoordeling_methode SET naam=:veld1, omschrijving=:veld2, min=:veld3, max=:veld4 ";
	$query .= "WHERE id = :veld0";	
	
	debug_log($query);

	$data = [
		"veld0" => $method_id,
		"veld1" => $name,
		"veld2" => $description,
		"veld3" => $min,
		"veld4" => $max
	];
	
	try {
		debug_log("About to update method.");
		$stmt = $database->prepare($query);
		if ($stmt->execute($data)) 
		{
			debug_log("Method successfully updated.");
			$id = $database->lastInsertId();
			return $id;
		} 
		else 
		{
			debug_warning("Database refused to update method.");
		}
	} catch (Exception $ex) {
		debug_error("Failed to update method because ", $ex);
	}
}

function remove_method($method_id)
{
	global $database;
	
	$query  = "DELETE FROM beoordeling_methode ";
	$query .= "WHERE id=:veld1";	
	
	debug_log($query);

	$data = [
		"veld1" => $method_id
	];
	
	try 
	{
		debug_log("About to remove method.");
		$stmt = $database->prepare($query);
		if ($stmt->execute($data)) 
		{
			debug_log("Method successfully removed.");
		} 
		else 
		{
			print_warning("Database refused to remove method.");
		}
	} 
	catch (Exception $ex) 
	{
		debug_error("Failed to remove method because ", $ex);
	}
}


function create_project_criterium($group_id, $name, $method_id, $weight, $autocalc)
{
	// Create a new criterium without a description... :(
	$crit_id = add_criterium($name, null, $method_id, $autocalc);
	// Add that new criterium to the project/group.
	add_project_criterium($group_id, $crit_id, $weight, $autocalc);	
}
?>