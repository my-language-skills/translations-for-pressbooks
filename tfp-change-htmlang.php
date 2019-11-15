<?php
/**
 * Change default htmlang from default Wordpress "en_US" to "en"
 *
 * @package           translations for pressbooks
 * @since             1.2.5
 * @package           translations-for-pressbooks
 *
 */

defined ("ABSPATH") or die ("No script assholes!");

add_filter( 'language_attributes', 'tfp_changeHeaderLanguageAttribute');

function tfp_changeHeaderLanguageAttribute( $output ){
  if ( preg_match( '#lang="[a-z-]+"#i', $output ) ) {
    if(strrpos ( $output , 'en-US' )){
      $output = preg_replace( '#lang="([a-z-]+)"#i', 'lang="en"', $output );
    }
  }
  return $output;
}
