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
	$content .= "<h3>User Spy</h3>
<table cellspacing=\"0\" class=\"threecol\">
<tr class=\"subheader\">
	<th>Hits</th>";
	if (function_exists("GrapePagesDisplay")) {
		$content .= "<th>Entry Page</th>";
		$columns++;
	}
	if (function_exists("GrapeOSDisplay")) {
		$content .= "<th>OS</th>";
		$columns++;
	}
	$content .="\n</tr>";

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

		$q = "SELECT * FROM " . SQL_PREFIX . "grapestat WHERE grapestat_day = '"
			. $temp_day . "' AND grapestat_month = '" . $temp_month 
			. "' AND grapestat_year = '" . $temp_year . "'";

		$r = mysql_query($q) or die(report_error("E_DB", mysql_error(), __LINE__, __FILE__));
		for ($j = 0; $j < mysql_num_rows($r); $j++) {
			$osNum = mysql_result($r, $j, "grapestat_os");
			$pgNum = mysql_result($r, $j, "grapestat_page");

			$hits = mysql_result($r, $j, "grapestat_hits");

			if ($osNum != 0) {
				$qOS = "SELECT * FROM " . SQL_PREFIX . "grapeos WHERE grapeos_id = '" . $osNum . "'";
				$rOS = mysql_query($qOS) or die(report_error("E_DB", mysql_error(), __LINE__, __FILE__));
				//$os = mysql_result($rOS, 0, "grapeos_os") . (strtolower(mysql_result($rOS, 0, "grapeos_version")) != "unknown" || strtolower(mysql_result($rOS, 0, "grapeos_version")) != "khtml," ? " " . mysql_result($rOS, 0, "grapeos_version") : "");

				$os = mysql_result($rOS, 0, "grapeos_os") . " ";
				$os .= mysql_result($rOS, 0, "grapeos_version") == "(KHTML," ? "unknown" : mysql_result($rOS, 0, "grapeos_version");
				/*
				$type = mysql_result($r, $j, "grapeos_os");
				$version = mysql_result($r, $j, "grapeos_version");
				$version = $version == "(KHTML," ? "unknown" : $version;
				*/
			}
			else {
				$os = "Not Recorded";
			}

			if ($pgNum != 0) {
				$qPage = "SELECT * FROM " . SQL_PREFIX . "grapepage WHERE grapepage_id = '" . $pgNum . "'";
				$rPage = mysql_query($qPage) or die(report_error("E_DB", mysql_error(), __LINE__, __FILE__));
				$page = mysql_result($rPage, 0, "grapepage_title") . ":URL:" . mysql_result($rPage, 0, "grapepage_url");
			}
			else {
				$page = "Not Recorded:URL:#";
			}

			$tmpK = $os . ":PAGE:" . $page;

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
		$tv = explode(":PAGE:", $k);
		$ps = explode(":URL:", $tv[1]);
		$ps[1] = $ps[1] != "#" ? "http://" . $ps[1] : $ps[1];
		$content .= "\n<tr class=\"alt$alt\">\n\t<td>$v</td>\n\t<td><a href=\"" . $ps[1] . "\">" . $ps[0] . "</a></td>\n\t<td>" . $tv[0] . "</td>\n</tr>";

		$alt = (($alt + 1) % 2);
	}
	
	$content .= "\n</table>\n";
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
