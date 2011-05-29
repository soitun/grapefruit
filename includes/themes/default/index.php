<?php
// Lock file if it is being accessed directly.
$temp = explode("/", $_SERVER['SCRIPT_FILENAME']);
$count = count($temp) - 3;
if ($temp[$count] == "themes") {
	$location = "../../../";
	require_once($location. "includes/functions.php");
	
	lock_file();
}

if ($pg['notice'] != "") {
	$pg['notice'] = "<div class=\"notice\">" .$pg['notice']. "</div>";
}
if (isset($pg['title'])) {
	$pg['title'] = "Grape - ".$pg['title'];
}
?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html lang="en-US" xml:lang="en-US" xmlns="http://www.w3.org/1999/xhtml">
<head>
    <title><?php echo $pg['title']; ?></title>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<link rel="stylesheet" href="<?php echo $location; ?>includes/themes/default/style.css" type="text/css" />
<link rel="shortcut icon" type="image/x-icon" href="<?php echo $location; ?>includes/themes/default/images/favicon.png" />
<script language="javascript" type="text/javascript" src="<?php echo $location; ?>includes/themes/default/js/flot/jquery.js"></script>
<script language="javascript" type="text/javascript" src="<?php echo $location; ?>includes/themes/default/js/flot/jquery.flot.js"></script>
<script language="javascript" type="text/javascript" src="<?php echo $location; ?>includes/themes/default/js/flot/jquery.flot.stack.js"></script>
<script language="javascript" type="text/javascript" src="<?php echo $location; ?>includes/themes/default/js/masonry.js"></script>

<script type="text/javascript">
$('#container').masonry({
  itemSelector: '.box',
  columnWidth: 286
})
</script>

<?php echo $pg['head']; ?>
</head>

<body<?php echo $pg['body']; ?>>

<div id="container">
<header>
	<div class="grid9 first"><a href="<?php echo $location; ?>./"><?php echo $cms['site']; ?> <span>analytics</span></a></div>

	<div class="grid3"><?php if (is_admin()) { echo "<a href=\"" .$location. "panel.php\">Admin Panel</a>"; } else { echo "<a href=\"" .$location. "login.php\">Login</a>"; } ?></div>
</header>

<?php echo $pg['notice']; ?>

<?php echo $pg['content']; ?>

<footer>
Designed and modified by <a href="http://dkuntz2.com">Don Kuntz</a>.
Originally developed by <a href="http://www.quate.net/">Quate.net</a>.
</footer>
</div>
</body>
</html>