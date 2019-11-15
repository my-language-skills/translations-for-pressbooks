<?php
/**
 * Print hreflang tags in <head> of the page.
 *
 * @package           translations for pressbooks
 * @since             1.2.5
 * @package           translations-for-pressbooks
 *
 */

defined ("ABSPATH") or die ("No script assholes!");

//add action to print to theme <head> tag
add_action('wp_head', 'tfp_printHreflangTags');

function tfp_printHreflangTags(){
  $blog_id = get_current_blog_id();

  global $wpdb;
  global $wp;

  $toprint = "";

  // identify if book is translation or not and get the source book ID, first part of the function is from tfp_checkTrans()
  //switch_to_blog($blog_id);
    $trans_lang = get_post_meta(tfp_getInfoPost(), 'efp_trans_language') ?: 'not_set';
    $source = get_post_meta(tfp_getInfoPost(), 'pb_is_based_on', true) ?: 'original';

    if ($source == 'original'){
      $origin_id = $blog_id; // origin id is the id for the original book
    } else {
      $origin = str_replace(['http://', 'https://'], '', $source).'/';
      switch_to_blog(1);
        $origin_id = $wpdb->get_results("SELECT `blog_id` FROM $wpdb->blogs WHERE CONCAT(`domain`, `path`) = '$origin'", ARRAY_A)[0]['blog_id'];
      restore_current_blog();
    }

  switch_to_blog(1);
    $relations = $wpdb->get_row("SELECT * FROM {$wpdb->prefix}trans_rel WHERE `a` = '$origin_id'", ARRAY_A);
  restore_current_blog();

  $current_link = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";

  //go through all translations and print them as hreflang tag in the <head>
  if(!empty($relations)){
    $languageArrayObject = new ArrayObject($relations);

      $toprint .= "\n<!-- Translations for pressbooks -->\n";
      foreach ($languageArrayObject as $lang => $id) {
        if ($id == 0) {
            continue;
        } elseif ($lang == "a"){
            $toprint .= "<link rel='alternate' hreflang='x-default' href='".str_replace(get_blog_details(get_current_blog_id())->path, get_blog_details($id)->path, $current_link)."'/>\n";
            $toprint .= "<link rel='alternate' hreflang='".tfp_getOriginalBookLanguage($blog_id)."' href='".str_replace(get_blog_details(get_current_blog_id())->path, get_blog_details($id)->path, $current_link)."'/>\n";
            continue;
        } else {
            $toprint .= "<link rel='alternate' hreflang='". $lang ."' href='".str_replace(get_blog_details(get_current_blog_id())->path, get_blog_details($id)->path, $current_link)."'/>\n";
        }
      }
      $toprint .= "<!-- Translations for pressbooks END -->\n\n";
    }
    echo $toprint;
}
