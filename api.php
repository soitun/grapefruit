<?php
	$location = "./";
	require_once($location. "includes/functions.php");

	// connect to database, because it's important
	dbconnect();
	// this is the initial jsonp
	echo "{";
	

	// figure out what api you want
	
	// "standard" api call - returns the past month's visitors in json form
	if (!isset($_GET['api']) || $_GET['api'] == "visits") {
		// this is the VISITS stuff, so, title that esse.
		echo "\n\t\"title\": \"Grapefruit Visits\", ";

		// now the fun stuffs, the visitor data!
		//
		// basically, I'm stealing the code from the pervious version's index.php file
		// and modifying it for json-y purposes.
		//
		// actually, that page is one of the main reasons I started working on the api
		// because echoing out all of that data on the index adds to page bloat, and
		// I thought having an api, not just a random function in each extension's
		// info.php file for the api would be useful.
		//
		// The api function will be used later in this file, but for the first api
		// call, the visitors, there isn't an extension that really fits with it.


		// okay, now time for some newly reformatted stuff. I may actually write
		// better the third time through...
		

		// starting with today - only pushing dates, not times.
		$day = date('j');
		$month = date('n');
		$year = date('y');

		// make two arrays: total visits and unique ($total and $unique)
		// key will be date in "YY-MM-DD" form, echo will be 
		//{ date: $key, unique: $unique(date), total: $total(date) }
		$total = array();
		$unique = array();

		// temp variable for last date
		$last = date('y-m-d');

		// loop and setup for loop
		$cdate = mktime(00, 00, 00, $month, $day, $year);
		$max = 0;

		// 30 days
		for ($i = 0; $i < 30; $i++) {
			// is it last month? it doesn't matter, php knows how to work with that...
			$cdate = mktime(00, 00, 00, $month, $day - $i, $year);

			// add stuff to the arrays, you know, they're really more like maps...
			$total[date('y-m-d', $cdate)] = grapefruit_total($cdate);
			$unique[date('y-m-d', $cdate)] = grapefruit_unique($cdate);

			if (grapefruit_total($cdate) >= $max) { $max = grapefruit_total($cdate); }
		}
		
		// temp variable for first date
		$first = date('y-m-d', $cdate);

		// tell people the first and last date
		echo "\n\t\"first\": \"$first\",";
		echo "\n\t\"last\": \"$last\",";
		echo "\n\t\"max\": \"$max\",";

		// echo start of json array
		echo "\n\t\"visitors\": [";

		// okay, now the loop for the dates and numbers, see above for information
		// on how it's being displayed

		foreach ($total as $k => $v) {
			echo "\n\t\t{";
			echo "\n\t\t\t\"date\": \"$k\",";
			echo "\n\t\t\t\"total\": \"$total[$k]\",";
			echo "\n\t\t\t\"unique\": \"$unique[$k]\"";
			echo "\n\t\t}" . ($k == $first ? "" : ","); 
		}

		// close out that json array
		echo "\n\t]";

	}
	else {
		$api = $_GET['api'];
			
		loadExtensions();
		if (isset($extensions)) {
			foreach ($extensions as $ext) {
				if ($api == $ext['name']) {
					echo $ext['api']();
				}
			}
		}

	}

	// finish off that json!
	echo "\n}";
?>