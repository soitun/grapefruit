<?php
$location = "./";
require_once($location. "includes/functions.php");

/*
$script_directory = substr($_SERVER['SCRIPT_FILENAME'], 0, strrpos($_SERVER['SCRIPT_FILENAME'], '/'));
$script_directory = str_replace($_SERVER['DOCUMENT_ROOT'], "", $script_directory);
$script_directory = "http://" .$_SERVER['HTTP_HOST']. "" .$script_directory. "/";
*/

$short_url = get_short_url('0');
if ($short_url == "js") {
	// NOTE: javascript section shouldn't need a database connection.
	header("Content-Type: application/x-javascript");

	//echo "// Retrieve info.";
	echo "title=document.title;
url=window.decodeURI(document.URL);
referrer=window.decodeURI(document.referrer);\n";

	// Load extension javascript: Variables to track, such as screen width.
	// NOTE: Needs to update the request_url according to the variables. Maybe have the javascript compile the string according to the variables given.
	loadExtensions();
	if (isset($extensions)) {
		foreach ($extensions as $ext) {
			//echo $ext['javascript'](). "\n";
			echo $ext['javascript']();
		}
	}

	/*
	// Debug mode detector
	$tmp = substr($_SERVER['HTTP_REFERER'], strrpos($_SERVER['HTTP_REFERER'], "/"));
	$urladd = "";
	if (strpos($tmp, "debug")) {
		//echo "alert(\"Test 1 out of 2: Grape javascript is working.\");";
		//echo "document.writeln('<div id=\"grapeDebug\" style=\"background:#fff;padding:3px;\">Grape seems to be working!</div>');";
	}
	*/
	
	//echo "// Send info.";
	echo "request_url=\"" .$cms['location']. "record.php?u=\"+encodeURI(url)+\"&t=\"+encodeURI(title)+\"&r=\"+encodeURI(referrer)+\"&d=\"+new Date().getTime();
document.write('<'+'script type=\"text/javascript\" src=\"'+request_url+'\"><'+'/script>');";

	// Old debug mode detector in javascript:
	/*
	echo "var debug = (window.location.href.search(/debug/) != -1);
if (debug == true) {

}";
*/

	exit();
} else if ($short_url == "api") {

	loadExtensions();
	if (isset($extensions)) {
		foreach ($extensions as $ext) {
			echo $ext['api']();
		}
	}
	
	exit();
}

// Make sure is installed.
is_installed();

// Connect to the database.
dbconnect();

// Login checker if required by settings.
if ($cms['display_protect'] == 1) {
	session_start();
	$session_start = 1;

	if (!is_admin()) {
		header("Location: " .$location. "login.php?&1");
		exit();
	}
}

// Set initial dates.
$minute = date("i");
$minute_selected = $minute;
$hour = date("G");
$hour_selected = $hour;
$day = date("j");
$day_selected = $day;
$month = date("n");
$month_selected = $month;
$year = date("y");
$year_selected = $year;
$display = "month";
//Debug: echo "Real Date: " .$year. "." .$month. "." .$day. "." .$hour. "<br>\n";

// Set modified dates to be used as the default selector for the filter (aka "last settings").
if ($_GET['hour'] != "") {
	$hour_selected = $_GET['hour'];
}
if ($_GET['day'] != "") {
	$day_selected = $_GET['day'];
}
if ($_GET['month'] != "") {
	$month_selected = $_GET['month'];
}
if ($_GET['year'] != "") {
	$year_selected = $_GET['year'];
}
if ($_GET['display'] != "") {
	$display = strtolower($_GET['display']);
}
if ($display == "hour") {
	$display_hour_selected = " selected=\"selected\"";
} else if ($display == "day") {
	$display_day_selected = " selected=\"selected\"";
} else if ($display == "month") {
	$display_month_selected = " selected=\"selected\"";
} else if ($display == "year") {
	$display_year_selected = " selected=\"selected\"";
} else {
	$display = "month";
	$display_month_selected = " selected=\"selected\"";
}

$pg['title'] .= $_SERVER['HTTP_HOST'];
$pg['head'] .= "";
$pg['body'] .= "";
$pg['notice'] .= "";
$pg['content'] .= "";

$pg['content'] .= "<div class='grid3 first'><div class=\"box\" rel='filter'>
<form action=\"./\" method=\"get\">
<div class='title'>Filter</div>
<table cellspacing='0'>
<tbody>
<tr><th>Day</th> <th>Month</th> <th>Year</th></tr>
<tr class='alt1'>

<td><select name=\"day\">";
$k = 0;
while ($k < 31) {
	$k++;
	
	// Why get days in current month when all 31 days should be displayed. We don't know if the user will chose another month with a different max amount of days.
	//$temp = date("j", mktime($hour, $minute, 0, $month, (0 + $k), $year));
	$temp = $k;
	if ($k == $day_selected) {
		$pg['content'] .= "\n<option value=\"" .$temp. "\" selected=\"selected\">" .$temp. "</option>";
	} else {
		$pg['content'] .= "\n<option value=\"" .$temp. "\">" .$temp. "</option>";
	}
}
$pg['content'] .= "</select>
</td>

<td><select name=\"month\">";
$k = 0;
while ($k < 12) {
	$k++;
	
	$temp = date("n", mktime($hour, $minute, 0, (0 + $k), $day, $year));
	if ($k == $month_selected) {
		$pg['content'] .= "\n<option value=\"" .$temp. "\" selected=\"selected\">" .$temp. "</option>";
	} else {
		$pg['content'] .= "\n<option value=\"" .$temp. "\">" .$temp. "</option>";
	}
}
$pg['content'] .= "</select>
</td>

<td><select name=\"year\">";

// Get first entry in the database, which should give us the date (year) at which these statistics were started.
$query = "SELECT grapestat_id, grapestat_year FROM " .SQL_PREFIX. "grapestat ORDER BY grapestat_id ASC";
$result = mysql_query($query) or die(report_error("E_DB", mysql_error(), __LINE__, __FILE__));
$row = mysql_fetch_array($result);
$k = $row['grapestat_year']; // First year...
while ($k <= $year) {
	$temp = date("y", mktime($hour, $minute, 0, $month, $day, $k));
	$temp_full = date("Y", mktime($hour, $minute, 0, $month, $day, $k));
	if ($k == $year_selected) {
		$pg['content'] .= "\n<option value=\"" .$temp. "\" selected=\"selected\">" .$temp_full. "</option>";
	} else {
		$pg['content'] .= "\n<option value=\"" .$temp. "\">" .$temp_full. "</option>";
	}
$k++;
}
$pg['content'] .= "</select>

</td>

</tr>";

$pg['content'] .= "
</tbody>
</table>

<div id='fbuttons'>
	<input type=\"submit\" value=\"Filter\" class='fbut' />
	<input type=\"button\" value=\"Clear\" class='fbut' onclick=\"document.location = './';\" />
</div>

</form>
</div>";

// Set modified dates (a filter)
if ($_GET['minute'] != "") {
	// Minute filtering probably won't ever be used, but it is here incase someone wants to use it.
	$minute = $_GET['minute'];
}
if ($_GET['hour'] != "") {
	$hour = $_GET['hour'];
}
if ($_GET['day'] != "") {
	$day = $_GET['day'];
}
if ($_GET['month'] != "") {
	$month = $_GET['month'];
}
if ($_GET['year'] != "") {
	$year = $_GET['year'];
}

//Debug: echo "Filter Date: " .$year. "." .$month. "." .$day. "." .$hour. "<br>\n";

// MODULE: Display basic visit statistics.
// Display stats within html box.
$pg['content'] .= "<div class=\"box\" rel='visits'>
<div class=\"title\">Visits</div>
<table cellspacing=\"0\">
<tr class=\"subheader\">
	<th>Past " .ucfirst($display). "</th>
	<th>Unique</th>
	<th>Total</th>
</tr>";

// Display overall statistics for the display type.
if ($display == "hour") {
	//if (!$_GET['showall']) {
	if (!$_GET['visits']) {
		$stat_display = 10;
	} else {
		$stat_display = 60;
	}
	$pg['content'] .= "\n<tr class=\"alt1\">
	<td><b>This " .ucfirst($display). "</b></td>
	<td><b>" .grape_hits_unique($year, $month, $day, $hour, ""). "</b></td>
	<td><b>" .grape_hits_total($year, $month, $day, $hour, ""). "</b></td>
</tr>";
/*
$pg['content'] .= "\n<tr class=\"alt2\">
	<td>Average</td>
	<td>" .round((grape_hits_unique($year, $month, $day, $hour, "") / $minute), 0). "</td>
	<td>" .round((grape_hits_total($year, $month, $day, $hour, "") / $minute), 0). "</td>
</tr>";
*/
} else if ($display == "day") {
	//if (!$_GET['showall']) {
	if (!$_GET['visits']) {
		$stat_display = 12;
	} else {
		$stat_display = 24;
	}
	$pg['content'] .= "\n<tr class=\"alt1\">
	<td><b>Today</b></td>
	<td><b>" .grape_hits_unique($year, $month, $day, "", ""). "</b></td>
	<td><b>" .grape_hits_total($year, $month, $day, "", ""). "</b></td>
</tr>";
/*
$pg['content'] .= "\n<tr class=\"alt2\">
	<td>Average</td>
	<td>" .round((grape_hits_unique($year, $month, $day, "", "") / $hour), 0). "</td>
	<td>" .round((grape_hits_total($year, $month, $day, "", "") / $hour), 0). "</td>
</tr>";
*/
} else if ($display == "month") {
	//if (!$_GET['showall']) {
	if (!$_GET['visits']) {
		$stat_display = 7;
	} else {
		$stat_display = 31;
	}
	$pg['content'] .= "\n<tr class=\"alt1\">
	<td><b>This " .ucfirst($display). "</b></td>
	<td><b>" .grape_hits_unique($year, $month, "", "", ""). "</b></td>
	<td><b>" .grape_hits_total($year, $month, "", "", ""). "</b></td>
</tr>";
/*
$pg['content'] .= "\n<tr class=\"alt2\">
	<td>Average</td>
	<td>" .round((grape_hits_unique($year, $month, "", "", "") / $day), 0). "</td>
	<td>" .round((grape_hits_total($year, $month, "", "", "") / $day), 0). "</td>
</tr>";
*/
} else {
	//if (!$_GET['showall']) {
	if (!$_GET['visits']) {
		$stat_display = 6;
	} else {
		$stat_display = 12;
	}
	$pg['content'] .= "\n<tr class=\"alt1\">
	<td><b>This " .ucfirst($display). "</b></td>
	<td><b>" .grape_hits_unique($year, "", "", "", ""). "</b></td>
	<td><b>" .grape_hits_total($year, "", "", "", ""). "</b></td>
</tr>";
/*
$pg['content'] .= "\n<tr class=\"alt2\">
	<td>Average</td>
	<td>" .round((grape_hits_unique($year, "", "", "", "") / $month), 0). "</td>
	<td>" .round((grape_hits_total($year, "", "", "", "") / $month), 0). "</td>
</tr>";
*/
}
$pg['content'] .= "
<tr class=\"subheader\">
	<th>Past " .ucfirst($display). "</th>
	<th>Unique</th>
	<th>Total</th>
</tr>";

$temp_minute = $minute;
$temp_hour = $hour;
$temp_day = $day;
$temp_month = $month;
$temp_year = $year;
$k = 0;
$alt = 1;
while ($k < $stat_display) {
	if ($display == "hour") {
		// Display visitor stats for this hour.
		if ($temp_minute <= 0) {
			$temp_minute += 60;
			$temp_hour--; // Is only decreased in this specific case so as to not cause wrong results.
		}
		$temp_date = date("G\:i", mktime($temp_hour, $temp_minute, 0, $month, $day, $year));
		$temp_unique = grape_hits_unique($year, $month, $day, $temp_hour, $temp_minute);
		$temp_total = grape_hits_total($year, $month, $day, $temp_hour, $temp_minute);
		
		$temp_minute--;
	} else if ($display == "day") {
		// Display visitor stats for this day.
		if ($temp_hour <= 0) {
			$temp_hour += 24;
			$temp_day--; // Is only decreased in this specific case so as to not cause wrong results.
		}
		$temp_date = date("G\:00", mktime($temp_hour, $minute, 0, $month, $temp_day, $year));
		$temp_unique = grape_hits_unique($year, $month, $temp_day, $temp_hour, "");
		$temp_total = grape_hits_total($year, $month, $temp_day, $temp_hour, "");
		
		$temp_hour--;
	} else if ($display == "month") {
		// Display visitor stats for this month.
		if ($temp_day <= 0) {
			$temp_day += date("t", mktime($hour, $minute, 0, $temp_month, $temp_day, $year)); // Depends on the number of days in the previous month!!!
			$temp_month--; // Is only decreased in this specific case so as to not cause wrong results.
		}
		$temp_date = date("D j", mktime($hour, $minute, 0, $temp_month, $temp_day, $year));
		$temp_unique = grape_hits_unique($year, $temp_month, $temp_day, "", "");
		$temp_total = grape_hits_total($year, $temp_month, $temp_day, "", "");
		
		$temp_day--;
	} else {
		// Display visitor stats for this year.
		if ($temp_month <= 0) {
			$temp_month += 12; // Depends on the number of days in the previous month!!!
			$temp_year--; // Is only decreased in this specific case so as to not cause wrong results.
		}
		$temp_date = date("M", mktime($hour, $minute, 0, $temp_month, $day, $temp_year));
		$temp_unique = grape_hits_unique($temp_year, $temp_month, "", "", "");
		$temp_total = grape_hits_total($temp_year, $temp_month, "", "", "");
	
		$temp_month--;
	}
	/*
	$pg['content'] .= "\n<div class=\"box-alt" .$alt. "\">
<div class=\"leftc\">" .$temp_date. "</div>
<div class=\"leftc2\">" .$temp_unique. "</div>
<div class=\"mainc\">" .$temp_total. "</div>
</div>";
*/
	$pg['content'] .= "\n<tr class=\"alt" .$alt. "\">
	<td>" .$temp_date. "</td>
	<td>" .$temp_unique. "</td>
	<td>" .$temp_total. "</td>
</tr>";

	if ($alt == 1) {
		$alt = 2;
	} else {
		$alt = 1;
	}
	
	$k++;
}
/*
$pg['content'] .= "<div class=\"box-alt" .$alt. "\"><a href=\"\">Show All</a></div>
</div>\n";
*/
/*
if (!$_GET['showall']) {
	$pg['content'] .= "<tr class=\"alt" .$alt. "\">
	<td colspan=\"3\"><a href=\"?" .$_SERVER['QUERY_STRING']. "&amp;showall=1\">Show All</a></td>
</tr>";
}
*/
if (!$_GET['visits']) {
	$pg['content'] .= "<tr>
	<td colspan=\"3\"><a href=\"?" .$_SERVER['QUERY_STRING']. "&amp;visits=1\">Show All</a></td>
</tr>";
}
$pg['content'] .= "\n</table>\n</div></div>\n";

// Display Extensions:
loadExtensions();
if (isset($extensions)) {
	foreach ($extensions as $ext) {
		$alt = 1;
		//$pg['content'] .= "<div class=\"clear\"></div>"; // Display all stats vertically.
		$pg['content'] .= "\n" .$ext['display'](). "\n";
	}
}

$pg['content'] .= "<div class=\"clear\"></div>";

require_once($template_location);
?>