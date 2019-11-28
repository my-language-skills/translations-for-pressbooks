# Translations for PressBooks

Contributors:  

* Contributors: @colomet, @danzhik, @hugues, @lukastonhajzer
* Donate link: https://opencollective.com/mylanguageskills
* Tags: wordpress, multisite, pressbooks
* Requires at least: 5.2
* Tested up to: 5.3 WordPress
* Tested up to 5.9.5 PressBooks
* Requires PHP: 5.6
* Stable tag: 1.2.7
* License: GNU 3.0
* License URI: https://www.gnu.org/licenses/gpl-3.0.html
* Gutenberg: compatible

Extended core functionalities for Pressbooks

## Description
 Feature, of this plugin is creation connections between original books and their translations (this feature will only work if translations of books are created with cloning tool of Pressbooks). With extensions-for-pressbooks v1.2.4 installation we are able to enable/disable translations for specific book and also of specific post.

As plugin is supposed to be used with our [child theme](https://github.com/my-language-skills/books4languages-book-child-theme-for-pressbooks), the plugin also will set up this theme for all newly created books if theme is installed and network active.

## Installation

1. Clone (or copy) this repository folder `translations-for-pressbooks` to the `/wp-content/plugins/` directory
1. Activate the plugin through the 'Plugins' screen in WordPress
1. In order to activate translations for certain book it is necessary to enable it in 'EFP Customization' settings page

## Upgrades

For upgrades, download the las stable version from github, delete from FTP the old plugin and install the new one.

## Requirements

The Translations For Pressbooks plugin works with:

 * [Pressbooks](https://github.com/pressbooks/pressbooks)
 * [extensions-for-pressbooks](https://github.com/my-language-skills/extensions-for-pressbooks) v1.2.2 or newer.
 * [books4languages-book-child-theme-for-pressbooks](https://github.com/my-language-skills/books4languages-book-child-theme-for-pressbooks) latest v1.4.4 or newer.

## Frequently Asked Questions

## Frequently Asked Questions

## Disclaimers

The Translations For Pressbooks plugin is supplied "as is" and all use is at your own risk.

## Instructions

If you need some help with understanding on how plugin works, take a look at [user manual](/doc/user-manual.md).
If you need some help with understanding on how plugin was structured, take a look at [folder structure](/doc/folder-structure.md).


### Now
## 0.xx
* **ADDITIONS**

* **ENHANCEMENTS**

* **List of Files revised**

### Soon

### Later

### Future

### Changelog
## 1.2.7
* **ADDITIONS**
  * Functions prefixing, data validation and code format enhancements
  * Changing to dynamic pathways for plugin directory

* **MODIFICATIONS**
  * Switched logic of enabling/disabling translations in post-edit page (now when checked translations are disabled for that post)
  * Some functions optimized

  * **REMOVED**
  * Admin EFP settings field related to saving posts translations values (enabled/disabled) and related functionality.
  * Redundant variable.

* **BUGFIXES**
  * Uninstall functionality

## 1.2.6
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

## 1.2.5
* **ADDITIONS**
  * Functionality for printing hreflang tags of the available translations.
  * Functionality for modifying default WP html lang tag

* **ENHANCEMENTS**
  * Remove condition which changes "cs" country code to "cz" and rename flags back accordingly.

* **List of Files revised**
  * added translations-for-pressbooks-change-htmlang.php
  * added translations-for-pressbooks-print-hreflang.php
  * added translations-for-pressbooks.php

## 1.2.4
* **ADDITIONS**
  * New functions: tfp_getOriginalBookLanguage() , tfp_getCurrentBookFlag() and tfp_getCurrentBookLanguageCode().

* **ENHANCEMENTS**
  * Modifies existing tfp_printTransLinks() function for needs of updated books4languages-book-child-theme-for-pressbooks v1.3 theme.
  * Changes flag name from "cs.png" to cz.png

* **List of Files revised**
  * translations-for-pressbooks.php

#### 1.2.3
* **REMOVED**
    *  Auto update from github

#### 1.2.2
* **ENHANCEMENTS**
   * Hide not use languages

* **List of Files revised**
   * translations-for-pressbooks.php

#### 1.2.1
* **ENHANCEMENTS**
   * Create  folder assets and add folder flag-icon

* **REMOVED**
   * Original mark, now it's in extensions-for-pressbooks

* **List of Files revised**
   * translations-for-pressbooks.php

#### 1.2
* **ADDITIONS**
	* Languages names alphabetical organization #8

* **BUGFIXES**
	* Translations bug #8
	* Concentration of code #10

* **List of Files revised**
     * translations-for-pressbooks.php
     * original-mark.php

#### 1.1
* Edition extension removed

#### 1.0 Initial release


## Upgrade Notice

---
[Up](/README.md)
