<?php
$ext['location'] = "GrapeOS"; // Name of the folder that contains this extension.

$ext['name'] = "GrapeOS";
$ext['description'] = "Tracks what operating systems visitors are using.";
$ext['descriptionFull'] = "Tracks what operating systems visitors are using.
It cannot detect all operating system versions or distributions, and currently does not detect anything other than the Windows, Mac, and Linux environments.";
$ext['url'] = "http://www.quate.net/grape";
$ext['version'] = "0102"; // Numeric-only version number to be compared to when checking for updates.
$ext['versionFull'] = "0.1.2";
$ext['updateUrl'] = "";
$ext['compatibleVersion'] = "0124";

$ext['author'] = "Quate";
$ext['authorUrl'] = "http://www.quate.net/";

// Name functions used by this extension.
$ext['display'] = "GrapeOSDisplay";
$ext['javascript'] = "GrapeOSJavascript";
$ext['record'] = "GrapeOSRecord";
$ext['api'] = "GrapeOSApi";
$ext['install'] = "GrapeOSInstall";
$ext['uninstall'] = "GrapeOSUninstall";

$ext['grapeStatColumn'] = "os";
// Check if version is compatible.
// Display and save preferences?

// Define functions.
function GrapeOSDisplay() {
	global $ext, $display;
	
	$content = '
		<h3>Operating Systems</h3>

		<div id="oshover">&nbsp;</div>
		<div id="osgraph"></div>
		<script>
		var oss = [];
		$(document).ready(function() {
			$.getJSON("' . $cms['location'] . 'api.php?api=GrapeOS",
				function(data) {
					//var oss = [];
					$.each(data.data, function(i, val) {
						oss.push({ label: val.os, data: parseInt(val.hits)});
						//alert("label: " + val.os + " // data: " + val.hits);
					});

					$.plot(
						$("#osgraph"),
						oss,
						{
							series: { pie: { show: true, radius: 3/4 } },
							grid: { hoverable: true, clickable: true }
						}
					);
					
					
				}
			);
			$("#osgraph").bind("plotclick", 
				function(event, pos, obj) {
					console.log("clicked");

					if (!obj)
						return;
					
					$("#oshover").html(
						"<span style=\"font-weight: bold; color: " + obj.series.color + "\">" + obj.series.label + " (" + Math.round(parseFloat(obj.series.percent)) + ")</span>"
					);
				}
			);
		});
		</script>
	';

	return $content;
}

function GrapeOSJavascript() {
	return false;
}
function GrapeOSRecord() {
	global $year, $month, $day, $hour, $minute, $record;
	$os_id = 0;
	// Analyse UserAgent string.
	$agent_temp = $record['agent'];
	$os = "Unknown";
	$version = "Unknown";
	if (strstr($agent_temp, "Win")) {
		$os = "Windows";
		//$version = "Unknown - (" .$agent_temp. ")";
		$version = "Unknown";
		if (strstr($agent_temp, "NT 6.1")) {
			// Windows Server 2008.
			$version = "7";
		} else if (strstr($agent_temp, "NT 6.0")) {
			$version = "Vista";
		} else if (strstr($agent_temp, "NT 5.1")) {
			$version = "XP";
		} else if (strstr($agent_temp, "NT 5.2")) {
			//Windows NT 5.2   ->  Windows Server 2003; Windows XP x64 Edition, Windows Home Server? (Server eds are not necessarily x64)
			$version = "2003";
		} else if (strstr($agent_temp, "NT 5.0")) {
			//"Windows NT 5.01"
			$version = "2000";
		} else if (strstr($agent_temp, "WinNT")) {
			$version = "NT";
		} else if (strstr($agent_temp, "Windows NT;")) {
			$version = "NT";
		} else if (strstr($agent_temp, "Windows NT 4.0")) {
			$version = "NT";
		} else if (strstr($agent_temp, "Win 9x 4.90")) {
			$version = "ME";
		} else if (strstr($agent_temp, "Windows 98")) {
			$version = "98";
		} else if (strstr($agent_temp, "Win98")) {
			$version = "98";
		} else if (strstr($agent_temp, "Windows 95")) {
			$version = "95";
		} else if (strstr($agent_temp, "Win95")) {
			$version = "95";
		} else if (strstr($agent_temp, "Windows CE")) {
			$version = "CE";
		}
	} else if (strstr($agent_temp, "Macintosh")) {
		$os = "Mac";
		$version = "Unknown";
		if (strstr($agent_temp, "OS X")) {
			//$temp = explode("OS X", $agent_temp);
			//$temp = explode(";", $temp[1]);
			//$temp = str_replace("_", ".", $temp[0]);
			//$version = "OS X" .$temp;
			$version = "OS X";
		}
	} else if (strstr($agent_temp, "Linux")) {
		// X11
		// NOTE: usually it follows this order: Mozilla/version (clientInformation;) RenderingEngine/version LinuxDistribution/version (optional brackets!) Browser/version
		// Translation. Here's the pattern. Name/version (optional extra info) Name/version (optional extra info) Name/version (optional extra info) Name/version (optional extra info)
		// (Operating system information is the third one, if at all. May need to count how many terms there are.)
		// Exceptions. This: "Iceweasel/2.0.0.13 (Debian-2.0.0.13-0etch1)" "Ubuntu/7.10 (gutsy) Firefox/2.0.0.13 (Linux Mint)" <- Browser parenthesis should override distribution stuff.
		// First, strip anything preceding the first set of parenthesis
		$os = "Linux";
		$version = "Unknown";
		//echo "{{{" .preg_replace("/(\w+)\/[^>]* (\((.*?)\)){0,1}/i", "$1", $agent_temp). "<br />\n";
		$parts = explode("/", $agent_temp);
		if (count($parts) == (1+4)) {
			// $parts[2] // Contains name of distro.
			// $parts[3] // Contains version of distro.
			$parts2 = explode(" ", $parts[2]);
			$parts3 = explode(" ", $parts[3]);
			//echo "{{{" .count($parts). "..." .$parts2[1]. "/" .$parts3[0]. "}}}";
			$dist = $parts2[1];
			if (strstr($dist, "Red")) {
				$version = "Red Hat";
			} else if (strstr($dist, "Firefox")) {
				//$version = "Unknown";
			} else if (strstr($dist, "Epiphany")) {
				//$version = "Unknown";
			} else {
				$version = $dist;
			}
		} else if (count($parts) == (1+3)) {
			/* Doesn't fully work.
			if ($k = strrchr($parts[3], "(")) {
				$dist = str_replace("(", "", $k);
				$dist = str_replace(")", "", $dist);
				if ($dist != "like Gecko") {
					$version = $dist;
				}
				
			}
			*/
			$parts2 = explode(" (", $parts[3]);
			$dist = str_replace(")", "", $parts2[1]);
			//$dist = str_replace(")", "", $dist);
			if ($dist) {
				if (strstr($dist, "Debian")) {
					$version = "Debian";
				} else if (strstr($dist, "Ubuntu")) {
					$version = "Ubuntu";
				} else if (strstr($dist, "like Gecko SUSE")) {
					$version = "SUSE";
				} else if (strstr($dist, "like Gecko")) {
					//$version = "Unknown";
				} else {
					$version = $dist;
				}
			}
		}
	}
	
	if ($record['title'] != "") {
		$query = "SELECT * FROM " .SQL_PREFIX. "grapeos WHERE
grapeos_hour = '" .$hour. "' AND
grapeos_day = '" .$day. "' AND
grapeos_month = '" .$month. "' AND
grapeos_year = '" .$year. "' AND
grapeos_os = '" .$os. "' AND
grapeos_version = '" .$version. "'";
		$result = mysql_query($query) or die(report_error("E_DB", mysql_error(), __LINE__, __FILE__));
		$num_rows = mysql_num_rows($result);

		if ($num_rows == 1) {
		
			// Get id of entry.
			$row = mysql_fetch_array($result);
			$os_id = $row['grapeos_id'];
		
			// Edit os grape entry.
			$query = "UPDATE " .SQL_PREFIX. "grapeos SET grapeos_hits = (grapeos_hits + 1) WHERE grapeos_id = '" .$os_id. "'";
			$result = mysql_query($query) or die(report_error("E_DB", mysql_error(), __LINE__, __FILE__));
		
		} else if ($num_rows == 0) {
			$url = $record['url']; // We don't want to tamper with the actual $record['url'] variable.
			$url = str_replace("http://", "", $url);
			$url = str_replace("www.", "", $url);
		
			$query = "INSERT INTO " .SQL_PREFIX. "grapeos(grapeos_os, grapeos_version, grapeos_hour, grapeos_day, grapeos_month, grapeos_year, grapeos_hits)
VALUES('" .$os. "', '" .$version. "', '" .$hour. "', '" .$day. "', '" .$month. "', '" .$year. "', '1')";
			$result = mysql_query($query) or die(report_error("E_DB", mysql_error(), __LINE__, __FILE__));
			$os_id = mysql_insert_id(); // Use LAST_INSERT_ID() instead?
			/*For debugging, add this junk to the insert query: grapeos_useragent -> '" .$record['agent']. "' */
		}
	}
	return $os_id;
}
function GrapeOSApi() {
	
	$day = date('j');
	$month = date('n');
	$year = date('y');

	$date = mktime(00, 00, 00, $month, $day, $year);
	$cdate = mktime(00, 00, 00, $month, $day, $year);
	$arr = array();
	$total = 0;
	for ($i = 0; $i < 30; $i++) {
		$cdate = mktime(00, 00, 00, $month, $day - $i, $year);

		$query  =  "SELECT * FROM " . SQL_PREFIX . "grapeos WHERE ";
		$query .= " grapeos_year = '" . date('y', $cdate) . "' AND";
		$query .= " grapeos_month = '" . date('n', $cdate) . "' AND";
		$query .= " grapeos_day = '" . date('j', $cdate) . "'";

		$r = mysql_query($query) or die (report_error("E_DB", mysql_error(), __LINE__, __FILE__));

		while ($row = mysql_fetch_array($r)) {
			$type = $row["grapeos_os"];
			$version = $row["grapeos_version"];
			$version = $version == "(KHTML," ? "unknown" : $version;

			$tmpName = "";
			if (strtolower($type) == "linux") {
				$tmp = $type;
				$type = $version;
				$version = $tmp;
				$tmpName = strtolower($type) == "unknown" ? $version : $type . " " . $version;
			}
			else {
				$tmpName = strtolower($version) == "unknown" ? $type : $type . " " . $version;
			}

			if (isset($arr[$tmpName])) {
				$arr[$tmpName] += $row['grapeos_hits'];
			}
			else {
				$arr[$tmpName] = $row['grapeos_hits'];
			}
			$total += $row['grapeos_hits'];
		}
	}
	arsort($arr);

	$first = date('y-m-d', $date);
	$last = date('y-m-d', $cdate);

	$data = array();

	foreach ($arr as $v => $hits) {
		array_push($data, 
			"
			{
				\"os\": \"$v\",
				\"hits\": \"$hits\"
			}
			"
		);

	}

	$cont = '
	"title": "Operating Systems",
	"first": "' . $first . '",
	"last": "' . $last . '",
	"total": "' . $total . '",
	"data": [
	';

	for ($i = 0; $i < count($data); $i++) {
		$cont .= $data[$i] . ($i < count($data) - 1 ? ',' : '');
	}

	$cont .= "\n\t]";

	return $cont;
	return false;
}
function GrapeOSInstall() {
	global $ext, $pg;
	// Create table
	$grapeos = "CREATE TABLE IF NOT EXISTS " .SQL_PREFIX. "grapeos (
grapeos_id int(11) NOT NULL auto_increment,
grapeos_os text NOT NULL default '',
grapeos_version text NOT NULL default '',
grapeos_year int(11) NOT NULL default '0',
grapeos_month int(11) NOT NULL default '0',
grapeos_day int(11) NOT NULL default '0',
grapeos_hour int(11) NOT NULL default '0',
grapeos_hits int(11) NOT NULL default '0',
PRIMARY KEY (grapeos_id)
)";
	$result = mysql_query($grapeos) or die ("<b>MySQL Error</b>: " .mysql_error());
	$pg['content'] .= "<img src=\"" .$location. "images/yes.png\" alt=\"\" /> Table '" .SQL_PREFIX. "grapeos' created.<br />";
	
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
function GrapeOSUninstall($keep = 1) {
	global $ext, $pg;
	// Delete table (if chosen to do so by the user).
	if ($keep == 0) {
		$query = "DROP TABLE " .SQL_PREFIX. "grapeos";
		$result = mysql_query($query) or die(report_error("E_DB", mysql_error(), __LINE__, __FILE__));
		$pg['content'] .= "<img src=\"" .$location. "images/yes.png\" alt=\"\" /> Table '" .SQL_PREFIX. "grapeos' deleted.<br />";
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
