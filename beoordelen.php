<?php
require_once "common/criteria.php";
require_once "common/projects.php";
require_once "common/evaluate.php";
include "templates/header_stars.txt";
?>
	<script type="text/javascript" src="js/load_klas.js"></script>	
<?php
	
if (array_key_exists("id", $_REQUEST)) {
	$project_id = $_REQUEST["id"];
	if (array_key_exists("example", $_REQUEST))
	{
?>
		<h2>Voorbeeld beoordelingsformulier</h2>
<?php		
		print_project_evaluation_form($project_id);
	}
}
else if (array_key_exists("start_evaluate", $_REQUEST))
{
	if (array_key_exists("student", $_REQUEST) && array_key_exists("project", $_REQUEST))
	{
?>
	<h2>Start project evaluatie voor een student</h2>		
	<script type="text/javascript" src="js/update_score.js"></script>	
<?php
//		debug_dump($_REQUEST);
		$student_id = $_REQUEST["student"];
		$project_id = $_REQUEST["project"];
		// @TODO: CHECK legal student and project.
		print_project_evaluation_form($project_id, $student_id);
	}
	else
	{
?>
	<h2>Selecteer een project en een student om te evalueren</h2>
<?php
	}
}
else if (array_key_exists("evaluate_project", $_REQUEST))
{	
	$rand1 = $_REQUEST["randcheck"];
	$rand2 = array_key_exists("rand", $_SESSION)?$_SESSION["rand"]:-1;
	if ($rand1==$rand2)
	{
		unset($_SESSION["rand"]);
		debug_dump($_REQUEST);
		$criteria = $_REQUEST["criterium"];
		$student = $_REQUEST["student"];
		$project = $_REQUEST["project"];
		add_student_project($student, $project, $criteria);
		print_select_klas_form();
	}
	else
	{
		debug_warning("Resubmit detected and avoided.");
	}
}
else if (array_key_exists("start_coach", $_REQUEST))
{
	if (array_key_exists("student", $_REQUEST))
	{
		$student_id = $_REQUEST["student"];
	//	debug_dump($_REQUEST);
		print_coach_student_form($student_id);
	}
	else
	{
?>
	<h2>Selecteer een student om te coachen</h2>
<?php
	}
}
else if (array_key_exists("evaluate_student", $_REQUEST))
{
//	debug_dump($_REQUEST);
	$student_id = $_REQUEST["student"];
	$text = $_REQUEST["conversation"];
	$time = $_REQUEST["tijd"];
	$rand1 = $_REQUEST["randcheck"];
	$rand2 = array_key_exists("rand", $_SESSION)?$_SESSION["rand"]:-1;
	if ($rand1==$rand2)
	{
		unset($_SESSION["rand"]);
		add_student_evalution($student_id, $time, $text);
		print_select_klas_form();
	}
	else
	{
		debug_warning("Resubmit detected and avoided.");
	}
}
else
{
//	$data = array(
//		"mijn.data" => "blurp"
//	);
//	
//	echo $data["mijn.data"];
	
	print_select_klas_form();
}	
?>
	<div id="recent_students">
	</div>
</body>
</html>