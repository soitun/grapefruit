<?php
$location = "./";
require_once($location. "includes/functions.php");

// Make sure is installed.
is_installed();

// Connect to the database.
dbconnect();

session_start();
$session_start = 1;
if (!is_admin()) {
	header("Location: " .$location. "login.php?&1");
	exit();
}

// User authentication for this page!!

$pg['title'] .= "Admin Panel";
$pg['head'] .= "";
$pg['body'] .= "";
$pg['notice'] .= "";

// Display alternative content
$url['type'] = get_short_url("0");
$url['id'] = get_short_url("1");
$url['confirm'] = get_short_url("2");
if ($url['type'] == "update") {
	$pg['content'] .= "<div class=\"box main-box\">
<a href=\"panel.php\"><b>Admin</b></a>
<span>&gt;</span>
<span><a href=\"?\">Update</a></span>
</div>";

	$pg['content'] .= "<div class=\"box box-full\">
<div class=\"box-header\">Update Grape</div>
<div class=\"box-content\">";

	// Both fetchurl and fetch_remote_file have been minimally tested. This feature needs to be tested!!
	//echo fetchurl($cms['updateUrl']);
	$content = fetch_remote_file($cms['updateUrl']);
	// see: http://www.ibm.com/developerworks/library/x-xmlphp1.html
	$dom = new domDocument;
	$dom->loadXML($content);
	if (!$dom) {
		report_error("E_MSG", "Error parsing the xml update document.", __FILE__, __LINE__);
		exit;
	}

	$xml = simplexml_import_dom($dom);
	// Run through xml until an entry for this software is found.
	$found = 0;
	foreach ($xml as $software) {
		if ($software->name == $cms['name']) {
			$found = 1;
			if ($software->version > $cms['version']) {
				$pg['content'] .= "<p>A new " .$cms['name']. " release, version  " .$software->versionFull. ", is available.</p>";
				if ($software->releaseNotes != "") {
					$pg['content'] .= "<p>" .$software->releaseNotes. "</p>";
				}
$pg['content'] .= "<input type=\"button\" value=\"View Website\" onclick=\"document.location.href = '" .$software->websiteUrl. "';\" />
<input type=\"button\" value=\"Direct Download\" onclick=\"document.location.href = '" .$software->downloadUrl. "';\" />
<input type=\"button\" value=\"Cancel\" onclick=\"document.location.href = '?';\" />";
			} else {
				$pg['content'] .= "<p>No new update was found. You are using the latest version.</p>
<input type=\"button\" value=\"Okay\" onclick=\"document.location.href = '?';\" />";
			}
		}
		//echo $software->name. ' version ' .$software->version. '<br />';
	}
	//http://www.openjs.com/scripts/jx/
	if ($found == 0) {
		$pg['content'] .= "<p>No update information was found.
You should manually check for updates at Grape's <a href=\"http://www.quate.net/grape\" target=\"_blank\">website</a>.</p>
<input type=\"button\" value=\"Okay\" onclick=\"document.location.href = '?';\" />";
	}

$pg['content'] .= "</div>
</div>";

	require_once($template_location);
	exit();
} else if ($url['type'] == "code") {
	$pg['content'] .= "<div class=\"box main-box\">
<a href=\"panel.php\"><b>Admin</b></a>
<span>&gt;</span>
<span><a href=\"?\">Code</a></span>
</div>";

	$pg['content'] .= "<div class=\"box box-full\">
<div class=\"box-header\">Get Tracking Code</div>
<div class=\"box-content\"><p>Copy the following javascript code to all pages you want to track statistics for.
If you are using some content management system, you probably only need to paste this code once into the template of your website.</p>
<div class=\"code-box\"><code>&lt;script src=\"" .$cms['location']. "?js\" type=\"text/javascript\"&gt;&lt;/script&gt;<br /></code></div>
<input type=\"button\" value=\"Okay\" onclick=\"document.location.href = '?';\" />
</div>
</div>";
	//echo fetchurl($cms['updateUrl']);
	//echo fetch_remote_file($cms['updateUrl']);
	//http://www.openjs.com/scripts/jx/

	require_once($template_location);
	exit();
} else if ($url['type'] == "about") {
	$pg['content'] .= "<div class=\"box main-box\">
<a href=\"panel.php\"><b>Admin</b></a>
<span>&gt;</span>
<span><a href=\"?\">About</a></span>
</div>";

	$pg['content'] .= "<div class=\"box box-full\">
<div class=\"box-header\">About " .$cms['name']. "</div>
<div class=\"box-content\">
<p><img src=\"" .$location. "includes/themes/" .$cms['theme']. "/images/logo.png\" alt=\"" .$cms['name']. "\" /></p>
<p><small>Version " .$cms['versionFull']. "</small></p>
<p><small>Timezone: " .$cms['timezone']. "</small></p>
<p>Grape is a free, open source program that allows web developers to keep accurate statistics of visitors.</p>
<p>This software makes use of the following projects:</p>
<ul>
<li>Tango Project icons <a href=\"http://tango.freedesktop.org/Tango_Desktop_Project\" target=\"_blank\"><img src=\"" .$location. "images/link.png\"></a> <i>CC Attribution Share-Alike 2.5 License</i></li>
<li>Secure Hash Algorithm javascript algorithm by Paul Johnston <a href=\"http://pajhome.org.uk/crypt/md5/\" target=\"_blank\"><img src=\"" .$location. "images/link.png\"></a> <i>BSD License</i></li>
</ul>
<p>Please visit <a href=\"http://www.quate.net/board/\" target=\"_blank\">our forums</a> if you are having any problems.</p>
<input type=\"button\" value=\"Okay\" onclick=\"document.location.href = '?';\" />
</div>
</div>";
	//echo fetchurl($cms['updateUrl']);
	//echo fetch_remote_file($cms['updateUrl']);
	//http://www.openjs.com/scripts/jx/

	require_once($template_location);
	exit();
}

// 
$pg['content'] .= "<div class=\"box box-full\">
<div class=\"box-header\">Administrator Panel</div>
<div class=\"box-content\">
<b>Admin</b><br />
<img src=\"images/icons/statistics.png\" alt=\"\" /> <a href=\"./\">View Statistics</a><br />
<img src=\"images/icons/extensions.png\" alt=\"\" /> <a href=\"extensions.php\">Manage Extensions</a><br />
<img src=\"images/icons/get-code.png\" alt=\"\" /> <a href=\"?code\">Get Tracking Code</a><br />

<br />
<b>Maintenance</b><br />
<img src=\"images/icons/update.png\" alt=\"\" /> <a href=\"?update\">Check for Updates</a><br />
<img src=\"images/icons/about.png\" alt=\"\" /> <a href=\"?about\">About/Credits</a><br />

<br />
<b>Account</b><br />
<img src=\"images/icons/settings.png\" alt=\"\" /> <a href=\"account.php\">Account Settings</a><br />
<img src=\"images/icons/logout.png\" alt=\"\" /> <a href=\"login.php?logout\">Logout</a><br />
</div>";
$pg['content'] .= "</div>";

require_once($template_location);
?>