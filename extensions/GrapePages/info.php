<?php
$ext['location'] = "GrapePages"; // Name of the folder that contains this extension.

$ext['name'] = "GrapePages";
$ext['description'] = "Tracks what pages visitors are viewing.";
$ext['descriptionFull'] = "Tracks what pages visitors are viewing.";
$ext['url'] = "http://www.quate.net/grape";
$ext['version'] = "0102"; // Numeric-only version number to be compared to when checking for updates.
$ext['versionFull'] = "0.1.2";
$ext['updateUrl'] = "http://www.quate.net/grape";
$ext['compatibleVersion'] = "0124";

$ext['author'] = "Quate";
$ext['authorUrl'] = "http://www.quate.net/";

// Name functions used by this extension.
$ext['display'] = "GrapePagesDisplay";
$ext['javascript'] = "GrapePagesJavascript";
$ext['record'] = "GrapePagesRecord";
$ext['api'] = "GrapePagesApi";
$ext['install'] = "GrapePagesInstall";
$ext['uninstall'] = "GrapePagesUninstall";

$ext['grapeStatColumn'] = "page";
// Check if version is compatible.
// Display and save preferences?

// Define functions.
function GrapePagesDisplay() {
	global $ext, $year, $month, $day, $hour, $minute, $display;
	$content .= "<h3>Pages</h3>
<table cellspacing=\"0\" class=\"twocol\">
<tr class=\"subheader\">
	<th>Hits</th>
	<th>Page Title</th>
</tr>";

	$alt = 1;


/*	while ($row = mysql_fetch_array($result)) {
		$href_url = "http://" .$row['grapepage_url'];
		$content .= "\n<tr class=\"alt" .$alt. "\">
	<td>" .$row['grapepage_hits']. "</td>
	<td><a href=\"" .$href_url. "\" target=\"_blank\">" .textcut($row['grapepage_title'], 50). "</a></td>
</tr>";*/

	$temp_minute = $minute;
	$temp_hour = $hour;
	$temp_day = $day;
	$temp_month = $month;
	$temp_year = $year;
	$pgArr = array();
	for ($i = 0; $i <= 30; $i++) {
		
		if ($temp_day <= 0) {
			$temp_day += date("t", mktime($hour, $minute, 0, $temp_month, $temp_day, $year)); // Depends on the number of days in the previous month!!!
			$temp_month--; // Is only decreased in this specific case so as to not cause wrong results.
			$temp_month = $temp_month % 12 == 0 ? 12 : $temp_month % 12;
		}

		$q = "SELECT * FROM " . SQL_PREFIX . "grapepage WHERE grapepage_day = '"
			. $temp_day . "' AND grapepage_month = '" . $temp_month 
			. "' AND grapepage_year = '" . $temp_year . "'";

		$r = mysql_query($q) or die(report_error("E_DB", mysql_error(), __LINE__, __FILE__));
		for ($j = 0; $j < mysql_num_rows($r); $j++) {
			$title = mysql_result($r, $j, "grapepage_title");
			$url = mysql_result($r, $j, "grapepage_url");

			$hits = mysql_result($r, $j, "grapepage_hits");

			$tmpK = $title . ":URL:" . $url;

			if (!isset($pgArr[$tmpK])) {
				$pgArr[$tmpK] = $hits;
			}
			else {
				$pgArr[$tmpK] = $pgArr[$tmpK] + $hits;
			}
		}

		$temp_day--;

	}
	arsort($pgArr);
	foreach ($pgArr as $k => $v) {
		$tv = explode(":URL:", $k);
		$tv[1] = "http://" . $tv[1];
		$content .= "\n<tr class=\"alt$alt\">\n\t<td>$v</td>\n\t<td><a href=\"" . $tv[1] . "\">" . $tv[0] . "</a></td>\n</tr>";

		$alt = (($alt + 1) % 2);
	}
	
	
	$content .= "\n</table>";
	return $content;
}

function GrapePagesJavascript() {
	return false;
}
function GrapePagesRecord() {
	global $year, $month, $day, $hour, $minute, $record;
	$page_id = 0;
	// In order to record, a page must have a title. Otherwise recording will be skipped and no $page_id will be recorded.
	if ($record['title'] != "") {
		$query = "SELECT * FROM " .SQL_PREFIX. "grapepage WHERE
grapepage_hour = '" .$hour. "' AND
grapepage_day = '" .$day. "' AND
grapepage_month = '" .$month. "' AND
grapepage_year = '" .$year. "' AND
grapepage_title = '" .$record['title']. "'";
		$result = mysql_query($query) or die(report_error("E_DB", mysql_error(), __LINE__, __FILE__));
		$num_rows = mysql_num_rows($result);

		if ($num_rows == 1) {
		
			// Get id of entry.
			$row = mysql_fetch_array($result);
			$page_id = $row['grapepage_id'];
		
			// Edit page grape entry.
			$query = "UPDATE " .SQL_PREFIX. "grapepage SET grapepage_hits = (grapepage_hits + 1) WHERE grapepage_id = '" .$page_id. "'";
			$result = mysql_query($query) or die(report_error("E_DB", mysql_error(), __LINE__, __FILE__));
		
		} else if ($num_rows == 0) {
			$url = $record['url']; // We don't want to tamper with the actual $record['url'] variable.
			$url = str_replace("http://", "", $url);
			$url = str_replace("www.", "", $url);
		
			$query = "INSERT INTO " .SQL_PREFIX. "grapepage(grapepage_title, grapepage_url, grapepage_hour, grapepage_day, grapepage_month, grapepage_year, grapepage_hits)
VALUES('" .$record['title']. "', '" .$url. "', '" .$hour. "', '" .$day. "', '" .$month. "', '" .$year. "', '1')";
			$result = mysql_query($query) or die(report_error("E_DB", mysql_error(), __LINE__, __FILE__));
			$page_id = mysql_insert_id(); // Use LAST_INSERT_ID() instead?
		}
	}
	return $page_id;
}
function GrapePagesApi() {
	return false;
}
function GrapePagesInstall() {
	global $ext, $pg;
	// Create table
	$grapepage = "CREATE TABLE IF NOT EXISTS " .SQL_PREFIX. "grapepage (
grapepage_id int(11) NOT NULL auto_increment,
grapepage_title text NOT NULL default '',
grapepage_url text NOT NULL default '',
grapepage_year int(11) NOT NULL default '0',
grapepage_month int(11) NOT NULL default '0',
grapepage_day int(11) NOT NULL default '0',
grapepage_hour int(11) NOT NULL default '0',
grapepage_hits int(11) NOT NULL default '0',
PRIMARY KEY (grapepage_id)
)";
	$result = mysql_query($grapepage) or die ("<b>MySQL Error</b>: " .mysql_error());
	$pg['content'] .= "<img src=\"" .$location. "images/yes.png\" alt=\"\" /> Table '" .SQL_PREFIX. "grapepage' created.<br />";
	
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
function GrapePagesUninstall($keep = 1) {
	global $ext, $pg;
	// Delete table (if chosen to do so by the user).
	if ($keep == 0) {
		$query = "DROP TABLE " .SQL_PREFIX. "grapepage";
		$result = mysql_query($query) or die(report_error("E_DB", mysql_error(), __LINE__, __FILE__));
		$pg['content'] .= "<img src=\"" .$location. "images/yes.png\" alt=\"\" /> Table '" .SQL_PREFIX. "grapepage' deleted.<br />";
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
