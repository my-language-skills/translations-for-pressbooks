<?php
/**
 * In order to display Network settings of this plugin extensions-for-pressbooks plugin v1.2.4 must be activated.
 *	Creates plugin setting section in "EFP setting" on the network level.
 *
 * @package           translations for pressbooks
 * @since             1.2.6
 *                    1.2.8 modifications
 */

defined ("ABSPATH") or die ("Action denied!");

if (( is_multisite()) && is_plugin_active('pressbooks/pressbooks.php') && is_plugin_active('extensions-for-pressbooks/extensions-for-pressbooks.php')){
     if (isset($_REQUEST['page']) && "efp-network-settings-page" == $_REQUEST['page']) {
       add_action('admin_init','tfp_renderTranslationsSection');
  	}
  }

/**
 *  Create section and call checkbox
 *
 *  @since 1.2.8
 *
 *  Unistall translation section is created in this plugin
**/
function tfp_renderTranslationsSection(){

    add_settings_section( 'translations_section',
                          'Translations section',
                          '',
                          'tfp-network-settings-page');

    add_option('tfp_uninstall_save', 0);

    add_settings_field(	'tfp_uninstall_save',               // Parameter
                        'Persist data on uninstall',        // Title
                        'tfp_unistall_checkbox',            // Function
                        'tfp-network-settings-page',        // Page
                        'translations_section');            // Add settings field to the translations_section

    register_setting( 'tfp-network-settings-page-grp',
                      'tfp_uninstall_save');
}

/**
 *  Create the checkbox
 *  If option 'tfp_uninstall_save' = 1 checkbox is checked
**/
function tfp_unistall_checkbox(){
  $option = get_option( 'tfp_uninstall_save' );
  echo "<label>";
  echo '<input name="tfp_uninstall_save" id="tfp_uninstall_save" type="checkbox" value="1" class= "code"' . checked(1, $option, false ) . '/> Check to keep translations data saved on plugin uninstall.';
  echo "</label>";
}
