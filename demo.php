<?php
include "common/config.inc.php";
include "common/update_students.php";
include "common/projects.php";
include "templates/header_stars.txt";
?>
	<h1>PHP demo pagina</h1>
	<p>
<?php

	print_add_project_form();
?>
	<form method="POST">
<?php		
		print_select_method(null, "Methode : ");
		echo "<br />";
		print_select_criterium(null, "Criterium : ");
		echo "<br />";
		print_select_project(null, "Project : ");
?>	
		<br />
		<input type="submit" name="choose" value="Kies project" />
	</form>
	</p>
<?php
	if (array_key_exists("choose", $_REQUEST)) {
		// Store the selected project id.
		$project_id = $_REQUEST["project"];
		// Print the edit form for this project.
		print_project_criteria($project_id);
	}
	else if (array_key_exists("update", $_REQUEST)) {
		
	}
?>
</body>
</html>