<?php
if ($bypass_setup_lock != 1) {
	$location = "../";
	require_once($location. "includes/functions.php");
	
	lock_file();
}

// Account.
$grapeaccount = "CREATE TABLE IF NOT EXISTS " .SQL_PREFIX. "grapeaccount (
  grapeaccount_id int(11) NOT NULL auto_increment,
  grapeaccount_name text NOT NULL default '',
  grapeaccount_password text NOT NULL default '',
  grapeaccount_theme text NOT NULL default '',
  PRIMARY KEY (grapeaccount_id)
)";

// 
$grapeaccount = "CREATE TABLE IF NOT EXISTS " .SQL_PREFIX. "grapeaccount (
  grapeaccount_id int(11) NOT NULL auto_increment,
  grapeaccount_name text NOT NULL default '',
  grapeaccount_password text NOT NULL default '',
  grapeaccount_theme text NOT NULL default '',
  PRIMARY KEY (grapeaccount_id)
)";

// Grape and extensions settings table.
$grapesetting = "CREATE TABLE IF NOT EXISTS " .SQL_PREFIX. "grapesetting (
  grapesetting_id int(11) NOT NULL auto_increment,
  grapesetting_parent text NOT NULL default '',
  grapesetting_parent_internal text NOT NULL default '',
  grapesetting_name text NOT NULL default '',
  grapesetting_name_internal text NOT NULL default '',
  grapesetting_description text NOT NULL default '',
  grapesetting_type text NOT NULL default '',
  grapesetting_options text NOT NULL default '',
  grapesetting_option_values text NOT NULL default '',
  grapesetting_default text NOT NULL default '',
  grapesetting_value text NOT NULL default '',
  grapesetting_load int(11) NOT NULL default '1',
  PRIMARY KEY (grapesetting_id)
)";

// Main statistics.
$grapestat = "CREATE TABLE IF NOT EXISTS " .SQL_PREFIX. "grapestat (
  grapestat_id int(11) NOT NULL auto_increment,
  grapestat_ip text NOT NULL default '',
  grapestat_year int(11) NOT NULL default '0',
  grapestat_month int(11) NOT NULL default '0',
  grapestat_day int(11) NOT NULL default '0',
  grapestat_hour int(11) NOT NULL default '0',
  grapestat_minute int(11) NOT NULL default '0',
  grapestat_hits int(11) NOT NULL default '0',
  grapestat_ref int(11) NOT NULL default '0',
  grapestat_page int(11) NOT NULL default '0',
  PRIMARY KEY (grapestat_id)
)";

/*
// Browser
$grapebrowser = "CREATE TABLE IF NOT EXISTS " .SQL_PREFIX. "grapebrowser (
  grapebrowser_id int(11) NOT NULL auto_increment,
  grapebrowser_browser text NOT NULL default '',
  grapebrowser_version text NOT NULL default '',
  grapebrowser_engine text NOT NULL default '',
  grapebrowser_year int(11) NOT NULL default '0',
  grapebrowser_month int(11) NOT NULL default '0',
  grapebrowser_day int(11) NOT NULL default '0',
  grapebrowser_hour int(11) NOT NULL default '0',
  grapebrowser_hits int(11) NOT NULL default '0',
  PRIMARY KEY (grapebrowser_id)
)";
*/
// Create tables
//$pg['content'] .= "Creating tables.<br>";
$result = mysql_query($grapeaccount) or die ("<b>MySQL Error</b>: " .mysql_error());
//$pg['content'] .= "> Table '" .SQL_PREFIX. "grapeaccount' created.<br>";
$result = mysql_query($grapesetting) or die ("<b>MySQL Error</b>: " .mysql_error());
//$pg['content'] .= "> Table '" .SQL_PREFIX. "grapestat' created.<br>";
$result = mysql_query($grapestat) or die ("<b>MySQL Error</b>: " .mysql_error());
//$pg['content'] .= "> Table '" .SQL_PREFIX. "grapestat' created.<br>";
?>