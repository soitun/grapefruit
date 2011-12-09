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

$pg['title'] .= "Extensions";
$pg['head'] .= "";
$pg['body'] .= "";
$pg['notice'] .= "";
$pg['content'] .= "<div class=\"box main-box\">
<a href=\"panel.php\"><b>Admin</b></a>
<span>&gt;</span>
<span><a href=\"?\">Manage Extensions</a></span>
</div>";

// Display alternative content
$url['type'] = get_short_url("0");
$url['id'] = get_short_url("1");
$url['confirm'] = get_short_url("2");
if ($url['type'] == "update") {
	// Load extension
	require_once("extensions/" .$url['id']. "/info.php");
	
	$pg['content'] .= "<div class=\"box box-full\">
<div class=\"box-header\">Update Extension</div>
<div class=\"box-content\"><p>Checking for updates for <b>" .$ext['name']. "</b>.</p>
<p>This feature does not work yet.
However, you can manually check for updates for this extension <a href=\"" .xss_protect($ext['updateUrl']). "\">here</a>.</p>
<input type=\"button\" value=\"Okay\" onclick=\"document.location.href = '?';\" />
</div>
</div>";
	//fetchurl($ext['updateUrl']);

	require_once($template_location);
	exit();
}
if ($url['type'] == "install") {
	// Load extension
	require_once("extensions/" .$url['id']. "/info.php");

	// Check if extension is installed.
	require("extensions/extensions.php");
	$is_installed = 0;
	if (isset($enabled_ext)) {
		foreach ($enabled_ext as $tmp) {
			if ($tmp == $url['id']) {
				$is_installed = 1;
			}
		}
	}
	if ($is_installed == 1) {
		$pg['content'] .= "<div class=\"box box-full\">
<div class=\"box-header\">Install Extension</div>
<div class=\"box-content\">";
		$pg['content'] .= "<p><b>" .$ext['name']. "</b> is already installed.</p>";
		$pg['content'] .= "
<input type=\"button\" value=\"Okay\" onclick=\"document.location.href = '?';\" />
</div>
</div>";
		require_once($template_location);
		exit();
	}
	
	// Check if the extension is compatible.
	if ($ext['compatibleVersion'] > $cms['version']) {
		$pg['content'] .= "<div class=\"box box-full\">
<div class=\"box-header\">Install Extension</div>
<div class=\"box-content\">";
		$pg['content'] .= "<p><b>" .$ext['name']. "</b> " .$ext['versionFull']. " is not compatible with Grape " .$cms['versionFull']. ".</p>";
		$pg['content'] .= "
<input type=\"button\" value=\"Okay\" onclick=\"document.location.href = '?';\" />
</div>
</div>";
		require_once($template_location);
		exit();
	}
	
	if ($url['confirm']) {
		$pg['content'] .= "<div class=\"box box-full\">
<div class=\"box-header\">Install Extension</div>
<div class=\"box-content\">";
		$ext['install']();
		$pg['content'] .= "<p><b>" .$ext['name']. "</b> has been installed.</p>
<input type=\"button\" value=\"Okay\" onclick=\"document.location.href = '?';\" />
</div>
</div>";
		
	} else {
		$pg['content'] .= "<div class=\"box box-full\">
<div class=\"box-header\">Install Extension</div>
<div class=\"box-content\">
<b>" .$ext['name']. "</b> version " .$ext['versionFull']. "<br />
<p>" .$ext['descriptionFull']. "</p>
<input type=\"button\" value=\"Install\" onclick=\"document.location.href = 'extensions.php?install&amp;" .$url['id']. "&amp;1';\" />
<input type=\"button\" value=\"Cancel\" onclick=\"document.location.href = '?';\" />
</div>
</div>";
	}
	
	require_once($template_location);
	exit();
}
if ($url['type'] == "uninstall") {
	// Load extension
	require_once("extensions/" .$url['id']. "/info.php");

	// Check if extension is installed.
	require("extensions/extensions.php");
	$is_installed = 0;
	if (isset($enabled_ext)) {
		foreach ($enabled_ext as $tmp) {
			if ($tmp == $url['id']) {
				$is_installed = 1;
			}
		}
	}
	if ($is_installed == 0) {
		$pg['content'] .= "<div class=\"box box-full\">
<div class=\"box-header\">Uninstall Extension</div>
<div class=\"box-content\">";
		$pg['content'] .= "<p><b>" .$ext['name']. "</b> is not installed.</p>";
		$pg['content'] .= "<input type=\"button\" value=\"Okay\" onclick=\"document.location.href = '?';\" />
</div>
</div>";
		require_once($template_location);
		exit();
	}
	
	if ($url['confirm']) {
		$keep = 1; // Keep data by default
		if ($url['confirm'] == 1) {
			$keep = 0;
		} else if ($url['confirm'] == 2) {
			$keep = 1;
		}
		$pg['content'] .= "<div class=\"box box-full\">
<div class=\"box-header\">Uninstall Extension</div>
<div class=\"box-content\">";
		$ext['uninstall']($keep);
		$pg['content'] .= "<p><b>" .$ext['name']. "</b> has been uninstalled.</p>
<input type=\"button\" value=\"Okay\" onclick=\"document.location.href = '?';\" />
</div>
</div>";
	} else {
		$pg['content'] .= "<div class=\"box box-full\">
<div class=\"box-header\">Uninstall Extension</div>
<div class=\"box-content\"><p>Would you like to keep any data from <b>" .$ext['name']. "</b> in case you want to install it again?
Uninstalling all will disable the extension and all data will be permanently deleted.</p>
<input type=\"button\" value=\"Uninstall All\" onclick=\"document.location.href = 'extensions.php?uninstall&amp;" .$url['id']. "&amp;1';\" />
<input type=\"button\" value=\"Keep Data Only\" onclick=\"document.location.href = 'extensions.php?uninstall&amp;" .$url['id']. "&amp;2';\" />
<input type=\"button\" value=\"Cancel\" onclick=\"document.location.href = '?';\" />
</div>
</div>";
	}
	
	require_once($template_location);
	exit();
}
	
// 
$pg['content'] .= "<div class=\"box box-full\">
<div class=\"box-header\">Extensions</div>
<table cellspacing=\"0\">
<tr class=\"subheader\">
	<th></th>
	<th>Extension Name</th>
	<th>Version</th>
	<th>Description</th>
	<th>Author</th>
	<th>Actions</th>
</tr>";

require("extensions/extensions.php");

chdir("./extensions/"); // Move (cd) to another directory, relative to the directory of this file.
$dirpath = getcwd();
$dh = opendir($dirpath);
$alt = 1;
while (false !== ($file = readdir($dh))) {
	if (is_dir("$dirpath/$file")) {
		// Continue, but ignore these files (in if case below)
		if ($file == "." || $file == ".." || $file == ".AppleDouble") {
		} else {
			require_once("" .$file. "/info.php");
			
			// Check if extension is installed/enabled:
			$is_ext_enabled = 0;
			$is_ext_enabled_text = "no";
			$is_ext_enabled_actions = "<a href=\"?install&amp;" .$file. "\">Install</a>";
			if (isset($enabled_ext)) {
				foreach ($enabled_ext as $tmp) {
					if ($tmp == $file) {
						$is_ext_enabled = 1;
						$is_ext_enabled_text = "yes";
						$is_ext_enabled_actions = "<a href=\"?uninstall&amp;" .$file. "\">Uninstall</a>";
					}
				}
			}
			
			$pg['content'] .= "\n<tr class=\"alt" .$alt. "\">
	<td><img src=\"images/" .$is_ext_enabled_text. ".png\" alt=\"\" /></td>
	<td>" .xss_protect($ext['name']). " <a href=\"" .xss_protect($ext['url']). "\" target=\"_blank\"><img src=\"images/link.png\" alt=\"\" /></a></td>
	<td><a href=\"?update&amp;" .$file. "\">" .xss_protect($ext['versionFull']). "</a></td>
	<td>" .textcut(xss_protect($ext['description']), 55). "</td>
	<td><a href=\"" .xss_protect($ext['authorUrl']). "\" target=\"_blank\">" .xss_protect($ext['author']). "</a></td>
	<td>" .$is_ext_enabled_actions. "</td>
</tr>";
			unset($ext); // Delete extension vars.
			
			if ($alt == 1) {
				$alt = 2;
			} else {
				$alt = 1;
			}
		}
	}
}
chdir("../");
$pg['content'] .= "</table>";
$pg['content'] .= "";
$pg['content'] .= "</div>";

require_once($template_location);
?>