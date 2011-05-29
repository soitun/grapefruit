# Grapefruit

Grapefruit is a complete overhaul to Grape Analytics. Grapefruit takes the basis of Grape and modifies some components to work
better, and adds new features.

Grapefruit was formerly known as CleanerGrape, and references to CleanerGrape may be found in the code right now (same as references to Grape itself).

## Installation Notes

The only thing you need to do besides running the install is to replace 
`extensions/extensions.php` with `extensions/extensions.empty.php`. 
After doing that, the only thing you need to do is run the install.

As soon as I determine Grapefruit to be ready for a beta release, this will be done for you (only on the download, not the 
repository, that will continue to have that problem for a while).

### Upgrading Grapefruit or moving from Grape (or CleanerGrape) to Grapefruit

If you've got an existing installation of Grapefruit or Grape 0.2 Beta 3, all you need to do
to upgrade is replace the following files and directories with the new ones:

 * `index.php`
 * `extensions/GrapeOS/info.php`
 * `extensions/GrapePages/info.php`
 * `extensions/GrapeReferrers/info.php`
 * `extensions/UserSpy/info.php`
 * `includes/themes/default/*` (the entire default folder)

If you were using a different theme, you'll need to switch it to "default" (on line 13 in 
`includes/config.php`).

You'll also need to add a new line to your `includes/config.php` file. The end should look like this originally:

    $cms['theme'] = "default";
    ?>

change that to:

    $cms['theme'] = "default";
    $cms['site'] = "NAME OF SITE BEING ANALYZED"; // I use "dkuntz2.com"
    ?>

If you do that, you should have a copy of Grapefruit up and running. If the layout looks a 
little broken, you probably need to change the order of your extensions (see below). If, even
after changing the order you still have errors, email me, grape@dkuntz2.com

## Additional Notes

* Grapefruit is licensed under the GPL like Grape itself.
* Grapefruit is under development right now, and may eventually move to being it's own program and not just a look and feel change to Grape.
* If you're looking for information on what's coming up, check the [todo list](todo.markdown).
* If you have any questions on how or why I did certain things, feel free to email me grape@dkuntz2.com