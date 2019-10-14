# User Manual

## Introduction

This manual gives brief explanations on features of the plugin, which could be useful for development of your website and for understanding how to receive desired behaviour of a plugin.


## Translations relations

In case you are creating translations of your books, 'Translations relations' can help you with automatic linking between all of the the translations and original book. For current moment plugin logically interconnects only books about languages.

### Adding new translations

**Read carefully the following instructions and follow them one by one, otherwise it can lead to incorrect working result of a plugin!** The working steps to create relations between you books are following:

1. With use of `Original Mark` tick the checkbox in `/wp-admin/network/sites.php` `Featured Book` column in front of book in original language. *Wait until notification!*
1. Go to `Book Info` page of a book which is a translation (this book **must** be created with `Cloning Tool` using original book as a source), allocate `Studying Content` metabox (if book was not cloned, this metabox will not be shown) and choose which language is this book about.
	* In case the book is not actually a translation, but just a modification, choose 'Not translation' option in that metabox in order too keep consistenty of translations interactions.
1. On the same page choose language of a book in `General Book Information` metabox, if it was not done before.
1. Update Book Info.
1. Go back to `/wp-admin/network/sites.php` page and mark translated book as `Featured Book`. *Wait until notification!*

For more translations repeat steps from 2 to 5 in the list above.

From database interaction point of view, all these steps make some impact on table of translations relations. When you mark original book with `Original Mark` table creates new row, responsible for series of the translations. After, when you mark translation with `Original Mark` after all required steps, the `id` of that book is added as value in corresponding column in original book row. That's why it is **important** for step 5 to be performed strictly after step 2-4, unless you would like to make this cloned book a root for another series of translations. Otherwise, translated book will be accounted as book in original language, which will start new translations series.

### Deleting translations

In order to delete book from relational system, just simply unmark `Featured Book` checkbox in `/wp-admin/network/sites.php` in front of that book.

If you would like to drop all the relations of series of book and its translations, unmark `Featured Book` checkbox in `/wp-admin/network/sites.php` in front of original book. *Be careful!* After this all the translation will also be unmarked as featured books.

### Changing language of translated book

If by mistake you have put the wrong language of a book in point 3 of working steps and you would like to change it to a correct one, in order to keep the consistent relations do following:
1. Go to `/wp-admin/network/sites.php` and unmark translated book in `Featured Book` column. *Wait until notification!*
1. Go to `Book Info` page of that book, change language of a book to a desired one in `General Book Information` metabox.
1. Go back to `/wp-admin/network/sites.php` page and mark translated book as `Featured Book`. *Wait until notification!*

**Note!** In case you will not unmark the book before changing the language in book info, it will not remove old language choice and will keep it in the table. In this case, manually remove unwanted column from the DB.

**Note!** Upon changing the language of the book, the previous language column in the DB table stays there with the value "0".

### Printing links in web-pages
If you want to print the links of translation in front-end them, you need to check if translations for current book and post is set to enabled from back-end settings with function *check_if_translations_enabled()*. Function returns "1" if translations are enabled.
For printing only current language Flag use: *getCurrentBookFlag()*.
For printing only current language Code use: *getCurrentBookLanguageCode()*.
Next function *pbc_print_trans_links()* prints out list of available translations in current context.


	<ul class="footer__pressbooks__links__list" style="margin-bottom: 1rem;">
	<?php
		$blog_id = get_current_blog_id();
		pbc_print_trans_links($blog_id);
	?>
	</ul>


**Note!** Do not change permalinks in your translations, otherwise links will lead to non-existing pages in other books.

In order for the links to be shown in the front-end of a website, use our [theme](https://github.com/my-language-skills/books4languages-book-child-theme-for-pressbooks). Since some relations will be established, the links will appear in the footer of every web-page of your connected books.


### Translations activation WORKFLOW with DESCRIPTION:

# After activation extensions-for-pressbooks plugin and translations-for-pressbooks plugin.

1. On a site (book) level go to Appearance-> EFP Customizations.
2. Here in 'Translations section' check on 'Display translations' to display 	translations in the front-end.
# Translations for this book is now enabled for metadata(cover/book-info), front-matter , chapter , part,  back-matter.

3. After checking this option it is now possible to enable/ disable showing translations option for each post separately in post-edit page (chapter, book-info,...). By default every post is set to enable (display) translations option.
4. To disable/ enable post translations option go to post-edit page of the specific post.
5. By default, every time 'Display translations' is re-enabled in 'Appearance-> EFP Customizations', default post translations settings are generated in DB (every post gets translations option enabled). If those options were modified and we want to keep those changes (post translations option) saved after re-enable of 'Display translations' option it is necessary to check 'Save previous post values' in 'Appearance-> EFP Customizations' in 'Translations section'.
# With this option turned on every time 'Display translations' (book translations) is now re-enabled, previous post translations options are persisted.

6. By default on plugin uninstall all the data plugin created in DB are erased. In order to keep this data after plugin gets uninstalled go to Network admin, Settings -> EFP settings and check 'Persist data on uninstall' checkbox.
