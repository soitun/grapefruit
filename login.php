<?php
$location = "./";
require_once($location. "includes/functions.php");

// Make sure is installed.
is_installed();

// Connect to the database.
dbconnect();

session_start();
$session_start = 1;

$pg['title'] .= "Login";
$pg['head'] .= "";
$pg['body'] .= "";
$pg['notice'] .= "";
$pg['content'] .= "<div class=\"box main-box\">
<a href=\"panel.php\"><b>Admin</b></a>
<span>&gt;</span>
<span><a href=\"?\">Login</a></span>
</div>";

// Display alternative content
$url['step'] = get_short_url("0");
if ($url['step'] == "login") {
	
	// Check if the password is sha1 hashed.
	/*
	if (strlen($_POST['pass']) != 40) {
		// sha1 hash the password.
		$_POST['pass'] = sha1($_POST['pass']);
	}
	*/
	// Check if the password is sha1 hashed.
	if ($_POST['pass_enc'] == "") {
		$_POST['pass'] = sha1($_POST['pass']);
	} else {
		$_POST['pass'] = $_POST['pass_enc'];
	}

	$query = "SELECT grapeaccount_id, grapeaccount_name, grapeaccount_password FROM " .SQL_PREFIX. "grapeaccount
WHERE grapeaccount_name = '" .sql_protect($_POST['user']). "' AND grapeaccount_password = BINARY '" .sql_protect($_POST['pass']). "'";
	$result = mysql_query($query) or die(report_error("E_DB", mysql_error(), __LINE__, __FILE__));
	$row = mysql_fetch_array($result);
	if (mysql_num_rows($result) != 1) {
		header("Location: ./login.php?&2");
		exit();
	}
	$_SESSION[$session_name] = $row['grapeaccount_id'];
	//header("Location: " .$location. "");
	header("Location: " .$location. "panel.php");
	exit();
	
	/*
	$pg['content'] .= "<div class=\"box box-full\">
<div class=\"box-header\">Login to Grape</div>
<div class=\"box-content\"></div>
</div>";

	require_once($template_location);
	exit();
	*/
}
if ($url['step'] == "logout") {
	if (isset($_SESSION[$session_name])) {
		unset($_SESSION[$session_name]);
	}
	session_destroy();
	header("Location: " .$location. "login.php?&3");
	exit();
}
	
// Display main login content.

// Include client-side sha1 encryption script.
$pg['head'] .= '<script type="text/javascript" src="' .$location. 'includes/scripts/sha1.js"></script>';
$pg['head'] .= '<script type="text/javascript" src="' .$location. 'includes/scripts/sendpass.js"></script>';
$pg['body'] .= ' onload="document.login.user.focus();"';

$pg['content'] .= "\n<div class=\"box box-float-small\">
<div class=\"box-header\">Login to Grape</div>
<div class=\"box-content\">";
$url['msg'] = get_short_url("1");
// Display messages (if any)
if ($url['msg'] == 1) {
	$pg['content'] .= "<p>You must be logged in to access the feature you requested.</p>";
}
if ($url['msg'] == 2) {
	$pg['content'] .= "<p>Invalid username or password.</p>";
}
if ($url['msg'] == 3) {
	$pg['content'] .= "<p>You have been logged out.</p>";
}
$pg['content'] .= "
<form action=\"?login\" name=\"login\" method=\"post\">
<table cellspacing=\"0\">
<tr>
	<td width=\"35%\">Username</td>
	<td><input type=\"text\" name=\"user\" /></td>
</tr>
<tr>
	<td>Password</td>
	<td><input type=\"password\" name=\"pass\" id=\"pass\" /></td>
</tr>
<tr>
	<td><input type=\"hidden\" name=\"pass_enc\" value=\"\" id=\"pass_enc\" /></td>
	<td><input type=\"submit\" value=\"Login\" onclick=\"sendpass('pass', 'pass_enc');\" /></td>
</tr>";

$alt = 1;
$pg['content'] .= "\n</table>\n</form>\n</div>\n</div>";
$pg['content'] .= "\n<div class=\"clear\"></div>";

require_once($template_location);
?>