<?php
/**
 * This file is responsible for compact management of the Displaying translation options in the front-end.
 * Works with conjunction of extensions-for-pressbooks plugin v1.2.4
 * Creates setting section in EFP Customization menu and 'Display post translations' metabox in post-edit page.
 * Also is responsible for generate_post_translation_entries in the DB.
 *
 * @package           translations for pressbooks
 * @since             1.2.6
 *
 */

defined ("ABSPATH") or die ("Action denied!");


if ((1 != get_current_blog_id()	|| !is_multisite()) && is_plugin_active('pressbooks/pressbooks.php')){
		if (is_plugin_active('extensions-for-pressbooks/extensions-for-pressbooks.php')) {
			add_action('admin_init','tfp_init_book_trans_section');
			if ( isset( $_REQUEST['settings-updated'])  && ("theme-customizations" == $_REQUEST['page']) ) {
					if("1" == $book_translation_enable = get_option( 'tfp_book_translation_enable' ) && "1" != $post_translations_save = get_option( 'tfp_post_translations_save' )){
							generate_post_translation_entries(); // generate translation entries in DB on 'Display translations' checkbox enable.
					}
			}
		}

		if ( "1" == $option = get_option( 'tfp_book_translation_enable' )){
			// If 'Display translations' checkbox enabled render metabox in post-edit page (chapters, book-info...).
			add_action('custom_metadata_manager_init_metadata', 'tfp_init_post_translations_section');
		}
}

/* --- BOOK translations --- */

/**
 * Render page sections.
 *
 * @since 1.2.6
 *
 */
function tfp_init_book_trans_section(){

	 	add_settings_section( 'translations_section',
	 												'Translations section',
	 												'translations_section_description',
	 												'theme-customizations');

		add_settings_field(	'tfp_book_translation_enable',
												'Display translations',
												'tfp_book_translation_callback',
												'theme-customizations',
												'translations_section'); //add settings field to the translations_section

		add_option('tfp_book_translation_enable',0); // add theme option to database

		add_settings_field(	'tfp_post_translations_save',
												'Save previous post values',
												'tfp_save_post_translations_callback',
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
function translations_section_description(){
	echo '<p>In order to see translations in the front-end it is necessary to enable "Display translations" option for each BOOK. </p>';
	echo '<p>Every time "Display translations" is enabled, every POST translation selections gets enabled automatically.</p>';
	echo '<p>In order to keep previous POST translations selections check "Save previous post values".</p>';
}

/**
 * Render page 'Display translation' checkbox.
 *
 * @since 1.2.6
 *
 */
function tfp_book_translation_callback(){
	$option = get_option( 'tfp_book_translation_enable' );
	echo '<input name="tfp_book_translation_enable" id="tfp_book_translation_enable" type="checkbox" value="1" class="code" ' . checked( 1, $option, false ) . ' /> Check to enable translations for this BOOK.';
}

/**
 * Save section settings to the DB.
 *
 * @since 1.2.6
 *
 */
function tfp_save_post_translations_callback(){
	if ("1" == $tfp_book_translation_enable = get_option( 'tfp_book_translation_enable' )) {

		$option = get_option( 'tfp_post_translations_save' );
		echo '<input name="tfp_post_translations_save" id="tfp_post_translations_save" type="checkbox" value="1" ' . checked( 1, $option, false )  . ' class="code"  /> Check to keep POST translations selections saved.';

	} else {

		$option = get_option( 'tfp_post_translations_save' );
			// Enable 'tfp_post_translations_save' checkbox only if 'tfp_book_translation_enable' is enabled.
		if ($option == "1"){
			echo '<style>#tfp_post_translations_save{ display:none; } </style>';
			echo '<input name="tfp_post_translations_save" id="tfp_post_translations_save" type="checkbox" value="1" ' . checked( 1, $option, false )  . ' class="code"  /> ';
			echo '<input type="checkbox" value="1" checked="checked" class="code" disabled /> Check to keep POST translations selections saved. (To modify enable "Display translations")';
		} else {
			echo '<style>#tfp_post_translations_save{ display:none; } </style>';
			echo '<input name="tfp_post_translations_save" id="tfp_post_translations_save" type="checkbox" value="1" ' . checked( 1, $option, false )  . ' class="code"  /> ';
			echo '<input type="checkbox" value="1"  class="code" disabled /> Check to keep POST translations selections saved.   (To modify enable "Display translations") ';
		}
	}
}


/* --- POST translations --- */

/**
 * Initializes metabox in post-edit page.
 *
 * @since 1.2.6
 *
 */
function tfp_init_post_translations_section () {
		$post_types = ['metadata','front-matter','chapter','part', 'back-matter'];
		add_meta_box( 'tre_translation_checkbox', 'Display post translations', 'tfp_render_post_metabox', $post_types, 'side', 'low');
}

/**
 * Renders metabox in post-edit page.
 *
 * @since 1.2.6
 *
 */
function tfp_render_post_metabox(){

    global $post;
    $custom = get_post_custom($post->ID);
		$result = get_post_meta($post->ID, 'tfp_post_translation_enable', true);
  	if ($result != "1"){
      add_post_meta( $post->ID, 'tfp_post_translation_enable', '1', true );
    }

		$option = get_post_meta($post->ID, 'tfp_post_translation_enable', true);

		echo '<input name="tfp_post_translation_enable" id="tfp_post_translation_enable" type="checkbox" value="1" class="code" ' . checked( 1, $option, false ) . ' /> Check to enable for this post.';
}

/**
 * Save post option from 'Post translations display' checkbox on post-edit page.
 *
 * @since 1.2.6
 *
 */
function save_post_option($post_ID = 0) {
    $post_ID = (int) $post_ID;
    $post_type = get_post_type( $post_ID );
    $post_status = get_post_status( $post_ID );

    if ($post_type) {
    update_post_meta($post_ID, "tfp_post_translation_enable", $_POST["tfp_post_translation_enable"]);
    }
   return $post_ID;
}
add_action('save_post', 'save_post_option');


/* --- FUNCTIONS --- */

/**
 * Function for generating post translation DB entries on Book translations enable ('Display translations' on EFP Customizations page)
 *
 * @since 1.2.6
 *
 */
function generate_post_translation_entries(  ){

	$post_types = ['metadata','front-matter','chapter','part', 'back-matter'];

	$args = array(
	    'fields'          => 'ids',
			'posts_per_page'  => -1,
			'post_type' => $post_types
		);

	$posts = get_posts($args);

	foreach ($posts as $key => $ID) {
		 add_post_meta( $ID, 'tfp_post_translation_enable', '1', true );
	}
}
