<?php
/**
 * Plugin Name: Import / Export Customizer Settings
 * Plugin URI: https://wpastra.com/
 * Description: This plugin is an add-on for the Astra WordPress Theme. It will help in Import Export Customizer settings.
 * Version: 1.0.5
 * Author: Brainstorm Force
 * Author URI: http://www.brainstormforce.com
 * Text Domain: astra-import-export
 *
 * @package Import / Export Customizer Settings
 */

if ( 'astra' !== get_template() ) {
	return;
}

/**
 * Set constants.
 */
define( 'ASTRA_IMPORT_EXPORT_VER', '1.0.5' );
define( 'ASTRA_IMPORT_EXPORT_FILE', __FILE__ );
define( 'ASTRA_IMPORT_EXPORT_BASE', plugin_basename( ASTRA_IMPORT_EXPORT_FILE ) );
define( 'ASTRA_IMPORT_EXPORT_DIR', plugin_dir_path( ASTRA_IMPORT_EXPORT_FILE ) );
define( 'ASTRA_IMPORT_EXPORT_URI', plugins_url( '/', ASTRA_IMPORT_EXPORT_FILE ) );


/**
 * Import / Export Customizer Settings Setup
 *
 * @since 1.0.0
 */
function astra_import_export_setup() {
	require_once ASTRA_IMPORT_EXPORT_DIR . 'inc/class-astra-import-export.php';
}

add_action( 'plugins_loaded', 'astra_import_export_setup' );

/**
 * Add plugin settings link.
 *
 * @param Array $links Plugin links to be displayed on the plugins.php.
 * @return Array
 */
function aix_plugin_action_links( $links ) {

	$page_slug = 'astra';

	if ( is_callable( 'Astra_Admin_Settings::get_theme_page_slug' ) ) {
		$page_slug = Astra_Admin_Settings::get_theme_page_slug();
	}

	$links[] = '<a href="' . esc_url( get_admin_url( null, 'themes.php?page=' . $page_slug ) ) . '">' . __( 'Settings', 'astra-import-export' ) . '</a>';

	return $links;
}

add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), 'aix_plugin_action_links' );
