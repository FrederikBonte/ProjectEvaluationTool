<?php
require_once "common/criteria.php";
include "templates/header.txt";
	
if (array_key_exists("add_crit", $_REQUEST)) 
{
	$name = $_REQUEST["name"];
	$description = $_REQUEST["description"];
	$method_id = $_REQUEST["method"];;
	$autocalc = (array_key_exists("autocalc", $_REQUEST))?1:0;
	add_criterium($name, $description, $method_id, $autocalc);
}
else if (array_key_exists("update_crit", $_REQUEST)) 
{
	$crit_id = $_REQUEST["crit_id"];
	$name = $_REQUEST["name"];
	$description = $_REQUEST["description"];
	$method_id = $_REQUEST["method"];;
	$autocalc = (array_key_exists("autocalc", $_REQUEST))?1:0;
	update_criterium($crit_id, $name, $description, $method_id, $autocalc);
}
else if (array_key_exists("add_method", $_REQUEST)) 
{
	$name = $_REQUEST["name"];
	$description = $_REQUEST["description"];
	$min = $_REQUEST["min"];
	$max = $_REQUEST["max"];
	add_method($name, $description, $min, $max);
}
else if (array_key_exists("update_method", $_REQUEST)) 
{
	$method_id = $_REQUEST["method_id"];
	$name = $_REQUEST["name"];
	$description = $_REQUEST["description"];
	$min = $_REQUEST["min"];
	$max = $_REQUEST["max"];
	update_method($method_id, $name, $description, $min, $max);
}
else if (array_key_exists("remove_method", $_REQUEST)) 
{
	remove_method($_REQUEST["method_id"]);
}
else if (array_key_exists("remove_crit", $_REQUEST)) 
{
	remove_criterium($_REQUEST["crit_id"]);
}


print_all_criteria();
print_all_methods();
?>
</body>
</html>