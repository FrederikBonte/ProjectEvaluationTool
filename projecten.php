<?php
include "common/form_gen.php";
include "common/update_projects.php";
include "templates/header_stars.txt";
?>
	<h1>Projecten</h1>
	<h2>Bestaande projecten</h2>	
<?php
	print_active_projects();
?>
	<h2>Ontwerp nieuw project</h2>
<?php
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