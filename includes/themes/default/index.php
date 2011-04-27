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
<?php echo $pg['head']; ?>
</head>

<body<?php echo $pg['body']; ?>>

<div class="container">
<div class="top">
<div class="version"><?php if (is_admin()) { echo "<a href=\"" .$location. "panel.php\">Admin Panel</a>"; } else { echo "<a href=\"" .$location. "login.php\">Login</a>"; } ?></div>
<a href="<?php echo $location; ?>./"><img src="<?php echo $location; ?>includes/themes/default/images/logo.png" border="0" alt="Grape Web Statistics" /></a>
</div>

<div class="subtop">Statistics for <b><?php echo $_SERVER['HTTP_HOST']; ?></b></div>

<?php echo $pg['notice']; ?>

<?php echo $pg['content']; ?>

<div class="footer">
<a href="http://validator.w3.org/check?uri=referer" target="_blank">
<img src="<?php echo $location; ?>includes/themes/default/images/valid-xhtml.png" alt="Valid XHTML 1.0 Traditional" title="Valid XHTML 1.0 Traditional" />
</a>
<a href="http://jigsaw.w3.org/css-validator/check?uri=referer" target="_blank">
<img src="<?php echo $location; ?>includes/themes/default/images/valid-css.png" alt="Valid CSS 2.1" title="Valid CSS 2.1" />
</a>
<br />
Developed by <a href="http://www.quate.net/" target="_blank">Quate.net</a>.
</div>
</div>
<script src="http://localhost/grape/?js" type="text/javascript"></script>
</body>
</html>