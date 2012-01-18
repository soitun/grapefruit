<?php
$ext['location'] = "CityFinder"; // Name of the folder that contains this extension.

$ext['name'] = "CityFinder";
$ext['description'] = "Get Locations of your visitors.";
$ext['descriptionFull'] = "CityFinder grabs the city/town names of
where your visitors are coming from, it also gets the country.";
$ext['url'] = "http://dkuntz2.com/portfolio/development/cityfinder/"; //update this when I upload it to the server.
$ext['version'] = "001"; // Numeric-only version number to be compared to when checking for updates.
$ext['versionFull'] = "0.0.1";
$ext['updateUrl'] = "http://dkuntz2.com/portfolio/development/cityfinder/"; //update this when I upload it to the server
$ext['compatibleVersion'] = "001";

$ext['author'] = "Don Kuntz";
$ext['authorUrl'] = "http://dkuntz2.com/";

// Name functions used by this extension.
$ext['display'] = "CityFinderDisplay";
$ext['javascript'] = "CityFinderJavascript"; // Not needed!
$ext['record'] = "CityFinderRecord"; // Not needed!
$ext['api'] = "CityFinderApi";
$ext['install'] = "CityFinderInstall"; // Only used for display purposes.
$ext['uninstall'] = "CityFinderUninstall"; // Only used for display purposes.

$ext['grapeStatColumn'] = ""; // Not needed!

// Define functions.
function CityFinderDisplay() {
	global $ext, $year, $month, $day, $hour, $minute, $display;

	$columns = 3;
	$content .= "<div class=\"box box-float-small\" rel=\"finder\">
<div class=\"box-header\">User Locations</div>
<table cellspacing=\"0\">
<tr class=\"subheader\">
	<th>Hits</th>
	<th>City, State</th>
	<th>Country (This " .ucfirst($display). ")</th>";

	$content .="\n</tr>";

	$query = "SELECT *, sum(grapecity_hits) AS grapecity_hits FROM " .SQL_PREFIX. "grapecity WHERE";
	/*
	//$query .= "\n grapecity_hour = '" .$hour. "' AND";
	//$query .= "\n grapecity_day = '" .$day. "' AND";
	$query .= "\n grapecity_month = '" .$month. "' AND";
	*/
	if ($display == "hour") {
		$query .= " grapecity_hour = '" .$hour. "' AND";
	}
	if ($display != "year" && $display != "month") {
		$query .= " grapecity_day = '" .$day. "' AND";
	}
	if ($display != "year") {
		$query .= " grapecity_month = '" .$month. "' AND";
	}
	$query .= " grapecity_year = '" .$year. "' GROUP BY grapecity_ip ORDER BY grapecity_hits DESC";
	if (!$_GET[strtolower($ext['name'])]) {
		$query .= " LIMIT 10";
	}
	$result = mysql_query($query) or die(report_error("E_DB", mysql_error(), __LINE__, __FILE__));
	$alt = 1;

	while ($row = mysql_fetch_array($result)) {
		

		$content .= "\n<tr class=\"alt" .$alt. "\">\n\t<td>" .$row['grapecity_hits']. "</td>";
		
		$city = $row['grapecity_city'];
		$country = $row['grapecity_country'];

		$content .= "<td>$city</td>";
				
		$content .= "<td>".$country."</td>";

		$content .= "\n</tr>";
		if ($alt == 1) {
			$alt = 2;
		} else {
			$alt = 1;
		}
	}
	/*
	if (!$_GET['showall']) {
		$content .= "<tr class=\"alt" .$alt. "\">
	<td colspan=\"2\"><a href=\"?" .$_SERVER['QUERY_STRING']. "&amp;showall=1\">Show All</a></td>
</tr>";
	}
	*/
	if (!$_GET[strtolower($ext['name'])]) {
		$content .= "\n<tr class=\"alt" .$alt. "\">
	<td colspan=\"" .$columns. "\"><a href=\"?" .$_SERVER['QUERY_STRING']. "&amp;" .strtolower($ext['name']). "=1\">Show More</a></td>
</tr>";
	}
	$content .= "\n</table>\n</div>\n";
	return $content;
}
function CityFinderJavascript() {
	return false;
}
function CityFinderRecord() {
	global $year, $month, $day, $hour, $minute, $record;
	$page_id = 0;
	// In order to record, a page must have a title. Otherwise recording will be skipped and no $page_id will be recorded.
	if ($record['ip'] != "") {
		$query = "SELECT * FROM " .SQL_PREFIX. "grapecity WHERE
grapecity_hour = '" .$hour. "' AND
grapecity_day = '" .$day. "' AND
grapecity_month = '" .$month. "' AND
grapecity_year = '" .$year. "' AND
grapecity_ip = '" .$record['ip']. "'";
		$result = mysql_query($query) or die(report_error("E_DB", mysql_error(), __LINE__, __FILE__));
		$num_rows = mysql_num_rows($result);

		if ($num_rows == 1) {
		
			// Edit page grape entry.
			$query = "UPDATE " .SQL_PREFIX. "grapecity SET grapecity_hits = (grapecity_hits + 1) WHERE grapecity_ip = '" .$record['ip']. "'";
			$result = mysql_query($query) or die(report_error("E_DB", mysql_error(), __LINE__, __FILE__));
		
		} else if ($num_rows == 0) {
			$href_url = "http://" .$row['graperef_http'];
			$ipapikey = "6fa0e63c3dc73a25abbbf37144f6a96e12a559ae289c0ecd8f6d01da6242635b";

			$ipGet = "http://api.ipinfodb.com/v3/ip-city/?key=$ipapikey&ip=" .$record['ip'];
			
			$ipFile = file_get_contents($ipGet);
			//$ipRead = fread($ipFile, filesize($ipGet));
			$ipLines = explode(";", $ipFile);
			$country = substr($ipLines[4], 0, 1) . strtolower(substr($ipLines[4], 1));
			$city = substr($ipLines[6], 0, 1) . strtolower(substr($ipLines[6], 1));

			$saf = file_get_contents($cms['location'] . "extensions/CityFinder/stateAbbr.txt");
			$sa = explode("\n", $saf);
			$state = $ipLines[5];
			foreach($sa as $s => $a) {
				$tmp = explode(",", $sa[$s]);
				if($state == $tmp[0]) {
					$state = $tmp[1];
				}
			}
			if($state == $ipLines[5]) {$state = "";}
			else {$state = ", " . $state;}

			$city = $city . $state;
		
			$query = "INSERT INTO " .SQL_PREFIX. "grapecity(grapecity_ip, grapecity_country, grapecity_city, grapecity_hour, grapecity_day, grapecity_month, grapecity_year, grapecity_hits)
VALUES('" .$record['ip']. "', '" .$country. "', '" .$city. "', '" .$hour. "', '" .$day. "', '" .$month. "', '" .$year. "', '1')";
			$result = mysql_query($query) or die(report_error("E_DB", mysql_error(), __LINE__, __FILE__));
			$page_id = mysql_insert_id(); // Use LAST_INSERT_ID() instead?
		}
	}
	return $page_id;
}
function CityFinderApi() {
	return false;
}
function CityFinderInstall() {
	global $ext, $pg;
	// Create table
	$grapecity = "CREATE TABLE IF NOT EXISTS " .SQL_PREFIX. "grapecity (
grapecity_id int(11) NOT NULL auto_increment,
grapecity_ip text NOT NULL default '',
grapecity_country text NOT NULL default '',
grapecity_city text NOT NULL default '',
grapecity_year int(11) NOT NULL default '0',
grapecity_month int(11) NOT NULL default '0',
grapecity_day int(11) NOT NULL default '0',
grapecity_hour int(11) NOT NULL default '0',
grapecity_hits int(11) NOT NULL default '0',
PRIMARY KEY (grapecity_id)
)";
	$result = mysql_query($grapecity) or die ("<b>MySQL Error</b>: " .mysql_error());
	$pg['content'] .= "<img src=\"" .$location. "images/yes.png\" alt=\"\" /> Table '" .SQL_PREFIX. "grapecity' created.<br />";
	
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
function CityFinderUninstall($keep = 1) {
	global $ext, $pg;
	// Delete table (if chosen to do so by the user).
	if ($keep == 0) {
		$query = "DROP TABLE " .SQL_PREFIX. "grapecity";
		$result = mysql_query($query) or die(report_error("E_DB", mysql_error(), __LINE__, __FILE__));
		$pg['content'] .= "<img src=\"" .$location. "images/yes.png\" alt=\"\" /> Table '" .SQL_PREFIX. "grapecity' deleted.<br />";
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