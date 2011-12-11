# Grapefruit

Grapefruit is a complete overhaul to Grape Analytics. Grapefruit takes the basis of Grape and modifies some components to work
better, and adds new features.

Grapefruit was formerly known as CleanerGrape, and references to CleanerGrape may be found in the code right now (same as references to Grape itself).

## Installer Error

I'm not quite sure why, but occasionally (well, only if you click the back button on the installer or if you hit the previous step button (I have ABSOLUTELY NO CLUE AS TO WHY)), you'll get the error:
	
	Warning Error
	require_once(../../includes/functions.php): failed to open stream: No such file or directory
	On line: 5
	In file: /medpart/www/grapefruit/includes/themes/index.php

What you need to do is open `includes/config.php` and change the value of `$cms['theme']` to `default`.

Essentially, change (probably) line 12 from `$cms['theme'] = ""` to `$cms['theme'] = "default"`. After doing this, you *should* be able to run the installer. If not, copy the master version of `includes/config.php` and use it for the installer (the installer will change the values for you). If that doesn't help, feel free to contact me at [grapefruit@dkuntz2.com](mailto:grapefruit@dkuntz2.com).

## Additional Notes

* Grapefruit is licensed under the GPL like Grape itself.
* If you're looking for information on what's coming up, check the [todo list](grapefruit/blob/master/todo.markdown).
* If you have any questions on how or why I did certain things, feel free to email me [grapefruit@dkuntz2.com](mailto:grapefruit@dkuntz2.com).