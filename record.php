<?php
$location = "./";
require_once($location. "includes/functions.php");

//@ignore_user_abort(true);

$record['ip'] = $_SERVER['REMOTE_ADDR']; //getenv("REMOTE_ADDR"); getenv("HTTP_CLIENT_IP");  getenv("HTTP_X_FORWARDED_FOR");  http://www.4webhelp.net/scripts/php/ip.php
$record['js_referrer'] = $_SERVER['HTTP_REFERER']; // Referrer to this page. It should be the javascript page. Note: referrer may still be spoofed.
$record['host'] = $_SERVER['HTTP_HOST'];
$record['agent'] = $_SERVER['HTTP_USER_AGENT'];
// Variables caught from javascript have been encodeURI.
// (There's a slight difference between the PHP and javascript decode/encode functions, but this is good enough.)
$record['referrer'] = rawurldecode($_GET['r']);
$record['title'] = rawurldecode($_GET['t']);
$record['url'] = rawurldecode($_GET['u']);

$minute = date("i");
$hour = date("G");
$day = date("j");
$month = date("n");
$year = date("y");

// Connect to the database.
dbconnect();

loadExtensions();
if (isset($extensions)) {
	$k = 0;
	foreach ($extensions as $ext) {
		if ($ext['grapeStatColumn']) {
			// Save record output info (should be an id to the table row) to be saved to the main stat table row, linking the two rows together.
			$extension_columns[$k]['column'] = $ext['grapeStatColumn'];
			$extension_columns[$k]['value'] = $ext['record']();
			$k++;
		} else {
			$ext['record']();
		}
	}
}

/*
Record visitor (IP) stats.
*/
$column = "";
$value = "";
if (isset($extension_columns)) {
	foreach ($extension_columns as $ext) {
		$columns .= ", grapestat_" .$ext['column'];
		$values .= ", '" .$ext['value']. "'";
	}
}
//Debug:echo $columns. "<br>" .$values;

$query = "SELECT * FROM " .SQL_PREFIX. "grapestat WHERE
grapestat_minute = '" .$minute. "' AND
grapestat_hour = '" .$hour. "' AND
grapestat_day = '" .$day. "' AND
grapestat_month = '" .$month. "' AND
grapestat_year = '" .$year. "' AND
grapestat_ip = '" .$record['ip']. "'";
$result = mysql_query($query) or die(report_error("E_DB", mysql_error(), __LINE__, __FILE__));
$num_rows = mysql_num_rows($result);

if ($num_rows == 1) {
	// The user has already visited this hour.
 
	// Get id of entry.
	$row = mysql_fetch_array($result);
	$id = $row['grapestat_id'];
	
	// Edit main grape entry.
	$query = "UPDATE " .SQL_PREFIX. "grapestat SET grapestat_hits = (grapestat_hits + 1) WHERE grapestat_id = '" .$id. "'";
	$result = mysql_query($query) or die(report_error("E_DB", mysql_error(), __LINE__, __FILE__));
} else if ($num_rows == 0) {
	// The user has not visited before this hour.

	$query = "INSERT INTO " .SQL_PREFIX. "grapestat(grapestat_ip, grapestat_minute, grapestat_hour, grapestat_day, grapestat_month, grapestat_year, grapestat_hits" .$columns. ")
VALUES('" .$record['ip']. "', '" .$minute. "', '" .$hour. "', '" .$day. "', '" .$month. "', '" .$year. "', '1'" .$values. ")";
	$result = mysql_query($query) or die(report_error("E_DB", mysql_error(), __LINE__, __FILE__));
}

// DEBUG:
//echo date("r");
//echo "<br>" .date("y"). "-" .date("n"). "-" .date("j"). "-" .date("G"). "";

/*
// Debug mode detector
if (strpos($_SERVER['QUERY_STRING'], "debug")) {
	//echo "alert(\"Grape javascript is working.\");";
	echo "document.writeln('<div style=\"background:#fff;padding:3px;border:1px solid #444;\">Grape seems to be working!</div>');";
}
*/
?>