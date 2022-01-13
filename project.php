<?php
require_once "common/criteria.php";
require_once "common/projects.php";
include "templates/header_stars.txt";
	
$edit = false;
if (array_key_exists("id", $_REQUEST)) {
	$project_id = $_REQUEST["id"];
	if (array_key_exists("edit", $_REQUEST))
	{
		$edit = true;
	}
}
if (array_key_exists("add_project", $_REQUEST)) {
	$name = $_REQUEST["name"];
	$semester = $_REQUEST["semester"];
	$stars = $_REQUEST["stars"];
	$description = $_REQUEST["description"];
	$project_id = add_project($name, $description, $semester, $stars);
	$edit = true;
}
else if (array_key_exists("update_project", $_REQUEST)) 
{
	$project_id = $_REQUEST["project_id"];
	$name = $_REQUEST["name"];
	$semester = $_REQUEST["semester"];
	$stars = $_REQUEST["stars"];
	$description = $_REQUEST["description"];
	update_project($project_id, $name, $description, $semester, $stars);
	$edit = true;
}
else if (array_key_exists("add_crit", $_REQUEST)) 
{
	$group_id = $_REQUEST["group_id"];
	$crit_id = $_REQUEST["criterium"];
	$weight = $_REQUEST["weight"];
	$autocalc = array_key_exists("autocalc", $_REQUEST)?1:0;
	add_project_criterium($group_id, $crit_id, $weight, $autocalc);
	$project_id = $group_id;
	$edit = true;
}
else if (array_key_exists("remove_crit", $_REQUEST)) 
{
	$group_id = $_REQUEST["group_id"];
	$crit_id = $_REQUEST["crit_id"];
	remove_project_criterium($group_id, $crit_id);
	$project_id = $group_id;
	$edit = true;
}
else if (array_key_exists("update_crit", $_REQUEST)) 
{
	$group_id = $_REQUEST["group_id"];
	$crit_id = $_REQUEST["crit_id"];
	$weight = $_REQUEST["weight"];
	$autocalc = array_key_exists("autocalc", $_REQUEST)?1:0;
	update_project_criterium($group_id, $crit_id, $weight, $autocalc);
	$project_id = $group_id;
	$edit = true;
}
else if (array_key_exists("create_crit", $_REQUEST)) 
{
	$group_id = $_REQUEST["group_id"];
	$crit_name = $_REQUEST["crit_name"];
	$method_id = $_REQUEST["method"];
	$weight = $_REQUEST["weight"];
	$autocalc = array_key_exists("autocalc", $_REQUEST)?1:0;
	create_project_criterium($group_id, $crit_name, $method_id, $weight, $autocalc);
	$project_id = $group_id;
	$edit = true;
}

if (isset($project_id))
{
	if ($edit)
	{
		// Print the forms for editing the current project.
		print_edit_project_form($project_id);
	}
	else
	{
		// View the project with all its criteria.
		print_project_long($project_id);
	}	
}
else
{
	// Apparently the user got here without a specific project to view or edit...
	// Simply provide the "Do you want to make a project?" form. #frozen
	print_add_project_form();
}

?>
</body>
</html>