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

if (isset($_POST['pass']) && $_POST['pass'] == $_POST['pass2']) {
	$pass = sha1($_POST['pass']);
	$query = "UPDATE " .SQL_PREFIX. "grapeaccount SET grapeaccount_password = '" .sql_protect($_POST['pass']). "' WHERE grapeaccount_id = '" .$_SESSION[$session_name]. "'";
	$result = mysql_query($query) or die(report_error("E_DB", mysql_error(), __LINE__, __FILE__));
}

// User authentication for this page!!

$pg['title'] .= "Admin Panel";
$pg['head'] .= "";
$pg['body'] .= "";
$pg['notice'] .= "";

$pg['content'] .= "

	<div class=\"main\">
		<section>
			<h2>Admin</h2>
			<ul>
				<li><a href=\"#\" onClick=\"pass\">Change Password</a></li>
				<li><a href=\"#\" onClick=\"code\">Get Code</a></li>
			</ul>
		</section>
	</div>

	<div class=\"left\">
		<img src=\"$location/includes/themes/default/logo-small.png\" alt=\"Grapefruit\" />

		<div id=\"stuff\">
		". 
			(isset($_POST['pass']) && $_POST['pass'] == $_POST['pass2'] ? "Password Changed" : "")
		. "&nbsp;</div>
	</div>

	<script>
		function pass() {
			$('#stuff').html(
				\"
				<form method=\"post\" action=\"?pass\">
					<label for=\"pass\">Password</label><br />
					<input type=\"password\" name=\"pass\" /><br />

					<label for=\"pass2\">Confirm Password</label><br />
					<input type=\"password\" name=\"pass2\" />

				</form>
				\"
			)
		}

		function code() {
			$('#stuff').html ()
		}
	</script>
";

require_once($template_location);
?>