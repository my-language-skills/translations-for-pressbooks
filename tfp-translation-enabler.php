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
			if ( isset( $_REQUEST['settings-updated'])  && ("theme-customizations" == $_REQUEST['page']) ) {
					if("1" == $book_translation_enable = get_option( 'tfp_book_translation_enable' ) && "1" != $post_translations_save = get_option( 'tfp_post_translations_save' )){
							tfp_generatePostTranslationEntries(); // generate translation entries in DB on 'Display translations' checkbox enable.
					}
			}
		}

		if ( "1" == $option = get_option( 'tfp_book_translation_enable' )){
			// If 'Display translations' checkbox enabled render metabox in post-edit page (chapters, book-info...).
			add_action('custom_metadata_manager_init_metadata', 'tfp_initPostTranslationsSection');
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
	 												'tfp_translationsSectionDescription',
	 												'theme-customizations');

		add_settings_field(	'tfp_book_translation_enable',
												'Display translations menu',
												'tfp_bookTranslationCallback',
												'theme-customizations',
												'translations_section'); //add settings field to the translations_section

		add_option( 'tfp_book_translation_enable',0); // add theme option to database

		add_settings_field(	'tfp_post_translations_save',
												'Post translations',
												'tfp_postTranslationsCallback',
												'theme-customizations',
												'translations_section');//add settings field to the translations_section

		register_setting( 'theme-customizations-grp',
											'tfp_book_translation_enable');
		register_setting( 'theme-customizations-grp',
											'tfp_post_translations_save');
}

/**
 * Render page description.
 *
 * @since 1.2.6
 *
 */
function tfp_translationsSectionDescription(){
 //TODO
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

/**
 * Render post translations field in Translations section
 *
 * @since 1.2.6
 *
 */
function tfp_postTranslationsCallback(){
	$option = get_option( 'tfp_post_translations_save' );
	$toprint = "";

	if ("1" == $tfp_book_translation_enable = get_option( 'tfp_book_translation_enable' )) {
		$toprint .= '<input name="tfp_post_translations_save" id="tfp_post_translations_save" type="checkbox" value="1" ' . checked( 1, $option, false )  . ' class="code"  />Keep post translations selections saved on Display translations menu reactivation.';
	} else {
		// Enable 'tfp_post_translations_save' checkbox only if 'tfp_book_translation_enable' is enabled.
		if ($option == "1"){
			$toprint .= '<style>#tfp_post_translations_save{ display:none; } </style>';
			$toprint .= '<input name="tfp_post_translations_save" id="tfp_post_translations_save" type="checkbox" value="1" ' . checked( 1, $option, false )  . ' class="code"  /> ';
			$toprint .= '<input type="checkbox" value="1" checked="checked" class="code" disabled />Keep post translations selections saved on Display translations menu reactivation.';
		} else {
			$toprint .= '<style>#tfp_post_translations_save{ display:none; } </style>';
			$toprint .= '<input name="tfp_post_translations_save" id="tfp_post_translations_save" type="checkbox" value="1" ' . checked( 1, $option, false )  . ' class="code"  /> ';
			$toprint .= '<input type="checkbox" value="1"  class="code" disabled /> Keep post translations selections saved on Display translations menu reactivation. ';
		}
	}
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
    $custom = get_post_custom($post->ID);
		$result = get_post_meta($post->ID, 'tfp_post_translation_enable', true);

		if ($result != "1"){
      add_post_meta( $post->ID, 'tfp_post_translation_enable', '1', true );
    }

		$option = get_post_meta($post->ID, 'tfp_post_translation_enable', true);
		echo '<input name="tfp_post_translation_enable" id="tfp_post_translation_enable" type="checkbox" value="1" class="code" ' . checked( 1, $option, false ) . ' /> Check to enable for this post.';
}

/* --- RELATED FUNCTIONS --- */

/**
 * Save post option from 'Post translations display' checkbox on post-edit page.
 *
 * @since 1.2.6
 *
 */
function tfp_savePostTranslationOption($post_ID = 0) {
    $post_ID = (int) $post_ID;
    $post_type = get_post_type( $post_ID );
    $post_status = get_post_status( $post_ID );

    if ($post_type && isset($_POST["tfp_post_translation_enable"])) { //validate
    	update_post_meta($post_ID, "tfp_post_translation_enable", $_POST["tfp_post_translation_enable"]);
    }
   return $post_ID;
}

add_action('save_post', 'tfp_savePostTranslationOption');

/**
 * Function for generating post translation DB entries on Book translations enable ('Display translations' on EFP Customizations page)
 *
 * @since 1.2.6
 *
 */
function tfp_generatePostTranslationEntries(  ){
	$post_types = ['metadata','front-matter','chapter','part', 'back-matter'];

	$args = array(
	    'fields'          => 'ids',
			'posts_per_page'  => -1,
			'post_type' => $post_types
		);

	$posts = get_posts($args);

	foreach ($posts as $key => $ID) {
		 update_post_meta( $ID, 'tfp_post_translation_enable', '1', false);
	}
}
