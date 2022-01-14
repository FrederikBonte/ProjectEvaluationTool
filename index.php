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
	//   ENFORCE that in-use projects cannot be modified in any way. (Also adding new criteria will skew the original rewarding.)
	// * Rename/edit an existing project...
	// * NEVER EDIT A PROJECT THAT was already rewarded!!!
	//   LEFT JOIN with beoordeling and check that MAX(leerlingnummer) is NULL!
	// * Manage the list of possible reward methods.
	//   NEVER REMOVE methods that are bound to a criterium. (Would cause an error.)
	//   ONLY un-awarded methods may still be changed (min/max value)
	// * Manage the list of all possible criteria.
	//   NEVER REMOVE CRITERIA THAT WERE ALREADY AWARDED TO STUDENTS!!!
	// @TODO:
	// * Create a new editable copy of a project for new classes.
	//   Original project then always(?) becomes inactive.
	// * Handle template projects (cannot be used to reward points, only as copy source.)
	// * Manage the list of teachers.
	// * Manage the list of classes.
	// * Manage the list of students.
	// * Choose who is teaching and select the required class.
	// * Generate an "I talked to this student" screen. ("evaluatie")
	// * Choose a project to reward for a student.
	// * Generate a project evaluation screen for the teacher.
	// * Process the values entered by the teacher into the final "beoordeling" table.
	// * Allow subgroups of criteria
	//    For instance two sub groups, and the main group calculates the average of those.
	//    Multiple rewards, only the highest is taken into account.
    //    Implement all the possible group calculations: sum, min, max, average and for now... Other...
	
?>
</body>
</html>