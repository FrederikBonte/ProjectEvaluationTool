<?php
include "templates/header.txt";
	// @DONE: 
	// * Show a project with its data and list of criteria.
	// * Allow user to add criteria...
	//		Select a criterium
	//		Choose a weight.
	//		Add it to the list...
	// * Create a new project.
	//		Name
	//		description
	//		Semester
	//		Difficulty (1-5 stars)
	// (ALL PROJECTS ALWAYS HAVE ONE MAIN GROUP with the same id, to keep things simple for now...)
	//		List of criteria (empty first).
	// * Rename/edit an existing project...
	// @TODO:
	// * Manage the list of possible reward methods.
	// * Manage the list of all possible criteria.
	// * NEVER REMOVE CRITERIA THAT WERE ALREADY AWARDED TO STUDENTS!!!
	// * NEVER EDIT A PROJECT THAT was already rewarded!!!
	//   LEFT JOIN with beoordeling and check that MAX(leerlingnummer) is NULL!
	// * Create a new editable copy of a project for new classes.
	// * Handle template projects (cannot be used to reward points, only as copy source.)
	// * Manage the list of teachers.
	// * Manage the list of classes.
	// * Manage the list of students.
	// * Choose who is teaching and select the required class.
	// * Generate an "I talked to this student" screen. ("evaluatie")
	// * Choose a project to reward for a student.
	// * Generate a project evaluation screen for the teacher.
	// * Process the values entered by the teacher into the final "beoordeling" table.
?>
</body>
</html>