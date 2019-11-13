=== Bible SuperSearch ===
Contributors: aicwebtech
Tags: Bible search, Bible search engine, Bible, Strong's numbers
Donate link: https://www.biblesupersearch.com/downloads/
Requires at least: 4.0
Tested up to: 5.3
Requires PHP: 5.3
Stable tag: trunk
License: GPLv3 or later
License URI: http://www.gnu.org/licenses/gpl-3.0.html

Easily add a powerful Bible search engine to your WordPress site.

== Description ==
Adds a powerful Bible search engine to your WordPress website.

This is the official [Bible SuperSearch](https://www.biblesupersearch.com) plugin.  Allows seamless use of the Bible SuperSearch client and API on any WordPress site.

You also have the option to install our API on your server. 

# Core Features

1. FULL functionality of the Bible search engine as seen on [BibleSuperSearch.com](https://www.BibleSuperSearch.com)
    * Many Bible translations (enable only the ones you want)
    * Look up multiple passages
    * Random Chapter and Verse
    * Limit search to specific sections or passages
    * Search for all words, any word or whole phrase
    * Search for words in nearby verses and not just all in the same verse
    * Search by boolean expressions and regular expressions
    * Verse proximity search: Find keywords in nearby verses
    * Strong's number searches and Strong's definition hover dialogs (on Strong's enabled Bibles)
    * Increase or decrease text size
    * Changable text style
    * Copy text with easy copy mode
    * Switch between verse mode and paragraph mode
    * Advanced search
1. Selectable skins (interfaces)
1. Small footprint.  Uses our API - Doesn't take up your web hosting space!
1. Works in PHP 7+

This plugin communicates with the Bible SuperSearch API to retrieve data it needs to function.   


Please see the Technical Details below for more information.

By installing and using this plugin, you agree to the [API Terms of Service](https://api.biblesupersearch.com/documentation#tab_tos).
Also see the [API Privacy Policy]((https://api.biblesupersearch.com/documentation#tab_privacy)) and [API full documentation](https://api.biblesupersearch.com/documentation)

However, you have the option to install our API on your server, and run Bible SuperSearch entirely from your website.  https://www.biblesupersearch.com/downloads/

## Technical Details

### API
This plugin communicates with the Bible SuperSearch API to retrieve data it needs to function.   

This data includes:
* Results for all Bible search queries
* List of available Bible translations
* List of available search types
* List of available shortcuts
* List of Bible books
* API Version

By installing and using this plugin, you agree to the [API Terms of Service](https://api.biblesupersearch.com/documentation#tab_tos).
Also see the [API Privacy Policy]((https://api.biblesupersearch.com/documentation#tab_privacy)) and [API full documentation](https://api.biblesupersearch.com/documentation)

### Client
This plugin is a wrapper around the official Bible SuperSearch client.  This client is written in the Javascript framework [Enyo](http://enyojs.com), and the code is minified.  However, the source code can be seen in this GIT repository: https://sourceforge.net/p/biblesuper/ui-d/ci/master/tree/

### Directories

* wp - code specific to this WordPress plugin
* app - Bible SuperSearch client (minified)

== Installation ==
1. Click 'Install Now' on the plugin through the WordPress plugins screen, OR Unzip the plugin .zip file, then upload the `biblesupersearch` directory to the `/wp-content/plugins/` directory.
1. Activate the plugin through the 'Plugins' screen in WordPress.
1. Configure the plugin settings.  Admin menu => Settings => Bible SuperSearch
1. Place `[biblesupersearch]` shortcode on any page or post 
1. Navigate to the page / post and see it in action.


== Frequently Asked Questions ==
= How do I only enable certain Bibles? =
Go to the Settings page, and uncheck 'Enable All Bibles'
You will be given a list of Bibles to enable or disable individually.

= How do I change the appearance of Bible SuperSearch? =
You can select a different skin (interface) in the settings.

Also, to make the software better match your WordPress theme, uncheck 'Override Styles'

= How do I get a custom skin (interface) developed? =
Please contact us directly.  https://www.biblesupersearch.com/contact/

= How do I run Bible SuperSearch entirely on my website? =
You can accomplish this by installing our API on your webiste.  

https://www.biblesupersearch.com/downloads/

== Screenshots ==
1. Expanding Search Form - Contracted
1. Expanding Search Form - Expanded
1. Classic Search Form - User Friendly 2
1. Reference Look Up
1. Search Results
1. Parallel Bibles
1. Strong's Numbers with Hover Definitions
1. Easy Copy
1. Paragraph View
1. Boolean Search
1. Plugin Configs
1. Enabling Bible Translations
1. Advanced Options

== Changelog ==

= 4.0.1 =
* Bugfix: Selection of book language now based on primary Bible

= 4.0.0 =
* Added: Expanding form
* Added: Inline Strong's Definitions - Displayed at top of search results when search includes Strong's number(s).
* Added: Disambiguation links - For when using the single request field but requesting something that could be either a keyword or a book of the Bible.
* Added: More config options and expanded config page into tabs.
* Added: Official release of our API source code.
* Bugfix: Fixed positioning of strongs dialog when inclosed in a positiioned element

= 2.7.7 =
* Bugfix: Fixed another issue with destination page options list

= 2.7.6 =
* Bugfix: Fixed a couple issues with destination page options list

= 2.7.5 =
* Bugfix: Fixed Internet Explorer issue causing passage field to be ignored
* Added Destination Page option

= 2.7.2 =
* Gutenberg editor support (quick fix)

= 2.7.1 =
* Bugfix: Fixed issues with Strong's dialogs causing breakage in Internet Explorer and Edge

= 2.7.0 =
* Added Strong's Numbers with definitions (KJV)
* Added support for Italicised words (KJV / RVG)
* Added support for Words of Christ in Red (KJV / RVG)
* Misc bugfixes

= 2.6.5 =
* Adding several new interfaces
* Misc bug fixes and formatting fixes

= 2.6.2 =
* Bugfix: checking custom API URL to make sure it's valid

= 2.6.1 =
* Bugfix: Allowing for cURL or allow_url_fopen for loading statics on options page

= 2.6.0 =
* Initial release of official WordPress plugin.
* Complete rebuild of legacy Bible SuperSearch (version 2.2.x) code.

== Upgrade Notice ==

= 2.6.1 =
Bugfix: Allowing for cURL or allow_url_fopen for loading statics

= 2.6.0 =
This version adds support for PHP 7+

