<?php
require_once "common/criteria.php";
require_once "common/projects.php";
require_once "common/evaluate.php";
include "templates/header_stars.txt";
	
if (array_key_exists("evaluate_project", $_REQUEST))
{	
	$rand1 = $_REQUEST["randcheck"];
	$rand2 = array_key_exists("rand", $_SESSION)?$_SESSION["rand"]:-1;
	if ($rand1==$rand2)
	{
		unset($_SESSION["rand"]);
		debug_dump($_REQUEST);
	}
	else
	{
		debug_warning("Resubmit detected and avoided.");
	}
}
else if (array_key_exists("id", $_REQUEST)) {
	$project_id = $_REQUEST["id"];
	if (array_key_exists("example", $_REQUEST))
	{
?>
		<h2>Voorbeeld beoordelingsformulier</h2>
<?php		
		print_project_evaluation_form($project_id);
	}
}
?>
</body>
</html>