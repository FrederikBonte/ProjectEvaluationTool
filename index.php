<?php
include "common/config.inc.php";
include "common/greetings.php";
include "common/form_gen.php";
include "common/update_students.php";
include "common/update_projects.php";
?>
<!doctype html>
<html>
<head>
	<title>PHP demo pagina voor SQL</title>
	<link rel="stylesheet" href="styles/style.css" type="text/css" />
	<!-- Add icon library -->
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
	<script>
function update_star(field, value) {
	var element = document.getElementsByName(field)[0];
	element.value = value;
	for (i=1;i<=5;i++) {
		var sterretje = document.getElementsByName("sterretje_"+i)[0];
		if (i<=value) {
			sterretje.style.color = "yellow";
		}
		else {
			sterretje.style.color = "black";
		}
	}
}

function update_star_size(value) {
	for (i=1;i<=5;i++) {
		var sterretje = document.getElementsByName("sterretje_"+i)[0];
		if (i<=value) {
			sterretje.style.fontSize = "110%";
		}
		else {
			sterretje.style.fontSize = "100%";
		}
	}
}
	</script>
</head>
<body>

	<h1>PHP demo pagina</h1>
	<p>
<?php
	// @TODO:
	// * Manage the list of possible reward methods.
	// * Manage the list of all possible criteria.
	// * Show a project with its data and list of criteria.
	// * Allow user to add criteria...
	//		Select a criterium
	//		Choose a weight.
	//		Add it to the list...
	//   NEVER REMOVE CRITERIA THAT WERE ALREADY AWARDED TO STUDENTS!!!
	// * Create a new project.
	//		Name
	//		description
	//		Semester
	//		Difficulty (1-5 stars)
	// (ALL PROJECTS ALWAYS HAVE ONE MAIN GROUP with the same id, to keep things simple for now...)
	//		List of criteria (empty first).
	// * Rename/edit an existing project...
	print_add_project();
?>
	<form action="." method="POST">
<?php		
		print_select_method();
		echo "<br />";
		print_select_criterium();
		echo "<br />";
		print_select_project();
?>	
		<br />
		<input type="submit" name="choose" value="Kies project" />
	</form>
	</p>
<?php
	$project_id = null;
	if (array_key_exists("choose", $_REQUEST)) {
		// Store the selected project id.
		$project_id = $_REQUEST["project"];
	}
	else if (array_key_exists("add_project", $_REQUEST)) {
		$name = $_REQUEST["name"];
		$description = $_REQUEST["description"];
		$semester = $_REQUEST["semester"];
		$stars = $_REQUEST["stars"];
		add_project($name, $description, $semester, $stars);
	}
	else if (array_key_exists("add_crit", $_REQUEST)) {
		// Store the selected project id.
		$project_id = $_REQUEST["project"];
		$criterium_id = $_REQUEST["criterium"];
		$weight = $_REQUEST["weight"];
		add_criterium($project_id, $criterium_id, $weight);
	}
	else if (array_key_exists("edit_crit", $_REQUEST)) {
		// Store the selected project id.
		$project_id = $_REQUEST["project"];
		$criterium_id = $_REQUEST["criterium"];
		$weight = $_REQUEST["weight"];
		update_criterium($project_id, $criterium_id, $weight);
	}
	else if (array_key_exists("del_crit", $_REQUEST)) {
		$project_id = $_REQUEST["project"];
		$criterium_id = $_REQUEST["criterium"];
		remove_criterium($project_id, $criterium_id);
	} 

	if ($project_id) 
	{
		// Print the edit form for this project.
		print_edit_project($project_id);
	}
?>
</body>
</html>