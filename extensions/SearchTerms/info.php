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

}
function SearchTermRecord() {
	global $year, $month, $day, $hour, $minute, $record;
	// Don't record self referring.
	$referrer = $record['referrer']; // We don't want to tamper with the actual $record['referrer'] variable.
	$host = $record['host']; // We don't want to tamper with the actual $record['host'] variable.

	$host = str_replace("http://", "", $host);
	$host = str_replace("www.", "", $host);
	
	if(substr($host, 0, 7) != "google" || substr($host, 0, 4) != "bing", || substr($host, 0, 5) != "yahoo" ) {
		return false;
	}

	$term = "";
	$provider = "";
	// GOOGLE
	if(substr($host, 0, 7) == "google") {
		$provider = "Google";
		$arr = explode("&", $host);

		$q = -1;
		for($i = 0; $i < count($arr); $i++) {
			if (substr($arr[$i], 0, 1) == "q") {
				$q = $i;
				$i = count($arr) + 1;
			}
		}

		$term = $arr[$q];
		$term = substr($term, 2, strlen($term));
		$term = str_replace("+", " ", $term);
	}
	// YAHOO
	elseif (substr($host, 0, 5) == "yahoo") {
		$provider = "Yahoo";
		$arr = explode("&", $host);

		$q = -1;
		for($i = 0; $i < count($arr); $i++) {
			if (substr($arr[$i], 0, 1) == "p") {
				$q = $i;
				$i = count($arr) + 1;
			}
		}

		$term = $arr[$q];
		$term = substr($term, 2, strlen($term));
		$term = str_replace("+", " ", $term);
	}

	else {
		$provider = "Bing";
		$arr = explode("&", $host);

		$q = -1;
		for($i = 0; $i < count($arr); $i++) {
			if (substr($arr[$i], 0, 1) == "q") {
				$q = $i;
				$i = count($arr) + 1;
			}
		}

		$term = $arr[$q];
		$term = substr($term, 2, strlen($term));
		$term = str_replace("+", " ", $term);
	}

	

	/*if (strpos(strtolower(" " .$referrer. " "), $host)) {
		$referrer = ""; // Set $referrer to zero so that it doesn't record it.
	}
	// Remove PHPSESSID from url.
	$referrer = preg_replace('/\?PHPSESSID=[^&]+/',"",$referrer);  
	$referrer = preg_replace('/\&PHPSESSID=[^&]+/',"",$referrer);

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
	}*/

	return $ref_id;
}
?>
