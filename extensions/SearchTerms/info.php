<?php
$ext['location'] = "SearchTerms"; // Name of the folder that contains this extension.

$ext['name'] = "SearchTerms";
$ext['description'] = "See what people are searching for when they get to your site.";
$ext['descriptionFull'] = "SearchTerms finds what search terms people are using on the big three search engines to get to your site.";
$ext['url'] = ""; //update this when I upload it to the server.
$ext['version'] = "001"; // Numeric-only version number to be compared to when checking for updates.
$ext['versionFull'] = "0.0.1";
$ext['updateUrl'] = ""; //update this when I upload it to the server
$ext['compatibleVersion'] = "001";

$ext['author'] = "Don Kuntz";
$ext['authorUrl'] = "http://dkuntz2.com/";

// Name functions used by this extension.
$ext['display'] = "SearchTermsDisplay";
$ext['javascript'] = "SearchTermsJavascript"; // Not needed!
$ext['record'] = "SearchTermsRecord"; // Not needed!
$ext['api'] = "SearchTermsApi";
$ext['install'] = "SearchTermsInstall"; // Only used for display purposes.
$ext['uninstall'] = "SearchTermsUninstall"; // Only used for display purposes.

$ext['grapeStatColumn'] = ""; // Not needed!


// Define functions.
function SearchTermsDisplay() {
	global $ext, $year, $month, $day, $hour, $minute, $display;
	
	$content .= "<div class='section' rel='right'>\n<div class=\"box box-float-small\" rel=\"refer\">
<div class=\"box-header\">Search Terms</div>
<table cellspacing=\"0\">
<tr class=\"subheader\">
	<th>Hits</th>
	<th>Term</th>
</tr>";

	$query = "SELECT *, sum(graperef_hits) AS graperef_hits FROM " .SQL_PREFIX. "graperef WHERE";
	if ($display == "hour") {
		$query .= " graperef_hour = '" .$hour. "' AND";
	}
	if ($display != "year" && $display != "month") {
		$query .= " graperef_day = '" .$day. "' AND";
	}
	if ($display != "year") {
		$query .= " graperef_month = '" .$month. "' AND";
	}
	$query .= " graperef_year = '" .$year. "' GROUP BY graperef_http ORDER BY graperef_hits DESC";
	if (!$_GET[strtolower($ext['name'])]) {
		$query .= " LIMIT 10";
	}
	$result = mysql_query($query) or die(report_error("E_DB", mysql_error(), __LINE__, __FILE__));
	$alt = 1;
	while ($row = mysql_fetch_array($result)) {
		$term = $row['graperef_http'];
		$urls = array();
		$s = false;

		if(substr($term, 0, 6) == "google") {
			$urls = explode("&", substr($term, 0, 10));
			$qr = -1;
			for($i = 0; $i < count($urls); $i++) {
				if(substr($urls[$i], 0, 2) == "q=") {$qr = $i;}
			}
			if ($qr = -1) {
				$term = "No Term Found";
			}
			else {
				$term = $urls[$qr];
				$term = str_replace("+", " ", $term);
			}
			$s = true;
		}
		else if (substr($term, 0, 5) == "yahoo") {
			$urls = explode("&", substr($term, 0, 9));
			$qr = -1;
			for($i = 0; $i < count($urls); $i++) {
				if(substr($urls[$i], 0, 2) == "p=") {$qr = $i;}
			}
			if ($qr = -1) {
				$term = "No Term Found";
			}
			else {
				$term = $urls[$qr];
				$term = str_replace("+", " ", $term);
			}
			$s = ture;
		}
		else if (substr($term, 0, 4) == "bing") {
			$urls = explode("&", substr($term, 0, 10));
			$qr = -1;
			for($i = 0; $i < count($urls); $i++) {
				if(substr($urls[$i], 0, 2) == "q=") {$qr = $i;}
			}
			if ($qr = -1) {
				$term = "No Term Found";
			}
			else {
				$term = $urls[$qr];
				$term = str_replace("+", " ", $term);
			}
			$s = true;
		}

		if ($s) {


		$content .= "\n<tr class=\"alt" .$alt. "\">
	<td>" .$row['graperef_hits']. "</td>
	<td>" .textcutsimple($term, 25). "</a></td>
</tr>";
		if ($alt == 1) {
			$alt = 2;
		} else {
			$alt = 1;
		}
		}
	}
	//$content .= "<div class=\"box-alt" .$alt. "\"><a href=\"\">Show All</a></div>";
	/*
	if (!$_GET['showall']) {
		$content .= "<tr class=\"alt" .$alt. "\">
	<td colspan=\"2\"><a href=\"?" .$_SERVER['QUERY_STRING']. "&amp;showall=1\">Show All</a></td>
</tr>";
	}*/
	if (!$_GET[strtolower($ext['name'])]) {
		$content .= "\n<tr class=\"alt" .$alt. "\">
	<td colspan=\"2\"><a href=\"?" .$_SERVER['QUERY_STRING']. "&amp;" .strtolower($ext['name']). "=1\">Show All</a></td>
</tr>";
	}
	$content .= "\n</table>\n</div>\n";
	return $content;
}
function SearchTermsJavascript() {
	return false;
}
function SearchTermsRecord() {
	return false;
}
function SearchTermsApi() {
	return false;
}
function SearchTermsInstall() {
	global $ext, $pg;

	// Add extension to the enabled extensions file.
	enableExtension($ext['location']);
	
	$pg['content'] .= "<img src=\"" .$location. "images/yes.png\" alt=\"\" /> " .$ext['name']. " added to enabled extensions list.<br />";
	
	return true;
}
function SearchTermsUninstall($keep = 1) {
	global $ext, $pg;
	
	// Remove extension from the enabled extensions file.
	disableExtension($ext['location']);
	
	$pg['content'] .= "<img src=\"" .$location. "images/yes.png\" alt=\"\" /> " .$ext['name']. " removed from the enabled extensions list.<br />";
	
	return true;
}
?>
