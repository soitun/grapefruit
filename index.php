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

/*
$pg['content'] .= "<div class=\"containerblock\">
<div class='grid3 first'><div class=\"box\" rel='filter'>
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
</div>
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
*/

$pg['content'] .= '<div id="placeholder" class="graph container"></div>';


$pg['content'] .= "<script type=\"text/javascript\">
$(function () {
	var total = [], unique = [];";
	
$temp_minute = $minute;
$temp_hour = $hour;
$temp_day = $day;
$temp_month = $month;
$temp_year = $year;

$nda = mktime(date("H"), date("i"), date("s"), $temp_month, $temp_day, $year) * 1000;

$pg['content'] .= "\n	var lastDate = (new Date($nda));";

	$maxVisits = 0;
	$lastDate = date("U");
	for ($i = 0; $i <= 30; $i++) {
		// Display visitor stats for this month.
		if ($temp_day <= 0) {
			$temp_day += date("t", mktime($hour, $minute, 0, $temp_month, $temp_day, $year)); // Depends on the number of days in the previous month!!!
			$temp_month--; // Is only decreased in this specific case so as to not cause wrong results.
		}
		$temp_date = date("D j", mktime($hour, $minute, 0, $temp_month, $temp_day, $year));
		$tempUE = (mktime($hour, $minute, 0, $temp_month, $temp_day, $year)) * 1000;
		$temp_unique = grape_hits_unique($year, $temp_month, $temp_day, "", "");
		$temp_total = grape_hits_total($year, $temp_month, $temp_day, "", "");
		
		//echo $temp_day . "  " . mktime($hour, $minute, 0, $temp_month, $temp_day, $year) . "\n";

		$temp_day--;
		$maxVisits = $maxVisits < $temp_total ? $temp_total : $maxVisits;

		//echo $tempUE . " \n";
		$pg['content'] .= "\ntotal.push([$tempUE, $temp_total]);\nunique.push([$tempUE, $temp_unique]);";
	}
	$temp_day++;
	$maxVisits++;
	$tfy = 2000 + $temp_year;
	$nda = mktime(date("H"), date("i"), date("s"), $temp_month, $temp_day, $year) * 1000;
	$pg['content'] .= "\n	var firstDate = (new Date($nda));";


$pg['content'] .= "\n\nvar plot = $.plot($(\"#placeholder\"),
		   [ { data: total, label: \"Total Visits\"}, { data: unique, label: \"Unique Visitors\" } ], {
			   series: {
				   lines: { show: true, fill: true },
				   points: { show: false }
				  
			   },
			   grid: { hoverable: true, clickable: false },
			   yaxis: { min: 0, max: $maxVisits }, 
			   xaxis: { mode: \"time\", min: firstDate, max: lastDate, minTickSize: [1, \"day\"] }
			 });

	function showTooltip(x, y, contents) {
		$('<div id=\"tooltip\">' + contents + '</div>').css( {
			position: 'absolute',
			display: 'none',
			top: y + 5,
			left: x + 5,
			border: '1px solid #fdd',
			padding: '2px',
			'background-color': '#fee',
			opacity: 0.80
		}).appendTo(\"body\").fadeIn(200);
	}

	var previousPoint = null;
	$(\"#placeholder\").bind(\"plothover\", function (event, pos, item) {
		$(\"#x\").text(pos.x.toFixed(0));
		$(\"#y\").text(pos.y.toFixed(0));

		if (item) {
			if (previousPoint != item.dataIndex) {
				previousPoint = item.dataIndex;
					
				$(\"#tooltip\").remove();
				
				var d = new Date(item.datapoint[0]);
				
				showTooltip(item.pageX, item.pageY, item.datapoint[1]);
			}
		}
		else {
			$(\"#tooltip\").remove();
			previousPoint = null;			
		}
	});
});
</script>
";


// Display Extensions:
loadExtensions();
$extCounter = 0;
$numRow = 1;
if (isset($extensions)) {
	foreach ($extensions as $ext) {
		$alt = 1;
		//$pg['content'] .= "<div class=\"clear\"></div>"; // Display all stats vertically.
		$pg['content'] .= "\n<div class=\"box" . "\" rel=\"" . $ext['name'] . "\">" .$ext['display'](). "\n</div>\n";

		$extCounter++;
	}
}

$pg['content'] .= "<br clear=\"both\">";

require_once($template_location);
?>