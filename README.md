# Translations for PressBooks

Contributors:  @danzhik, @hugues, @!ndeed

Donate link: https://opencollective.com/mylanguageskills

Tags: wordpress, multisite, pressbooks

Tested up to: [![WordPress](https://img.shields.io/wordpress/v/akismet.svg)](https://wordpress.org/download/)

Requires:  [![Pressbooks](https://img.shields.io/badge/Pressbooks-V%205.4.7-red.svg)](https://github.com/pressbooks/pressbooks/releases/tag/5.3)

Stable tag: [![Current Release](https://img.shields.io/github/release/my-language-skills/extensions-for-pressbooks.svg)](https://github.com/my-language-skills/extensions-for-pressbooks/releases/latest/)

License:  [![License](https://img.shields.io/badge/license-GPL--3.0-red.svg)](https://github.com/my-language-skills/all-in-one-metadata/blob/master/LICENSE.txt)

License URI: http://www.gnu.org/licenses/gpl-3.0.txt

Extended core functionalities for Pressbooks

## Description
 The last, but not the least feature, of this plugin is creation connections between original books and their translations (this feature will only work if trnaslations of books are created with cloning tool of Pressbooks).

As plugin is supposed to be used with our [child theme](https://github.com/my-language-skills/books4languages-book-child-theme-for-pressbooks), the plugin also will set up this theme for all newly created books if theme is installed and network active.

## Installation

1. Clone (or copy) this repository folder `translations-for-pressbooks` to the `/wp-content/plugins/` directory
1. Activate the plugin through the 'Plugins' screen in WordPress

## Upgrades

For upgrades, download the las stable version from github, delete from FTP the old plugin and install the new one.

## Requirements

The Translations For Pressbooks plugin works with:

 * ![extensions-for-pressbooks](https://github.com/my-language-skills/extensions-for-pressbooks/releases/tag/1.2)
 * ![PHP](https://img.shields.io/badge/PHP-7.2.X-blue.svg)
 * [![Pressbooks](https://img.shields.io/badge/Pressbooks-V%205.4.7-red.svg)](https://github.com/pressbooks/pressbooks/releases/tag/5.4.7)
 * books4languages-book-child-theme-for-pressbooks latest v1.3 or newer.


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

## 1.2.4
* **ADDITIONS**
  * New functions: getOriginalBookLanguage() , getCurrentBookFlag() and getCurrentBookLanguageCode().

* **ENHANCEMENTS**
  * Modifies existing pbc_print_trans_links() function for needs of updated books4languages-book-child-theme-for-pressbooks v1.3 theme.
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
