<?php
require_once "common/teachers.php";
require_once "common/locations.php";
include "templates/header.txt";
header('Content-type: text/html; charset=utf-8');

if (!$admin)
{
	// Not an admin
	debug_log("Not an admin opened the admin page.");
	header("Location: index.php");
	exit();
}
else if (array_key_exists("add_teacher", $_REQUEST))
{
	$code = $_REQUEST["code"];
	$firstname = $_REQUEST["firstname"];
	$lastname = $_REQUEST["lastname"];
	$title = $_REQUEST["title"];
	add_teacher($code, $firstname, $lastname, $title);
}
else if (array_key_exists("update_teacher", $_REQUEST))
{
	$code = $_REQUEST["code"];
	$firstname = $_REQUEST["firstname"];
	$lastname = $_REQUEST["lastname"];
	$title = $_REQUEST["title"];
	update_teacher($code, $firstname, $lastname, $title);
}
else if (array_key_exists("add_location", $_REQUEST))
{
	$code = $_REQUEST["code"];
	$name = $_REQUEST["name"];
	$description = $_REQUEST["description"];
	add_location($code, $name, $description);
}
else if (array_key_exists("update_location", $_REQUEST))
{
	$code = $_REQUEST["code"];
	$name = $_REQUEST["name"];
	$description = $_REQUEST["description"];
	update_location($code, $name, $description);
}
else if (array_key_exists("remove_location", $_REQUEST))
{
	$code = $_REQUEST["code"];	
	remove_location($code);
}
else if (array_key_exists("link_teacher", $_REQUEST))
{
	$location_code = $_REQUEST["code"];
	$teacher_code = $_REQUEST["teacher_code"];
	link_teacher_and_location($teacher_code, $location_code);
}
else if (array_key_exists("unlink_teacher", $_REQUEST))
{
	$location_code = $_REQUEST["code"];
	$teacher_code = $_REQUEST["teacher_code"];
	unlink_teacher_and_location($teacher_code, $location_code);
}

print_edit_teachers_form();
print_edit_locations_form();
?>
</body>
</html>