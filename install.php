<?php
$location = "./";
require_once($location. "includes/functions.php");

// Check to see if already installed
if (is_file("includes/installed")) {
	$simple_gui['title'] = "Grape Already Installed";
	$simple_gui['header'] .= $simple_gui['title'];
	$simple_gui['content'] .= "Grape has already been installed.
To reinstall, delete the lock file <code>/includes/installed</code> and try again.
You may need to clear the database to preform a fresh Grape install.";
	require("includes/simple_gui.php");
	exit();
}

$pg['title'] .= "Install";
$pg['head'] .= "";
$pg['body'] .= "";
$pg['notice'] .= "";
$pg['content'] .= "<div class=\"box main-box\">
<b>Admin</b>
<span>&gt;</span>
<span><a href=\"?\">Install</a></span>
</div>";

// Display alternative content
$url['step'] = get_short_url("0");
$url['confirm'] = get_short_url("1");
if ($url['step'] == "1") {
	
	$pg['content'] .= "<div class=\"box box-full\">
<div class=\"box-header\">Install Grape</div>
<div class=\"box-content\">
<img src=\"includes/themes/default/images/logo.png\" vspace=\"5\" hspace=\"5\" alt=\"\" />
<p>
Grape Statistics is available as open source software.
That is to say it is free to be used, modified, and redistributed as long as it stays within the scope of the <a href=\"http://www.gnu.org/licenses/gpl-2.0.txt\">GNU General Public License v2</a>, replicated below.
</p>
<textarea style=\"overflow:auto;height:300px;width:99%;margin:2px;\">";
	$file = "includes/license.txt";
	$handle = fopen($file, 'r');
	$contents = fread($handle, (filesize($file) * 2));
	fclose($handle);
	$pg['content'] .= $contents;
	$pg['content'] .= "</textarea>
<input type=\"button\" value=\"Agree (Next)\" onclick=\"document.location = '?2';\">
<input type=\"button\" value=\"Previous Step\" onclick=\"document.location = '?0';\">
</div>
</div>";

	require_once($template_location);
	exit();
}
if ($url['step'] == "2") {
	
	$meet_req = 1;
	$meet_maybe = 0;
	
	// Check PHP Version.
	$php_version = phpversion();
	$php_version = explode("-", $php_version);
	$temp = explode(".", $php_version[0]);
	if ($temp[0] > 4) {
		$php_version = "<img src=\"images/yes.png\" alt=\"\" /> " .$php_version[0];
	} else if ($temp[0] == 4) {
		$php_version = "<img src=\"images/maybe.png\" alt=\"\" /> " .$php_version[0];
		$meet_maybe = 1;
	} else {
		$php_version = "<img src=\"images/no.png\" alt=\"\" /> " .$php_version[0];
		$meet_req = 0;
	}
	
	// Check MySQL Version.
	if (extension_loaded('mysql')) {
		$mysql_version = mysql_get_client_info();
		$mysql_version = explode("-", $mysql_version);
		$temp = explode(".", $mysql_version[0]);
		if ($temp[0] > 3) {
			$mysql_version = "<img src=\"images/yes.png\" alt=\"\" /> " .$mysql_version[0];
		} else if ($temp[0] == 3) {
			$mysql_version = "<img src=\"images/maybe.png\" alt=\"\" /> " .$mysql_version[0];
			$meet_maybe = 1;
		} else {
			$mysql_version = "<img src=\"images/no.png\" alt=\"\" /> " .$mysql_version[0];
			$meet_req = 0;
		}
	} else {
		$mysql_version = "<img src=\"images/no.png\" alt=\"\" /> Not Installed";
		$meet_req = 0;
	}
	
	// Check if the following are writable:
	// includes/ - To add the 'installed' lock file.
	// includes/config.php - To write settings to.
	// extensions/extensions.php - To enable/disable extensions.
	if (is_writable($location. "includes/config.php") && is_writable($location. "includes/") && is_writable($location. "extensions/extensions.php")) {
		$writable = "<img src=\"images/yes.png\" alt=\"\" /> Yes";
	} else {
		$writable = "<img src=\"images/no.png\" alt=\"\" /> No";
		$meet_req = 0;
	}
	
	// Check to see that Register Globals is off.
	$rg = (bool) ini_get('register_globals');
	if ($rg === false) {
		$register_globals = "<img src=\"images/yes.png\" alt=\"\" /> Off";
	} else {
		$register_globals = "<img src=\"images/maybe.png\" alt=\"\" /> On";
		$meet_maybe = 1;
	}

	
	$pg['content'] .= "<div class=\"box box-full\">
<div class=\"box-header\">Install Grape</div>
<div class=\"box-content\">
<img src=\"includes/themes/default/images/logo.png\" vspace=\"5\" hspace=\"5\" alt=\"\" />
<p>
This application requires certain versions of PHP, MySQL, and write permissions are recommended.
PHP's register globals setting should be turned off for security issues.
</p>
<table cellspacing=\"0\">
<tr>
	<td width=\"20%\">PHP</td>
	<td>" .$php_version. "</td>
</tr>
<tr>
	<td>MySQL</td>
	<td>" .$mysql_version. "</td>
</tr>
<tr>
	<td>Write Permissions</td>
	<td>" .$writable. "</td>
</tr>
<tr>
	<td>Register Globals</td>
	<td>" .$register_globals. "</td>
</tr>
</table>";
	if ($meet_req == 0) {
		$pg['content'] .= "<p>This server does not meet the minimum requirements to run Grape.
If you can't change file/folder write permissions for Grape, you may eventually need to manually edit the <code>/includes/config.php</code> file.</p>
<input type=\"button\" value=\"Continue Anyway\" onclick=\"document.location = '?3';\">";
	} else {
		if ($meet_maybe == 1) {
			// Check again if Register Globals, because if it is, a message should be displayed regarding it.
			if ($rg === true) {
				$pg['content'] .= "<p>PHP's Register Globals are turned on but should be turned off in your PHP ini settings.
Leaving this on creates a security risk for Grape and many other PHP programs.
If editing your PHP settings is not an option, and if you have Apache, you can create a file called <i>.htaccess</i>, and within it place the following: <i><code>php_flag register_globals 0</code></i></p>";
			}
			$pg['content'] .= "<p>Grape has been tested very little and may not work properly with this server's configuration.</p>";
			
		}
	$pg['content'] .= "
<input type=\"button\" value=\"Continue\" onclick=\"document.location = '?3';\">";
	}
$pg['content'] .= "<input type=\"button\" value=\"Previous Step\" onclick=\"document.location = '?1';\">
</div>
</div>";

	require_once($template_location);
	exit();
}
if ($url['step'] == "3") {
	// Include timezone ajax script and client-side sha1 encryption.
	$pg['head'] .= '<script type="text/javascript" src="' .$location. 'includes/scripts/timezone.js"></script>
<script type="text/javascript" src="' .$location. 'includes/scripts/sha1.js"></script>
<script type="text/javascript" src="' .$location. 'includes/scripts/sendpass.js"></script>';

	// Get path of current directory from the domain name's perspective rather than the actual drive location.
	// This could be wrong, which is why it is a config setting so that the user may edit it.
	// (In order to work properly, the javascript needs the full location of the record.php page.)
	$file_loc = $_SERVER['PHP_SELF'];
	$parts = explode('/', $file_loc);
	$file_loc = str_replace($parts[count($parts) - 1], "", $file_loc);
	$grape_loc = "http://" .$_SERVER['HTTP_HOST']. "" .$file_loc;

	$pg['content'] .= "<div class=\"box box-full\">
<div class=\"box-header\">Install Grape</div>
<div class=\"box-content\">
<img src=\"includes/themes/default/images/logo.png\" vspace=\"5\" hspace=\"5\" alt=\"\" />
<form method=\"post\" action=\"?4\">
<p>Setting up an administrator account and database is required in order for a successful installation.
If write permissions are an issue with this server, you may need to manually edit the <code>/includes/config.php</code> file with database settings, then refresh this page.</p>
<table cellspacing=\"0\">
<tr>
	<td width=\"30%\"><div class=\"header\">Admin Setup</div></td>
	<td></td>
</tr>
<tr>
	<td>Admin Username</td>
	<td><input type=\"text\" name=\"admin_user\" style=\"width:200px;\"></td>
</tr>
<tr>
	<td>Admin Password</td>
	<td><input type=\"password\" name=\"admin_pass\" style=\"width:200px;\" id=\"pass\"><input type=\"hidden\" name=\"admin_pass_enc\" id=\"pass_enc\"></td>
</tr>
<tr>
	<td>Admin Password (Repeat)</td>
	<td><input type=\"password\" name=\"admin_pass2\" style=\"width:200px;\" id=\"pass2\"><input type=\"hidden\" name=\"admin_pass2_enc\" id=\"pass2_enc\"></td>
</tr>
<tr>
	<td><div class=\"header\">Database Setup</div></td>
	<td></td>
</tr>
<tr>
	<td>Database Host</td>
	<td><input type=\"text\" name=\"db_host\" value=\"" .$db['host']. "\" style=\"width:200px;\"></td>
</tr>
<tr>
	<td>Database Name</td>
	<td><input type=\"text\" name=\"db_name\" value=\"" .$db['name']. "\" style=\"width:200px;\"></td>
</tr>
<tr>
	<td>Database Username</td>
	<td><input type=\"text\" name=\"db_user\" value=\"" .$db['user']. "\" style=\"width:200px;\"></td>
</tr>
<tr>
	<td>Database Password</td>
	<td><input type=\"text\" name=\"db_pass\" value=\"" .$db['pass']. "\" style=\"width:200px;\"></td>
</tr>
<tr>
	<td>Table Prefix</td>
	<td><input type=\"text\" name=\"db_prefix\" value=\"" .$db['prefix']. "\" style=\"width:200px;\"></td>
</tr>
<tr>
	<td><div class=\"header\">Configurations</div></td>
	<td></td>
</tr>
<tr>
	<td>Stats Display Permissions</td>
	<td><select name=\"config_display_protect\" style=\"width:200px;\">
	<option value=\"0\">Viewable to anyone
	<option value=\"1\">Login required to view
</select></td>
</tr>
<tr>
	<td>Grape Location</td>
	<td><input type=\"text\" name=\"config_location\" value=\"" .$grape_loc. "\" style=\"width:200px;\"></td>
</tr>
<tr>
	<td>Timezone</td>
	<td><select name=\"config_timezone\" id=\"timezone\" onchange=\"fetchTimezone();\" style=\"width:250px;\">
<option value=\"\">-Select a timezone-";
	$file = "includes/timezones.txt";
	$handle = fopen($file, 'r');
	$contents = fread($handle, (filesize($file) * 2));
	fclose($handle);
	$split_contents = explode("\n", $contents);
	foreach ($split_contents as $line) {
		$pg['content'] .= "<option value=\"" .$line. "\">" .$line. "\n";
	}
	$pg['content'] .= "
</select> <span id=\"timezone_display\" style=\"font-size:11px;\"></span></td>
</tr>
<tr>
	<td>Grape Theme</td>
	<td><select style=\"width:200px;\" name=\"config_theme\">\n";
chdir("./includes/themes/"); // Move (cd) to another directory, relative to the directory of this file.
$dirpath = getcwd();
$dh = opendir($dirpath);
while (false !== ($file = readdir($dh))) {
	if (is_dir("$dirpath/$file")) {
		// Continue, but ignore these files (in if case below)
		if ($file == "." || $file == ".." || $file == ".AppleDouble") {
		} else {
			// Have selected whatever is currently in the config file (should be "default" by default).
			$selected = "";
			if ($file == $cms['theme']) {
				$selected = " selected=\"selected\"";
			}

			$pg['content'] .= "<option value=\"" .$file. "\"" .$selected. ">" .ucfirst($file). "\n";
		}
	}
}
chdir("../../");
$pg['content'] .= "</select></td>
</tr>
<tr>
	<td></td>
	<td><input type=\"submit\" value=\"Continue\" onclick=\"sendpass('pass', 'pass_enc'); sendpass('pass2', 'pass2_enc');\">
<input type=\"button\" value=\"Previous Step\" onclick=\"document.location = '?2';\"></td>
</tr>
</table>
</form>
</div>
</div>";
	
	require_once($template_location);
	exit();
}

if ($url['step'] == "4") {
	$pg['content'] .= "<div class=\"box box-full\">
<div class=\"box-header\">Install Grape</div>
<div class=\"box-content\">
<img src=\"includes/themes/default/images/logo.png\" vspace=\"5\" hspace=\"5\" alt=\"\" />";

	// DEBUG: Display _POST variables:
	/*
	while (list ($key, $val) = each ($_POST)) {
		$pg['content'] .= "<br />" .$key. ":" .$val. "\n";
	}
	*/
	
	$pg['content'] .= "<p>Installing Grape Statistics:</p>";
	
	// Write to config
	if (is_writable($location. "includes/config.php")) {
		writeconfig("db['host']", $_POST['db_host'], $location. "includes/");
		writeconfig("db['name']", $_POST['db_name'], $location. "includes/");
		writeconfig("db['prefix']", $_POST['db_prefix'], $location. "includes/");
		writeconfig("db['user']", $_POST['db_user'], $location. "includes/");
		writeconfig("db['pass']", $_POST['db_pass'], $location. "includes/");
		writeconfig("cms['timezone']", $_POST['config_timezone'], $location. "includes/");
		writeconfig("cms['display_protect']", $_POST['config_display_protect'], $location. "includes/");
		writeconfig("cms['location']", $_POST['config_location'], $location. "includes/");
		writeconfig("cms['theme']", $_POST['config_theme'], $location. "includes/");
		$pg['content'] .= "<p><img src=\"images/yes.png\" alt=\"\" /> Configurations have been saved.</p>";
	} else {
		// Display content for if write permissions are disabled for /includes/config.php
		
		// Sort between the confirmation step (to allow people to continue the installation), or the main informative step that stops the user from finishin the install.
		if ($url['confirm'] != 1) {
			$pg['content'] .= "<p><img src=\"images/no.png\" alt=\"\" /> Configurations failed to save.
You will need to edit the <code>/includes/config.php</code> configurations file and replace its contents with the following code from the details you provided:<br />
<div class=\"code-box\"><code>
&lt;?php<br />
\$db['host'] = \"" .$_POST['db_host']. "\"; // Usually 'localhost'<br />
\$db['name'] = \"" .$_POST['db_name']. "\";<br />
\$db['prefix'] = \"" .$_POST['db_prefix']. "\"; // Usually nothing unless the database is being shared.<br />
\$db['user'] = \"" .$_POST['db_user']. "\";<br />
\$db['pass'] = \"" .$_POST['db_pass']. "\";<br />

\$cms['timezone'] = \"" .$_POST['config_timezone']. "\";<br />
\$cms['display_protect'] = \"" .$_POST['config_display_protect']. "\";<br />
\$cms['location'] = \"" .$_POST['config_location']. "\";<br />
\$cms['theme'] = \"" .$_POST['config_theme']. "\";<br />
?&gt;<br />
</code></div></p>
<p>If you have finished the above step, <a href=\"?4&amp;1\">click here</a> to continue.</p>
<input type=\"button\" value=\"Previous Step\" onclick=\"document.location = '?3';\">";
			$pg['content'] .= "</div></div>";
			require_once($template_location);
			exit();
		} else {
			// Don't display anything. Let the user continue the installation.
			//$pg['content'] .= "<p></p>";
		}
	}
	
	// Make sure the admin password is already in sha1 form. (This will happen if the client does not have javascript enabled.)
	/*
	if (strlen($_POST['admin_pass']) != 40) {
		// sha1 hash the password.
		$_POST['admin_pass'] = sha1($_POST['admin_pass']);
	}
	*/
	// Check if the password is sha1 hashed.
	if ($_POST['admin_pass_enc'] == "") {
		$_POST['admin_pass'] = sha1($_POST['admin_pass']);
	} else {
		$_POST['admin_pass'] = $_POST['admin_pass_enc'];
	}
	if ($_POST['admin_pass2_enc'] == "") {
		$_POST['admin_pass2'] = sha1($_POST['admin_pass2']);
	} else {
		$_POST['admin_pass2'] = $_POST['admin_pass2_enc'];
	}
	
	// Make sure the admin passwords given are the same.
	if ($_POST['admin_pass'] != $_POST['admin_pass2']) {
		$pg['content'] .= "<p><img src=\"images/no.png\" alt=\"\" /> The admin passwords provided do not match.</p>
<input type=\"button\" value=\"Previous Step\" onclick=\"document.location = '?3';\">";
		$pg['content'] .= "</div></div>";
		require_once($template_location);
		exit();
	}
	
	if ($_POST['config_timezone'] == "") {
		$pg['content'] .= "<p><img src=\"images/no.png\" alt=\"\" /> A timezone was not specified.</p>
<input type=\"button\" value=\"Previous Step\" onclick=\"document.location = '?3';\">";
		$pg['content'] .= "</div></div>";
		require_once($template_location);
		exit();
	}	
	
	// Establish database connection.
	if ($url['confirm'] != 1) {
		// Load values inputted by the user.
		$db['host'] = $_POST['db_host'];
		$db['name'] = $_POST['db_name'];
		$db['prefix'] = $_POST['db_prefix'];
		$db['user'] = $_POST['db_user'];
		$db['pass'] = $_POST['db_pass'];
		$cms['location'] = $_POST['config_location'];
	} else {
		// Load the config file.
		require($location. "includes/config.php");
	}
	if (dbconnect()) {
		$pg['content'] .= "<p><img src=\"images/yes.png\" alt=\"\" /> Database connection was established.</p>";
	}
	
	// Create database tables.
	$bypass_setup_lock = 1;
	require($location. "includes/setup.php");
	// Create admin user.
	$query = "INSERT INTO " .SQL_PREFIX. "grapeaccount(grapeaccount_name, grapeaccount_password)
VALUES('" .sql_protect($_POST['admin_user']). "', '" .sql_protect($_POST['admin_pass']). "')";
	$result = mysql_query($query) or die(report_error("E_DB", mysql_error(), __LINE__, __FILE__));
	
	$pg['content'] .= "<p><img src=\"images/yes.png\" alt=\"\" /> Database tables were created.
Administrator account was successfully created.</p>";

	$pg['content'] .= "<p><img src=\"images/yes.png\" alt=\"\" /> Grape has finished installed. Place the following in all pages you want to track statistics for:
	</p>
<div class=\"code-box\"><code>&lt;script src=\"" .$cms['location']. "?js\" type=\"text/javascript\"&gt;
&lt;/script&gt;<br /></code></div>
<p>If you misplace this code, don't worry. You may find it again in Grape admin panel as well as in the Grape documentation.
If for some reason Grape is not tracking your statistics, post on <a href=\"http://www.quate.net/board/\" target=\"_blank\">Quate's forums</a> and someone should be able to help you.</p>";

	// Create 'installed' file
	if (!$file_open = fopen($location. "includes/installed", "a")) {
		$pg['content'] .= "<p><img src=\"images/no.png\" alt=\"\" /> Failed to create the <code>installed</code> file that tells Grape that it has been installed.
While Grape has been successfully installed, this file still needs to be created.
Create a file called <code>installed</code> within the <code>/includes/</code> directory.</p>";
		// (Script is not to exit, but is to contine.)
	} else {
		fclose($file_open); // Close file.
		//$pg['content'] .= "<p><img src=\"images/yes.png\" alt=\"\" /> Created a file called 'installed' for enhanced security authorization.</p>";
	}

	$pg['content'] .= "<input type=\"button\" value=\"Finish\" onclick=\"document.location = './';\">
<input type=\"button\" value=\"Previous Step\" onclick=\"document.location = '?3';\">
</div></div>";
	require_once($template_location);
	exit();
}

// Display initial install step.
$pg['content'] .= "<div class=\"box box-full\">
<div class=\"box-header\">Install Grape</div>
<div class=\"box-content\">
<img src=\"includes/themes/default/images/logo-large.png\" vspace=\"5\" hspace=\"5\" alt=\"\" />
<p>Grape Web Statistics is a web statistics recording and monitoring program.
The goal of this application is to provide accurate visitor statistics.
Statistics are recorded with a timestamp so they can be queried easily and accurately.
</p>
<p>The secondary goals of this application are to display statistics in an appealing manner, and to offer expandability of statistics by means of an extension system.
This means that every Grape user may choose what information to track according to their needs.
</p>
<input type=\"button\" value=\"Install\" onclick=\"document.location = '?1';\">
<a href=\"http://www.quate.net/grape\" target=\"_blank\" style=\"font-size:10px;\">(Visit the Grape Statistics website)</a>
</div>
</div>";

require_once($template_location);
?>