<?php
require_once "common/settings.php";
include "templates/header.txt";

$edit = can_edit();
?>
<h2>Huidige docent code <?=$_SESSION["docent"]?></h2>
<p>
	Aanspreekvorm : <?=$_SESSION["name"]?><br />
	Mag wijzigen? <b><?=$edit?"ja":"nee"?></b>
</p>
</body>
</html>