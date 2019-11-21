<?php

/**
 * Translations for PressBooks
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://github.com/my-language-skills/translations-for-pressbooks
 * @since             1.0
 * @package           translations-for-pressbooks
 *
 * @wordpress-plugin
 * Plugin Name:       Translations for PressBooks
 * Plugin URI:        https://github.com/my-language-skills/translations-for-pressbooks
 * Description:       Generates and displays translations of books.
 * Version:           1.2.7
 * Pressbooks tested up to: 5.10
 * Author:            My Language Skills team
 * Author URI:        https://github.com/my-language-skills/
 * License:           GPL 3.0
 * License URI:       http://www.gnu.org/licenses/gpl-3.0.txt
 * Text Domain:       translations-for-pressbooks
 * Domain Path:       /languages
 * Network: 					True
 */

defined ("ABSPATH") or die ("No script assholes!");

include_once plugin_dir_path( __FILE__ ) . "tfp-print-hreflang.php";
include_once plugin_dir_path( __FILE__ ) . "tfp-change-htmlang.php";
include_once plugin_dir_path( __FILE__ ) . "tfp-translation-enabler.php";
include_once plugin_dir_path( __FILE__ ) . "tfp-network-settings.php";

add_action('wp_ajax_efp_mark_as_original', 'tfp_updateTransTable', 2);
add_action('admin_init', 'tfp_createLanguageBox');

/**
* Function responsible for creation/updating translations table in database (admin area)
*
* @since
*
*/
function tfp_updateTransTable () {

	//security check
	if ( ! current_user_can( 'manage_network' ) || ! check_ajax_referer( 'pressbooks-aldine-admin' ) ) {
		return;
	}

	global $wpdb;

	if (!empty($_POST['book_id'])){
		$post_book_id = (int) $_POST['book_id'];
	}

	$table_name = $wpdb->prefix . 'trans_rel'; //table in database

	//>> check if the book was marked as translation of another book
	switch_to_blog($post_book_id);
		$info_post_id = tfp_getInfoPost();
		$trans_lang = get_post_meta($info_post_id, 'efp_trans_language') ?: 'not_set';
	switch_to_blog( 1 );

	//if book was marked as original, not unmarked
	if (1 == get_blog_option($post_book_id, 'efp_publisher_is_original')){

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

				$wpdb->insert( $table_name, [ 'a' => absint( $post_book_id ) ] );

		} elseif(isset($trans_lang)) {
				//book is a translation, add it as a translation to original one

				//get translation's language
				switch_to_blog($post_book_id);
				$lang = get_post_meta($info_post_id, 'pb_language', true);
				$origin = str_replace(['http://', 'https://'], '', get_post_meta($info_post_id, 'pb_is_based_on', true)).'/';
				//The str_replace() function replaces some characters with some other characters in a string.
				// str_replace(find,replace,string,count)

				//>> Add column if not present.
				switch_to_blog(1);
				$check = $wpdb->get_row("SELECT * FROM $table_name;");

				//! $a 	Not (Non) 	TRUE si $a n'est pas TRUE.
				if(!isset($check->$lang)){
	   			 	$wpdb->query("ALTER TABLE $table_name ADD $lang BIGINT(20);");
				}
				//<<

				$origin_id = $wpdb->get_results("SELECT `blog_id` FROM $wpdb->blogs WHERE CONCAT(`domain`, `path`) = '$origin'", ARRAY_A)[0]['blog_id'];
				$wpdb->query("UPDATE $table_name SET $lang = '$post_book_id' WHERE `a` = '$origin_id';");
		}

	} else {

		if ($trans_lang == 'non_tr' || $trans_lang == 'not_set'){
			$trans = $wpdb->get_row("SELECT * FROM $table_name WHERE `a` = '$post_book_id';", ARRAY_A);
			unset($trans['a']);
			$wpdb->query("DELETE FROM $table_name WHERE `a` = '$post_book_id'");
			foreach ($trans as $tran){
				delete_blog_option($tran, 'efp_publisher_is_original');
			}
		} elseif (isset($trans_lang)) {
			switch_to_blog($post_book_id);
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
*
* @since
*
*/

function tfp_createLanguageBox () {

	if (get_post_meta(tfp_getInfoPost(),'pb_is_based_on')) {

		x_add_metadata_group( 'efp_trans', 'metadata', array(
			'label'    => 'Studying content',
			'priority' => 'high'
		) );

		x_add_metadata_field( 'efp_trans_language', 'metadata', array(
				'group'            => 'efp_trans',
				'field_type'       => 'select',
				'values'           => [
					'non_tr' => 'Not translation',
					// 'aa'     => 'Afar',  //not attribut flag icons right now
					// 'ab'     => 'Abkhazian',  //not attribut flag icons right now
					// 'ae'     => 'Avestan', //not attribut flag icons right now
					// 'af'     => 'Afrikaans', //not attribut flag icons right now
					// 'ak'     => 'Akan', //not attribut flag icons right now
					// 'am'     => 'Amharic', //not attribut flag icons right now
					// 'an'     => 'Aragonese', //not attribut flag icons right now
					'ar'     => 'Arabic',
					// 'as'     => 'Assamese', //not attribut flag icons right now
					// 'av'     => 'Avaric', //not attribut flag icons right now
					// 'ay'     => 'Aymara', //not attribut flag icons right now
					// 'az'     => 'Azerbaijani', //not attribut flag icons right now
					// 'ba'     => 'Bashkir', //not attribut flag icons right now
					// 'be'     => 'Belarusian', //not attribut flag icons right now
					'bg'     => 'Bulgarian',
					// 'bh'     => 'Bihari languages', //not attribut flag icons right now
					// 'bm'     => 'Bambara', //not attribut flag icons right now
					// 'bi'     => 'Bislama', //not attribut flag icons right now
					// 'bn'     => 'Bengali', //not attribut flag icons right now
					// 'bo'     => 'Tibetan', //not attribut flag icons right now
					// 'br'     => 'Breton', //not attribut flag icons right now
					// 'bs'     => 'Bosnian', //not attribut flag icons right now
					// 'ce'     => 'Chechen', //not attribut flag icons right now
					// 'ch'     => 'Chamorro', //not attribut flag icons right now
					// 'co'     => 'Corsican', //not attribut flag icons right now
					// 'cr'     => 'Cree', //not attribut flag icons right now
					'cs'     => 'Czech',
					// 'cv'     => 'Chuvash', //not attribut flag icons right now
					// 'cy'     => 'Welsh', //not attribut flag icons right now
					'da'     => 'Danish',
					'de'     => 'German',
					// 'dv'     => 'Maldivian', //not attribut flag icons right now
					// 'dz'     => 'Dzongkha', //not attribut flag icons right now
					// 'ee'     => 'Ewe', //not attribut flag icons right now
					'el'     => 'Greek',
					'en'     => 'English',
					// 'eo'     => 'Esperanto', //not attribut flag icons right now
					'es'     => 'Spanish',
					'et'     => 'Estonian',
					// 'eu'     => 'Basque', //not attribut flag icons right now
					// 'fa'     => 'Persian', //not attribut flag icons right now
					// 'ff'     => 'Fulah', //not attribut flag icons right now
					'fi'     => 'Finnish',
					// 'fj'     => 'Fijian', //not attribut flag icons right now
					// 'fo'     => 'Faroese', //not attribut flag icons right now
					'fr'     => 'French',
					// 'fy'     => 'Western Frisian', //not attribut flag icons right now
					'ga'     => 'Irish',
					// 'gd'     => 'Gaelic', //not attribut flag icons right now
					// 'gl'     => 'Galician', //not attribut flag icons right now
					// 'gn'     => 'Guarani', //not attribut flag icons right now
					// 'gu'     => 'Gujarati', //not attribut flag icons right now
					// 'gv'     => 'Manx', //not attribut flag icons right now
					// 'ha'     => 'Hausa', //not attribut flag icons right now
					'he'     => 'Hebrew',
					'hi'     => 'Hindi',
					// 'ho'     => 'Hiri Motu', //not attribut flag icons right now
					'hr'     => 'Croatian',
					// 'ht'     => 'Haitian', //not attribut flag icons right now
					'hu'     => 'Hungarian',
					// 'hy'     => 'Armenian', //not attribut flag icons right now
					// 'hz'     => 'Herero', //not attribut flag icons right now
					// 'ia'     => 'Interlingua', //not attribut flag icons right now
					'id'     => 'Indonesian',
					// 'ie'     => 'Interlingue', //not attribut flag icons right now
					// 'ig'     => 'Igbo', //not attribut flag icons right now
					// 'ii'     => 'Sichuan Yi', //not attribut flag icons right now
					// 'ik'     => 'Inupiaq', //not attribut flag icons right now
					// 'io'     => 'Ido', //not attribut flag icons right now
					// 'is'     => 'Icelandic', //not attribut flag icons right now
					'it'     => 'Italian',
					// 'iu'     => 'Inuktitut', //not attribut flag icons right now
					'ja'     => 'Japanese',
					// 'jv'     => 'Javanese', //not attribut flag icons right now
					'ka'     => 'Georgian', //not attribut flag icons right now
					// 'kg'     => 'Kongo', //not attribut flag icons right now
					// 'ki'     => 'Kikuyu; Gikuyu', //not attribut flag icons right now
					// 'kj'     => 'Kuanyama; Kwanyama', //not attribut flag icons right now
					// 'kk'     => 'Kazakh', //not attribut flag icons right now
					// 'kl'     => 'Kalaallisut; Greenlandic', //not attribut flag icons right now
					// 'km'     => 'Central Khmer', //not attribut flag icons right now
					// 'kn'     => 'Kannada', //not attribut flag icons right now
					// 'ko'     => 'Korean', //not attribut flag icons right now
					// 'kr'     => 'Kanuri', //not attribut flag icons right now
					// 'ks'     => 'Kashmiri', //not attribut flag icons right now
					// 'ku'     => 'Kurdish', //not attribut flag icons right now
					// 'kv'     => 'Komi', //not attribut flag icons right now
					// 'kw'     => 'Cornish', //not attribut flag icons right now
					// 'ky'     => 'Kirghiz; Kyrgyz', //not attribut flag icons right now
					// 'la'     => 'Latin', //not attribut flag icons right now
					// 'lb'     => 'Luxembourgish; Letzeburgesch', //not attribut flag icons right now
					// 'lg'     => 'Ganda', //not attribut flag icons right now
					// 'li'     => 'Limburgan; Limburger; Limburgish', //not attribut flag icons right now
					// 'ln'     => 'Lingala', //not attribut flag icons right now
					// 'lo'     => 'Lao', //not attribut flag icons right now
					'lt'     => 'Lithuanian',
					// 'lu'     => 'Luba-Katanga', //not attribut flag icons right now
					'lv'     => 'Latvian',
					// 'mg'     => 'Malagasy', //not attribut flag icons right now
					// 'mh'     => 'Marshallese', //not attribut flag icons right now
					// 'mi'     => 'Maori', //not attribut flag icons right now
					// 'mk'     => 'Macedonian', //not attribut flag icons right now
					// 'ml'     => 'Malayalam', //not attribut flag icons right now
					// 'mn'     => 'Mongolian', //not attribut flag icons right now
					// 'mr'     => 'Marathi', //not attribut flag icons right now
					// 'ms'     => 'Malay', //not attribut flag icons right now
					'mt'     => 'Maltese',
					// 'my'     => 'Burmese', //not attribut flag icons right now
					// 'na'     => 'Nauru', //not attribut flag icons right now
					// 'nb'     => 'Bokmål, Norwegian; Norwegian Bokmål', //not attribut flag icons right now
					// 'nd'     => 'Ndebele, North; North Ndebele', //not attribut flag icons right now
					// 'ne'     => 'Nepali', //not attribut flag icons right now
					// 'ng'     => 'Ndonga', //not attribut flag icons right now
					'nl'     => 'Dutch; Flemish',
					// 'nn'     => 'Norwegian Nynorsk; Nynorsk, Norwegian', //not attribut flag icons right now
					// 'no'     => 'Norwegian', //not attribut flag icons right now
					// 'nr'     => 'Ndebele, South; South Ndebele', //not attribut flag icons right now
					// 'nv'     => 'Navajo; Navaho', //not attribut flag icons right now
					// 'ny'     => 'Chichewa; Chewa; Nyanja', //not attribut flag icons right now
					// 'oc'     => 'Occitan; Provençal', //not attribut flag icons right now
					// 'oj'     => 'Ojibwa', //not attribut flag icons right now
					// 'om'     => 'Oromo', //not attribut flag icons right now
					// 'or'     => 'Oriya', //not attribut flag icons right now
					// 'os'     => 'Ossetian; Ossetic', //not attribut flag icons right now
					// 'pa'     => 'Panjabi; Punjabi', //not attribut flag icons right now
					// 'pi'     => 'Pali', //not attribut flag icons right now
					'pl'     => 'Polish',
					// 'ps'     => 'Pushto; Pashto', //not attribut flag icons right now
					'pt'     => 'Portuguese',
					// 'qu'     => 'Quechua', //not attribut flag icons right now
					// 'rm'     => 'Romansh', //not attribut flag icons right now
					// 'rn'     => 'Rundi', //not attribut flag icons right now
					'ro'     => 'Romanian; Moldavian; Moldovan',
					'ru'     => 'Russian',
					// 'rw'     => 'Kinyarwanda', //not attribut flag icons right now
					// 'sa'     => 'Sanskrit', //not attribut flag icons right now
					// 'sc'     => 'Sardinian', //not attribut flag icons right now
					// 'sd'     => 'Sindhi', //not attribut flag icons right now
					// 'se'     => 'Northern Sami', //not attribut flag icons right now
					// 'sg'     => 'Sango', //not attribut flag icons right now
					// 'si'     => 'Sinhala; Sinhalese', //not attribut flag icons right now
					'sk'     => 'Slovak',
					'sl'     => 'Slovenian',
					// 'sm'     => 'Samoan', //not attribut flag icons right now
					// 'sn'     => 'Shona', //not attribut flag icons right now
					// 'so'     => 'Somali', //not attribut flag icons right now
					// 'sq'     => 'Albanian', //not attribut flag icons right now
					'sr'     => 'Serbian',
					// 'ss'     => 'Swati', //not attribut flag icons right now
					// 'st'     => 'Sotho, Southern', //not attribut flag icons right now
					// 'su'     => 'Sundanese', //not attribut flag icons right now
					'sv'     => 'Swedish',
					// 'sw'     => 'Swahili', //not attribut flag icons right now
					// 'ta'     => 'Tamil', //not attribut flag icons right now
					// 'te'     => 'Telugu', //not attribut flag icons right now
					// 'tg'     => 'Tajik', //not attribut flag icons right now
					// 'th'     => 'Thai', //not attribut flag icons right now
					// 'ti'     => 'Tigrinya', //not attribut flag icons right now
					// 'tk'     => 'Turkmen', //not attribut flag icons right now
					// 'tl'     => 'Tagalog', //not attribut flag icons right now
					// 'tn'     => 'Tswana', //not attribut flag icons right now
					// 'to'     => 'Tonga', //not attribut flag icons right now
					'tr'     => 'Turkish',
					// 'ts'     => 'Tsonga', //not attribut flag icons right now
					// 'tt'     => 'Tatar', //not attribut flag icons right now
					// 'tw'     => 'Twi', //not attribut flag icons right now
					// 'ty'     => 'Tahitian', //not attribut flag icons right now
					// 'ug'     => 'Uighur; Uyghur', //not attribut flag icons right now
					'uk'     => 'Ukrainian', //not attribut flag icons right now
					// 'ur'     => 'Urdu', //not attribut flag icons right now
					// 'uz'     => 'Uzbek', //not attribut flag icons right now
					// 'vl'     => 'Valencian', //not attribut flag icons right now
					// 've'     => 'Venda', //not attribut flag icons right now
					'vi'     => 'Vietnamese',
					// 'vo'     => 'Volapük', //not attribut flag icons right now
					// 'wa'     => 'Walloon', //not attribut flag icons right now
					// 'wo'     => 'Wolof', //not attribut flag icons right now
					// 'xh'     => 'Xhosa', //not attribut flag icons right now
					// 'yi'     => 'Yiddish', //not attribut flag icons right now
					// 'yo'     => 'Yoruba', //not attribut flag icons right now
					// 'za'     => 'Zhuang; Chuang', //not attribut flag icons right now
					'zh'     => 'Chinese'
					// 'zu'     => 'Zulu' //not attribut flag icons right now
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
*
* @since
*
*/

function tfp_getInfoPost () {
	global $wpdb;
	$info_post = $wpdb->get_results("SELECT `ID` FROM $wpdb->posts WHERE `post_type` = 'metadata' LIMIT 1", ARRAY_A);

	return isset($info_post[0]['ID']) ? $info_post[0]['ID'] : 0;
}

/**
* Function to check if there are translations for this book
*
* @since
*
*/

function tfp_checkTrans($blog_id) {
	global $wpdb;
 	global $wp;

 	//>> identify if book is translation or not and get the source book ID
 	switch_to_blog($blog_id);
	 	$trans_lang = get_post_meta(tfp_getInfoPost(), 'efp_trans_language') ?: 'not_set';
	 	$source = get_post_meta(tfp_getInfoPost(), 'pb_is_based_on', true) ?: 'original';
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
*
* @since
*
*/
/* print availible translations based on current book opened. Different print layouts for different page locations (header/ footer) */
 function tfp_printTransLinks($blog_id, $translations_print_location){

 	global $wpdb;
 	global $wp;

	$toprint = "";
 	//>> identify if book is translation or not and get the source book ID
 	switch_to_blog($blog_id);
	 	$trans_lang = get_post_meta(tfp_getInfoPost(), 'efp_trans_language') ?: 'not_set';
	 	$source = get_post_meta(tfp_getInfoPost(), 'pb_is_based_on', true) ?: 'original';
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
	$flagUrl = plugin_dir_url( __FILE__ ) . "assets/flag-icon/";

if(!empty($relations)){
   $languageArrayObject = new ArrayObject($relations);
   $languageArrayObject->ksort();

	 $current_lang_code = tfp_getCurrentBookLanguageCode();
	 $origin_lang_code = tfp_getOriginalBookLanguage($blog_id);

	  if ($translations_print_location == "header"){
		 foreach ($languageArrayObject as $lang => $id) {

		 	 	 if ($id == $blog_id){
					 // echo language code of currently selelected book
					 $toprint .= '<li class="dropdown-content-selected-lang"><a href="#"><img width="16" height="11" src=" '. $flagUrl .'' . $current_lang_code . '.png">&nbsp;'. $current_lang_code . '</a></li>';
					 continue;
				 } elseif ($id == 0) {
						 continue;
				 } elseif ($lang == 'a'){
					 // echo language code of original book
					 $toprint .= '<li ><a href="'.$source.'/'.add_query_arg( array(), $wp->request ).'" rel="nofollow"><img width="16" height="11" src=" '. $flagUrl .'' . $origin_lang_code . '.png">&nbsp;'. $origin_lang_code . ' ' . __('(original)', 'pressbooks-book').'</a></li>';
					 $flag = 1;
					 continue;
				 }

		   if ($flag == 0){
				 // echo language of original book in case it is currently selected
				 $toprint .= '<li class="dropdown-content-selected-lang"><a href="#"><img width="16" height="11" src=" '. $flagUrl .'' . $origin_lang_code . '.png">&nbsp;'. $origin_lang_code . ' ' . __('(original)', 'pressbooks-book').'</a></li>';
			 }
			 	// echo rest of the availible languages
 				$toprint .= '<li> <a href="'.str_replace(get_blog_details(get_current_blog_id())->path, get_blog_details($id)->path, $current_link).'" rel="nofollow"><img width="16" height="11" src=" '. $flagUrl .''.$lang.'.png">&nbsp;'.$lang.'</a> </li>';
  			$flag = 1;
		 }

	 } else if ($translations_print_location == "footer"){
		 	foreach ($languageArrayObject as $lang => $id) {
				 $separator = $flag ? '|' : '';

		 		 if ($id == $blog_id){
					 // echo language code of currently selelected book
					 $toprint .= '<li class="footer-lang-selected" >'.$separator.' <a href="#">'.$lang.'</a> </li>';
					 continue;
				 } elseif ($id == 0) {
						 continue;
				 } elseif ($lang == 'a'){
					 // echo language code of original book
					 $toprint .= '<li><a href="'.$source.'/'.add_query_arg( array(), $wp->request ).'">'. $origin_lang_code . ' ' .__('(original)', 'pressbooks-book').'</a></li>';
					 $flag = 1;
					 continue;
				 }
				 if ($flag == 0){
					 // echo language of original book in case it is currently selected
					 $toprint .= '<li class="footer-lang-selected">'.$separator.' <a href="#">'.$origin_lang_code.' ' .__('(original)', 'pressbooks-book').'</a>| </li>';
					}
					// echo rest of the availible languages
				 $toprint .= '<li>'.$separator.' <a href="'.str_replace(get_blog_details(get_current_blog_id())->path, get_blog_details($id)->path, $current_link).'">'.$lang.'</a> </li>';
			 	 $flag = 1;
			 }
	 }
 	}
 	if ($source != 'original' && ($trans_lang == 'not_set' || $trans_lang == 'non_tr')){
 		$toprint .= '<li><a href="'.$source.'/'.add_query_arg( array(), $wp->request ).'">'.__('Original Book', 'pressbooks-book').'</a></li>';
 	}
	restore_current_blog();
	echo $toprint;
 }

/**
* Functionality called from the front-end. Checks if 'tfp_book_translation_enable' enabled and if 'tfp_post_translation_disable' is not disabled.
* If so, returns "1" meaning it is enabled.
*
* @since 1.2.6
*
*/
	function tfp_checkIfTranslationsEnabled(){

		// first check if translation option for current book is enabled
		$tfp_book_translation_enable = get_option( 'tfp_book_translation_enable' );

		// second check if translation option for current post is enabled
		// for the cover page we want to get post_meta from book-info page. Folowed functionality finds out if we are on cover page, if yes we get data from book-info (metadata) page (not cover page)
		global $wpdb;
		$current_post_id = get_the_ID();

		$table_name = $wpdb->prefix . 'posts';
		$cover_id =  $wpdb->get_row("SELECT ID FROM $table_name WHERE post_name = 'cover';");
		$cover_id = get_object_vars($cover_id);
		$cover_id = reset($cover_id);

		//if $current_post_id and $cover_id are equal we change post_id from where to get translation option.
		if($current_post_id == $cover_id){
			$table_name = $wpdb->prefix . 'posts';
			$book_info_id = $wpdb->get_row("SELECT ID FROM $table_name WHERE post_name = 'book-info' OR post_name = 'book-information';");
			if(isset($book_info_id)){ // IF  book-info or book-information post found
				$book_info_id = get_object_vars($book_info_id);
				$book_info_id = reset($book_info_id);
				$tfp_post_translation_disable = get_post_meta($book_info_id, 'tfp_post_translation_disable', true);
			}
			} else {
				global $post;
				if (isset($post->ID)){
						$tfp_post_translation_disable = get_post_meta($post->ID, 'tfp_post_translation_disable', true);
				}
		}

		//if book translation and post translation are both set and enabled we display translations option.
		if (isset($tfp_book_translation_enable) && $tfp_book_translation_enable == "1" && $tfp_post_translation_disable != "1"){
			return "1";
		} else {
			return;
			}
}

// When called returns Language code of currently opened book.
function tfp_getCurrentBookLanguageCode(){

	global $wpdb;
	$meta_key="pb_language";
	$blog_id = get_current_blog_id();

	switch_to_blog($blog_id);
 		$lang = $wpdb->get_var( $wpdb->prepare("SELECT meta_value FROM $wpdb->postmeta WHERE meta_key = %s LIMIT 1" , $meta_key) );
	restore_current_blog();

	return $lang;
}

// When called returns Language flag of currently opened book.
function tfp_getCurrentBookFlag(){

	global $wpdb;
	$meta_key="pb_language";
	$blog_id = get_current_blog_id();

	switch_to_blog($blog_id);
 		$flag = $wpdb->get_var( $wpdb->prepare("SELECT meta_value FROM $wpdb->postmeta WHERE meta_key = %s LIMIT 1" , $meta_key) );
	restore_current_blog();

	$flagPath = plugin_dir_path( __FILE__ ) . "assets/flag-icon/$flag.png";
	$flagUrl = plugin_dir_url( __FILE__ ) . "assets/flag-icon/$flag.png";

	if (file_exists($flagPath)) {
		 return $langFlag = '<img width="16" height="11" src="' . $flagUrl . '">';
	} else {
	   return;
	}
}

// Identify if current book is translation or not and get the source book ID and eventualy correct language code.
function tfp_getOriginalBookLanguage($blog_id){

	global $wpdb;
 	global $wp;

	$source = get_post_meta(tfp_getInfoPost(), 'pb_is_based_on', true) ?: 'original';
	if ($source == 'original'){
	 $origin_id = $blog_id;
 } else {
	 $origin = str_replace(['http://', 'https://'], '', $source).'/';
	 switch_to_blog(1);
	 	$origin_id = $wpdb->get_results("SELECT `blog_id` FROM $wpdb->blogs WHERE CONCAT(`domain`, `path`) = '$origin'", ARRAY_A)[0]['blog_id'];
	 restore_current_blog();
 }

	$meta_key="pb_language";
	switch_to_blog($origin_id);
 		$lang = $wpdb->get_var( $wpdb->prepare("SELECT meta_value FROM $wpdb->postmeta WHERE meta_key = %s LIMIT 1" , $meta_key) );
	restore_current_blog();

	return $lang;
}

/**
* Sanitizes checkboxes based on passed expected values.
*
* @since 1.2.6
*
*/
function tfp_sanitize_checkbox( $input, $expected_value1, $expected_value2 ) {
    if ( $expected_value1 == $input || $expected_value2 == $input) {
        return $input;
    } else {
        return '';
    }
}
 ?>
