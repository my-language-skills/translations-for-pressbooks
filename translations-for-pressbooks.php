<?php

/*

Plugin Name:  Translations for PressBooks
Plugin URI:   https://developer.wordpress.org/plugins/the-basics/
Description:  Add translations tools
Version:      1.2
Author:       @huguespages (My Language Skills)
Author URI:   https://developer.wordpress.org/
License:      GPL 3.0
License URI:  https://www.gnu.org/licenses/gpl-3.0.html
Text Domain:  translations-for-pressbooks
Domain Path:  /languages
*/
defined ("ABSPATH") or die ("No script assholes!");
include_once plugin_dir_path( __FILE__ ) . "original-mark/original-mark.php";

add_action('wp_ajax_efp_mark_as_original', 'tre_update_trans_table', 2);
add_action('custom_metadata_manager_init_metadata', 'tre_create_language_box', 10);

/**
 * Function responsible for creation/updating translations table in database
 */
function tre_update_trans_table () {

	//security check
	if ( ! current_user_can( 'manage_network' ) || ! check_ajax_referer( 'pressbooks-aldine-admin' ) ) {
		return;
	}

	global $wpdb;

	$table_name = $wpdb->prefix . 'trans_rel'; //table in database

	//>> check if the book was marked as translation of another book

	switch_to_blog($_POST['book_id']);

	$info_post_id = tre_get_info_post();

	$trans_lang = get_post_meta($info_post_id, 'efp_trans_language') ?: 'not_set';
	//<<

	switch_to_blog( 1 );

	//if book was marked as original, not unmarked
	if (1 == get_blog_option($_POST['book_id'], 'efp_publisher_is_original')){

		//if book was not marked as translation, create a new row in translations table
		if ($trans_lang == 'non_tr' || $trans_lang == 'not_set') {

			//if translations table doesn't exist, create
			if ( $wpdb->get_var( "SHOW TABLES LIKE '$table_name'" ) != $table_name ) {
				//table not in database. Create new table

				$charset_collate = $wpdb->get_charset_collate();

				$sql = "CREATE TABLE $table_name (
          		a bigint(20) NOT NULL,
          		UNIQUE KEY a (a)
     			) $charset_collate;";

				require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
				dbDelta( $sql );
			}

			$wpdb->insert( $table_name, [ 'a' => absint( $_POST['book_id'] ) ] );

		} elseif(isset($trans_lang)) {
			//book is a translation, add it as a translation to original one

			//get translation's language
			switch_to_blog($_POST['book_id']);
			$lang = get_post_meta($info_post_id, 'pb_language', true);
			$origin = str_replace(['http://', 'https://'], '', get_post_meta($info_post_id, 'pb_is_based_on', true)).'/';
			//The str_replace() function replaces some characters with some other characters in a string.
			// str_replace(find,replace,string,count)

			//>> Add column if not present.
			switch_to_blog(1);
			$check = $wpdb->get_row("SELECT * FROM $table_name;");
//Isset The isset () function is used to check whether a variable is set or not.
// If a variable is already unset with unset() function, it will no longer be set.
//The isset() function return false if testing variable contains a NULL value.

//! $a 	Not (Non) 	TRUE si $a n'est pas TRUE.
			if(!isset($check->$lang)){
   			 	$wpdb->query("ALTER TABLE $table_name ADD $lang BIGINT(20);");
			}
			//<<

			$origin_id = $wpdb->get_results("SELECT `blog_id` FROM $wpdb->blogs WHERE CONCAT(`domain`, `path`) = '$origin'", ARRAY_A)[0]['blog_id'];

			$wpdb->query("UPDATE $table_name SET $lang = '$_POST[book_id]' WHERE `a` = '$origin_id';");

		}
	} else {

		if ($trans_lang == 'non_tr' || $trans_lang == 'not_set'){
			$trans = $wpdb->get_row("SELECT * FROM $table_name WHERE `a` = '$_POST[book_id]';", ARRAY_A);
			unset($trans['a']);
			$wpdb->query("DELETE FROM $table_name WHERE `a` = '$_POST[book_id]'");
			foreach ($trans as $tran){
				delete_blog_option($tran, 'efp_publisher_is_original');
			}
		} elseif (isset($trans_lang)) {
			switch_to_blog($_POST['book_id']);
			$origin = str_replace(['http://', 'https://'], '', get_post_meta($info_post_id, 'pb_is_based_on', true)).'/';
			$lang = get_post_meta($info_post_id, 'pb_language', true);
			switch_to_blog(1);
			$origin_id = $wpdb->get_results("SELECT `blog_id` FROM $wpdb->blogs WHERE CONCAT(`domain`, `path`) = '$origin'", ARRAY_A)[0]['blog_id'];
			$wpdb->query("UPDATE $table_name SET `$lang` = '' WHERE `a` = '$origin_id';");
		}

	}
}


/**
 * Function for producing metabox for selecting translation language
 */
function tre_create_language_box () {

	if (get_post_meta(tre_get_info_post(),'pb_is_based_on')) {

		x_add_metadata_group( 'efp_trans', 'metadata', array(
			'label'    => 'Studying content',
			'priority' => 'high'
		) );

		x_add_metadata_field( 'efp_trans_language', 'metadata', array(
				'group'            => 'efp_trans',
				'field_type'       => 'select',
				'values'           => [
					'non_tr' => 'Not translation',
					'aa'     => 'Afar',
					'ab'     => 'Abkhazian',
					'ae'     => 'Avestan',
					'af'     => 'Afrikaans',
					'ak'     => 'Akan',
					'am'     => 'Amharic',
					'an'     => 'Aragonese',
					'ar'     => 'Arabic',
					'as'     => 'Assamese',
					'av'     => 'Avaric',
					'ay'     => 'Aymara',
					'az'     => 'Azerbaijani',
					'ba'     => 'Bashkir',
					'be'     => 'Belarusian',
					'bg'     => 'Bulgarian',
					'bh'     => 'Bihari languages',
					'bm'     => 'Bambara',
					'bi'     => 'Bislama',
					'bn'     => 'Bengali',
					'bo'     => 'Tibetan',
					'br'     => 'Breton',
					'bs'     => 'Bosnian',
					'ce'     => 'Chechen',
					'ch'     => 'Chamorro',
					'co'     => 'Corsican',
					'cr'     => 'Cree',
					'cs'     => 'Czech',
					'cv'     => 'Chuvash',
					'cy'     => 'Welsh',
					'da'     => 'Danish',
					'de'     => 'German',
					'dv'     => 'Maldivian',
					'dz'     => 'Dzongkha',
					'ee'     => 'Ewe',
					'el'     => 'Greek',
					'en'     => 'English',
					'eo'     => 'Esperanto',
					'es'     => 'Spanish',
					'et'     => 'Estonian',
					'eu'     => 'Basque',
					'fa'     => 'Persian',
					'ff'     => 'Fulah',
					'fi'     => 'Finnish',
					'fj'     => 'Fijian',
					'fo'     => 'Faroese',
					'fr'     => 'French',
					'fy'     => 'Western Frisian',
					'ga'     => 'Irish',
					'gd'     => 'Gaelic',
					'gl'     => 'Galician',
					'gn'     => 'Guarani',
					'gu'     => 'Gujarati',
					'gv'     => 'Manx',
					'ha'     => 'Hausa',
					'he'     => 'Hebrew',
					'hi'     => 'Hindi',
					'ho'     => 'Hiri Motu',
					'hr'     => 'Croatian',
					'ht'     => 'Haitian',
					'hu'     => 'Hungarian',
					'hy'     => 'Armenian',
					'hz'     => 'Herero',
					'ia'     => 'Interlingua',
					'id'     => 'Indonesian',
					'ie'     => 'Interlingue',
					'ig'     => 'Igbo',
					'ii'     => 'Sichuan Yi',
					'ik'     => 'Inupiaq',
					'io'     => 'Ido',
					'is'     => 'Icelandic',
					'it'     => 'Italian',
					'iu'     => 'Inuktitut',
					'ja'     => 'Japanese',
					'jv'     => 'Javanese',
					'ka'     => 'Georgian',
					'kg'     => 'Kongo',
					'ki'     => 'Kikuyu; Gikuyu',
					'kj'     => 'Kuanyama; Kwanyama',
					'kk'     => 'Kazakh',
					'kl'     => 'Kalaallisut; Greenlandic',
					'km'     => 'Central Khmer',
					'kn'     => 'Kannada',
					'ko'     => 'Korean',
					'kr'     => 'Kanuri',
					'ks'     => 'Kashmiri',
					'ku'     => 'Kurdish',
					'kv'     => 'Komi',
					'kw'     => 'Cornish',
					'ky'     => 'Kirghiz; Kyrgyz',
					'la'     => 'Latin',
					'lb'     => 'Luxembourgish; Letzeburgesch',
					'lg'     => 'Ganda',
					'li'     => 'Limburgan; Limburger; Limburgish',
					'ln'     => 'Lingala',
					'lo'     => 'Lao',
					'lt'     => 'Lithuanian',
					'lu'     => 'Luba-Katanga',
					'lv'     => 'Latvian',
					'mg'     => 'Malagasy',
					'mh'     => 'Marshallese',
					'mi'     => 'Maori',
					'mk'     => 'Macedonian',
					'ml'     => 'Malayalam',
					'mn'     => 'Mongolian',
					'mr'     => 'Marathi',
					'ms'     => 'Malay',
					'mt'     => 'Maltese',
					'my'     => 'Burmese',
					'na'     => 'Nauru',
					'nb'     => 'Bokmål, Norwegian; Norwegian Bokmål',
					'nd'     => 'Ndebele, North; North Ndebele',
					'ne'     => 'Nepali',
					'ng'     => 'Ndonga',
					'nl'     => 'Dutch; Flemish',
					'nn'     => 'Norwegian Nynorsk; Nynorsk, Norwegian',
					'no'     => 'Norwegian',
					'nr'     => 'Ndebele, South; South Ndebele',
					'nv'     => 'Navajo; Navaho',
					'ny'     => 'Chichewa; Chewa; Nyanja',
					'oc'     => 'Occitan; Provençal',
					'oj'     => 'Ojibwa',
					'om'     => 'Oromo',
					'or'     => 'Oriya',
					'os'     => 'Ossetian; Ossetic',
					'pa'     => 'Panjabi; Punjabi',
					'pi'     => 'Pali',
					'pl'     => 'Polish',
					'ps'     => 'Pushto; Pashto',
					'pt'     => 'Portuguese',
					'qu'     => 'Quechua',
					'rm'     => 'Romansh',
					'rn'     => 'Rundi',
					'ro'     => 'Romanian; Moldavian; Moldovan',
					'ru'     => 'Russian',
					'rw'     => 'Kinyarwanda',
					'sa'     => 'Sanskrit',
					'sc'     => 'Sardinian',
					'sd'     => 'Sindhi',
					'se'     => 'Northern Sami',
					'sg'     => 'Sango',
					'si'     => 'Sinhala; Sinhalese',
					'sk'     => 'Slovak',
					'sl'     => 'Slovenian',
					'sm'     => 'Samoan',
					'sn'     => 'Shona',
					'so'     => 'Somali',
					'sq'     => 'Albanian',
					'sr'     => 'Serbian',
					'ss'     => 'Swati',
					'st'     => 'Sotho, Southern',
					'su'     => 'Sundanese',
					'sv'     => 'Swedish',
					'sw'     => 'Swahili',
					'ta'     => 'Tamil',
					'te'     => 'Telugu',
					'tg'     => 'Tajik',
					'th'     => 'Thai',
					'ti'     => 'Tigrinya',
					'tk'     => 'Turkmen',
					'tl'     => 'Tagalog',
					'tn'     => 'Tswana',
					'to'     => 'Tonga',
					'tr'     => 'Turkish',
					'ts'     => 'Tsonga',
					'tt'     => 'Tatar',
					'tw'     => 'Twi',
					'ty'     => 'Tahitian',
					'ug'     => 'Uighur; Uyghur',
					'uk'     => 'Ukrainian',
					'ur'     => 'Urdu',
					'uz'     => 'Uzbek',
					'vl'     => 'Valencian',
					've'     => 'Venda',
					'vi'     => 'Vietnamese',
					'vo'     => 'Volapük',
					'wa'     => 'Walloon',
					'wo'     => 'Wolof',
					'xh'     => 'Xhosa',
					'yi'     => 'Yiddish',
					'yo'     => 'Yoruba',
					'za'     => 'Zhuang; Chuang',
					'zh'     => 'Chinese',
					'zu'     => 'Zulu'
				],
				'label'            => 'Language',
				'description'      => 'Choose language, which original book is about (if current book is original, choose "Not translation option")',
				'display_callback' => ''
			)
		);
	}
}

/**
 * Function for getting book info post ID
 */
function tre_get_info_post () {

	global $wpdb;
	$info_post = $wpdb->get_results("SELECT `ID` FROM $wpdb->posts WHERE `post_type` = 'metadata' LIMIT 1", ARRAY_A);

	return isset($info_post[0]['ID']) ? $info_post[0]['ID'] : 0;

}

/**
 * Function to check if there are translations for this book
 */
function pbc_check_trans($blog_id) {
	global $wpdb;
 	global $wp;

 	//>> identify if book is translation or not and get the source book ID
 	switch_to_blog($blog_id);
 	$trans_lang = get_post_meta(tre_get_info_post(), 'efp_trans_language') ?: 'not_set';
 	$source = get_post_meta(tre_get_info_post(), 'pb_is_based_on', true) ?: 'original';
 	if ($source == 'original'){
 		$origin_id = $blog_id; // origin id is the id for the original book
 	} else {
 		$origin = str_replace(['http://', 'https://'], '', $source).'/';
		switch_to_blog(1);
		$origin_id = $wpdb->get_results("SELECT `blog_id` FROM $wpdb->blogs WHERE CONCAT(`domain`, `path`) = '$origin'", ARRAY_A)[0]['blog_id'];
	}
	//<<
	//fetching all related translations
 	switch_to_blog(1);
 	$relations = $wpdb->get_row("SELECT * FROM {$wpdb->prefix}trans_rel WHERE `a` = '$origin_id'", ARRAY_A);
 	restore_current_blog();
 	if (!empty($relations)){
 		return true;
 	} else {
 		return false;
 	}
}

/**
 * Function for printing links to translations
 */
 function pbc_print_trans_links($blog_id){

 	global $wpdb;
 	global $wp;

 	//>> identify if book is translation or not and get the source book ID
 	switch_to_blog($blog_id);
 	$trans_lang = get_post_meta(tre_get_info_post(), 'efp_trans_language') ?: 'not_set';
 	$source = get_post_meta(tre_get_info_post(), 'pb_is_based_on', true) ?: 'original';
 	if ($source == 'original'){
 		$origin_id = $blog_id;
 	} else {
 		$origin = str_replace(['http://', 'https://'], '', $source).'/';
		switch_to_blog(1);
		$origin_id = $wpdb->get_results("SELECT `blog_id` FROM $wpdb->blogs WHERE CONCAT(`domain`, `path`) = '$origin'", ARRAY_A)[0]['blog_id'];
		restore_current_blog();

	}
	//<<
	//fetching all related translations
 	switch_to_blog(1);
// SELECT column_name FROM information_schema.columns WHERE table_name = 'pb_int_wp_trans_rel' AND table_schema='colomet_pb_int' ORDER BY column_name ASC
// SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS  WHERE TABLE_NAME LIKE 'pb_int_wp_trans_rel' ORDER BY column_name ASC
 	$relations = $wpdb->get_row("SELECT * FROM {$wpdb->prefix}trans_rel WHERE `a` = '$origin_id'", ARRAY_A);
 	restore_current_blog(); //Contrary to the function's name, this does NOT restore the original blog but the previous blog. Calling `switch_to_blog()` twice in a row and then calling this function will result in being on the blog set by the first `switch_to_blog()` call.
 	//if book is orginal, unset 'id' property, as no need to point itself
 	if($source == 'original'){
 		unset($relations['a']);
 	}

 	$current_link = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
 	$flag = 0;
 	if(!empty($relations)){
   $languageArrayObject = new ArrayObject($relations);
   $languageArrayObject->ksort();

 		foreach ($languageArrayObject as $lang => $id) {
 			$separator = $flag ? '|' : '';
 			if ($id == $blog_id || $id == 0){
 				continue;
 			} elseif ($lang == 'a'){
 				echo '<li><a href="'.$source.'/'.add_query_arg( array(), $wp->request ).'">'.__('Original Language', 'pressbooks-book').'</a></li>';
 				$flag = 1;
 				continue;
 			}

 			echo '<li>'.$separator.' <a href="'.str_replace(get_blog_details(get_current_blog_id())->path, get_blog_details($id)->path, $current_link).'">'.$lang.'</a> </li>';
			//transform the language code into flag picture but you have to comment the line before this
			//echo '<li>'.$separator.' <a href="'.str_replace(get_blog_details(get_current_blog_id())->path, get_blog_details($id)->path, $current_link).'"><img onmouseover="bigImg(this)" onmouseout="normalImg(this)"  width="16" height="11" src="/wp-content/plugins/extensions-for-pressbooks/flag-icon/'.$lang.'.png" </a> </li>';

 			$flag = 1;
 		}
 	}
 	if ($source != 'original' && ($trans_lang == 'not_set' || $trans_lang == 'non_tr')){
 		echo '<li><a href="'.$source.'/'.add_query_arg( array(), $wp->request ).'">'.__('Original Book', 'pressbooks-book').'</a></li>';
 	}
	//unknown bug fix
	restore_current_blog();
 }
 /*
 * Auto update from github
 *
 * @since 4.6
 */
 require 'vendor/plugin-update-checker/plugin-update-checker.php';
 $myUpdateChecker = Puc_v4_Factory::buildUpdateChecker(
     'https://github.com/my-language-skills/translations-for-pressbooks/',
     __FILE__,
     'translations-for-pressbooks'
 );

 ?>

 <?php
