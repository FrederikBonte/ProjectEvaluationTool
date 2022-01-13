<?php
require_once "common/projects.php";
include "templates/header_stars.txt";
	
if (array_key_exists("add_project", $_REQUEST)) {
	$name = $_REQUEST["name"];
	$semester = $_REQUEST["semester"];
	$stars = $_REQUEST["stars"];
	$description = $_REQUEST["description"];
	$project_id = add_project($name, $description, $semester, $stars);
}
else if (array_key_exists("update_project", $_REQUEST)) {
	$project_id = $_REQUEST["project_id"];
	$name = $_REQUEST["name"];
	$semester = $_REQUEST["semester"];
	$stars = $_REQUEST["stars"];
	$description = $_REQUEST["description"];
	update_project($project_id, $name, $description, $semester, $stars);
}

print_active_projects();
print_add_project_form();
?>
</body>
</html>