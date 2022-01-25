<?php
require_once "common/settings.php";
include "templates/header.txt";

if (array_key_exists("change_passwd", $_REQUEST))
{
	$rand1 = $_REQUEST["randcheck"];
	$rand2 = array_key_exists("rand", $_SESSION)?$_SESSION["rand"]:-1;
	if ($rand1==$rand2)
	{
		unset($_SESSION["rand"]);
		debug_dump($_REQUEST);
		$username = $_REQUEST["username"];
		$password = $_REQUEST["passwd2"];
		update_username_password($username, $password);
	}
	else
	{
		debug_warning("Resubmit detected and avoided.");
	}	
}

print_teacher_information();
print_change_passwd_form();
?>
	<script type="text/javascript" src="js/check_passwd.js"></script>
</body>
</html>