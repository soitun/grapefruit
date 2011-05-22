# Todo - Cleaner Grape

This is a list of things that should be worked on for the first beta release of CleanerGrape
(for those unaware, the current status of CleanerGrape is SUPER DEVELOPMENT, DON'T TRY IF YOU
DON'T WANT BUGS).

## Big Things

 * Add the `$cms['site']` variable to the installer. This variable will be used to keep track 
 of the actual site being analyzed (so if you have grape on a different domain or a subdomain, 
 right now it checks to see )

 * Create a SearchTerms extension (in progress. The referrer extension should now record what I 
 need for it to work (because it's nice to know what search terms get people to your site)).

 * Update CityFinder to only show state (region code) if it's the US, otherwise, B3 comes in 
 for places like france.

 * Change GrapeOS to display OS & Version together, instead of OS as a sub section and version
 as an item in the section.

## Fun Things

Fun things as defined as things that would be nice to ipliment but aren't needed.

 * Create an API for getting data from CleanerGrape to another application. I mean, right now
 you can essentially just use the database to get the data, but that's not always an option. 
 Plus it could be used to add a number of visitors this DATERANGE to your own site.