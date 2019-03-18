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
			// Enqueue.
			wp_enqueue_style( 'astra-import-export-css' );
			?>
			<div class="postbox" id="astra-ie">
				<h2 class="hndle ast-normal-cusror"><span class="dashicons dashicons-download"></span><?php esc_html_e( 'Export Settings', 'astra-import-export' ); ?></h2>
				<div class="inside">
					<p><?php esc_html_e( 'Export Active addons list with Customizer settings.', 'astra-import-export' ); ?></p>
					<form method="post">
						<hr style="margin:10px 0;border-bottom:0;">
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

			$extension = end( explode( '.', $filename ) );

			if ( 'json' !== $extension ) {
				wp_die( esc_html( 'Please upload a valid .json file', 'astra-import-export' ) );
			}

			$import_file = $_FILES['import_file']['tmp_name'];

			if ( empty( $import_file ) ) {
				wp_die( esc_html( 'Please upload a file to import', 'astra-import-export' ) );
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

			// Delete existing dynamic CSS cache.
			delete_option( 'astra-settings' );

			update_option( 'astra-settings', $settings['customizer-settings'] );

			wp_safe_redirect( admin_url( 'admin.php?page=astra&status=imported' ) );
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
			$theme_options['customizer-settings'] = Astra_Theme_Options::get_options();
			$theme_options                        = apply_filters( 'astra_export_data', $theme_options );
			nocache_headers();
			header( 'Content-Type: application/json; charset=utf-8' );
			header( 'Content-Disposition: attachment; filename=astra-settings-export-' . date( 'm-d-Y' ) . '.json' );
			header( 'Expires: 0' );
			echo wp_json_encode( $theme_options );
			// Start the download.
			die();
		}
	}
}

add_filter( 'astra_export_data', 'astra_sites_do_site_options_export', 10, 2 );
/**
 * Add to our export .json file.
 *
 * @since 1.0.0
 *
 * @param array $data The current data being exported.
 * @return array Existing and extended data.
 */
function astra_sites_do_site_options_export( $data ) {
	// Astra addons.
	if ( class_exists( 'Astra_Ext_Extension' ) ) {
		$data['astra-addons'] = Astra_Ext_Extension::get_enabled_addons();
	}
	return $data;
}

add_action( 'admin_notices', 'astra_admin_errors' );
/**
 * Add our admin notices
 *
 * @since 0.1
 */
function astra_admin_errors() {
	$screen = get_current_screen();
	if ( ! isset( $_GET['status'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		return;
	}
	if ( 'imported' === $_GET['status'] ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		add_settings_error( 'astra-notices', 'imported', esc_html__( 'Import successful.', 'astra-import-export' ), 'updated' );
	}
	settings_errors( 'astra-notices' );
}

/**
 * Kicking this off by calling 'get_instance()' method
 */
Astra_Import_Export_Loader::get_instance();
