<?php
require_once "common/admin.php";
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

print_edit_teachers_form();
?>
</body>
</html>