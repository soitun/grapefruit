<?php
function showtime() {
	$minute = date("i");
	$hour = date("G");
	$day = date("j");
	$month = date("n");
	$year = date("y");
	//return date_default_timezone_get(). " => " .date('e'). " => " .date('T'). " => " .$year. "/" .$month. "/" .$day. " " .$hour. ":" .$minute. "<br>\n";
	return date('T'). ", " .$year. "/" .$month. "/" .$day. " " .$hour. ":" .$minute. "<br>\n";
}

if (phpversion() >= "5.1.0") {
	date_default_timezone_set($_GET['timezone']); //date_default_timezone_set("UTC");
} else {
	putenv("TZ=" .$_GET['timezone']);
}
//putenv("TZ=GMT"); for everything else?
echo showtime();
?>