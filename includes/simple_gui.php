<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html lang="en-US" xml:lang="en-US" xmlns="http://www.w3.org/1999/xhtml">
<head>
	<title><?php echo $simple_gui['title']; ?></title>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<style type="text/css">
body {
color : #2e3436;
background : #ECE2EC;
font-family : Arial;
font-size : 12px;
margin : 0px;
padding : 0px;
}
.display {
width : 100%;
background : #ffffff;
border-top : 1px solid #E3D4E3;
border-bottom : 1px solid #E3D4E3;
padding : 12px 100px;
text-align : left;
line-height : 1.25em;
margin-top: 20px;
}
.header {
color : #75507B;
font-family : Verdana;
font-weight : bold;
}
.content {
width : 650px;
}
a:link, a:visited, a:hover {
text-decoration : underline;
color : #663366;
}
</style>
</head>

<body>

<br>
<center>
<div class="display">
<img src="<?php echo $location; ?>includes/themes/default/logo-small.png" hspace="4" vspace="4" alt="" />
<br>
<div class="header"><?php echo $simple_gui['header']; ?></div>
<div class="content">
<?php echo $simple_gui['content']; ?>
</div>
</div>
</center>

</body>
</html>