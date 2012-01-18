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
	global $ext, $display;

	// starting from scratch here

	// initial contents
	$content .= "
	<h3>User Spy</h3>
	
	";

	// start with today?
	$day = date('j');
	$month = date('n');
	$year = date('y');

	$cdate = mktime(00, 00, 00, $month, $day, $year);
	$arr = array();

	// 30 days
	for ($i = 0; $i < 30; $i++) {
		// temp date
		$date = mktime(00, 00, 00, $month, $day - $i, $year);

		// grabby grabby and display, with multiple queries
		$query = "SELECT * FROM " . SQL_PREFIX . "grapestat WHERE ";
		$query .= " grapestat_year = '" . date('y', $date) . "' AND";
		$query .= " grapestat_month = '" . date('n', $date) . "' AND";
		$query .= " grapestat_day = '" . date('j', $date) . "'";

		$r = mysql_query($query) or die(report_error("E_DB", mysql_error(), __LINE__, __FILE__));

		while ($row = mysql_fetch_array($r)) {
			$osn = $row['grapestat_os'];
			$pg = $row['grapestat_page'];
			$hits = $row['grapestat_hits'];

			$os = "";
			$page = "";
			$url = "";

			$ip = $row['grapestat_ip'];

			if ($osn != 0 && $pg != 0) {
				$q = "SELECT * FROM " . SQL_PREFIX . "grapeos WHERE grapeos_id = '$osn'";
				$res = mysql_query($q) or die(report_error("E_DB", mysql_error(), __LINE__, __FILE__));
				
				$os = mysql_result($res, 0, "grapeos_os") . ' ';
				$os .= mysql_result($res, 0, "grapeos_version") == "(KHTML," ? "Unknown" : mysql_result($res, 0, "grapeos_version");


				$q = "SELECT * FROM " . SQL_PREFIX . "grapepage WHERE grapepage_id = '$pg'";
				$res = mysql_query($q) or die(report_error("E_DB", mysql_error(), __LINE__, __FILE__));

				$page = mysql_result($res, 0, "grapepage_title");
				$url = mysql_result($res, 0, "grapepage_url");
				
				if (isset($arr["$ip:::$os:::$page:::$url"])) {
					$arr["$ip:::$os:::$page:::$url"] += $hits;
				}
				else {
					$arr["$ip:::$os:::$page:::$url"] = $hits;			
				}

			}
		}
	}
	
	arsort($arr);
	// display content

	$i = 0;
	$tmp = "<div class='pill-content'>";
	foreach ($arr as $v => $hits) {
		$k = explode(":::", $v);
		$ip = $k[0];
		$os = $k[1];
		$page = $k[2];
		$url = $k[3];

		if ($i % 15 == 0) {
			$tmp .= "\n\t\t<div class='tab-pane" . ($i == 0 ? " active" : "") . "' id='us" . (($i / 15) + 1) . "'>";

			$tmp .= "\n\t\t\t<table class='threecol'>
			<tr>
				<th>Hits</th>
				<th>Entry Page</th>
				<th>OS</th>
			</tr>";
		}


		$tmp .= "
			<tr>
				<td>$hits</td>
				<td><a href='http://$url'>$page</a></td>
				<td>$os</td>
			</tr>
		";

		if ($i % 15 == 14 && $i != 0) {
			$tmp .= "</table></div>";
		}

		$i++;
	}

	if ($i % 15 != 14) {
		$tmp .= "</table>\n\t</div>";
	}

	$i--;

	$k = ($i / 15) + 1;
	$t2 = "<ul class='tabs'>";
	for ($e = 1; $e <= $k; $e++) {
		$t2 .= "\n\t\t<li" . ($e == 1 ? " class='active'" : "") . "><a href='#us$e'>$e</a></li>";
	}
	$t2 .= "\n\t</ul>\n\t";



	$content .= $t2 . $tmp;
	// close the table
	$content .= "\n\t</div>";

	return $content;
}
function UserSpyJavascript() {
	return false;
}
function UserSpyRecord() {
	return false;
}
function UserSpyApi() {
	// first api, FUN!
	$cont = "";

	$day = date('j');
	$month = date('n');
	$year = date('y');
	echo $day;
	echo date('j') . "-----";

	$cdate = mktime(00, 00, 00, $month, $day, $year);
	$last = date('y-m-d');
	$arr = array();
	$data = array();

	// 30 days
	for ($i = 0; $i < 30; $i++) {
		// temp date
		$date = mktime(00, 00, 00, $month, $day - $i, $year);

		// grabby grabby and display, with multiple queries
		$query = "SELECT * FROM " . SQL_PREFIX . "grapestat WHERE ";
		$query .= " grapestat_year = '" . date('y', $date) . "' AND";
		$query .= " grapestat_month = '" . date('n', $date) . "' AND";
		$query .= " grapestat_day = '" . date('j', $date) . "'";

		$r = mysql_query($query) or die(report_error("E_DB", mysql_error(), __LINE__, __FILE__));

		while ($row = mysql_fetch_array($r)) {
			$osn = $row['grapestat_os'];
			$pg = $row['grapestat_page'];
			$hits = $row['grapestat_hits'];

			$os = "";
			$page = "";
			$url = "";

			$ip = $row['grapestat_ip'];

			if ($osn != 0 && $pg != 0) {
				$q = "SELECT * FROM " . SQL_PREFIX . "grapeos WHERE grapeos_id = '$osn'";
				$res = mysql_query($q) or die(report_error("E_DB", mysql_error(), __LINE__, __FILE__));
				
				$os = mysql_result($res, 0, "grapeos_os") . ' ';
				$os .= mysql_result($res, 0, "grapeos_version") == "(KHTML," ? "Unknown" : mysql_result($res, 0, "grapeos_version");


				$q = "SELECT * FROM " . SQL_PREFIX . "grapepage WHERE grapepage_id = '$pg'";
				$res = mysql_query($q) or die(report_error("E_DB", mysql_error(), __LINE__, __FILE__));

				$page = mysql_result($res, 0, "grapepage_title");
				$url = mysql_result($res, 0, "grapepage_url");
				
				if (isset($arr["$ip:::$os:::$page:::$url"])) {
					$arr["$ip:::$os:::$page:::$url"] += $hits;
				}
				else {
					$arr["$ip:::$os:::$page:::$url"] = $hits;			
				}

			}
		}
	}
	$first = date('y-m-d', $day);

	// display content
	foreach ($arr as $v => $hits) {
		$k = explode(":::", $v);
		$ip = $k[0];
		$os = $k[1];
		$page = $k[2];
		$url = $k[3];	
		array_push($data,
			"
			{
				\"hits\": \"$hits\",
				\"os\": \"$os\",
				\"page\": \"$page\",
				\"url\": \"http://$url\"
			}"
		);
	}



	// things you need to know
	$cont .= '
	"title": "User Spy",
	"first": "' . $first . '",
	"last": "' . $last . '",
	"data" : [
	';

	for ($i = 0; $i < count($data); $i++) {
		$cont .= $data[$i] . ($i < count($data) - 1 ? ',' : '');
	}

	$cont .= "\n\t]";

	return $cont;
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
