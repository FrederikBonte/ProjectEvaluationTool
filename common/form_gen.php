<?php
require_once "common/config.inc.php";
require_once "common/debug.php";

/// FUNCTIONS that are useful for generating forms.

function print_select($query, $name, $message, $selected_id = null, $label = null, $javascript = null) 
{
	global $database;
	debug_log($query);
?>
		<?=$label?><select name="<?=$name?>" onchange="<?=$javascript?>">
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

function print_hidden_input($name, $value, $show = false)
{
?><input type="hidden" name="<?=$name?>" value="<?=$value?>" /><?php
	if ($show)
	{
		echo $value;
	}
}

function print_hidden_time($name)
{
?><input type="hidden" name="<?=$name?>" id="<?=$name?>" value="0" /><?php
}

function print_number_input($name, $min, $max, $value = null, $label = null)
{
	// When the value is null, don't actually add a value="" to the field.
	$value_disp = $value?" value=\"$value\" ":"";
?><?=$label?><input type="number" name="<?=$name?>" min="<?=$min?>" max="<?=$max?>" <?=$value_disp?> /><?php
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

function print_rand_check()
{
$rand=rand();
$_SESSION['rand']=$rand;
print_hidden_input("randcheck", $rand);
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
?>