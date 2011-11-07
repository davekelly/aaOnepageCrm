<?php
/**
 * @package aa_onepage
 * 
 * Based on Yoast's Wordpress SEO Plugin Admin Class
 */
if ( !class_exists('AAOnepage_Plugin_Admin') ) {
    
    class AAOnepage_Plugin_Admin {

            var $hook 		= '';
            var $filename	= '';
            var $longname	= '';
            var $shortname	= '';
            var $ozhicon	= '';
            var $optionname = '';
            var $homepage	= '';		
            var $accesslvl	= 'manage_options';
            var $adminpages = array( 'aaonepage_settings');

            function __construct() {
            }

            function config_page_styles() {
                global $pagenow;
                if ( $pagenow == 'admin.php' && isset($_GET['page']) && in_array($_GET['page'], $this->adminpages) ) {
                    wp_enqueue_style('dashboard');
                    // wp_enqueue_style('thickbox');
                    wp_enqueue_style('global');
                    wp_enqueue_style('wp-admin');				
                }
            }

            
            function register_settings_page() {                
                add_submenu_page('options-general.php','OnepageCRM Settings','OnepageCRM Settings',$this->accesslvl, 'aaonepage_settings_page', array(&$this,'aaonepage_settings_page'));					
            }

            function plugin_options_url() {
                return admin_url( 'options-general.php?page=aaonepage_settings_page' );
            }

            /**
             * Add a link to the settings page to the plugins list
             */
            function add_action_link( $links, $file ) {
                static $this_plugin;
                if( empty($this_plugin) ) $this_plugin = $this->filename;
                if ( $file == $this_plugin ) {
                        $settings_link = '<a href="' . $this->plugin_options_url() . '">' . __('OnePageCRM Settings') . '</a>';
                        array_unshift( $links, $settings_link );
                }
                return $links;
            }

        
            function config_page_scripts() {
                global $pagenow;

                if ( $pagenow == 'admin.php' && isset($_GET['page']) && in_array($_GET['page'], $this->adminpages) ) {
                    wp_enqueue_script( 'postbox' );
                    wp_enqueue_script( 'dashboard' );
                    // wp_enqueue_script( 'thickbox' );
                }
            }
            
            
            
            /**
             * All settings are left, except the username & password            
             */
            function on_deactivate(){
                
                delete_option('aa_onepage_username');
                delete_option('aa_onepage_pwd');   
                
                delete_transient( 'aa_onepage_account_details');
            }

            /**
             * Remove/Delete everything - If the user wants to uninstall, then he wants the state of origin.
             */
            function on_uninstall()
            {
                // important: check if the file is the one that was registered with the uninstall hook (function)
                if ( __FILE__ != WP_UNINSTALL_PLUGIN )
                    return;

                // delete the stored settings
                delete_option('aa_onepage_username');
                delete_option('aa_onepage_pwd');   						                                        
                delete_option('aa_onepage_form_header' );						                                        
                delete_option('aa_onepage_form_width' );						                                        
                delete_option('aa_onepage_basic_style' );						                                        
                delete_option('aa_onepage_contact_tags' );						                                        
                delete_option('aa_onepage_success_message' );	
                
                // lose the transient login.
                delete_transient( 'aa_onepage_account_details');
            }


            /**
             * trigger_error()
             * 
             * @param (string) $error_msg
             * @param (boolean) $fatal_error | catched a fatal error - when we exit, then we can't go further than this point
             * @param unknown_type $error_type
             * @return void
             */
            function error( $error_msg, $fatal_error = false, $error_type = E_USER_ERROR )
            {
                if( isset( $_GET['action'] ) && 'error_scrape' == $_GET['action'] ) 
                {
                    echo "{$error_msg}\n";
                    if ( $fatal_error )
                        exit;
                }
                else 
                {
                    trigger_error( $error_msg, $error_type );
                }
            }
            
        }
}