<?php
require_once "common/projects.php";
include "templates/header_stars.txt";
	
if (array_key_exists("add_project", $_REQUEST)) {
	$name = $_REQUEST["name"];
	$semester = $_REQUEST["semester"];
	$stars = $_REQUEST["stars"];
	$description = $_REQUEST["description"];
	// @TODO: Security check create role.
	$project_id = add_project($name, $description, $semester, $stars);
}
else if (array_key_exists("update_project", $_REQUEST)) {
	$project_id = $_REQUEST["project_id"];
	$name = $_REQUEST["name"];
	$semester = $_REQUEST["semester"];
	$stars = $_REQUEST["stars"];
	$description = $_REQUEST["description"];
	// @TODO: Security check edit role.
	update_project($project_id, $name, $description, $semester, $stars);
}
else if (array_key_exists("copy_project", $_REQUEST)) {
	$project_id = $_REQUEST["project_id"];
	$name = $_REQUEST["name"];
	$semester = $_REQUEST["semester"];
	$stars = $_REQUEST["stars"];
	$description = $_REQUEST["description"];
	// @TODO: Security check edit role.
	copy_project($project_id, $name, $description, $semester, $stars);
}

if (can_view("project"))
{
	print_active_projects();
}

if (can_create("project"))
{
	print_add_project_form();
}
?>
</body>
</html>