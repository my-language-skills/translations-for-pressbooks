<?php
/**
 * This file is responsible for compact management of the Displaying translation options in the front-end.
 * Works with conjunction of extensions-for-pressbooks plugin v1.2.4
 * Creates setting section in EFP Customization menu and 'Display post translations' metabox in post-edit page.
 * Also is responsible for tfp_generatePostTranslationEntries in the DB.
 *
 * @package           translations for pressbooks
 * @since             1.2.6
 *
 */

defined ("ABSPATH") or die ("Action denied!");

if ((1 != get_current_blog_id()	|| !is_multisite()) && is_plugin_active('pressbooks/pressbooks.php')){
		if (is_plugin_active('extensions-for-pressbooks/extensions-for-pressbooks.php')) {
			add_action('admin_init','tfp_initBookTransSection');
		}

		if ( "1" == get_option( 'tfp_book_translation_enable' )){
			// If 'Display translations' checkbox enabled render metabox in post-edit page (chapters, book-info...).
			add_action('admin_menu', 'tfp_initPostTranslationsSection');
		}
}

/* --- BOOK translations --- */

/**
 * Render page sections.
 *
 * @since 1.2.6
 *
 */
function tfp_initBookTransSection(){

	 	add_settings_section( 'translations_section',
	 												'Translations section',
	 												'',
	 												'theme-customizations');

		add_settings_field(	'tfp_book_translation_enable',
												'Display translations menu',
												'tfp_bookTranslationCallback',
												'theme-customizations',
												'translations_section'); //add settings field to the translations_section

		add_option( 'tfp_book_translation_enable',0); // add theme option to database

		register_setting( 'theme-customizations-grp',
											'tfp_book_translation_enable');
}

/**
 * Render page 'Display translation' checkbox.
 *
 * @since 1.2.6
 *
 */
function tfp_bookTranslationCallback(){
	$option = get_option( 'tfp_book_translation_enable' );
	$toprint = '<input name="tfp_book_translation_enable" id="tfp_book_translation_enable" type="checkbox" value="1" class="code" ' . checked( 1, $option, false ) . ' /> Enable translations in front-end <br> <i>If the book is not featured, translations will not point to this book.</i>' ;
	echo $toprint;
}


/* --- POST-edit page translations --- */

/**
 * Initializes metabox in post-edit page.
 *
 * @since 1.2.6
 *
 */
function tfp_initPostTranslationsSection () {
		$post_types = ['metadata','front-matter','chapter','part', 'back-matter'];
		add_meta_box( 'tre_translation_checkbox', 'Display post translations', 'tfp_renderPostMetabox', $post_types, 'side', 'low');
}

/**
 * Renders metabox in post-edit page.
 *
 * @since 1.2.6
 *
 */
function tfp_renderPostMetabox(){
    global $post;
		$option = get_post_meta($post->ID, 'tfp_post_translation_disable', true);
		echo '<input name="tfp_post_translation_disable" id="tfp_post_translation_disable" type="checkbox" value="1" class="code" ' . checked( 1, $option, false ) . ' /> Disable for this post.';
}

/* --- RELATED FUNCTIONS --- */

/**
 * Save post option from 'Post translations display' checkbox on post-edit page.
 *
 * @since 1.2.6
 *
 */
function tfp_savePostTranslationOption() {
	global $post;
	if(isset($post)){
		$post_type = get_post_type($post->ID );
	}
		if (isset($post_type)){
				$post_trans_state = !empty($_POST['tfp_post_translation_disable']) ? tfp_sanitize_checkbox($_POST['tfp_post_translation_disable'], '1', '0') : '';
				update_post_meta($post->ID, "tfp_post_translation_disable", $post_trans_state);
    }
}

add_action('save_post', 'tfp_savePostTranslationOption');
