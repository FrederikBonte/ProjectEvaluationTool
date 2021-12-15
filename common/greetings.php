<?php
function print_greeting() {
	// 24-hour format of an hour without leading zeros (0 through 23)
	$Hour = date('G');

	if ( $Hour >= 5 && $Hour <= 11 ) {
?>
		<h2 class="redheader">Good Morning</h2>
<?php
	} else if ( $Hour >= 12 && $Hour <= 18 ) {
?>
		<h2 class="redheader">Good Afternoon</h2>
<?php
	} else if ( $Hour >= 19 && $Hour <= 24 ) {
		print "<h2 class=\"redheader\">Good Evening</h2>";
	} else {
?>
		<h2 class="redheader">Good Night</h2>
<?php
	}
}
?>