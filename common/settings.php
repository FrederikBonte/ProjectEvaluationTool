<?php
require_once "common/config.inc.php";
require_once "common/debug.php";
require_once "common/form_gen.php";

// Functions to edit personal settings
// Change username
// Change password
// View current settings...

function print_teacher_information()
{
	global $database;
	
	// Read the record for this teacher.
	$sql = 
	"SELECT * ".
	"FROM docent ".
	"WHERE code=:field1";
	
	debug_log($sql);
	
	$code = $_SESSION["docent"];
	try {		
		// Prepare a query...
		$stmt = $database->prepare($sql);
		// Additional database
		$data = [
			"field1" => $code,
		];
		
		// Activate the query...
		$stmt->execute($data);
		// Retrieve one record.
		$row=$stmt->fetch(PDO::FETCH_ASSOC);
		
		//debug_dump($row);
		$firstname = $row["voornaam"];
		$lastname = $row["achternaam"];
		$title = $row["aanspreekvorm"];
		$username = $row["username"];
?>
	<h3>Gegevens voor '<?=$code?>'</h3>
	<ul>
		<li>Voornaam : <?=$firstname?></li>
		<li>Achternaam : <?=$lastname?></li>
		<li>Aanspreekvorm : <?=$title?></li>
		<li>Inlognaam : <?=$username?></li>
		<li>Kan inzien : <?=can_view()?"ja":"<i>nee</i>"?></li>
		<li>Kan wijzigen : <?=can_edit()?"ja":"<i>nee</i>"?></li>
		<li>Kan aanmaken : <?=can_create()?"ja":"<i>nee</i>"?></li>
	</ul>
<?php
		
	}
	catch (Exception $ex)
	{
		debug_error("Failed to read teacher information because ", $ex);
	}		
}

function get_username()
{
	global $database;
	
	// Read the record for this teacher.
	$sql = 
	"SELECT username ".
	"FROM docent ".
	"WHERE code=:field1";
	
	debug_log($sql);
	
	$code = $_SESSION["docent"];
	try {		
		// Prepare a query...
		$stmt = $database->prepare($sql);
		// Additional database
		$data = [
			"field1" => $code,
		];
		
		// Activate the query...
		$stmt->execute($data);
		// Retrieve one record.
		$row=$stmt->fetch(PDO::FETCH_ASSOC);
		return $row["username"];
	}
	catch (Exception $ex)
	{
		debug_error("Failed to read teacher information because ", $ex);
	}
}

function print_change_passwd_form()
{
	$code = $_SESSION["docent"];
?>
	<h3>Inlog gegevens wijzigen</h3>
	<p>
		Uw inlog naam moet uniek zijn. 
		Als u dus een naam kiest die al in gebruik is,
		dan wordt de wijziging niet geaccepteerd.
	</p>
	<form method="POST">
<?php
	print_rand_check();
	print_text_input("username", get_username(), "Inlog naam : " );
	echo "<br />";
	print_password_input("passwd1", null, "Wachtwoord 1 : ", "passwd1", "check_passwd()");
	echo "<br />";
	print_password_input("passwd2", null, "Wachtwoord 2 : ", "passwd2", "check_passwd()");
	echo "<br />";
	print_submit_button("change_passwd", "Wijzigen", "chpasswd");
?>
	</form>
<?php
}


/// BELOW ARE THE ACTUAL DATABASE MANIPULATION FUNCTIONS

function update_username_password($username, $password)
{
	global $database;
	
	// Read the record for this teacher.
	$sql = 
	"UPDATE docent ".
	"SET username=:field1, password=:field2 ".
	"WHERE code=:field0";
	
	debug_log($sql);
	
	try {		
		// Prepare a query...
		$stmt = $database->prepare($sql);
		// Additional database
		$data = [
			"field0" => $_SESSION["docent"],
			"field1" => $username,
			"field2" => $password
		];
		
		// Activate the query...
		if ($stmt->execute($data))
		{
			debug_warning("Username en wachtwoord zijn aangepast. De volgende keer dat u inlogt moet u deze gebruiken!");
		}
		else
		{
			debug_warning("Uw inlognaam en wachtwoord zijn niet gewijzigd.");
		}
	}
	catch (Exception $ex)
	{
		debug_error("Failed to change username and password because ", $ex);
	}	
}
?>