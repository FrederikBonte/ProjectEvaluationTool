<?php
include "common/form_gen.php";
include "common/update_projects.php";
include "templates/header_stars.txt";
	
print_active_projects();
print_add_project();
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