<?php
require_once "common/config.inc.php";
require_once "common/debug.php";
require_once "common/form_gen.php";

function print_project_evaluation_form($project_id, $student_number = null)
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
			print_project_evaluation_criteria($project_id);
		} 
		else 
		{
			debug_warning("Database refused to read project information.");
		}
	} catch (Exception $ex) {
		debug_error("ERROR: Failed to load project : ", $ex);
	}
}

function print_project_evaluation_criteria($project_id) 
{
	global $database;
	$query = "SELECT criteriumid, gewicht, ROUND(m.max*gewicht,2) as max, c.naam as crit_naam, c.omschrijving as crit_omschrijving, pc.autocalc, methodeid, m.naam as methode_naam, m.min as methode_min, m.max as methode_max, m.omschrijving as methode_omschrijving ".
			 "FROM `project_criterium` pc, criterium c, beoordeling_methode m ".
			 "WHERE pc.criteriumid = c.id AND c.methodeid = m.id AND groepid = :id";	
	debug_log($query);
	$data = [
		"id" => $project_id
	];
	
	try {
		$stmt = $database->prepare($query);
		if ($stmt->execute($data)) 
		{
?>
			<form>
			<table>
			<tr>
				<th>Criterium</th>
				<th>Omschrijving</th>
				<th>Beoordeling</th>
			</tr>
<?php
			foreach ($stmt as $record) 	{
?>
			<tr>
				<td><?=$record["crit_naam"]?></td>
				<td><?=$record["crit_omschrijving"]?></td>
				<td><?php debug_log($record["methode_naam"]); print_evaluate_method($record["criteriumid"], $record["methodeid"], $record["methode_min"], $record["methode_max"]) ?></td>
			</tr>
<?php				
				print_rand_check();
			}
?>
			<tr><td>Opslaan</td><td></td><td><?php print_submit_button("evaluate_project", "Opslaan"); ?></td></tr>
			</table>
			</form>
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

function print_evaluate_method($crit_id, $method_id, $min, $max)
{
	if ($method_id==1 || ($min==0 && $max==1)) {
		print_evaluate_yes_no($crit_id);
	}
	else if ($method_id==5 || ($min==0 && $max==2)) 
	{
		print_evaluate_NMJ($crit_id);
	}
	else if ($method_id==2 || ($min==0 && $max==3)) 
	{
		print_evaluate_IVG($crit_id);
	}
	else if ($method_id==6 || ($min==1 && $max==1)) 
	{
		print_evaluate_bias($crit_id, 1);
	}
	else if ($min==0 && $max==0)
	{
		print_evaluate_bias($crit_id, 0);
	}
	else 
	{
		print_number_input("criterium[$crit_id]", $min, $max);
	}
}

function print_evaluate_yes_no($crit_id)
{
?>
	<select name="criterium[<?=$crit_id?>]">
		<option value="-1" selected disabled>Nee/Ja</option>
		<option value="0">Nee</option>
		<option value="1">Ja</option>
	</select>
<?php
}

function print_evaluate_NMJ($crit_id)
{
?>
	<select name="criterium[<?=$crit_id?>]">
		<option value="-1" selected disabled>Nee/Matig/Ja</option>
		<option value="0">Nee</option>
		<option value="1">Matig</option>
		<option value="2">Ja</option>
	</select>
<?php
}

function print_evaluate_IVG($crit_id)
{
?>
	<select name="criterium[<?=$crit_id?>]">
		<option value="-1" selected disabled>Incompleet/Goed</option>
		<option value="0">Nee</option>
		<option value="1">Incompleet</option>
		<option value="2">Voldoende</option>
		<option value="2">Goed</option>
	</select>
<?php
}

function print_evaluate_bias($crit_id, $value = 1)
{
	print_hidden_input("criterium[<?=$crit_id?>]", $value, true);
}

/// BELOW are the actual database manipulation functions for projects.

?>