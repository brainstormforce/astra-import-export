<?php
/**
 * Plugin Name: Astra Import Export
 * Plugin URI: https://wpastra.com/
 * Description: This plugin is an add-on for the Astra WordPress Theme. It will help in Import Export Customizer settings.
 * Version: 1.0.0
 * Author: Brainstorm Force
 * Author URI: http://www.brainstormforce.com
 * Text Domain: astra-import-export
 *
 * @package Astra Import Export
 */


/**
 * Set constants.
 */

if ( ! defined( 'ASTRA_IMPORT_EXPORT_VER' ) ) {
	define( 'ASTRA_IMPORT_EXPORT_VER', '1.0.0' );
}

if ( ! defined( 'ASTRA_IMPORT_EXPORT_FILE' ) ) {
	define( 'ASTRA_IMPORT_EXPORT_FILE', __FILE__ );
}

if ( ! defined( 'ASTRA_IMPORT_EXPORT_BASE' ) ) {
	define( 'ASTRA_IMPORT_EXPORT_BASE', plugin_basename( ASTRA_IMPORT_EXPORT_FILE ) );
}

if ( ! defined( 'ASTRA_IMPORT_EXPORT_DIR' ) ) {
	define( 'ASTRA_IMPORT_EXPORT_DIR', plugin_dir_path( ASTRA_IMPORT_EXPORT_FILE ) );
}

if ( ! defined( 'ASTRA_IMPORT_EXPORT_URI' ) ) {
	define( 'ASTRA_IMPORT_EXPORT_URI', plugins_url( '/', ASTRA_IMPORT_EXPORT_FILE ) );
}


if ( ! function_exists( 'astra_import_export_setup' ) ) :

	/**
	 * Astra Import Export Setup
	 *
	 * @since 1.0.0
	 */
	function astra_import_export_setup() {
		require_once ASTRA_IMPORT_EXPORT_DIR . 'inc/class-astra-import-export.php';
	}

	add_action( 'plugins_loaded', 'astra_import_export_setup' );

	function my_plugin_action_links( $links ) {
	   $links[] = '<a href="'. esc_url( get_admin_url(null, 'themes.php?page=astra') ) .'">Settings</a>';
	   return $links;
	}
	add_filter( 'plugin_action_links_' . plugin_basename(__FILE__), 'my_plugin_action_links' );

endif;
