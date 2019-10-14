<?php
/**
 * This file is responsible for clearing the Database of all plugin entries.
 *
 * @package           translations for pressbooks
 * @since             1.2.6
 *
 */

register_uninstall_hook( 'translations-for-pressbooks/translations-for-pressbooks.php', 'tfp_clear_plugin_data' );

/**
 *	If 'tfp_uninstall_save' option is not checked clears all plugin DB data on uninstall.
 *
 * @since 1.2.6
 *
 */
function tfp_clear_plugin_data(){
	if (1 != get_site_option( 'tfp_uninstall_save' )){

		global $wpdb;

		$post_types = ['metadata','front-matter','chapter','part', 'back-matter'];
		$args = array(
		    'fields'          => 'ids',
				'posts_per_page'  => -1,
				'post_type' => $post_types
			);

		$sites = get_sites($args);

		// go through all the sites(books) and delete entries which were created by the plugin.
		 foreach ( $sites as $site ) {
				switch_to_blog( $site );

				$wpdb->query(
					$wpdb->prepare(
							"
					     DELETE FROM $wpdb->postmeta
							 WHERE meta_key = %s",
				         'tfp_post_translation_enable'
			        )
						);

				$wpdb->query(
					$wpdb->prepare(
							"
							 DELETE FROM $wpdb->options
							 WHERE option_name = %s
							 OR option_name = %s
							 ",
								 'tfp_post_translations_save', 'tfp_book_translation_enable'
							)
						);

				 restore_current_blog();
		 }
	 }
	 	return;
}
