<?php

function debug_log($message)
{
	echo "<!-- $message -->\n";
}

function debug_dump($variable)
{
	echo "<pre>\n";
	print_r($variable);
	echo "</pre>\n";	
}

function debug_warning($message)
{
?>
	<p class="warning"><?=$message?></p>
<?php
}
	
function debug_error($message, $ex=null)
{
?>
	<p class="error"><?=$message?>
<?php
	if ($ex!=null)
	{
		echo $ex->getMessage();
	}
	echo "</p>\n";
}

?>