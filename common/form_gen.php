<?php
require_once "common/config.inc.php";
require_once "common/debug.php";

/// FUNCTIONS that are useful for generating forms.

function print_select($query, $name, $message, $selected_id = null, $label = null) 
{
	global $database;
	debug_log($query);
?>
		<?=$label?><select name="<?=$name?>">
		<option value="-1" disabled selected><?=$message?></option>
<?php
	// Send the query to the database server.
	$stmt = $database->query($query, PDO::FETCH_ASSOC);
	// Loop through all the records.
	foreach ($stmt as $record) 	
	{
		$id = $record["id"];
		$value = $record["value"];
		
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

function print_text_input($name, $value = null, $label = null)
{
?><?=$label?><input type="text" name="<?=$name?>" value="<?=$value?>" /><?php
}

function print_hidden_input($name, $value)
{
?><input type="hidden" name="<?=$name?>" value="<?=$value?>" /><?php
}

function print_submit_button($name, $value)
{
?><button type="submit" name="<?=$name?>"><?=$value?></button><?php	
}

function print_checkbox($name, $value = false)
{
	$checked = $value?"checked":"";
?><input type="checkbox" name="<?=$name?>" <?=$checked?> /><?php
}

?>