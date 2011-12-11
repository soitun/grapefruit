<?php
// Lock file if it is being accessed directly.
$temp = explode("/", $_SERVER['SCRIPT_FILENAME']);
$count = count($temp) - 3;
if ($temp[$count] == "themes") {
	$location = "../../../";
	require_once($location. "includes/functions.php");
	
	lock_file();
}

if (isset($pg['title'])) {
	$pg['title'] = $pg['title'] . " | Grapefruit";
}
?>

<!DOCTYPE html>
<html>
<head>
	<title><?php echo $pg['title']; ?></title>

	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />

	<link href='http://fonts.googleapis.com/css?family=PT+Sans:400,700,400italic,700italic|PT+Serif+Caption:400,400italic' rel='stylesheet' type='text/css'>

	<link rel="stylesheet/less" href="<?php echo $location; ?>includes/themes/default/style.less" type="text/css" />

	<link rel="shortcut icon" type="image/x-icon" href="<?php echo $location; ?>includes/themes/default/images/favicon.png" />

	<script language="javascript" type="text/javascript" src="<?php echo $location; ?>includes/themes/default/js/jquery.js"></script>

	<script language="javascript" type="text/javascript" src="<?php echo $location; ?>includes/themes/default/js/flot/jquery.flot.js"></script>
	<script language="javascript" type="text/javascript" src="<?php echo $location; ?>includes/themes/default/js/flot/jquery.flot.stack.js"></script>
	<script language="javascript" type="text/javascript" src="<?php echo $location; ?>includes/themes/default/js/flot/jquery.flot.pie.js"></script>

	<script type="text/javascript" src="<?php echo $location; ?>includes/themes/default/js/less.js"></script>

	<?php echo $pg['head']; ?>
</head>

<body<?php echo $pg['body']; ?>>

	<div id="container">
	<header>
		<?php /*<h1><a href="<?php echo $location; ?>./">Grapefruit <span>analytics</span></a></h1>*/ ?>
		<a href="<?php echo $location; ?>./"><img src="<?php echo $location; ?>./includes/themes/default/logo-small.png" alt="Grapefruit Analytics" /></a>
	</header>

	<section id="notice">
		<?php echo $pg['notice']; ?>
	</section>

	<section id="content">
		<?php echo $pg['content']; ?>
	</section>

	</div>
	<?php if ($cms['site'] == "127.0.0.1/grapefruit") {
		
		echo "\n" . '<script src="http://127.0.0.1/grapefruit/?js" type="text/javascript"> </script>';
	}?>
</body>
</html>