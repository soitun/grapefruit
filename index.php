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


$pg['title'] .= $_SERVER['HTTP_HOST'];
$pg['head'] .= "";
$pg['body'] .= "";
$pg['notice'] .= "";
$pg['content'] .= "<div class=\"iso\"><div class=\"main box\">";

$pg['content'] .= '<h3>Visitors</h3><div id="placeholder" class="placeholder"></div>';


$pg['content'] .= "<script type=\"text/javascript\">
var maxData = 0;
$(function () {
	var total = [], unique = [];
	var firstDate = \"\", lastDate = \"\";
	//var maxData = \"\";";
	
// so, this is the new, api fun stuff that I implemented. It's in JSON, which is SWEET
// ref: total.push([$tempUE, $temp_total]);\nunique.push([$tempUE, $temp_unique])

$pg['content'] .= "
	$.getJSON(\"" . $cms['location'] . "api.php\",
		function(data) {
			var theDate;
			$.each(data.visitors, function(i, val) {
				d = val.date.split('-');
				var day = new Date(parseInt(d[0]) + 2000, parseInt(d[1]) - 1, parseFloat(d[2]));

				total.push([day.getTime(), val.total]);
				unique.push([day.getTime(), val.unique]);
			});

			maxData = data.max;

			d = data.first.split('-');
			firstDate = new Date(parseInt(d[0]) + 2000, parseInt(d[1]) - 1, parseInt(d[2]));

			d = data.last.split('-');
			lastDate = new Date(parseInt(d[0]) + 2000, parseInt(d[1]) - 1, parseInt(d[2]));
			

			$.plot($(\"#placeholder\"),
           [ { data: total, label: \"Total Visits\"}, { data: unique, label: \"Unique Visitors\" } ], {
               series: {
                   lines: { show: true, fill: true },
                   points: { show: false }
                  
               },
               grid: { hoverable: true, clickable: false },
               yaxis: { min: 0, max: maxData }, 
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
                var monthNames = [ 'Jan', 'Feb', 'Mar', 'Apr', 'May', 'June', 'July', 'Aug', 'Sept', 'Oct', 'Nov', 'Dec' ]
                showTooltip(item.pageX, item.pageY, d.getDate() + ' ' + monthNames[d.getMonth()] + ': ' + item.datapoint[1]);
            }
        }
        else {
            $(\"#tooltip\").remove();
            previousPoint = null;           
        }
    });

		}
	);

";

$pg['content'] .= "


		
});
</script>
";


// Display Extensions:
loadExtensions();
$extCounter = 0;
$numRow = 1;
if (isset($extensions)) {
	foreach ($extensions as $ext) {

		$pg['content'] .= "\n<div class=\"box\" rel=\"" . $ext['name'] . "\">" .$ext['display'](). "\n</div>\n";

		if ($extCounter == 1) { $pg['content'] .= "</div><div class=\"left\">"; }

		$extCounter++;

	}
	$pg['content'] .= "</div>";
}

$pg['content'] .= "</div></div><br clear=\"both\">";

require_once($template_location);
?>