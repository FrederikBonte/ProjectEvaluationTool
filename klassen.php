<?php
require_once "common/students.php";
include "templates/header.txt";
header('Content-type: text/html; charset=utf-8');

if (array_key_exists("update_student", $_REQUEST))
{
	if (!array_key_exists("klas", $_REQUEST))
	{
		debug_warning("De student die u wilt wijzigen zit (waarschijnlijk) nog in een klas die niet langer actief is. Kies a.u.b. een nieuwe actieve klas voor deze leerling.");
	}
	else 
	{
		$id = $_REQUEST["student_id"];
		$firstname = $_REQUEST["firstname"];
		$middlename = $_REQUEST["middlename"];
		if (strlen(trim($middlename))==0)
		{
			$middlename = null;
		}
		$lastname = $_REQUEST["lastname"];
		$klas = $_REQUEST["klas"];
		$active = array_key_exists("actief", $_REQUEST)?1:0;
		update_student($id, $firstname, $middlename, $lastname, $klas, $active);
	}
}
	
print_select_klas();
print_select_student("0SV1");
?>
<br/>
<form>
<?php
$klas_id = null;
if (array_key_exists("klas", $_REQUEST))
{
	$klas_id = $_REQUEST["klas"];
}

print_select_any_klas($klas_id, "Toon deze klas : ");
print_submit_button("show", "Tonen");
?>
</form>
<?php
print_edit_students($klas_id);
?>
</body>
</html>