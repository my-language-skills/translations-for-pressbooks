=== Translations for PressBooks ===
Contributors: colomet, danzhik, dcazzorla, lukastonhajzer
Donate link: https://opencollective.com/mylanguageskills
Tags: multisite, pressbooks, translations, internacionalization, wordpress plugin
Requires at least: 3.0.1
Tested up to: 5.2.2
Requires PHP: 5.6
Stable tag: 1.2.7
License: GPLv3 or later
License URI: https://www.gnu.org/licenses/gpl-3.0.en.html

This plugin provides simple handy tool for automatic relationship between original (home pages and chapters) content and translations in a PressBooks installation.

== Description ==

With use of this plugin you will be able to select the target language of the book (besides the focus language) and to create relationships between the pages and the available translated languages.

**Only works with [multisite](https://wordpress.org/support/article/create-a-network/) installation!**
**Only works with [PressBooks](https://github.com/pressbooks/pressbooks) installation!**

== Installation ==

= This plugin requires: =

* Wordpress Multisite installation

= Installation instructions: =

This section describes how to install the plugin and get it working.

1. Clone (or copy) this repository folder `stranslations-for-pressbooks
` to the `/wp-content/plugins/` directory, or install the plugin through the WordPress plugins screen directly.
2. Activate the plugin through the 'Plugins' screen in WordPress
3. To configure the translations, choose book language and book target language in Book Info in your dashboard.

== Frequently Asked Questions ==

= I have a feature request, I've found a bug, a plugin is incompatible... =

Please visit [the support forums](https://wordpress.org/support/plugin/xxxxxxxx)

= I am a developer; how can I help? =

Any input is much appreciated, and everything will be considered.
Please visit the [GitHub project page](https://github.com/my-language-skills/translations-for-pressbooks) to submit issues or even pull requests.

= Is possible to extend with new languages?=

No, the plugin only use main languages of the official countries, new languages require the creation of flags. If you would like to use with one specifically language, send us the icon of the flag, the language name (in english) and te iso code (3) of the language and we will update the plugin.

== Screenshots ==


== Changelog ==
=== 1.X ===
=== 1.2.8 ===
* **BUGFIXES**
  * 'Studying content' metabox displaying fixed
  * Change querry checking from post_name to post_type (more stable)
  * Add fallback option for specific pages like 'site index' (not to display translations by default)

=== 1.2.7 ===
* **ADDITIONS**
  * Functions prefixing, data validation and code format enhancements
  * Changing to dynamic pathways for plugin directory

=== 1.2.6 ===
* **ADDITIONS**
  * Functionality for control of displaying translations options in the front-end (book and post level). More info in user-manual.
  * Functionality which removes all the plugin data from DB on plugin uninstall (with option to keep the data if we want to). More info in user-manual.

* **ENHANCEMENTS**
  * Updated user-manual to provide up to date setup information.
  * Some Internationalization

* **BUGFIXES**
  * Post translations were not freshly set to enable after "Display translations" enabled while "Save previous post values" disabled
  * add_action hook changed
  * Network setting option for saving plugin data after plugin uninstall now set to 'enabled' by default
  * Updated querry now accepts both "book-information" and "book-info" post names
  * Check if 'tfp_post_translation_enable' related to current post exists (table-of-contents)
  * Increase number of sites in uninstall functionality to 500

* **List of Files revised**
  * translations-for-pressbooks.php
  * translations-for-pressbooks-change-htmlang.php renamed to tfp-change-htmlang.php
  * translations-for-pressbooks-print-hreflang.php renamed to tfp-print-hreflang.php
  * added tfp-network-settings.php
  * added tfp-translation-enabler.php
  * added uninstall.php
  * user-manual.php

=== 1.2.5 ===
* **ADDITIONS**
  * Functionality for printing hreflang tags of the available translations.
  * Functionality for modifying default WP html lang tag

* **ENHANCEMENTS**
  * Remove condition which changes "cs" country code to "cz" and rename flags back accordingly.

* **List of Files revised**
  * added translations-for-pressbooks-change-htmlang.php
  * added translations-for-pressbooks-print-hreflang.php
  * added translations-for-pressbooks.php

=== 1.2.4 ===
* **ADDITIONS**
  * New functions: getOriginalBookLanguage() , getCurrentBookFlag() and getCurrentBookLanguageCode().

* **ENHANCEMENTS**
  * Modifies existing pbc_print_trans_links() function for needs of updated books4languages-book-child-theme-for-pressbooks **v1.3** theme.
  * Changes flag name from "cs.png" to cz.png

* **List of Files revised**
  * translations-for-pressbooks.php

=== 1.2.3 ===
* **REMOVED**
    *  Auto update from github

=== 1.2.2 ===
* **ENHANCEMENTS**
   * Hide not use languages

* **List of Files revised**
   * translations-for-pressbooks.php

=== 1.2.1 ===
* **ENHANCEMENTS**
   * Create  folder assets and add folder flag-icon

* **REMOVED**
   * Original mark, now it's in extensions-for-pressbooks

* **List of Files revised**
   * translations-for-pressbooks.php

=== 1.2 ===
* **ADDITIONS**
	* Languages names alphabetical organization #8

* **BUGFIXES**
	* Translations bug #8
	* Concentration of code #10

* **List of Files revised**
     * translations-for-pressbooks.php
     * original-mark.php

=== 1.1 ===
* Edition extension removed



== Upgrade Notice ==

= 1.2.3 =

= 1.0.0 =

== Disclaimers ==

The Translations for PressBooks is supplied "as is" and all use is at your own risk.
