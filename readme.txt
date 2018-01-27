=== BibleSuperSearch ===
Contributors: aicwebtech
Tags: Bible search, Bible search engine, Bible
Donate link: http://www.biblesupersearch.com/downloads/
Requires at least: 4.0
Tested up to: 4.9
Requires PHP: 5.3
Stable tag: 0.1
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Easily add a powerful Bible search engine to your WordPress site.

== Description ==
Official [Bible SuperSearch](https://www.biblesupersearch.com) plugin.  Allows seamless use of the Bible SuperSearch client and API on any WordPress site.

# Core Features

1. Full functionality of the Bible search engine as seen on http://www.BibleSuperSearch.com
1. Small footprint.   Uses our API - Doesn't take up your web hosting space!
1. Now works in PHP 7
1. Enable only the Bible translation(s) you want.
1. Selectable interfaces

## Technical Details

### API
This plugin communicates with the Bible SuperSearch API to retrieve data it needs to function.   

This data includes
* Results for all Bible search queries
* List of available Bible translations
* List of available search types
* List of available shortcuts
* List of Bible books
* API Version

By installing and using this plugin, you agree to the [API Terms of Service](https://api.biblesupersearch.com/documentation#tab_tos).
Also see the [API Privacy Policy]((https://api.biblesupersearch.com/documentation#tab_privacy)) and [API full documentation](https://api.biblesupersearch.com/documentation)

### Client
This plugin is a wrapper around the official Bible SuperSearch client.  This client is written in the Javascript framework [Enyo](http://enyojs.com), and the code is minified.  However, the source code can be see in this GIT repository: https://sourceforge.net/p/biblesuper/ui-d/ci/master/tree/

### Directories

* wp - code specific to this WordPress plugin
* app - Bible SuperSearch client (minified)
* assets - Screenshots


== Installation ==
1. Unzip the plugin .zip file.
1. Upload the \"biblesupersearch\" directory to the \"/wp-content/plugins/\" directory.
1. Activate the plugin through the \"Plugins\" menu in WordPress.
1. Configure the plugin settings.  Admin menu => Settings => Bible SuperSearch
1. Place `[biblesupersearch]` shortcode on a page or post 
1. Navigate to the page / post and see it in action.

== Frequently Asked Questions ==
= How do I only enable certain Bibles? =
Go to the Settings page, and uncheck \'Enable All Bibles\'
You will be given a list of Bibles to enable or disable individually.

= How do I change the appearance of Bible SuperSearch? =
You can select a different skin (interface) in the settings.
To make the software better match your WordPress theme, uncheck \'Override Styles\'

= How do I get a custom interface developed? =
Please contact us directly.  https://www.biblesupersearch.com/contact/

== Screenshots ==
1. Client
2. Plugin Configs

== Changelog ==
= 0.1 =
* Initial release of plugin.

== Upgrade Notice ==

= 0.1 =
This version adds support for PHP 7+
