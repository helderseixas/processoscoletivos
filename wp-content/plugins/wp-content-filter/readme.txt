=== WP Content Filter ===
Contributors: dgwyer
Tags: banned, replace, words, edit, content filter, filter, filtered, content censor, censor, censored, keywords, options, profanity, swearing, post, admin, abuse, explicit, seo
Requires at least: 2.7
Tested up to: 4.5.2
Stable tag: 2.7

Take control of your site today! Censor all content containing profanity, swearing, and abusive comments. Flexible Plugin options.

== Description ==

Use this plugin to censor content on your site! Easily block out all forms of unwanted content by specifying a list of keywords to be filtered.

Flexible options include advanced control over content such as strict/non-strict filtering. Flagged keywords which are embedded in whole words can be ignored.

e.g. 'competition' will be ignored if you have 'pet' as a flagged keyword. Otherwise with strict filtering OFF it will appear as 'com***ition'.

You can also select which sections of your WordPress site to censor including:

* Posts (including recent posts sidebar widget)
* Post title
* Comments (including recent comments sidebar widget)
* Comment authors
* Tags
* Tag cloud

Keywords are replaced with wild card characters. Further options retain the first letter of each filtered keyword, remove all letters (wild card characters only), or retain the first and last letter of the filtered keyword.

e.g. The keyword pluto would be replaced with either p****, *****, or p***o. Keywords can also be filtered using case insensitive, or case sensitive options.

Please consider <a href="https://wordpress.org/support/view/plugin-reviews/wp-content-filter"><strong>rating</strong></a> this Plugin if you find it useful. It only takes a moment but it's very much appreciated. :)

><strong>Upgrade <a href="https://wpgoplugins.com/plugins/content-censor/">WP Content Filter</a> today for even more plugin options!
>
> *NEW* Filter BuddyPress and bbPress content too!
>
>The Pro version includes a <strong>batch processor</strong> to actively scan your entire site for unwanted content! Checkout the <a href="https://wordpress.org/plugins/wp-content-filter/screenshots/">screenshots</a>, or click <a href="https://wpgoplugins.com/plugins/content-censor/">here</a> for more details.
>
>See our <a href="https://www.wpgoplugins.com" target="_blank">WordPress plugin site</a> for more top plugins!

== Installation ==

Instructions for installing the WP Content Filter Plugin.

1. Download and extract the Plugin zip file.
2. Upload the folder containing the Plugin files to your WordPress Plugins folder (usually ../wp-content/plugins/ folder).
3. Activate the Plugin via the 'Plugins' menu in WordPress.
4. Once activated you go to the Plugin options by clicking the 'WP Content Filter' link under the 'Settings' menu.
5. Enter your required filter keywords and configure other options and your site content will be protected automatically.

== Frequently Asked Questions ==

= How do I specify a character other than '*' to blank out swear words etc? =

As of version 1.1 you can now set this in the Plugin options page.

= Does the Plugin change the content in the WordPress database? =

No, the Plugin just filters content directly before it is displayed on the screen. No content in the database is ever altered.

== Screenshots ==

1. Free plugin options page.
2. Example of the Plugin working on a live site, filtering post content, comments, and the comment author name!
3. Activated Plugin on the Manage Plugins page.
4. Ever expanding array of plugin options.(Pro only)
5. Actively scan your entire site for unwanted content. (Pro only)
6. License and Support tab provide links to documentation and allow you to enter a license key for automatic plugin updates. (Pro only)

== Changelog ==

*2.7*

* Fixed broken image links on plugin settings page.

*2.6 update*

* Plugin settings page updated.

*2.56 update*

* Added video demonstrating new feature.

*2.55 update*

* Updated plugin links.

*2.51 update*

* Updated plugin settings links.

*2.50 update*

* Fixed bug when replacing first or first/last letter in matched keyword. Replaced characters now respect the original case.

*2.49 update*

* Fixed typos and missing info.

*2.48 update*

* Updated plugin with 'Pro' links.

*2.47 update*

* Fixed typo on main readme page.

*2.46 update*

* Settings page updated.

*2.45 update*

* Fixed bug in new default options code.

*2.44 update*

* Modified the way default Plugin options are handled. It's now more stable when adding new options.

*2.43 update*

* Patched security issue.

*2.42 update*

* Added extra check for post global variable inside the content filter allowing you to add extra custom bbPress filters on pages where the post global variable isn't defined.

*2.41 update*

* Security issue addressed and fixed.

*2.4 update*

* Enhancement: You can now selectively exclude certain pages from being filtered by entering a comma separated list of page ID's in Plugin settings.

*2.34 update*

* Bug fix. Ajax generated content is now filtered too!

*2.33 update*

* Now filters post excerpt too!
* Options page text updated.

*2.32 update*

* Plugin options page updated.
* Fixed issue that echoed debug info.

*2.31 update*

* Minor update to Plugin options page.

*2.3 update*

* Support for filtering of bbPress title and content.
* Plugin options page updated.

*2.28 update*

* New internal function pre-fix used due to clash with another Plugin(s).
* Tested with WordPress 3.2.1.

*2.27 update*

* Updated for WordPress 3.1.

*2.26 update*

* Minor changes to Plugin options page.
* Tested and updated to work with WordPress 3.0.

*2.25 update*

* Ammendments to the Plugin options page.

*2.20 update*

* Strict filtering issue now fixed! Flagged keywords which are embedded in whole words can now be ignored with new Plugin options.
* New Plugin support package available.

*2.11 update*

* Admin help information labels updated.
* Screenshots resized, and a third one added.

*2.1 update*

* Added new filter character option - Blank, which replaces flagged keywords with nothing. i.e. basically just deletes the keyword(s).
* Changed drop down text for other filter character options to be in line with newly added option.
* Small CSS changes to WP Content Filter options page, to be consistent with WordPress options pages.

*2.05 update*

* Options table entries now removed when Plugin is deleted via WordPress admin.
* Legacy options entries now checked for and removed by default.

*2.01 update*

* Small edit to the menu page code.

*2.0 update*

* Overhaul of the Plugin code base.
* New options engine used.
* Options page completely updated.
* Keywords textbox input now validated, and automatically strips out HMTL tags.
* New option to preserve settings or restore defaults if the Plugin is temporarily deactivated and then reactivated.
* Option to delete Option db entries upon deactivation removed. Instead, a future version will delete these entries automatically when the Plugin is deactivated AND deleted.

*1.2 update*

* Comment author field, now filtered for keywords.
* Larger input area for entering keywords to be filtered.

*1.1 update*

* New option to remove all Plugin database settings when Plugin is deactivated. This is useful if you wish to delete the Plugin; in this case the option will clean up the database automatically. Note: If this option is selected, and the Plugin deactivated/activated then the Plugin options will revert to their defaults (i.e. same effect as installing the Plugin for the first time). So if you wish to retain your settings between updgrades leave this option at its default setting (off).
* Existing admin controls swapped around a bit.
* Can now select the wildcard character used in blanking out keywords (previous was fixed to only an asterisk).
* Choice of letters to retain in filtered keywords, options include: retain first letter (default), retain first and last letter, blank out ALL characters.