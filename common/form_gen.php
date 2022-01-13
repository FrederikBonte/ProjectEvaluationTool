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

function print_select_project($selected_id = null) 
{
	global $database;
	$query = "SELECT id, naam FROM project";	
	print "<!-- $query -->\n";
?>
		Project : <select name="project">
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

?>























