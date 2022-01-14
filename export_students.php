<?php
require_once "common/students.php";
$mode = "csv";
if (array_key_exists("xml", $_REQUEST))
{
	header('Content-Type: application/xml; charset=utf-8');
	$mode = "xml";
}
else
{
	header('Content-Type: text/plain; charset=utf-8');	
}
if (array_key_exists("klas", $_REQUEST))
{
	$klas_id = $_REQUEST["klas"];
	header('Content-Disposition: inline; filename="students_'.$klas_id.'.'.$mode.'"');
	if ($mode=="xml")
	{
		export_students_xml($klas_id);		
	}
	else 
	{
		export_students_csv($klas_id);
	}
}
else
{
	print "Kan geen klas exporteren zonder te weten welke klas.";
}
?>