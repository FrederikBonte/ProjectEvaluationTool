<?php
require_once "common/config.inc.php";
require_once "common/debug.php";

/// FUNCTIONS that are useful for generating forms.

function print_select($query, $name, $message, $selected_id = null, $label = null, $javascript = null) 
{
	global $database;
	debug_log($query);
?>
		<?=$label?><select name="<?=$name?>" class="slct_<?=$name?>" onchange="<?=$javascript?>">
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

function print_text_input($name, $value = null, $label = null, $javascript = null)
{
?><?=$label?><input type="text" class="text_<?=$name?>" name="<?=$name?>" value="<?=$value?>"  onchange="<?=$javascript?>" /><?php
}

function print_password_input($name, $value = null, $label = null, $id=null, $javascript = null)
{
?><?=$label?><input type="password" required id="<?=$id?>" name="<?=$name?>" value="<?=$value?>" 
onchange="<?=$javascript?>" onkeypress="this.onchange();" onpaste="this.onchange();" oninput="this.onchange();" /><?php
}

function print_hidden_input($name, $value, $show = false, $id = null)
{
?><input type="hidden" id="<?=$id?>" name="<?=$name?>" value="<?=$value?>" /><?php
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
?><?=$label?><input type="number" name="<?=$name?>" class="num_<?=$name?>" min="<?=$min?>" max="<?=$max?>" <?=$value_disp?> /><?php
}

function print_submit_button($name, $value, $id = null)
{
?><button type="submit" class="smt_<?=$name?>" id="<?=$id?>" name="<?=$name?>"><?=$value?></button><?php	
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

/// Bit of a weird location for this, 
/// but I don't want a dedicated security.php yet.
function can_edit($id = null)
{
	return has_role_for($id, "edit");
}

function can_create($id = null)
{
	return has_role_for($id, "edit");
}

/// Bit of a weird location for this, 
/// but I don't want a dedicated security.php yet.
function can_view($id = null)
{
	// @TODO: Build much more complex check...
	return true;
}

function has_role_for($id, $role)
{
	global $database;
	// @TODO: Build more complex role checking system.
	global $database;
	
	// For now simply check if user is member of the admin group.
	$sql = 
	"SELECT * ".
	"FROM docent_rol ".
	"WHERE docentcode=:field1 AND rol=1";
	
	echo "<!-- $sql -->\n\r";
	
	try {		
		// Prepare a query...
		$stmt = $database->prepare($sql);
		// Additional database
		$data = [
			"field1" => $_SESSION["docent"],
		];
		
		// Activate the query...
		$stmt->execute($data);
		// Retrieve one record.
		$row=$stmt->fetch(PDO::FETCH_ASSOC);
		// Return true if a record exists.
		return $row;
	}
	catch (Exception $ex)
	{
		debug_error("Failed to check for login because ", $ex);
	}	
}
?>