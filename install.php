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
$pg['content'] .= "<div class=\"location\">
<b>Admin</b>
<span>&gt;</span>
<span><a href=\"?\">Install</a></span>
</div>";

// Display alternative content
$url['step'] = get_short_url("0");
$url['confirm'] = get_short_url("1");
if ($url['step'] == "1") {
	
	$pg['content'] .= "

<img src=\"includes/themes/default/logo-small.png\" alt=\"Grapefruit\" class=\"installer logo\" />

<div class=\"installer s2\">Install Grape

<p>Grapefruit is licensed under the GNU General Public License v2.</p>

<textarea id=\"gpl\">";
	$file = "includes/license.txt";
	$handle = fopen($file, 'r');
	$contents = fread($handle, (filesize($file) * 2));
	fclose($handle);
	$pg['content'] .= $contents;
	$pg['content'] .= "</textarea>

<div class=\"actionpane\">
	<input type=\"button\" value=\"Previous Step\" onclick=\"document.location = '?0';\">
	<input type=\"button\" value=\"Agree (Continue)\" onclick=\"document.location = '?2';\">
</div>
</div>
";

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
	//
	// NEW - check to see if there is an extensions/extensions.php,
	// and if not, check to see if there's an extensions/extensions.empty.php
	if (is_writable($location. "includes/config.php") && is_writable($location. "includes/") && is_writable($location. "extensions/extensions.php")) {
		if (file_exists($location. "extensions/extensions.empty.php")) {
			if (copy($location. "extensions/extensions.php", $location. "extensions/extensions.empty.php")) {
				$writable = "<img src=\"images/yes.png\" alt=\"\" /> Yes";
			}
			else {
				$writable = "<img src=\"images/no.png\" alt=\"\" /> No";
				$meet_req = 0;
			}
		}
		else {
			$writable = "<img src=\"images/yes.png\" alt=\"\" /> Yes";
		}
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

	
	$pg['content'] .= "
<img src=\"includes/themes/default/logo-small.png\" alt=\"Grapefruit\" class=\"installer logo\" />

<div class=\"installer s3\">Install Grape
<p>
Grapefruit requires certain versions of PHP, MySQL, and write permissions are recommended.
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
		$pg['content'] .= "<p>Your server does not meet the minimum requirements to run Grapefruit.</p>

		<p>If you can't change file/folder write permissions for Grapefruit, you may eventually need to manually edit the <code>/includes/config.php</code> file.</p>";
	} 
	
		$pg['content'] .= "
<div class=\"actionpane\">
	<input type=\"button\" value=\"Previous Step\" onclick=\"document.location = '?1';\">
	<input type=\"button\" value=\"Continue"
		. ($meet_req == 0 ? " Anyways" : "")
		. "\" onclick=\"document.location = '?3';\">
</div>

</div>
";

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

	$pg['content'] .= "

<img src=\"includes/themes/default/logo-small.png\" class=\"installer logo\" alt=\"\" />

<div class=\"installer s4\">Install Grapefruit

<form method=\"post\" action=\"?4\">
	<p>Setting up an administrator account and database is required in order for a successful installation.
	If write permissions are an issue with this server, you may need to manually edit the <code>/includes/config.php</code> file with database settings, then refresh this page.</p>

	<section>
		<h2>Admin Setup</h2>
		
		<div class=\"full\">
			<label for=\"admin_user\">Admin Username</label><br />
			<input type=\"text\" name=\"admin_user\" />
		</div>
		
		<div class=\"half\">
			<label for=\"admin_pass\">Admin Password</label><br />
			<input type=\"password\" name=\"admin_pass\" id=\"pass\" /><input type=\"hidden\" name=\"admin_pass_enc\" id=\"pass_enc\" />
		</div>

		<div class=\"half\">
			<label for=\"admin_pass2\">Admin Password (Repeat)</label><br />
			<input type=\"password\" name=\"admin_pass2\" id=\"pass2\" /><input type=\"hidden\" name=\"admin_pass2_enc\" id=\"pass2_enc\" />
		</div>
	</section>

	<section>
		<h2>Database Setup</h2>
		
		<div class=\"full\">
			<label for=\"db_host\">Database Host</label><br />
			<input type=\"text\" name=\"db_host\" value=\"" .$db['host']. "\" />
		</div>

		<div class=\"half\">
			<label for=\"db_name\">Database Name</label><br />
			<input type=\"text\" name=\"db_name\" value=\"" .$db['name']. "\"  />
		</div>

		<div class=\"half\">
			<label for=\"db_user\">Database Username</label><br />
			<input type=\"text\" name=\"db_user\" value=\"" .$db['user']. "\"  />
		</div>

		<div class=\"half\">
			<label for=\"db_pass\">Database Password</label><br />
			<input type=\"text\" name=\"db_pass\" value=\"" .$db['pass']. "\"  />
		</div>

		<div class=\"half\">
			<label for=\"db_prefix\">Table Prefix</label><br />
			<input type=\"text\" name=\"db_prefix\" value=\"" .$db['prefix']. "\"  />
		</div>
	</section>

	<section>
		<h2>Configurations</h2>
		
		<div class=\"full\">
			<label for=\"config_display_protect\">Stats Display Permissions</label><br />

			<input type=\"radio\" name=\"config_display_protect\" value=\"0\" />
			Viewable to anyone.<br />
			
			<input type=\"radio\" name=\"config_display_protect\" value=\"0\" />
			Login required to view.
		</div>

		<div class=\"full\">
			<label for=\"config_location\">Where are the Grapefruit files?</label><br />
			<input type=\"text\" name=\"config_location\" value=\"" .$grape_loc. "\"  />
		</div>

		<div class=\"full\">
			<label for=\"config_site\">What site is Grapefruit watching?</label><br />
			<input type=\"text\" name=\"config_site\" value=\"" . substr(str_replace("http://", "", str_replace("www.", "", $grape_loc)), 0, strpos(str_replace("http://", "", str_replace("www.", "", $grape_loc)), "/")) . "\"  />
		</div>

		<div class=\"full\">
			<label for=\"config_timezone\">Timezone</label><br />
			<select name=\"config_timezone\" id=\"timezone\" onchange=\"fetchTimezone();\">
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
			</select> <span id=\"timezone_display\">Current Time: </span>
			
		</div>


		<div class=\"actionpane\">
			<input type=\"button\" value=\"Previous Step\" onclick=\"document.location = '?2';\">
			<input type=\"submit\" value=\"Continue\" onclick=\"sendpass('pass', 'pass_enc'); sendpass('pass2', 'pass2_enc');\">
		</div>
	</section>
</form>
</div>
";
	
	require_once($template_location);
	exit();
}

if ($url['step'] == "4") {
	$pg['content'] .= "
<img src=\"includes/themes/default/logo-small.png\" class=\"installer logo\" alt=\"Grapefruit\" />

<div class=\"installer s5\">
<h2>Install Grape</h2>";

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
		writeconfig("cms['site']", $_POST['config_site'], $location. "includes/");
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
	
	require_once("./extensions/UserSpy/info.php");
	UserSpyInstall();
	require_once("./extensions/CityFinder/info.php");
	CityFinderInstall();
	require_once("./extensions/GrapeOS/info.php");
	GrapeOSInstall();
	require_once("./extensions/GrapePages/info.php");
	GrapePagesInstall();
	require_once("./extensions/GrapeReferrers/info.php");
	GrapeReferrersInstall();


	$pg['content'] .= "<p><img src=\"images/yes.png\" alt=\"\" /> Grapefruit has finished installed. Place the following in all pages you want to track statistics for:
	</p>
<div class=\"code-box\"><code>&lt;script src=\"" .$cms['location']. "?js\" type=\"text/javascript\"&gt;
&lt;/script&gt;<br /></code></div>
<p>If you misplace this code, don't worry. You may find it again in Grapefruit admin panel as well as in the Grapefruit documentation.
If for some reason Grapefruit is not tracking your statistics, post on <a href=\"http://www.quate.net/board/\" target=\"_blank\">Quate's forums</a> and someone should be able to help you.</p>";

	// Create 'installed' file
	if (!$file_open = fopen($location. "includes/installed", "a")) {
		$pg['content'] .= "<p><img src=\"images/no.png\" alt=\"\" /> Failed to create the <code>installed</code> file that tells Grapefruit that it has been installed.
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
$pg['content'] .= "<div class=\"installer s1\">
<h2>Install Grapefruit</h2>

<img src=\"includes/themes/default/logo-small.png\" alt=\"Grapefruit\" />

<p>Grapefruit is a self-hosted website analytics program. Grapefruit's main goal is to provide accurate visitor statistics.</p>

<p>Grapefruit's secondary goal is to provide these statistics in a visually appealing manner, and to provide developers with an extensible environment.</p>

<p>Grapefruit is a fork of <a href=\"http://www.quate.net/grape\">Grape</a>, meant to continue and expand on the original project.</p>

<div class=\"actionpane\">
<input type=\"button\" value=\"Start the installer\" onclick=\"document.location = '?1'; \">
</div>

";

require_once($template_location);
?>