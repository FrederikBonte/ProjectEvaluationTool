<?php
require_once "common/students.php";
include "templates/header.txt";
	
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