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
else if (array_key_exists("import", $_REQUEST))
{
	$klas = $_REQUEST["klas"];
	//debug_dump($_FILES);	
	$file = $_FILES["students"];
	//debug_dump($file);	
	if (substr($file["name"], strlen($file["name"])-3=="txt"))
	{
		import_students_csv($klas, $file["tmp_name"]);
	}
	else
	{
		debug_warning("Alleen tekst bestanden kunnen op dit moment geimporteerd worden.");
	}
}
	
//print_select_klas();
//print_select_student("0SV1");

$can_edit = can_edit("klas");
$can_view = can_view("klas");
?>
<br/>
<h3>Klas bewerken</h3>
<form method="POST" enctype="multipart/form-data">
<?php
$klas_id = null;
if (array_key_exists("klas", $_REQUEST))
{
	$klas_id = $_REQUEST["klas"];
}

print_select_any_klas($klas_id, "Toon deze klas : ");
print_submit_button("show", "Tonen");
if ($can_edit)
{
	echo "<br />";
?>
<input type="file" name="students" />
<?php
print_submit_button("import", "Importeren");

?>
</form>
<?php
}
if ($can_view)
{
?>
<h3>Exporteren</h3>
<form method="POST" action="export_students.php">
<?php
print_select_any_klas($klas_id, "Exporteer deze klas : ");
print_submit_button("xml", "XML");
print_submit_button("csv", "CSV");
?>
</form>
<?php
}
if ($can_edit)
{
	print_edit_students($klas_id);
}
else if ($can_view)
{
	print_list_students($klas_id);
}
?>
</body>
</html>