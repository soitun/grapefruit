<?php

$ext['location']			= "SearchTerms";
$ext['name']				= "SearchTerms";
$ext['description']			= "What are people searching for?";
$ext['descriptionFull']		= "Find what search terms people are using to get to your site.";
$ext['url']					= "http://dkuntz2.com/grapefruit/";
$ext['version']				= "001";	// the sector
$ext['versionFull']			= "0.0.1";
$ext['updateUrl']			= "http://dkuntz2.com/grapefruit/";
$ext['compatibleVersion']	= "1000";
$ext['author']				= "Don Kuntz";
$ext['authorUrl']			= "http://dkuntz2.com/";
$ext['display']				= "SearchTermsDisplay";
$ext['javascript']			= "SearchTermsJavascript";
$ext['record']				= "SearchTermsRecord";
$ext['api']					= "SearchTermsApi";
$ext['install']				= "SearchTermsInstall";
$ext['uninstall']			= "SearchTermsUninstall";
$ext['grapeStatColumn']		= "terms";



function SearchTermsDisplay() {
	
}

function SearchTermsJavascript() {
	return false;
}

function SearchTermsRecord() {
	global $year, $month, $day, $hour, $minute, $record;

	$ref = $record['referrer'];
	//$host = $record['host'];

	//$host = str_replace("http://", "", str_replace("www.", "", $host));
	$ref = str_replace("http://", "", str_replace("www.", "", $ref));

	

	$term = "";

}

function SearchTermsApi() {
	return false;
}

function SearchTermsInstall() {
	global $ext, $pg;

	// create table
	$q = 	"CREATE TABLE IF NOT EXISTS " . SQL_PREFIX . "searchterms (
				term_id int(11) NOT NULL auto_increment,
				term_term text NOT NULL default '',
				term_year int(11) NOT NULL default '0',
				term_month int(11) NOT NULL default '0',
				term_day int(11) NOT NULL default '0',
				term_hour int(11) NOT NULL default '0',
				term_hits int(11) NOT NULL default '0',
				PRIMARY KEY (term_id)
			)";

	$result = mysql_query($q) or die ("<strong>MySQL Error</strong>: " . mysql_error());

	$pg['content'] .= "<img src=\"" . $location . "images/yes.png\" alt=\"\" /> Table '" . SQL_PREFIX . "searchterms' created.<br />";

	// add col to main grapefruit table - holds reference id
	// but check if it's in existence
	$query = "SHOW COLUMNS FROM " . SQL_PREFIX . "grapestat";
	$result = mysql_query($query) or die(report_error("E_DB", mysql_error(), __LINE__, __FILE__));
	$col_exists = false;
	while ($row = mysql_fetch_array($result)) {
		if ($row['Field'] == "grapestat_" . $ext['grapeStatColumn']) {
			$col_exists = true;
		}
	}
	if (!$col_exists) {
		$query = "ALTER TABLE " . SQL_PREFIX . "grapestat ADD grapestat_" . $ext['grapeStatColumn'] . " int(11) NOT NULL default '0'";
		$result = mysql_query($query) or die(report_error("E_DB", mysql_error(), __LINE__, __FILE__));
	}
	$pg['contnet'] .= "<img src=\"" . $location . "images/yes.png\" alt=\"\" /> " . $ext['name'] . " added to enabled extensions list.<br />";

	return true;
}

function SearchTermsUninstall() {
	
}


?>