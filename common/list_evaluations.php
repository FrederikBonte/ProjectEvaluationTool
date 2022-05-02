<?php
require_once "config.inc.php";
require_once "debug.php";

session_start();

if (array_key_exists("klas", $_REQUEST))
{
	// Print students in order of last evaluation.
	// Students that haven't had a talk longest first.
	print_evaluated_students($_REQUEST["klas"]);
}

function print_evaluated_students($klas_id = null)
{
	global $database;
	$code = substr($_SESSION["docent"],0,5);
	$query = "SELECT leerling.*, MAX(evaluatie.datum) as datum ".
			"FROM leerling ".
			"LEFT JOIN evaluatie ON evaluatie.leerlingnummer = leerling.nummer ".
			"WHERE leerling.actief=true AND locatiecode in (SELECT locatiecode FROM docent_locatie WHERE docentcode='$code') ".
			(($klas_id==null)?"":"AND klas='$klas_id' ").
			"GROUP BY leerling.nummer, leerling.voornaam, leerling.tussenvoegsel, leerling.achternaam, leerling.klas ".
			"ORDER BY MAX(evaluatie.datum) DESC";
	debug_log($query);
	
	try {
		$stmt = $database->prepare($query);
		if ($stmt->execute()) 
		{
?>
			<h2>Studenten</h2>
			<table>
			<tr>
				<th>Nummer</th>
				<th>Naam</th>
				<th>Tussenvoegsel</th>
				<th>Achternaam</th>
				<th>Datum</th>
			</tr>
<?php
			foreach ($stmt as $record) 	{
?>
			<tr>
				<td><?=$record["nummer"]?></td>
				<td><?=$record["voornaam"]?></td>
				<td><?=$record["tussenvoegsel"]?></td>
				<td><?=$record["achternaam"]?></td>
				<td><?=$record["datum"]?></td>
			</tr>
<?php
			}
?>
			</table>
<?php
		} 
		else 
		{
			debug_warning("Database refused to read students.");
		}
	} catch (Exception $ex) {
		debug_error("ERROR: Failed to load students : ", $ex);
	}	
}
?>