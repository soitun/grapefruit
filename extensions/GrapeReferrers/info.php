<?php
$ext['location'] = "GrapeReferrers"; // Name of the folder that contains this extension.

$ext['name'] = "GrapeReferrers";
$ext['description'] = "Tracks website referals.";
$ext['descriptionFull'] = "Tracks website referals.";
$ext['url'] = "http://www.quate.net/grape";
$ext['version'] = "0102"; // Numeric-only version number to be compared to when checking for updates.
$ext['versionFull'] = "0.1.2";
$ext['updateUrl'] = "http://www.quate.net/grape";
$ext['compatibleVersion'] = "0124";

$ext['author'] = "Quate";
$ext['authorUrl'] = "http://www.quate.net/";

// Name functions used by this extension.
$ext['display'] = "GrapeReferrersDisplay";
$ext['javascript'] = "GrapeReferrersJavascript";
$ext['record'] = "GrapeReferrersRecord";
$ext['api'] = "GrapeReferrersApi";
$ext['install'] = "GrapeReferrersInstall";
$ext['uninstall'] = "GrapeReferrersUninstall";

$ext['grapeStatColumn'] = "ref";
// Check if version is compatible.
// Display and save preferences?

// Define functions.
function GrapeReferrersDisplay() {
	global $ext, $year, $month, $day, $hour, $minute, $display;
	/*
	$content .= "<div class=\"box-header\">Referrers</div>
<div class=\"box-subheader\">
<div class=\"leftc2\">Hits</div>
<div class=\"mainc\">URL (This " .ucfirst($display). ")</div>
</div>\n";
*/
	$content .= "<h3>Referrers</h3>
<table cellspacing=\"0\" class=\"twocol\">
<tr class=\"subheader\">
	<th>Hits</th>
	<th>URL</th>
</tr>";

$temp_minute = $minute;
	$temp_hour = $hour;
	$temp_day = $day;
	$temp_month = $month;
	$temp_year = $year;
	$rfArr = array();
	for ($i = 0; $i <= 30; $i++) {
		
		if ($temp_day <= 0) {
			$temp_day += date("t", mktime($hour, $minute, 0, $temp_month, $temp_day, $year)); // Depends on the number of days in the previous month!!!
			$temp_month--; // Is only decreased in this specific case so as to not cause wrong results.
			$temp_month = $temp_month % 12 == 0 ? 12 : $temp_month % 12;
		}

		$q = "SELECT * FROM " . SQL_PREFIX . "graperef WHERE graperef_day = '"
			. $temp_day . "' AND graperef_month = '" . $temp_month 
			. "' AND graperef_year = '" . $temp_year . "'";

		$r = mysql_query($q) or die(report_error("E_DB", mysql_error(), __LINE__, __FILE__));
		for ($j = 0; $j < mysql_num_rows($r); $j++) {
			$url = mysql_result($r, $j, "graperef_http");

			$hits = mysql_result($r, $j, "graperef_hits");

			if (substr($url, strlen($cms['site'])) != $cms['site'])
			if (!isset($pgArr[$url])) {
				$rfArr[$url] = $hits;
			}
			else {
				$rfArr[$url] = $pgArr[$tmpK] + $hits;
			}
		}

		$temp_day--;

	}
	arsort($rfArr);
	foreach ($rfArr as $k => $v) {
		$k = "http://" . $k;
		$content .= "\n<tr class=\"alt$alt\">\n\t<td>$v</td>\n\t<td><a href=\"" . $k . "\">" . textcutsimple($k, 90) . "</a></td>\n</tr>";

		$alt = (($alt + 1) % 2);
	}
	
	$content .= "\n</table>";
	return $content;
}
/* Display parts:
+ Title
+ Column Names
+ Query:
  - Show results appropriately (needs time to be given) ... What about graphs??!?
+ Expand results.
*/
function GrapeReferrersJavascript() {
	return false;
}
function GrapeReferrersRecord() {
	global $year, $month, $day, $hour, $minute, $record;
	// Don't record self referring.
	$referrer = $record['referrer']; // We don't want to tamper with the actual $record['referrer'] variable.
	$host = $record['host']; // We don't want to tamper with the actual $record['host'] variable.
	//$host = $cms['site'];
	$host = str_replace("http://", "", $host);
	$host = str_replace("www.", "", $host);
	if (strpos(strtolower(" " .$referrer. " "), $host)) {
		$referrer = ""; // Set $referrer to zero so that it doesn't record it.
	}
	// Remove PHPSESSID from url.
	//$referrer = preg_replace('/\?PHPSESSID=[^&]+/',"",$referrer);  
	//$referrer = preg_replace('/\&PHPSESSID=[^&]+/',"",$referrer);

	$ref_id = 0;
	// In order to record, there must be a referrer. Otherwise recording will be skipped and no $ref_id will be recorded.
	if ($referrer != "") {
		$referrer = str_replace("http://www.", "", $referrer);
		$referrer = str_replace("http://", "", $referrer);

		$query = "SELECT * FROM " .SQL_PREFIX. "graperef WHERE
	graperef_hour = '" .$hour. "' AND
	graperef_day = '" .$day. "' AND
	graperef_month = '" .$month. "' AND
	graperef_year = '" .$year. "' AND
	graperef_http = '" .$referrer. "'";
		$result = mysql_query($query) or die(report_error("E_DB", mysql_error(), __LINE__, __FILE__));
		$num_rows = mysql_num_rows($result);

		if ($num_rows == 1) {
		
			// Get id of entry.
			$row = mysql_fetch_array($result);
			$ref_id = $row['graperef_id'];
		
			// Edit ref grape entry.
			$query = "UPDATE " .SQL_PREFIX. "graperef SET graperef_hits = (graperef_hits + 1) WHERE graperef_id = '" .$ref_id. "'";
			$result = mysql_query($query) or die(report_error("E_DB", mysql_error(), __LINE__, __FILE__));
		
		} else if ($num_rows == 0) {
			$query = "INSERT INTO " .SQL_PREFIX. "graperef(graperef_http, graperef_hour, graperef_day, graperef_month, graperef_year, graperef_hits)
	VALUES('" .$referrer. "', '" .$hour. "', '" .$day. "', '" .$month. "', '" .$year. "', '1')";
			$result = mysql_query($query) or die(report_error("E_DB", mysql_error(), __LINE__, __FILE__));
			$ref_id = mysql_insert_id(); // Use LAST_INSERT_ID() instead?
		}
	}
	return $ref_id;
}
function GrapeReferrersApi() {
	return false;
}
function GrapeReferrersInstall() {
	global $ext, $pg;
	// Create table
	$graperef = "CREATE TABLE IF NOT EXISTS " .SQL_PREFIX. "graperef (
graperef_id int(11) NOT NULL auto_increment,
graperef_http text NOT NULL default '',
graperef_year int(11) NOT NULL default '0',
graperef_month int(11) NOT NULL default '0',
graperef_day int(11) NOT NULL default '0',
graperef_hour int(11) NOT NULL default '0',
graperef_hits int(11) NOT NULL default '0',
PRIMARY KEY (graperef_id)
)";
	$result = mysql_query($graperef) or die ("<b>MySQL Error</b>: " .mysql_error());
	$pg['content'] .= "<img src=\"" .$location. "images/yes.png\" alt=\"\" /> Table '" .SQL_PREFIX. "graperef' created.<br />";
	
	// Add column to main grapestat table. This will be used to hold an id.
	// Check to see if the column already exists first.
	$query = "SHOW COLUMNS FROM " .SQL_PREFIX. "grapestat";
	$result = mysql_query($query) or die(report_error("E_DB", mysql_error(), __LINE__, __FILE__));
	$column_exists = 0;
	while ($row = mysql_fetch_array($result)) {
		if ($row['Field'] == "grapestat_" .$ext['grapeStatColumn']) {
			$column_exists = 1;
		}
	}
	if ($column_exists == 0) {
		// Add column.
		$query = "ALTER TABLE  " .SQL_PREFIX. "grapestat ADD grapestat_" .$ext['grapeStatColumn']. " int(11) NOT NULL default '0'";
		$result = mysql_query($query) or die(report_error("E_DB", mysql_error(), __LINE__, __FILE__));
	}
	$pg['content'] .= "<img src=\"" .$location. "images/yes.png\" alt=\"\" /> Table '" .SQL_PREFIX. "grapestat' updated.<br />";
	
	// Add extension to the enabled extensions file.
	enableExtension($ext['location']);
	
	$pg['content'] .= "<img src=\"" .$location. "images/yes.png\" alt=\"\" /> " .$ext['name']. " added to enabled extensions list.<br />";
	
	return true;
}
function GrapeReferrersUninstall($keep = 1) {
	global $ext, $pg;
	// Delete table (if chosen to do so by the user).
	if ($keep == 0) {
		$query = "DROP TABLE " .SQL_PREFIX. "graperef";
		$result = mysql_query($query) or die(report_error("E_DB", mysql_error(), __LINE__, __FILE__));
		$pg['content'] .= "<img src=\"" .$location. "images/yes.png\" alt=\"\" /> Table '" .SQL_PREFIX. "graperef' deleted.<br />";
	}
	
	// Delete column to main grapestat table (if chosen to do so by the user).
	if ($keep == 0) {
		$query = "ALTER TABLE " .SQL_PREFIX. "grapestat DROP grapestat_" .$ext['grapeStatColumn'];
		$result = mysql_query($query) or die(report_error("E_DB", mysql_error(), __LINE__, __FILE__));
		$pg['content'] .= "<img src=\"" .$location. "images/yes.png\" alt=\"\" /> Table '" .SQL_PREFIX. "grapestat' updated.<br />";
	}
	
	// Remove extension from the enabled extensions file.
	disableExtension($ext['location']);
	
	$pg['content'] .= "<img src=\"" .$location. "images/yes.png\" alt=\"\" /> " .$ext['name']. " removed from the enabled extensions list.<br />";
	
	return true;
}
?>
