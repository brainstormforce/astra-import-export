<?php
/**
 * Astra Import Export - Customizer.
 *
 * @package Astra Import Export for Astra Theme
 * @since 1.0.0
 */

if ( ! class_exists( 'Astra_Import_Export_Loader' ) ) {

	/**
	 * Customizer Initialization
	 *
	 * @since 1.0.0
	 */
	class Astra_Import_Export_Loader {

		/**
		 * An array of core options that shouldn't be imported.
		 *
		 * @since 0.3
		 * @access private
		 * @var array $core_options
		 */
		static private $core_options = array(
			'blogname',
			'blogdescription',
			'show_on_front',
			'page_on_front',
			'page_for_posts',
		);

		/**
		 * Member Variable
		 *
		 * @var instance
		 */
		private static $instance;

		/**
		 *  Initiator
		 */
		public static function get_instance() {
			if ( ! isset( self::$instance ) ) {
				self::$instance = new self;
			}
			return self::$instance;
		}

		/**
		 *  Constructor
		 */
		public function __construct() {

			add_action( 'customize_register', array( $this, 'customize_register' ) );
			add_action( 'customize_controls_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
		}

		/**
		 * Add postMessage support for site title and description for the Theme Customizer.
		 *
		 * @param WP_Customize_Manager $wp_customize Theme Customizer object.
		 */
		function customize_register( $wp_customize ) {

			if ( ! defined( 'ASTRA_THEME_SETTINGS' ) ) {
				return;
			}

			if ( current_user_can( 'edit_theme_options' ) ) {

				if ( isset( $_REQUEST['astra-export'] ) ) {
					self::_export( $wp_customize );
				}
				if ( isset( $_REQUEST['astra-import'] ) && isset( $_FILES['astra-import-file'] ) ) {
					self::_import( $wp_customize );
				}
			}

			/**
			 * Register Sections & Panels
			 */
			require_once ASTRA_IMPORT_EXPORT_DIR . 'inc/classes/customizer-panels-and-sections.php';

			/**
			 * Sections
			 */
			require_once ASTRA_IMPORT_EXPORT_DIR . 'inc/classes/sections/section-banner.php';
		}

		/**
		 * Customizer Preview
		 */
		function enqueue_scripts() {

			wp_register_style( 'astra-import-export-css', ASTRA_IMPORT_EXPORT_URI . 'inc/assets/css/customizer.css', array(), ASTRA_IMPORT_EXPORT_VER );
			wp_register_script( 'astra-import-export-js', ASTRA_IMPORT_EXPORT_URI . 'inc/assets/js/customizer.js', array( 'jquery' ), ASTRA_IMPORT_EXPORT_VER, true );
			// Localize
			wp_localize_script( 'astra-import-export-js', 'ASTRAl10n', array(
				'emptyImport'	=> __( 'Please choose a file to import.', 'customizer-export-import' )
			));

			// Config
			wp_localize_script( 'astra-import-export-js', 'ASTRAConfig', array(
				'customizerURL'	  => admin_url( 'customize.php' ),
				'exportNonce'	  => wp_create_nonce( 'astra-exporting' )
			));

			// Enqueue
			wp_enqueue_style( 'astra-import-export-css' );
			wp_enqueue_script( 'astra-import-export-js' );
		}

		/**
		 * Export customizer settings.
		 *
		 * @since 0.1
		 * @since 0.3 Added $wp_customize param and exporting of options.
		 * @access private
		 * @param object $wp_customize An instance of WP_Customize_Manager.
		 * @return void
		 */
		static private function _export( $wp_customize )
		{
			if ( ! wp_verify_nonce( $_REQUEST['astra-export'], 'astra-exporting' ) ) {
				return;
			}

			$theme		= get_stylesheet();
			$template	= get_template();
			$charset	= get_option( 'blog_charset' );
			$mods		= get_theme_mods();
			$data		= array(
							  'template'  => $template,
							  'mods'	  => $mods ? $mods : array(),
							  'options'	  => array()
						  );

			require_once ASTRA_IMPORT_EXPORT_DIR . 'inc/classes/array2xml.php';

			// Get options from the Customizer API.
			$settings = $wp_customize->settings();

			foreach ( $settings as $key => $setting ) {

				if ( 'option' == $setting->type ) {

					// Don't save widget data.
					if ( 'widget_' === substr( strtolower( $key ), 0, 7 ) ) {
						continue;
					}

					// Don't save sidebar data.
					if ( 'sidebars_' === substr( strtolower( $key ), 0, 9 ) ) {
						continue;
					}

					// Don't save core options.
					if ( in_array( $key, self::$core_options ) ) {
						continue;
					}

					$data['options'][ $key ] = $setting->value();
				}
			}

			// Plugin developers can specify additional option keys to export.
			$option_keys = apply_filters( 'aie_export_option_keys', array() );

			foreach ( $option_keys as $option_key ) {
				$data['options'][ $option_key ] = get_option( $option_key );
			}

			if( function_exists( 'wp_get_custom_css_post' ) ) {
				$data['wp_css'] = wp_get_custom_css();
			}

			// Set the download headers.
			header( 'Content-disposition: attachment; filename=' . $theme . '-export.xml' );
			header( 'Content-Type: text/xml; charset=' . $charset );

			$xml = Array2XML::createXML('root-element-here', $data);
  
  			echo $xml->saveXML();

			// Serialize the export data.
			// echo serialize( $data );

			// Start the download.
			die();
		}

		/**
		 * Imports uploaded mods and calls WordPress core customize_save actions so
		 * themes that hook into them can act before mods are saved to the database.
		 *
		 * @since 0.1
		 * @since 0.3 Added $wp_customize param and importing of options.
		 * @access private
		 * @param object $wp_customize An instance of WP_Customize_Manager.
		 * @return void
		 */
		static private function _import( $wp_customize )
		{
			// Make sure we have a valid nonce.
			if ( ! wp_verify_nonce( $_REQUEST['astra-import'], 'astra-importing' ) ) {
				return;
			}

			// Make sure WordPress upload support is loaded.
			if ( ! function_exists( 'wp_handle_upload' ) ) {
				require_once( ABSPATH . 'wp-admin/includes/file.php' );
			}

			// Load the export/import option class.
			require_once ASTRA_IMPORT_EXPORT_DIR . 'inc/classes/class-astra-ie-option.php';

			// Setup global vars.
			global $wp_customize;
			global $AIE_error;

			// Setup internal vars.
			$AIE_error	 = false;
			$template	 = get_template();
			$overrides   = array( 'test_form' => false, 'test_type' => false, 'mimes' => array('dat' => 'text/plain') );
			$file        = wp_handle_upload( $_FILES['astra-import-file'], $overrides );

			// Make sure we have an uploaded file.
			if ( isset( $file['error'] ) ) {
				$AIE_error = $file['error'];
				return;
			}
			if ( ! file_exists( $file['file'] ) ) {
				$AIE_error = __( 'Error importing settings! Please try again.', 'customizer-export-import' );
				return;
			}

			// Get the upload data.
			$raw  = file_get_contents( $file['file'] );
			$data = @unserialize( $raw );

			// Remove the uploaded file.
			unlink( $file['file'] );

			// Data checks.
			if ( 'array' != gettype( $data ) ) {
				$AIE_error = __( 'Error importing settings! Please check that you uploaded a customizer export file.', 'customizer-export-import' );
				return;
			}
			if ( ! isset( $data['template'] ) || ! isset( $data['mods'] ) ) {
				$AIE_error = __( 'Error importing settings! Please check that you uploaded a customizer export file.', 'customizer-export-import' );
				return;
			}
			if ( $data['template'] != $template ) {
				$AIE_error = __( 'Error importing settings! The settings you uploaded are not for the current theme.', 'customizer-export-import' );
				return;
			}

			// Import images.
			if ( isset( $_REQUEST['astra-import-images'] ) ) {
				$data['mods'] = self::_import_images( $data['mods'] );
			}

			// Import custom options.
			if ( isset( $data['options'] ) ) {

				foreach ( $data['options'] as $option_key => $option_value ) {

					$option = new Astra_IE_Option( $wp_customize, $option_key, array(
						'default'		=> '',
						'type'			=> 'option',
						'capability'	=> 'edit_theme_options'
					) );

					$option->import( $option_value );
				}
			}

			// If wp_css is set then import it.
			if( function_exists( 'wp_update_custom_css_post' ) && isset( $data['wp_css'] ) && '' !== $data['wp_css'] ) {
				wp_update_custom_css_post( $data['wp_css'] );
			}

			// Call the customize_save action.
			do_action( 'customize_save', $wp_customize );

			// Loop through the mods.
			foreach ( $data['mods'] as $key => $val ) {

				// Call the customize_save_ dynamic action.
				do_action( 'customize_save_' . $key, $wp_customize );

				// Save the mod.
				set_theme_mod( $key, $val );
			}

			// Call the customize_save_after action.
			do_action( 'customize_save_after', $wp_customize );
		}

		/**
		 * Imports images for settings saved as mods.
		 *
		 * @since 0.1
		 * @access private
		 * @param array $mods An array of customizer mods.
		 * @return array The mods array with any new import data.
		 */
		static private function _import_images( $mods )
		{
			foreach ( $mods as $key => $val ) {

				if ( self::_is_image_url( $val ) ) {

					$data = self::_sideload_image( $val );

					if ( ! is_wp_error( $data ) ) {

						$mods[ $key ] = $data->url;

						// Handle header image controls.
						if ( isset( $mods[ $key . '_data' ] ) ) {
							$mods[ $key . '_data' ] = $data;
							update_post_meta( $data->attachment_id, '_wp_attachment_is_custom_header', get_stylesheet() );
						}
					}
				}
			}

			return $mods;
		}

		/**
		 * Taken from the core media_sideload_image function and
		 * modified to return an array of data instead of html.
		 *
		 * @since 0.1
		 * @access private
		 * @param string $file The image file path.
		 * @return array An array of image data.
		 */
		static private function _sideload_image( $file )
		{
			$data = new stdClass();

			if ( ! function_exists( 'media_handle_sideload' ) ) {
				require_once( ABSPATH . 'wp-admin/includes/media.php' );
				require_once( ABSPATH . 'wp-admin/includes/file.php' );
				require_once( ABSPATH . 'wp-admin/includes/image.php' );
			}
			if ( ! empty( $file ) ) {

				// Set variables for storage, fix file filename for query strings.
				preg_match( '/[^\?]+\.(jpe?g|jpe|gif|png)\b/i', $file, $matches );
				$file_array = array();
				$file_array['name'] = basename( $matches[0] );

				// Download file to temp location.
				$file_array['tmp_name'] = download_url( $file );

				// If error storing temporarily, return the error.
				if ( is_wp_error( $file_array['tmp_name'] ) ) {
					return $file_array['tmp_name'];
				}

				// Do the validation and storage stuff.
				$id = media_handle_sideload( $file_array, 0 );

				// If error storing permanently, unlink.
				if ( is_wp_error( $id ) ) {
					@unlink( $file_array['tmp_name'] );
					return $id;
				}

				// Build the object to return.
				$meta					= wp_get_attachment_metadata( $id );
				$data->attachment_id	= $id;
				$data->url				= wp_get_attachment_url( $id );
				$data->thumbnail_url	= wp_get_attachment_thumb_url( $id );
				$data->height			= $meta['height'];
				$data->width			= $meta['width'];
			}

			return $data;
		}

		/**
		 * Checks to see whether a string is an image url or not.
		 *
		 * @since 0.1
		 * @access private
		 * @param string $string The string to check.
		 * @return bool Whether the string is an image url or not.
		 */
		static private function _is_image_url( $string = '' )
		{
			if ( is_string( $string ) ) {

				if ( preg_match( '/\.(jpg|jpeg|png|gif)/i', $string ) ) {
					return true;
				}
			}

			return false;
		}
	}

}

/**
 * Kicking this off by calling 'get_instance()' method
 */
Astra_Import_Export_Loader::get_instance();
