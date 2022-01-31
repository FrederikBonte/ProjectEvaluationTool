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
	// * Teachers must now login to ensure safety also added credentials to the rocvantwente.frederikbonte.nl site in preparation.
	// * Manage the list of possible reward methods.
	//   NEVER REMOVE methods that are bound to a criterium. (Would cause an error.)
	//   ONLY un-awarded methods may still be changed (min/max value)
	// * Manage the list of all possible criteria.
	//   NEVER REMOVE CRITERIA THAT WERE ALREADY AWARDED TO STUDENTS!!!
	// * Manage the list of classes.
	// * Manage the list of students.
	// * Import students from CSV.
	// * Export students to CSV and XML.
	// * Create a new editable copy of a project for new classes.
	//   Original project then always(?) becomes inactive.
	//   Simple revisit avoids resubmit value...
	// * Generate an "I talked to this student" screen. ("evaluatie")
	// * Choose who is teaching and select the required class.
	//   No longer needed because of login system.
	// * Generate a project evaluation screen for the teacher.
	// * Choose a project to reward for a student.
	// * Process the values entered by the teacher into the final "beoordeling" table.
	//   Only show projects that match the semester of the class?
	// * Added help text to the home page. (@TODO: Add help to EACH page.)
	// * Personal (settings) page
	//   Allow teachers to change their password. (settings.php) DONE
	//   Allow teachers to choose a new username. DONE
	//   Link Teachers to classes to make selecting students easier. DONE
	// * Admin page to easily add new teachers.
	// * Link Teachers to locations to that not everybody sees all students.
	//	 Create a location table (ID, name, address, etc.)
	//   Link teachers, students and classes to locations. (projects can be shared.)
	//   Only show students and classes to which the currently logged-in teacher is connected.
	// @TODO:
	// * Create admin form to link teachers and locations.
	// * Handle template projects (cannot be used to reward points, only as copy source.)
	// * Import students through xml.
	// * Manage the list of teachers.
	//   Implement a role system that limits change rights.
	// * Allow subgroups of criteria
	//    For instance two sub groups, and the main group calculates the average of those.
	//    Multiple rewards, only the highest is taken into account.
    //    Implement all the possible group calculations: sum, min, max, average and for now... Other...
	// * Nieuwe totaal bereken methode... Gewogen gewicht als maximale score meenemen.
	//
	// Additionally: 
	// More icons: https://fontawesome.com/v4.7/icons/
	
//phpinfo();
include "templates/help.txt";
?>
</body>
</html>