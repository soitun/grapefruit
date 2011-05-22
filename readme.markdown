# CleanerGrape

CleanerGrape is a custom Grape Analytics installation that comes prebundled with a nicer theme.
The main goal of CleanerGrape is to keep everything that Grape has, but add a slightly cleaner
and better laid out theme than a vanilla Grape install.

I have a CleanerGrape install running on my own site, which is set to be visible for anyone if you want to see what it looks like when it's up and running.

## Differences Between CleanerGrape and Grape

 * CleanerGrape uses a different theme, one that requires you to install the extensions in a certain order if you dont' want to break the entire design (I'm working on having them installed by default).
 * CleanerGrape switches the link in the UserSpy extension from api.hostip.info to freegeoip.appspot.com, in reality, this doesn't do much for the end user, but the switch after being made in CityFinder made the results much better (visitors went from unknown to where they were from (specifically, me, so I know that it works better)).

## Installation Notes

The only thing you need to do besides running the install is to replace 
`extensions/extensions.php` with `extensions/extensions.empty.php`. 
After doing that, the only thing you need to do is run the install.

### Upgrading CleanerGrape or moving from Grape to CleanerGrape

If you've got an existing installation of CleanerGrape or Grape 0.2 Beta 3, all you need to do
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

If you do that, you should have a copy of CleanerGrape up and running. If the layout looks a 
little broken, you probably need to change the order of your extensions (see below). If, even
after changing the order you still have errors, email me, grape@dkuntz2.com

### Extensions

You Need To Install Extensions In This Order

1. UserSpy
2. GrapePages
3. GrapeReferrers
4. GrapeOS

If you don't install them in that order, you can uninstall/reinstall (which takes a lot of time) or change the order in `extensions/extensions.php` to match that.

__CityFinder__

If you want to use CityFinder with CleanerGrape, you need to put it between UserSpy and GrapePages, or you need to manually edit it yourself to make it fit properly.

## Additional Notes

* CleanerGrape is licensed under the GPL like Grape itself.
* CleanerGrape is under development right now, and may eventually move to being it's own program and not just a look and feel change to Grape.
* If you're looking for information on what's coming up, check the todo list (`todo.markdown`).
* If you have any questions on how or why I did certain things, feel free to email me grape@dkuntz2.com