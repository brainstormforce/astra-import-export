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

			add_action( 'astra_welcome_page_right_sidebar_content', array( $this, 'astra_import_export_section' ), 50 );
			add_action( 'admin_init',					array( $this, 'export' ) );
			add_action( 'admin_init',					array( $this, 'import' ) );
		}

		/**
		 * Add postMessage support for site title and description for the Theme Customizer.
		 *
		 * @param WP_Customize_Manager $wp_customize Theme Customizer object.
		 */
		function astra_import_export_section( ) {
			?>
			<div class="postbox" id="astra-ie">
				<h3 class="hndle"><?php _e( 'Export Settings', 'astra-import-export' );?></h3>
				<div class="inside">
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
				<h3 class="hndle"><?php _e( 'Import Settings', 'astra-import-export' );?></h3>
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
		 * @since 1.7
		 */
		public static function import() {
			if ( empty( $_POST['astra_ie_action'] ) || 'import_settings' != $_POST['astra_ie_action'] ) {
				return;
			}

			if ( ! wp_verify_nonce( $_POST['astra_import_nonce'], 'astra_import_nonce' ) ) {
				return;
			}

			if ( ! current_user_can( 'manage_options' ) ) {
				return;
			}

			$filename = $_FILES['import_file']['name'];
			$extension = end( explode( '.', $_FILES['import_file']['name'] ) );

			if ( $extension != 'json' ) {
				wp_die( __( 'Please upload a valid .json file', 'astra-import-export' ) );
			}

			$import_file = $_FILES['import_file']['tmp_name'];

			if ( empty( $import_file ) ) {
				wp_die( __( 'Please upload a file to import', 'astra-import-export' ) );
			}

			// Retrieve the settings from the file and convert the json object to an array.
			$settings = json_decode( file_get_contents( $import_file ), true );

			$astra_theme_options = get_option( 'astra-settings' );
			
			// Delete existing dynamic CSS cache
			delete_option( 'astra-settings' );
			
			update_option( 'astra-settings', $settings );

			wp_safe_redirect( admin_url( 'admin.php?page=astra&status=imported' ) );
			exit;
		}

		/**
		 * Export our chosen options.
		 *
		 * @since 1.7
		 */
		public static function export( ) {

			if ( empty( $_POST['astra_ie_action'] ) || 'export_settings' != $_POST['astra_ie_action'] ) {
				return;
			}

			if ( ! wp_verify_nonce( $_POST['astra_export_nonce'], 'astra_export_nonce' ) ) {
				return;
			}

			if ( ! current_user_can( 'manage_options' ) ) {
				return;
			}

			// Get options from the Customizer API.

			$theme_options = Astra_Theme_Options::get_options();
			
			$encode = json_encode( $theme_options );

			nocache_headers();
			header( 'Content-Type: application/json; charset=utf-8' );
			header( 'Content-Disposition: attachment; filename=astra-settings-export-' . date( 'm-d-Y' ) . '.json' );
			header( "Expires: 0" );

			echo $encode;

			// Start the download.
			die();
		}
	}
}

/**
 * Kicking this off by calling 'get_instance()' method
 */
Astra_Import_Export_Loader::get_instance();
