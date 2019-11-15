<?php
/**
 * In order to display Network settings of this plugin extensions-for-pressbooks plugin v1.2.4 must be activated.
 *	Creates plugin setting section in "EFP setting" on the network level.
 *
 * @package           translations for pressbooks
 * @since             1.2.6
 *
 */

defined ("ABSPATH") or die ("Action denied!");

if (( is_multisite()) && is_plugin_active('pressbooks/pressbooks.php') && is_plugin_active('extensions-for-pressbooks/extensions-for-pressbooks.php')){
     if (isset($_REQUEST['page']) && "efp-network-settings-page" == $_REQUEST['page']) {
       add_action('admin_init','tfp_renderTranslationsSection');
  	}
  }

/**
 *	Renders settings section in EFP settings
 *
 * @since 1.2.6
 *
 */
function tfp_renderTranslationsSection(){

    add_settings_section( 'net_translations_section',
                          'Translations section',
                          'tfp_netSectionDescription',
                          'tfp-network-settings-page');

    add_settings_field(	'tfp_net_setting',
                        'Persist data on uninstall',
                        'tfp_netSectionCallback',
                        'tfp-network-settings-page',
                        'net_translations_section'); //add settings field to the translations_section

		// add 	DB entry in sitemeta table
    add_site_option('tfp_uninstall_save',1);
}

function tfp_netSectionDescription(){
	echo '<p>Settings related to the plugin:</p>';
}

function tfp_netSectionCallback(){
	echo '<input name="tfp_uninstall_save" type="checkbox" value="1" ' . checked('1', get_site_option( 'tfp_uninstall_save' ) , false ) . '/> Check to keep translations data saved on plugin uninstall.';
}
