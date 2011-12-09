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
$minute = 59;
$minute_selected = $minute;
$hour = 23;
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

$pg['content'].="\n<div id=\"mason\">";
$pg['content'] .= '<div id="placeholder" class="box graph col2"></div>';


$pg['content'] .= "<script type=\"text/javascript\">
$(function () {
	var total = [], unique = [];";
	
$temp_minute = $minute;
$temp_hour = $hour;
$temp_day = $day;
$temp_month = $month;
$temp_year = $year;

$nda = mktime(00, 00, 00, $temp_month, $temp_day, $year) * 1000;

$pg['content'] .= "\n	var lastDate = (new Date($nda));";

	$maxVisits = 0;
	$lastDate = date("U", mktime(00, 00, 00, $temp_month, $temp_day, $year));
	for ($i = 0; $i <= 30; $i++) {
		// Display visitor stats for this month.
		if ($temp_day <= 0) {
			$temp_day += date("t", mktime(00, 00, 00, $temp_month, $temp_day, $year)); // Depends on the number of days in the previous month!!!
			$temp_month--; // Is only decreased in this specific case so as to not cause wrong results.
		}
		$temp_date = date("D j", mktime(00, 00, 00, $temp_month, $temp_day, $year));
		$tempUE = (mktime(00, 00, 00, $temp_month, $temp_day, $year)) * 1000;
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
	$nda = mktime(00, 00, 00, $temp_month, $temp_day, $year) * 1000;
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

		$numCol = 1;
		if ( $ext['name'] == "UserSpy") { $numCol = 2; }
		else if ( $ext['name'] == "GrapeReferrers") { $numCol = 2; }

		$pg['content'] .= "\n<div class=\"box col" . $numCol . "\" rel=\"" . $ext['name'] . "\">" .$ext['display'](). "\n</div>\n";

		$extCounter++;
	}
}

$pg['content'] .= "</div><br clear=\"both\">";

require_once($template_location);
?>