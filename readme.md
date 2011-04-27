# CleanerGrape

CleanerGrape is a custom Grape Analytics installation that comes prebundled with a nicer theme.
The main goal of CleanerGrape is to keep everything that Grape has, but add a slightly cleaner
and better laid out theme than a vanilla Grape install.

I have a CleanerGrape install running on my own site, which is set to be visible for anyone if you want to see what it looks like when it's up and running.

## Installation Notes

This is a development version, that means that I'm putting working files in the repo, so you need to clear out the `extensions/extensions.php` file until it becomes

    <?php
    
    ?>

You also need to delete the file `includes/installed`, otherwise the system reads as having
been installed.

I'm working on eventually adding a ready to use database and instructions after I have some
sample data (right now the tracking code is inside of the theme's index (as of this commit on line 53 in `themes/default/index.php`)).

### You Need To Install Extensions In This Order

1. UserSpy
2. GrapePages
3. GrapeReferrers
4. GrapeOS

If you don't install them in that order, you can uninstall/reinstall (which takes a lot of time) or change the order in `extensions/extensions.php` to match that.

## Additional Notes

* CleanerGrape is licensed under the GPL like Grape itself.
* CleanerGrape is under development right now, and may eventually move to being it's own program and not just a look and feel change to Grape.
* If you have any questions on how or why I did certain things, feel free to email me grape@dkuntz2.com