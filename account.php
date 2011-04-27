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

$pg['title'] .= "Account Settings";
$pg['head'] .= "";
$pg['body'] .= "";
$pg['notice'] .= "";
$pg['content'] .= "<div class=\"box main-box\">
<a href=\"panel.php\"><b>Admin</b></a>
<span>&gt;</span>
<span><a href=\"?\">Account Settings</a></span>
</div>";

// Display alternative content
$url['type'] = get_short_url("0");
if ($url['type'] == "save") {

	// Check if password is set to be changed:
	if ($_POST['pass'] != "") {
		// Check if the password is sha1 hashed.
		if ($_POST['pass_enc'] == "") {
			$_POST['pass'] = sha1($_POST['pass']);
		} else {
			$_POST['pass'] = $_POST['pass_enc'];
		}
		$query = "UPDATE " .SQL_PREFIX. "grapeaccount SET grapeaccount_password = '" .sql_protect($_POST['pass']). "' WHERE grapeaccount_id = '" .$_SESSION[$session_name]. "'";
		$result = mysql_query($query) or die(report_error("E_DB", mysql_error(), __LINE__, __FILE__));
	}
	
	$pg['content'] .= "<div class=\"box box-full\">
<div class=\"box-header\">" .$pg['title']. "</div>
<div class=\"box-content\"><p>Settings saved.</p>
<input type=\"button\" value=\"Okay\" onclick=\"document.location.href = '?';\">
</div>
</div>";
	//fetchurl($ext['updateUrl']);

	require_once($template_location);
	exit();
}

// Include client-side sha1 encryption script.
$pg['head'] .= '<script type="text/javascript" src="' .$location. 'includes/scripts/sha1.js"></script>';
$pg['head'] .= '<script type="text/javascript" src="' .$location. 'includes/scripts/sendpass.js"></script>';

$pg['content'] .= "<div class=\"box box-full\">
<div class=\"box-header\">" .$pg['title']. "</div>
<form action=\"?save\" method=\"post\">
<table cellspacing=\"0\">
<tr class=\"subheader\">
	<th>Setting</th>
	<th>Value</th>
</tr>
<!--
<tr class=\"alt1\">
	<td><b>Change Theme</b>
<p>Change the Grape theme.</p></td>
	<td><select style=\"width:150px;\" name=\"theme\">\n";
chdir("./includes/themes/"); // Move (cd) to another directory, relative to the directory of this file.
$dirpath = getcwd();
$dh = opendir($dirpath);
while (false !== ($file = readdir($dh))) {
	if (is_dir("$dirpath/$file")) {
		// Continue, but ignore these files (in if case below)
		if ($file == "." || $file == ".." || $file == ".AppleDouble") {
		} else {
			$pg['content'] .= "<option value=\"" .$file. "\">" .ucfirst($file). "\n";
		}
	}
}
chdir("../../");
$pg['content'] .= "</select></td>
</tr>
-->
<tr class=\"alt1\">
	<td><b>Change Password</b>
<p>Leave blank to keep current password.</p></td>
	<td><input type=\"password\" style=\"width:150px;\" name=\"pass\" id=\"pass\" /></td>
</tr>
<tr class=\"alt2\">
	<td colspan=\"2\"><div style=\"text-align:right;\">
<input type=\"hidden\" name=\"pass_enc\" value=\"\" id=\"pass_enc\" />
<input type=\"submit\" value=\"Save\" onclick=\"sendpass('pass', 'pass_enc');\" />
<input type=\"button\" value=\"Cancel\" onclick=\"document.location.href = 'panel.php';\" />
</div></td>
</tr>
</table>
</form>";
$pg['content'] .= "</div>";

require_once($template_location);
?>