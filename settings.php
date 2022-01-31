<?php
require_once "common/teachers.php";
require_once "common/students.php";
include "templates/header.txt";

if (array_key_exists("change_passwd", $_REQUEST))
{
	$rand1 = $_REQUEST["randcheck"];
	$rand2 = array_key_exists("rand", $_SESSION)?$_SESSION["rand"]:-1;
	if ($rand1==$rand2)
	{
		unset($_SESSION["rand"]);
		//debug_dump($_REQUEST);
		$username = $_REQUEST["username"];
		$password = $_REQUEST["passwd2"];
		update_username_password($username, $password);
	}
	else
	{
		debug_warning("Resubmit detected and avoided.");
	}	
}
else if (array_key_exists("add_klas", $_REQUEST))
{
	$code = $_REQUEST["code"];
	$description = $_REQUEST["description"];
	$year = $_REQUEST["year"];
	$semester = $_REQUEST["semester"];
	add_klas($code, $description, $year, $semester);
}
else if (array_key_exists("assign_klas", $_REQUEST))
{
	$code = $_REQUEST["klas"];
	assign_klas_to_teacher($code, $_SESSION["docent"]);
}
else if (array_key_exists("unassign_klas", $_REQUEST))
{
	$code = $_REQUEST["code"];
	unassign_klas_from_teacher($code, $_SESSION["docent"]);
}
else if (array_key_exists("update_klas", $_REQUEST))
{
	$code = $_REQUEST["code"];
	$description = $_REQUEST["description"];
	$year = $_REQUEST["year"];
	$semester = $_REQUEST["semester"];
	$active = array_key_exists("active", $_REQUEST)?1:0;
	update_klas($code, $description, $year, $semester, $active);	
}

print_teacher_information();
print_change_passwd_form();
print_edit_docent_klas_form($_SESSION["docent"]);
?>
	<script type="text/javascript" src="js/check_passwd.js"></script>
</body>
</html>