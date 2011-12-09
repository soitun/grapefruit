<?php
	ini_set('display_errors', 1);
	error_reporting(E_ALL|E_STRICT);


/* Functions
* This page is the framework called by all pages imediately before any actual php code, (with exception to the $location variable, which tells where a page is relative to the index directory).
*/

// ****************
// Location.
/* NOTE: This is a target for exploits.
Most servers are safe when PHP's Register Global's is turned off (which is off by default and should never be turned on anyway).
The reason there is no exploit checker is because it means one extra thing to process, and almost all servers are secure from this.
FIXED BELOW:
Kill that remote file include exploit once and for all by overriding any $_GET['location'] or $_POST['location'].
The location variable should never be used for anything other than for the location of the root installation.
It is an internal variable and shouldn't be modifiable otherwise.
NOTE: For whatever reason, I am unable to turn Register Global on to test this. This could very well kill the script by overriding the main $location when register global is on?
*/
$_GET['location'] = "";
$_POST['location'] = "";

/*
// Lock file if it is being executed by itself.
$temp = explode("/", $_SERVER['SCRIPT_FILENAME']);
$count = count($temp) - 1;
if ($temp[$count] == "functions.php") {
	$location = "../";
	
	lock_file();
}
*/

if (!isset($location)) {
	$location = "../"; // Make the location default relative to this file.
	$temp = explode("/", $_SERVER['SCRIPT_FILENAME']);
	$count = count($temp) - 1;
	report_error("E_MSG", "The location variable was not supplied.", $temp[$count]);
}

// Change error handling to custom
set_error_handler("report_error");
error_reporting(E_ALL ^ E_NOTICE);

// Load settings
require($location. "includes/config.php");

// Set up database variables for when the database gets connected.
$connected = 0;

$session_start = 1;
session_start();

if (phpversion() >= "5.1.0") {
	date_default_timezone_set($cms['timezone']); //date_default_timezone_set("UTC");
} else {
	putenv("TZ=" .$cms['timezone']);
}

// ****************
// Globally used variables.
$session_name = "grape";
$template_location = $location. "includes/themes/" .$cms['theme']. "/index.php";

$cms['name'] = "Grape Web Statistics";
$cms['version'] = "0126"; // (0200 is for 0.2 final.)
$cms['versionFull'] = "0.2.0.5 Beta 3";
$cms['versionType'] = "beta"; // dev, rc, full
$cms['updateUrl'] = "http://www.quate.net/updates.php";

// ****************
/* Grape Statistics Functions */

function grape_hits_total($fyear = "", $fmonth = "", $fday = "", $fhour = "", $fminute = "") {
	//http://www.tizag.com/mysqlTutorial/mysqlsum.php
	
	$query = "SELECT SUM(grapestat_hits) FROM " .SQL_PREFIX. "grapestat WHERE";
	
	if ($fminute != "") {
		$query .= " grapestat_minute = '" .$fminute. "' AND";
	}
	if ($fhour != "") {
		$query .= " grapestat_hour = '" .$fhour. "' AND";
	}
	if ($fday != "") {
		$query .= " grapestat_day = '" .$fday. "' AND";
	}
	if ($fmonth != "") {
		$query .= " grapestat_month = '" .$fmonth. "' AND";
	}
	if ($fyear != "") {
		$query .= " grapestat_year = '" .$fyear. "' AND";
	}
	// Clean up query additions (the tailing 'AND' may cause an error).
	$query .= "-+-";
	$query = str_replace("AND-+-", "", $query);
	$query = str_replace("-+-", "", $query);
	
	$result = mysql_query($query) or die(report_error("E_DB", mysql_error(), __LINE__, __FILE__));
	$count = mysql_fetch_array($result);
	if ($count[0] == "") {
		$count[0] = "0";
	}
	return $count[0];
}

function grape_hits_unique($fyear = "", $fmonth = "", $fday = "", $fhour = "", $fminute = "") {
	//OLD NOTE: select DISTINCT ip_addr from some_table
	// http://justinsomnia.org/2004/06/how-to-count-unique-records-with-sql/
	$query = "SELECT COUNT(DISTINCT grapestat_ip) FROM " .SQL_PREFIX. "grapestat WHERE";
	
	if ($fminute != "") {
		$query .= " grapestat_minute = '" .$fminute. "' AND";
	}
	if ($fhour != "") {
		$query .= " grapestat_hour = '" .$fhour. "' AND";
	}
	if ($fday != "") {
		$query .= " grapestat_day = '" .$fday. "' AND";
	}
	if ($fmonth != "") {
		$query .= " grapestat_month = '" .$fmonth. "' AND";
	}
	if ($fyear != "") {
		$query .= " grapestat_year = '" .$fyear. "' AND";
	}
	// Clean up query additions (the tailing 'AND' may cause an error).
	$query .= "-+-";
	$query = str_replace("AND-+-", "", $query);
	$query = str_replace("-+-", "", $query);
	
	$result = mysql_query($query) or die(report_error("E_DB", mysql_error(), __LINE__, __FILE__));
	$count_unique = mysql_fetch_array($result);
	if ($count_unique[0] == "") {
		$count_unique[0] = "0";
	}
	return $count_unique[0];
}

// ****************
/* Grape API/Extensions Functions */

function enableExtension($ext_location) {
	global $location;
	$file = $location. "extensions/extensions.php";
	$openfile = file($location. "extensions/extensions.php");
	$openfile = str_replace("?>", "", $openfile);
	$contents = "";
	// Rebuild file contents by getting the prevous lines.
	foreach ($openfile as $line_num => $line) {
		//$contents .= "\$enabled_ext[] = \"" .$line. "\";\n";
		if ($line_num != 0) {
			$contents .= $line;
		}
	}
	$contents .= "\$enabled_ext[] = \"" .$ext_location. "\";\n";
	$contents = "<?php\n" .$contents. "\n?>";
	$contents = str_replace("\n\n", "\n", $contents);
	
	$handle = fopen($file, 'r+');
	fwrite($handle, $contents);
	fclose($handle);
	return true;
}

function disableExtension($ext_location) {
	global $location;
	$file = $location. "extensions/extensions.php";
	$handle = fopen($file, "r+");
	$contents = fread($handle, (filesize($file) * 2)); // * 2 to fix bug with only reading part of file (windows platform).
	$contents = str_replace("\$enabled_ext[] = \"" .$ext_location. "\";\n", "", $contents);
	fclose($handle);
	
	$handle = fopen($file, "w+");
	fwrite($handle, $contents);
	fclose($handle);
	return true;
}

function getExtensionId($ext) {
	// Get extension id by extension's name.
	global $extensions;
	$k = 0;
	foreach ($extensions as $e) {
		$k++;
		//echo $k. "-" .$e['name']. "<br>\n";
		if ($e['name'] == $ext) {
			$e_id = $k;
		}
	}
	if ($e_id) {
		return $e_id;
	} else {
		return false;
	}
}

function setExtensionId($ext) {
	global $extensions;
	//global $extensions, $extensions_count;
	/*
	if (!$extensions_count) {
		$extensions_count = 0;
	}
	$extensions_count++;
	$extensions[$extensions_count] = $ext;
	*/
	$extensions[] = $ext;
	return $extensions_count;
}

/*
function loadExtensions() {
	chdir("./extensions/"); // Move (cd) to another directory, relative to the directory of this file.
	$dirpath = getcwd();
	$dh = opendir($dirpath);

	while (false !== ($file = readdir($dh))) {
		if (is_dir("$dirpath/$file")) {
			// Continue, but ignore these files (in if case below)
			if ($file == "." || $file == ".." || $file == ".AppleDouble") {
			} else {
				unset($ext); // Delete previous extension vars.
				require_once("" .$file. "/info.php");
				$id = setExtensionId($ext);
			}
		}
	}
	chdir("../");
	
	return true;
}
*/
function loadExtensions() {
	require($location. "extensions/extensions.php");
	if (isset($enabled_ext)) {
		foreach ($enabled_ext as $enabled) {
			$file = $enabled;
			require_once($location . "extensions/" .$file. "/info.php");
			$id = setExtensionId($ext);
			unset($ext); // Delete extension vars to prepare for loading the next extension vars.
		}
	}
	return true;
}

// ****************
/* Generic Functions */

function fetch_remote_file($url) {
	global $cms;
	$url_parts = parse_url($url);
	
	// Reference: http://www.bin-co.com/php/scripts/load/
	$fp = fsockopen($url_parts['host'], 80, $errno, $errstr, 10);
	$response = "";
	if ($fp) {
		$out = "GET " .$url. " HTTP/1.0\r\n"; // "POST $page HTTP/1.1\r\n";
		$out .= "Host: " .$url_parts['host']. "\r\n";
		$out .= "Accept: text/*\r\n";
		$out .= "User-Agent: " .$cms['name']. "/" . $cms['version']. "\r\n";
		$out .= "Connection: Close\r\n";
		$out .= "\r\n";
		fwrite($fp, $out);
		while (!feof($fp)) {
			$response .= fgets($fp, 128);
		}
		fclose($fp);
	}
	if ($response) {
		// Remove headers
        $separator_position = strpos($response, "\r\n\r\n");
        $header_text = substr($response, 0, $separator_position);
        $results = substr($response, $separator_position + 4);
        
        foreach(explode("\n",$header_text) as $line) {
            $parts = explode(": ",$line);
            if(count($parts) == 2) $headers[$parts[0]] = chop($parts[1]);
        }
		return $results;
	}
}

function settings_load() {
	global $connected;
	$query = "SELECT * FROM " .SQL_PREFIX. "grapesetting WHERE grapesetting_load = '1'";
	$result = mysql_query($query) or die(report_error("E_DB", mysql_error(), __LINE__, __FILE__));
	while ($row = mysql_fetch_array($result)) {
		$settings[$row['grapesetting_parent_internal']][$row['grapesetting_name_internal']] = $row['grapesetting_value'];
	}
	return $settings;
}

function settings_enable() {
	return 1;
}

function settings_disable() {
	return 1;
}

function is_admin() {
	global $session_name, $_SESSION, $session_start, $connected;
	global $location, $template_location;
	// Make sure session has started.
	/*
	if ($session_start == 0) {
		session_start();
		$session_start = 1;
	}
	*/
	
	// Database needs to be connected.
	if (!$connected) {
		return false;
	}
	
	if (isset($_SESSION[$session_name])) {
		$query = "SELECT * FROM " .SQL_PREFIX. "grapeaccount WHERE grapeaccount_id = '" .sql_protect($_SESSION[$session_name]). "'";
		$result = mysql_query($query) or die(report_error("E_DB", mysql_error(), __LINE__, __FILE__));
		$row = mysql_fetch_array($result);
		// If player is not an admin.
		if (mysql_num_rows($result) != "1") {
			//header("Location: " .$location. "./admin/login");
			return false;
		}
		
		// Set admin theme:
		/*
		if ($row['grapeaccount_theme'] != "") {
			$template_location = $location. "includes/themes/" .$row['grapeaccount_theme']. "/index.php";
		}
		*/
		return true;
	} else {
		return false;
		//header("Location: " .$location. "./admin/login");
	}
}

function lock_file() {
	global $location;
	$simple_gui['title'] = "Error";
	$simple_gui['header'] .= $simple_gui['title'];
	$simple_gui['content'] .= "This file or directory is locked.";
	require($location. "includes/simple_gui.php");
	exit();
}

function is_installed() {
	global $location;
	// Make sure database setup has been run
	if (!is_file("includes/installed")) {
		$simple_gui['title'] = "Grape Not Installed";
		$simple_gui['header'] .= $simple_gui['title'];
		$simple_gui['content'] .= "The <a href=\"install.php\">installer</a> needs to be run before Grape can function properly.";
		require($location. "includes/simple_gui.php");
		exit();
	}
}

function dbconnect() {
	global $db, $connected;
	
	// Connecting to the database twice may cause errors.
	if (!$connected) {
		// Create constants:
		define('SQL_HOST', $db['host']);
		define('SQL_USER',$db['user']);
		define('SQL_PASS', $db['pass']);
		define('SQL_DB', $db['name']);
		define('SQL_PREFIX', $db['prefix']);
		
		// Connect to database.
		$connect = mysql_connect(SQL_HOST, SQL_USER, SQL_PASS)
		or die(report_error("E_DB", mysql_error(), __LINE__, __FILE__));

		mysql_select_db(SQL_DB, $connect)
		or die(report_error("E_DB", mysql_error(), __LINE__, __FILE__));
		
		$connected = $connect; // Tell script that the database has been connected. If not connected, $connected will equal zero.
	}
	return true;
}

function fetchurl($url)
{
// Could use try/catch?! for various methods.
	// Try Curl method first.
	if (extension_loaded('curl'))
	{
		$handle = curl_init();
		curl_setopt($handle, CURLOPT_URL, $url);
		curl_setopt($handle, CURLOPT_FRESH_CONNECT, TRUE);
		curl_setopt($handle, CURLOPT_TIMEOUT, 10);
		curl_setopt($handle, CURLOPT_CONNECTTIMEOUT, 4);
		curl_setopt($handle, CURLOPT_RETURNTRANSFER, 1);
		//curl_setopt($handle, CURLOPT_HTTPHEADER, $headers);
		//curl_setopt($handle, CURLOPT_USERAGENT, $defined_vars['HTTP_USER_AGENT']);
		//curl_setopt($handle, CURLOPT_POST, true);
		//curl_setopt($handle, CURLOPT_POSTFIELDS, $post);
      
		$data = curl_exec($handle);
		if (curl_errno($handle))
		{
			$error = curl_error($handle);
			//echo $error;
			return false;
		}
		curl_close($handle);
		return $data;
	} else {
		// Try fopen.
		
		// Code based on this code: http://us.php.net/manual/en/function.fopen.php#76899
		$url = str_replace("http://", "", $url);
		if (preg_match("#/#", $url))
		{
			$page = $url;
			$url = @explode("/", $url);
			$url = $url[0];
			$page = str_replace($url, "", $page);
		} 
		if ($page == "")
		{
			$page = "/";
		}
		$ip = gethostbyname($url);
		
		$open = fsockopen($ip, 80, $errno, $errstr, 60);
		$send = "GET " .$page. " HTTP/1.0\r\n";
		$send .= "Host: " .$url. "\r\n";
		//$send .= "Accept-Language: en-us, en;q=0.50\r\n";
		$send .= "Connection: Close\r\n\r\n";
		$put = fputs($open, $send);
		if (!$put) {
			return false;
		}
		while (!feof($open)) {
			$return .= fgets($open, 4096);
		}
		fclose($open);
		
		$return = @explode("\r\n\r\n", $return, 2);
		//$header = $return[0];
		$body = $return[1];
		return $body;
	}
}

function xss_protect($val) {
	// Use these instead?!
	// http://quickwired.com/kallahar/smallprojects/php_xss_filter_function.php
	// http://www.phpclasses.org/browse/package/2189.html
	$val = htmlentities($val, ENT_QUOTES, 'UTF-8');
	//$val = str_replace('\\"', '"', $val);
	return $val;
}

// The following function is taken from here: http://us3.php.net/manual/en/function.str-replace.php#70689
function str_replaceonce($search, $replace, $content) {
    $pos = strpos($content, $search);
    if ($pos === false) { return $content; }
    else { return substr($content, 0, $pos) . $replace . substr($content, $pos+strlen($search)); }
}

function nameuc($var) {
	$name = explode(" ", $var);
	
	$name_string = "";
    for ($i = 0; $i < count($name); $i++) {
		$name_string .= " " .ucfirst(strtolower($name[$i]));
	}
	
	return $name_string;
}

//
// Cut text length
function textcut($str, $len, $end = "...") {
	$str = html_entity_decode($str, ENT_QUOTES); // Replaces &#039; as a single quotation mark and so on, so that the &#039; doesn't get cut off as &#0 and not work!
	$strlen = strlen($str);
	if ($strlen > $len) {
		$newstr = substr($str, 0, $len);
		$newstr = substr($str, 0, strrpos($newstr, " ")); // Using strrpos to get the length rather than strrchar to get the end of the string!
		$str = $newstr. "" .$end;
	}
	$str = htmlentities($str, ENT_QUOTES); // Replaces single quotation marks back to  &#039;  and so on.
	return $str;
}

// This function cuts at exactly the indicated amount. Words may be cut, rather than "rounding" to the nearest space.
// This is a quick method, particularly useful for single word texts, like URLs.
function textcutsimple($str, $len, $end = "...") {
	$strlen = strlen($str);
	if ($strlen > $len) {
		$newstr = substr($str, 0, $len);
		return $newstr. "" .$end;
	} else {
		return $str;
	}
}
/*
function textcut($text, $cutlen) {
  $textlen = strlen($text);
  if ($textlen > $cutlen) {
    $new_text = substr($text, 0, $cutlen); // Cut text.
    $back_text = substr($text, $cutlen); // Get text that was cut.
    $remove_text = strrchr($new_text, " ");
    $remove_text = $remove_text. "" .$back_text;
    $new_text = str_replace($remove_text, "", $text);
    return $new_text. "...";
 } else {
    return $text;
 }
}
*/

//
// Make variable to make safe before insterting to a MySQL query.
// (Original function from http://us3.php.net/mysql_real_escape_string)
function sql_protect($value) {
  // Stripslashes - only strip slashes if hasn't been done yet. (we don't want data to be escaped twice).
  if (get_magic_quotes_gpc()) {
    $value = stripslashes($value);
  }

  if (phpversion() >= '4.3.0') {
    $value = mysql_real_escape_string($value);
  } else {
    $value = mysql_escape_string($value);	
  }
  return $value;
}


//
// Function to support shorter URLs.
function get_short_url($location) {
 /*
 Description: Get a _GET variable from
 Why?: Normally, $_GET[''] does not work. This function allows you to use it.
 Parameters: $location: starting point of the URL, starting from 0.  (So, 0 is the first position, 1 is the second, and 2 is the third, etc.,.)
 */
 
 // If the location given is nothing, assume it is 0.
 if ($location == "") {
  $location = 0;
 }
 
 // Get the string that contains the entire _GET query for this page.
 $query = $_SERVER['QUERY_STRING'];
 
 // Split the query into the different _GET variable name and value parts.
 $query_parts = explode("&", $query);
 
 // Split the query part from the location specified, into the _GET variable name as one variable, and it's corresponding value as another variable.
 $value = explode("=", $query_parts[$location]);
 
 // If the _GET variable that was split has no value, it means we put the value as the _GET variable name instead.
 if ($value[1] == "") {
  // Give the resulting value.
  return $value[0];
 } else {
  // Give the resulting value.
  return $value[1];
 }
}

//
// Write to config file
function writeconfig($term, $value, $location) {
	// Define file
	$file = $location. "config.php";

	// Open file
	$handle = fopen($file, 'r');
	$data = fread($handle, (filesize($file) * 2)); // * 2 to fix bug with only reading part of file (windows platform).
	fclose($handle);

	$value = str_replace('"', '\"', $value);

	$term_pattern = $term;
	$term_pattern = str_replace("[", "\[", $term_pattern);
	$term_pattern = str_replace("]", "\]", $term_pattern);
	$term_pattern = str_replace("'", "\'", $term_pattern);

	// Replace
	$data = preg_replace("/" .$term_pattern. "\ \=\ \"(.*?)\"\;/", "" .$term. " = \"" .$value. "\";", $data);

	// Write to file
	if (!is_writable($file)) {
		return false;
	} else {
		$handle = fopen($file, 'w');
		fwrite($handle, $data);
		fclose($handle);

		return true;
	}
}

//
// Custom PHP error handling (Including database handling)
// error file and error line are not required. The report function may be manually called simply by using: report_error("E_MSG", "Message here.");
function report_error($error_type, $error_message, $error_file="", $error_line="") {
	global $location;
	switch($error_type) {
	case E_PARSE:
		$error_type = "Parse Error";
		$error_text = "An error occurred while parsing.";
		break;
	case E_ERROR:
		$error_type = "Fatal Error";
		$error_text = "A fatal error occurred.";
		break;
	case E_WARNING:
		$error_type = "Warning Error";
		$error_text = "An error occurred (warning).";
		break;
	case E_NOTICE:
		$error_type = "Notice Error";
		$error_text = "An error occurred (notice).";
		break;
	case "E_DB":
		$error_type = "Database Error";
		$error_text = "A database error has occurred.";
		break;
	case "E_MSG":
		$error_type = "Error";
		$error_text = "A system error has occured.";
		break;
	}
	if ($error_type == "Notice Error") {
		// Continue as if everything was ok in the script
	} else {
		$simple_gui['title'] = "Error";
		$simple_gui['header'] = $error_type;
		//$simple_gui['content'] = $error_text. "<br>\n" .$error_message. "<br>";
		$simple_gui['content'] = $error_message. "<br />";
		if ($error_line != "") {
			$simple_gui['content'] .= "On line: <i>" .$error_line ."</i><br />";
		}
		if ($error_file != "") {
			$simple_gui['content'] .= "In file: <i>" .$error_file ."</i><br />";
		}
		require("simple_gui.php");
		exit(); // Exit script so that it doesn’t cause any more problems!
	}
}

function days_in_month($month, $year) { 
	return $month == 2 ? ($year % 4 ? 28 : ($year % 100 ? 29 : ($year % 400 ? 28 : 29))) : (($month - 1) % 7 % 2 ? 30 : 31); 
}
?>