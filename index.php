<?php
include "common/form_gen.php";
include "common/update_students.php";
include "common/update_projects.php";
include "templates/header.txt";
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
?>
</body>
</html>