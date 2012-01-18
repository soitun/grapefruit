# City Finder

City Finder is a simple Grape Analytics plugin for analyzing where users visiting your site
are coming from.

## Installation Instructions
1.	Copy the CityFinder folder into the grape 
	Extension Folder	
2.	Go to the Admin and activate CityFinder just
	like any other extension.
3.  You're probably not using CleanerGrape, so delete the 
	current `info.php` file and rename `nonCleanerGrape.php`
	to `info.php`
	

CityFinder integrates with any theme, but probably
integrates better when told how to fit in.

### NOTE:

You may need to have curl enabled on your server if you don't and activate the CityFinder extension you'll just get errors;
	
If you do get the errors, you have to manually uninstall
CityFinder, to do so, open the extensions.php file in the
extensions folder and remove the line that says
`$enabled_ext[] = "CityFinder";`
