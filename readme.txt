=== Bible SuperSearch ===
Contributors: aicwebtech
Tags: Bible search, Bible search engine, Bible, Strong's numbers
Donate link: https://www.biblesupersearch.com/downloads/
Requires at least: 4.0
Tested up to: 5.7
Requires PHP: 5.3
Stable tag: trunk
License: GPLv3 or later
License URI: http://www.gnu.org/licenses/gpl-3.0.html

Powerful Bible search engine plus a Bible download tool.  Option to install our API on your server for complete independence.

== Description ==
Add powerful Bible tools to your website, including a Bible search engine, and a Bible download page. Our Bible search engine includes multiple selectable interfaces, allowing you to make it appear as simple or as complex as desired.

Keep your users on your website!   Unlike similar plugins, Bible SuperSearch displays the Bible text directly on your website; your web users will not be redirected to a 3rd party website to view the text.

This is the official [Bible SuperSearch](https://www.biblesupersearch.com) plugin.  It allows for seamless use of the Bible SuperSearch client and API on any WordPress site.

This plugin pulls data from our API transparently.  You also have the option to install our API on your server for complete autonomy.

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
    * Bible help and getting started dialogs
    * Bible downloads dialog and shortcode
1. Selectable skins (interfaces) allow the Bible search to appear as simple or as complex as desizred
1. Widget providing a small Bible search form. 
1. Bible downloads - ability to download public domain or non-commercial use only Bibles in several formats

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
* com_test - Bible SuperSearch client (minified)

== Installation ==
1. Click 'Install Now' on the plugin through the WordPress plugins screen, OR Unzip the plugin .zip file, then upload the `biblesupersearch` directory to the `/wp-content/plugins/` directory.
1. Activate the plugin through the 'Plugins' screen in WordPress.
1. Configure the plugin settings.  Admin menu => Settings => Bible SuperSearch
1. Place `[biblesupersearch]` shortcode on any page or post 
1. Navigate to the page / post and see it in action.

Shortcodes include:

    [biblesupersearch] - displays the main Bible SuperSearch application
        These attributes override the settings on this plugin.
        * interface - the name or ID of the skin to be used on the application.  
            For a complete list, and to see them in action, please visit https://www.biblesupersearch.com/client/
        * destination_url - URL to page or post where form will redirect to when submitted.
                            The destionation will need to have the <cod>[biblesupersearch]</code> shortcode.
                            Set to '' to force to current page and override the 'Default Destination Page'
        * formatButtons - Which formatting buttons to use?  Options: default, Classic or Stylable
        * navigationButtons - Which navigation buttons to use?  Options: default, Classic or Stylable
        * pager - Which pager to use? Options: default, Classic, Clean

    [biblesupersearch_bible_list] - Displays a list of all Bibles available (even if not enabled)
        Attributes: 
        * verbose - Displays some extra columns on the list of Bibles (true/false)

    [biblesupersearch_downloads] - Displays the Bible downloads page
        Attributes:
        * verbose - Displays all Bibles, even if they are not downloadable. (true/false)


== Frequently Asked Questions ==
= How do I only enable certain Bibles? =
Go to the Settings page, and uncheck 'Enable All Bibles'
You will be given a list of Bibles to enable or disable individually.

= Please add the (ABC) Bible =
We only offer 'Shareable' Bibles.  These are Bibles that anyone can legally copy and redistribute, for non-commercial purposes, without needing permission from the publisher.
If you would like to use a proprietary Bible with Bible SuperSearch, you will need to install our API on your server, then properly license the text from the publisher.
Request the text in an Excel file, and the importer within the API can import it for you.

= How do I add more Bibles? =
First, you will need to install our API on your server.
Then, use the Bible importer within the API to add the desired Bibles.

= How do I change the appearance of Bible SuperSearch? =
You can select a different skin (interface) in the settings.

Also, to make the software better match your WordPress theme, uncheck 'Override Styles'

= How do I get a custom skin (interface) developed? =
Please contact us directly.  https://www.biblesupersearch.com/contact/

= How do I run Bible SuperSearch entirely on my website? =
You can accomplish this by installing our API on your webiste.
The API can be downloaded here:

https://www.biblesupersearch.com/downloads/

Once installed, you will have to point this plugin to your API install.
Settings => Bible SuperSearch => Advanced => API URL => Insert the URL to your API in this box.

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

= 4.4.2 = 
* Fixed issues with RTL detection on Bibles
* Fixed poor documentation of plugin shortcodeds
* Fixed broken shortcode attributes

= 4.4.1 =
* Fixed breakage when advanced toggle disabled

= 4.4.0 =
* Added UI language option
* Added ability to display help / download dialog buttons separately from formatting buttons.
* Added 'Minimal with Parallel Bible' skin
* Added print button
* Added new Basic help dialog
* Added new share link dialog

= 4.3.3 =
* Bugfix: Fixed issues with responsiveness on the Bible SOS, Start, and Download dialogs
* Bugfix: Fixed display issues with Download button

= 4.3.2 =
* Bugfix: Fixed breakage in the 'limit search to' presets.  When one of the preset values were selected, it would revert to 'Passage(s) Listed Below (or above), with the passage set to 'value'
* Improved plugin description

= 4.3.1 =
* Bugfix: New download dialog breaks when requesting two or more Bibles, but leaving the last Bible selector empty.

= 4.3.0 =
* Added widget
* Added dialog for downloading Bibles
* Added Escape (ESC) to all dialogs
* Bugfix: Fixed Bible sort mapping

= 4.2.8 =
* Fixed total breakage / no display on EZ-Copy

= 4.2.7 = 
* Fixed Bible selected breakage on expanding form when contracting the form.

= 4.2.6 =
* Added download Bible limitation as a quick fix to download issues on shared hosting

= 4.2.5 = 
* Bugfix: Force refresh of statics data if previous load failed.

= 4.2.3 =
* Bugfix / Improvement: Statics are now loaded from the local cache, eliminating an AJAX call at page load.

= 4.2.2 =
* Bugfix: Fixed more issues with root URL affecting websites hosted on WordPress.com

= 4.2.1 =
* Bugfix: Fixed issue discovering root URL in the client software, affecting websites hosted on WordPress.com

= 4.2.0 =
* Added ability to group Bibles by language, and added a config for this to this WP plugin
* Added Bible SOS, Bible Start Guide dialogs
* Changed Strongs Definition dialogs to stay open longer, allowing a user to copy and paste the Strong's text

= 4.1.1 = 
* Fixed bug preventing the Bible selector from resizing responsivly 

= 4.1.0 =
* Added [biblesupersearch_downloads] shortcode for displaying a page from which to download Bibles
* Added more embedded shortcode documentation.

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

