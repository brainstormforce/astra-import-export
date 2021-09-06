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
				self::$instance = new self();
			}
			return self::$instance;
		}

		/**
		 *  Constructor
		 */
		public function __construct() {
			add_action( 'astra_welcome_page_right_sidebar_content', array( $this, 'astra_import_export_section' ), 50 );
			add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
			add_action( 'admin_init', array( $this, 'export' ) );
			add_action( 'admin_init', array( $this, 'import' ) );
			add_action( 'admin_notices', array( $this, 'astra_admin_errors' ) );
		}

		/**
		 * Function enqueue_scripts() to enqueue files.
		 */
		public function enqueue_scripts() {
			wp_register_style( 'astra-import-export-css', ASTRA_IMPORT_EXPORT_URI . 'inc/assets/css/style.css', array(), ASTRA_IMPORT_EXPORT_VER );
		}

		/**
		 * Add postMessage support for site title and description for the Theme Customizer.
		 */
		public function astra_import_export_section() {

			$theme_name = apply_filters( 'astra_page_title', __( 'Astra', 'astra-import-export' ) );
			// Enqueue.
			wp_enqueue_style( 'astra-import-export-css' );
			?>
			<div class="postbox" id="astra-ie">
				<h2 class="hndle ast-normal-cusror"><span class="dashicons dashicons-download"></span><?php esc_html_e( 'Export Settings', 'astra-import-export' ); ?></h2>
				<div class="inside">
					<p><?php esc_html_e( 'Export your current ' . esc_html( $theme_name ) . ' Customizer settings.', 'astra-import-export' ); // phpcs:ignore WordPress.WP.I18n.NonSingularStringLiteralText ?></p>

					<form method="post">
						<p><input type="hidden" name="astra_ie_action" value="export_settings" /></p>
						<p style="margin-bottom:0">
							<?php wp_nonce_field( 'astra_export_nonce', 'astra_export_nonce' ); ?>
							<?php submit_button( __( 'Export', 'astra-import-export' ), 'button', 'submit', false, array( 'id' => '' ) ); ?>
						</p>
					</form>
				</div>
			</div>

			<div class="postbox" id="astra-ie">
				<h2 class="hndle ast-normal-cusror"><span class="dashicons dashicons-upload"></span><?php esc_html_e( 'Import Settings', 'astra-import-export' ); ?></h2>
				<div class="inside">
					<p><?php esc_html_e( 'Import your ' . esc_html( $theme_name ) . ' Customizer settings.', 'astra-import-export' ); // phpcs:ignore WordPress.WP.I18n.NonSingularStringLiteralText ?>

					<form method="post" enctype="multipart/form-data">
						<p>
							<input type="file" name="import_file"/>
						</p>
						<p style="margin-bottom:0">
							<input type="hidden" name="astra_ie_action" value="import_settings" />
							<?php wp_nonce_field( 'astra_import_nonce', 'astra_import_nonce' ); ?>
							<?php submit_button( __( 'Import', 'astra-import-export' ), 'button', 'submit', false, array( 'id' => '' ) ); ?>
						</p>
					</form>

				</div>
			</div>
			<?php
		}

		/**
		 * Display import status in the admin notices.
		 *
		 * @since 1.0.0
		 */
		public function astra_admin_errors() {
			// Verify correct source for the $_GET data.
			if ( ! isset( $_GET['_wpnonce'] ) || ! wp_verify_nonce( $_GET['_wpnonce'], 'astra-import-complete' ) ) {
				return;
			}

			if ( ! isset( $_GET['status'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
				return;
			}

			if ( 'imported' === $_GET['status'] ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
				add_settings_error( 'astra-notices', 'imported', esc_html__( 'Import successful.', 'astra-import-export' ), 'updated' );
			}

			settings_errors( 'astra-notices' );
		}

		/**
		 * Import our exported file.
		 *
		 * @since 1.0.0
		 */
		public static function import() {

			if ( ! isset( $_POST['astra_import_nonce'] ) || ! wp_verify_nonce( $_POST['astra_import_nonce'], 'astra_import_nonce' ) ) {
				return;
			}
			if ( empty( $_POST['astra_ie_action'] ) || 'import_settings' !== $_POST['astra_ie_action'] ) {
				return;
			}

			if ( ! current_user_can( 'manage_options' ) ) {
				return;
			}

			$filename = $_FILES['import_file']['name'];

			if ( empty( $filename ) ) {
				return;
			}
			$file_ext  = explode( '.', $filename );
			$extension = end( $file_ext );

			if ( 'json' !== $extension ) {
				wp_die( esc_html__( 'Please upload a valid .json file', 'astra-import-export' ) );
			}

			$import_file = $_FILES['import_file']['tmp_name'];

			if ( empty( $import_file ) ) {
				wp_die( esc_html__( 'Please upload a file to import', 'astra-import-export' ) );
			}

			global $wp_filesystem;
			if ( empty( $wp_filesystem ) ) {
				require_once ABSPATH . '/wp-admin/includes/file.php';
				WP_Filesystem();
			}
			// Retrieve the settings from the file and convert the json object to an array.
			$file_contants = $wp_filesystem->get_contents( $import_file );
			$settings      = json_decode( $file_contants, 1 );

			// Astra addons activation.
			if ( class_exists( 'Astra_Admin_Helper' ) ) {
				Astra_Admin_Helper::update_admin_settings_option( '_astra_ext_enabled_extensions', $settings['astra-addons'] );
			}

			$page_slug = 'astra';

			if ( is_callable( 'Astra_Admin_Settings::get_theme_page_slug' ) ) {
				$page_slug = Astra_Admin_Settings::get_theme_page_slug();
			}

			// Delete existing dynamic CSS cache.
			delete_option( 'astra-settings' );

			if ( ! empty( $settings['customizer-settings'] ) ) {
				foreach ( $settings['customizer-settings'] as $option => $value ) {
					update_option( $option, $value );
				}
			}

			wp_safe_redirect(
				wp_nonce_url(
					add_query_arg(
						array(
							'page'   => $page_slug,
							'status' => 'imported',
						),
						admin_url( 'themes.php' )
					),
					'astra-import-complete'
				)
			);
			exit;
		}

		/**
		 * Export our chosen options.
		 *
		 * @since 1.0.0
		 */
		public static function export() {
			if ( ! isset( $_POST['astra_export_nonce'] ) || ! wp_verify_nonce( $_POST['astra_export_nonce'], 'astra_export_nonce' ) ) {
				return;
			}
			if ( empty( $_POST['astra_ie_action'] ) || 'export_settings' !== $_POST['astra_ie_action'] ) {
				return;
			}
			if ( ! current_user_can( 'manage_options' ) ) {
				return;
			}

			// Get options from the Customizer API.
			$theme_options['customizer-settings']['astra-settings'] = Astra_Theme_Options::get_options();

			// Get Global color palette option.
			if ( function_exists( 'astra_get_palette_colors' ) ) {
				$theme_options['customizer-settings']['astra-color-palettes'] = astra_get_palette_colors();
			}

			// Get Typography Presets option.
			if ( function_exists( 'astra_get_typography_presets' ) ) {
				$theme_options['customizer-settings']['astra-typography-presets'] = astra_get_typography_presets();
			}

			// Add Astra Addons to import.
			if ( class_exists( 'Astra_Ext_Extension' ) ) {
				$theme_options['astra-addons'] = Astra_Ext_Extension::get_enabled_addons();
			}

			$theme_options = apply_filters( 'astra_export_data', $theme_options );
			nocache_headers();
			header( 'Content-Type: application/json; charset=utf-8' );
			header( 'Content-Disposition: attachment; filename=astra-settings-export-' . gmdate( 'm-d-Y' ) . '.json' );
			header( 'Expires: 0' );
			echo wp_json_encode( $theme_options );
			// Start the download.
			die();
		}
	}
}

/**
 * Kicking this off by calling 'get_instance()' method
 */
Astra_Import_Export_Loader::get_instance();
