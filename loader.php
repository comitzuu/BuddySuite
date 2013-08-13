<?php
/*
Plugin Name: BuddySuite
Plugin URI: http://buddypress.org
Description: A suite of settings to customize a BuddyPress site.
Version: 1.2.9.8
Requires at least: WP 3.4, BuddyPress 1.5
Tested up to: WP 3.5, BuddyPress 1.7
License: GNU General Public License 2.0 (GPL) http://www.gnu.org/licenses/gpl.html
Author: modemlooper
Author URI: http://twitter.com/modemlooper
*/

// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;

/* Only load the component if BuddyPress is loaded and initialized. */
function buddysuite_init() {
	// Check if bp is active
	if ( !class_exists( 'buddypress' ) )
		return;

	require( dirname( __FILE__ ) . '/includes/buddysuite-admin.php' );
	require( dirname( __FILE__ ) . '/includes/buddysuite-admin-settings.php' );
	require( dirname( __FILE__ ) . '/includes/buddysuite-core.php' );
	require( dirname( __FILE__ ) . '/includes/menus/buddysuite-menu-router.php' );
	require( dirname( __FILE__ ) . '/includes/menus/buddysuite-menu-meta-box.php' );

}
add_action( 'init', 'buddysuite_init' );

function wee() {
// Check if bp is active
	if ( !class_exists( 'buddypress' ) )
		return;

	require( dirname( __FILE__ ) . '/includes/buddysuite-user-settings.php' );
	require( dirname( __FILE__ ) . '/includes/buddysuite-bp-settings.php' );
	require( dirname( __FILE__ ) . '/includes/stalker/buddysuite-stalker-screens.php' );

}
add_action( 'bp_include', 'wee' );

function buddysuite_menu_item_script() {
    // Register the script like this for a plugin:
    wp_register_script( 'my-buddypress-links_menus', plugins_url( '/includes/menus/buddysuite-menus.js', __FILE__ ), array( 'jquery' ) );

     // For either a plugin or a theme, you can then enqueue the script:
    wp_enqueue_script( 'my-buddypress-links_menus' );
}
add_action( 'wp_enqueue_scripts', 'buddysuite_menu_item_script' );


/*  Add settings link on plugin page */
function buddysuite_settings_link( $links, $file ) {

	$this_plugin = plugin_basename(__FILE__);

	if ( $file == $this_plugin ) {

		$settings_link = '<a href="options-general.php?page=buddysuite/includes/buddysuite-admin.php">Settings</a>';
		array_unshift( $links, $settings_link );
	}
  return $links;
}
add_filter( 'plugin_action_links', 'buddysuite_settings_link', 10, 2 );


function buddysuite_textdomain_init() {
	$mofile        = sprintf( 'buddysuite-%s.mo', get_locale() );
	$mofile_local  = dirname( __FILE__ )  . '/languages/' . $mofile;

	if ( file_exists( $mofile_local ) )
	return load_textdomain( 'buddysuite', $mofile_local );
}
add_action( 'plugins_loaded', 'buddysuite_textdomain_init' );



/////////////////



// this is the URL our updater / license checker pings. This should be the URL of the site with EDD installed
define( 'TAPTAPPRESS_STORE_URL_BS', 'http://shop.taptappress.com' ); // you should use your own CONSTANT name, and be sure to replace it throughout this file

// the name of your product. This should match the download name in EDD exactly
define( 'TAPTAPPRESS_ITEM_NAME_BS', 'BuddySuite' ); // you should use your own CONSTANT name, and be sure to replace it throughout this file

if( !class_exists( 'EDD_SL_Plugin_Updater' ) ) {
	// load our custom updater
	include( dirname( __FILE__ ) . '/includes/EDD_SL_Plugin_Updater.php' );
}

global $buddysuite_options;

$buddysuite_options = get_option('buddysuite_plugin_options');

// retrieve our license key from the DB
$license_key = trim( $buddysuite_options['buddysuite_license_key'] );

// setup the updater
$edd_updater = new EDD_SL_Plugin_Updater( TAPTAPPRESS_STORE_URL_BS, __FILE__, array(
		'version' 	=> '1.2.9.8', 				// current version number
		'license' 	=> $license_key, 		// license key (used get_option above to retrieve from DB)
		'item_name' => TAPTAPPRESS_ITEM_NAME_BS, 	// name of this plugin
		'author' 	=> 'modemlooper'  // author of this plugin
	)
);