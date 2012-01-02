<?php
/*
Plugin Name: AA OnepageCRM Add-in
Plugin URI: http://www.ambientage.com/plugins/onepage/
Description: Capture leads on your site and push them to <a href="http://onepagecrm.com">OnepageCRM</a>.
Author: Dave Kelly
Version: 0.1.1
Author URI: http://www.ambientage.com/
 

Copyright 2012  David Kelly (email : plugins@ambientage.com)

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License, version 2, as 
published by the Free Software Foundation.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
http://www.gnu.org/copyleft/gpl.html 
 
 */


// Make sure we don't expose any info if called directly
if ( !function_exists( 'add_action' ) ) {
	echo "Wha'sup. Not much happening here. Sorry ;)";
	exit;
}

define( 'AA_ONEPAGE', '0.0.1' );

$pluginurl = plugin_dir_url(__FILE__);
if ( preg_match( '/^https/', $pluginurl ) && !preg_match( '/^https/', get_bloginfo('url') ) )
	$pluginurl = preg_replace( '/^https/', 'http', $pluginurl );
define( 'AA_ONEPAGE_FRONT_URL', $pluginurl );

define( 'AA_ONEPAGE_URL', plugin_dir_url(__FILE__) );
define( 'AA_ONEPAGE_PATH', plugin_dir_path(__FILE__) );
define( 'AA_ONEPAGE_BASENAME', plugin_basename( __FILE__ ) );


// Onepage api access
require AA_ONEPAGE_PATH.'inc/class-onepage-api.php';
require AA_ONEPAGE_PATH.'inc/class-onepage-api-contact.php';

if(is_admin()){    
    // admin side stuff...
    require AA_ONEPAGE_PATH.'admin/class-onepage-admin.php';
    require AA_ONEPAGE_PATH.'admin/class-onepage-config.php';
}else{
    // frontend stuff
    wp_enqueue_style('aa-onepage', AA_ONEPAGE_FRONT_URL . 'frontend/style/onepage.css');
    wp_enqueue_script('jquery');
    wp_enqueue_script('aa-onepage', AA_ONEPAGE_FRONT_URL . 'frontend/js/onepage.js');
    
    require AA_ONEPAGE_PATH. 'frontend/aa-onepage-form.php';
}

if( null === $onePageApi){
    $onePageApi = new AAOnepage_Api();
}


// Get rid of everything on de-activation / deletion
register_deactivation_hook( __FILE__, array( 'AAOnepage_Plugin_Admin', 'on_deactivate' ) );
register_uninstall_hook( __FILE__, array( 'AAOnepage_Plugin_Admin', 'on_uninstall' ) );
