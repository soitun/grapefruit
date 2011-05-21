<?php
$ext['location'] = "UserSpy"; // Name of the folder that contains this extension.

$ext['name'] = "UserSpy";
$ext['description'] = "Browse information of individual users.";
$ext['descriptionFull'] = "Browse information of individual users.
This extension only displays user information from already existing statistics and does not track any additional information.
In other words, it does not mess with other recording data.
It currently works in conjunction with the GrapePages and GrapeOS extension.
Please note that this extension is experimental.";
$ext['url'] = "http://www.quate.net/grape";
$ext['version'] = "0101"; // Numeric-only version number to be compared to when checking for updates.
$ext['versionFull'] = "0.1.1";
$ext['updateUrl'] = "http://www.quate.net/grape";
$ext['compatibleVersion'] = "0124";

$ext['author'] = "Quate";
$ext['authorUrl'] = "http://www.quate.net/";

// Name functions used by this extension.
$ext['display'] = "UserSpyDisplay";
$ext['javascript'] = "UserSpyJavascript"; // Not needed!
$ext['record'] = "UserSpyRecord"; // Not needed!
$ext['api'] = "UserSpyApi";
$ext['install'] = "UserSpyInstall"; // Only used for display purposes.
$ext['uninstall'] = "UserSpyUninstall"; // Only used for display purposes.

$ext['grapeStatColumn'] = ""; // Not needed!

// Define functions.
function UserSpyDisplay() {
	global $ext, $year, $month, $day, $hour, $minute, $display;
	
	$columns = 2;
	$content .= "<div class='section' rel='middle'><div class=\"box box-float-small\" rel=\"spy\">
<div class=\"box-header\">User Spy</div>
<table cellspacing=\"0\">
<tr class=\"subheader\">
	<th>Hits</th>
	<th>IP (This " .ucfirst($display). ")</th>";
	if (function_exists("GrapePagesDisplay")) {
		$content .= "<th>Entry Page</th>";
		$columns++;
	}
	if (function_exists("GrapeOSDisplay")) {
		$content .= "<th>OS</th>";
		$columns++;
	}
	$content .="\n</tr>";

	$query = "SELECT *, sum(grapestat_hits) AS grapestat_hits FROM " .SQL_PREFIX. "grapestat WHERE";
	if ($display == "hour") {
		$query .= " grapestat_hour = '" .$hour. "' AND";
	}
	if ($display != "year" && $display != "month") {
		$query .= " grapestat_day = '" .$day. "' AND";
	}
	if ($display != "year") {
		$query .= " grapestat_month = '" .$month. "' AND";
	}
	// Group By grapestat_http
	$query .= " grapestat_year = '" .$year. "' GROUP BY grapestat_ip ORDER BY grapestat_id DESC";
	if (!$_GET[strtolower($ext['name'])]) {
		$query .= " LIMIT 5";
	} else {
		$query .= " LIMIT 25";
	}
	$result = mysql_query($query) or die(report_error("E_DB", mysql_error(), __LINE__, __FILE__));
	$alt = 1;
	while ($row = mysql_fetch_array($result)) {
		$href_url = "http://" .$row['graperef_http'];
		$content .= "\n<tr class=\"alt" .$alt. "\">
	<td>" .$row['grapestat_hits']. "</td>
	<td><a href=\"http://api.hostip.info/get_html.php?ip=" .$row['grapestat_ip']. "\" target=\"_blank\">" .$row['grapestat_ip']. "</a></td>";
		// If the GrapePages extensions exists. This is the only method that seems to work!
		if (function_exists("GrapePagesDisplay")) {
			$query2 = "SELECT grapepage_id, grapepage_title, grapepage_url FROM " .SQL_PREFIX. "grapepage WHERE grapepage_id = '" .$row['grapestat_page']. "'";
			$result2 = mysql_query($query2) or die(report_error("E_DB", mysql_error(), __LINE__, __FILE__));
			$row2 = mysql_fetch_array($result2);
			$href_url = "http://" .$row2['grapepage_url'];
			$content .= "<td><a href=\"" .$href_url. "\" target=\"_blank\">" .textcutsimple($row2['grapepage_title'], 9). "</a></td>";
		}
		// If the GrapeOS extensions exists. This is the only method that seems to work!
		if (function_exists("GrapeOSDisplay")) {
			$query2 = "SELECT grapeos_id, grapeos_os, grapeos_version FROM " .SQL_PREFIX. "grapeos WHERE grapeos_id = '" .$row['grapestat_os']. "'";
			$result2 = mysql_query($query2) or die(report_error("E_DB", mysql_error(), __LINE__, __FILE__));
			$row2 = mysql_fetch_array($result2);
			$content .= "\n<td>" .textcutsimple($row2['grapeos_os']. " " .$row2['grapeos_version'], 7). "</td>";
		}
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
function UserSpyJavascript() {
	return false;
}
function UserSpyRecord() {
	return false;
}
function GrapeSpyApi() {
	return false;
}
function UserSpyInstall() {
	global $ext, $pg;

	// Add extension to the enabled extensions file.
	enableExtension($ext['location']);
	
	$pg['content'] .= "<img src=\"" .$location. "images/yes.png\" alt=\"\" /> " .$ext['name']. " added to enabled extensions list.<br />";
	
	return true;
}
function UserSpyUninstall($keep = 1) {
	global $ext, $pg;
	
	// Remove extension from the enabled extensions file.
	disableExtension($ext['location']);
	
	$pg['content'] .= "<img src=\"" .$location. "images/yes.png\" alt=\"\" /> " .$ext['name']. " removed from the enabled extensions list.<br />";
	
	return true;
}
?>
