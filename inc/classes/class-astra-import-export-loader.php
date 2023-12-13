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
		 * Check Astra is with 4.0.0
		 *
		 * @var bool
		 */
		private static $astra_with_modern_dashboard = false;

		/**
		 * Admin page for Astra.
		 *
		 * @var bool
		 */
		private static $home_slug = 'astra';

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
			self::$home_slug = apply_filters( 'astra_theme_page_slug', 'astra' );
			add_filter( 'astra_collect_customizer_builder_data', '__return_true' );
			add_action( 'after_setup_theme', array( $this, 'init_admin_settings' ), 99 );
			add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
			add_action( 'admin_init', array( $this, 'export' ) );
			add_action( 'admin_init', array( $this, 'import' ) );
			add_action( 'admin_notices', array( $this, 'astra_admin_errors' ) );
		}

		/**
		 * Include required classes.
		 *
		 * @since 1.0.7
		 */
		public function init_admin_settings() {
			self::$astra_with_modern_dashboard = ( defined( 'ASTRA_THEME_VERSION' ) && version_compare( ASTRA_THEME_VERSION, '4.0.0', '>=' ) ) ? true : false;
			if ( false === self::$astra_with_modern_dashboard ) {
				add_action( 'astra_welcome_page_right_sidebar_content', array( $this, 'astra_import_export_section' ), 50 );
			}
		}

		/**
		 * Function enqueue_scripts() to enqueue files.
                 * @since 1.0.7
		 */
		public function enqueue_scripts() {
			if ( true === self::$astra_with_modern_dashboard && ( ! empty( $_GET['page'] ) && ( self::$home_slug === $_GET['page'] || false !== strpos( $_GET['page'], self::$home_slug . '_' ) ) ) ) { //phpcs:ignore.
				$script_asset_path = ASTRA_IMPORT_EXPORT_DIR . 'admin/assets/build/dashboard-app.asset.php';
				$script_info = file_exists( $script_asset_path ) ? include $script_asset_path : array(
					'dependencies' => array(),
					'version'      => ASTRA_IMPORT_EXPORT_VER,
				);

				$script_dep = array_merge( $script_info['dependencies'], array( 'wp-hooks' ) );

				wp_enqueue_script(
					'astra-import-export-admin-setup',
					ASTRA_IMPORT_EXPORT_URI . 'admin/assets/build/dashboard-app.js',
					$script_dep,
					$script_info['version'],
					true
				);

				wp_localize_script( 'astra-import-export-admin-setup', 'ast_import_export_admin', apply_filters( 'astra_import_export_localize', array(
					'astra_import_nonce'     => wp_create_nonce( 'astra_import_nonce' ),
					'astra_export_nonce'     => wp_create_nonce( 'astra_export_nonce' ),
					'header_footer_layout_caps' => ( defined( 'ASTRA_THEME_VERSION' ) && version_compare( ASTRA_THEME_VERSION, '4.5.2', '>=' ) ) ? true : false,
				) ) );

				wp_enqueue_style( 'astra-import-export-css', ASTRA_IMPORT_EXPORT_URI . 'inc/assets/css/modern-admin-style.css', array(), ASTRA_IMPORT_EXPORT_VER );
			} else {
				wp_register_style( 'astra-import-export-css', ASTRA_IMPORT_EXPORT_URI . 'inc/assets/css/style.css', array(), ASTRA_IMPORT_EXPORT_VER );
			}
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

			$filename = isset( $_FILES['import_file']['name'] ) ? $_FILES['import_file']['name'] : '';

			if ( false !== strpos( $filename, 'header' ) ) {
				$process_type = 'header';
			} elseif ( false !== strpos( $filename, 'footer' ) ) {
				$process_type = 'footer';
			} else {
				$process_type = 'all';
			}

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
			if ( class_exists( 'Astra_Admin_Helper' ) && isset( $settings['astra-addons'] ) ) {
				Astra_Admin_Helper::update_admin_settings_option( '_astra_ext_enabled_extensions', $settings['astra-addons'] );
			}

			$page_slug = self::$home_slug;
			$redirection_url = admin_url( 'themes.php' );

			if ( is_callable( 'Astra_Admin_Settings::get_theme_page_slug' ) ) {
				$page_slug = Astra_Admin_Settings::get_theme_page_slug();
			}

			if ( is_callable( 'Astra_Menu::get_theme_page_slug' ) ) {
				$redirection_url = admin_url( 'admin.php' );
			}

			if ( ( 'header' === $process_type || 'footer' === $process_type ) && isset( $settings['customizer-settings']['astra-settings'] ) ) {
				$astra_settings = Astra_Theme_Options::get_options();
				$astra_settings = array_merge( $astra_settings, $settings['customizer-settings']['astra-settings'] );

				update_option( 'astra-settings', $astra_settings );
			} else {
				// Delete existing dynamic CSS cache.
				delete_option( 'astra-settings' );

				if ( ! empty( $settings['customizer-settings'] ) ) {
					foreach ( $settings['customizer-settings'] as $option => $value ) {
						update_option( $option, $value );
					}
				}
			}

			wp_safe_redirect(
				wp_nonce_url(
					add_query_arg(
						array(
							'page'   => $page_slug,
							'status' => 'imported',
						),
						$redirection_url
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

			$process_type    = 'all';
			$astra_settings  = Astra_Theme_Options::get_options();

			if ( ! empty( $_POST['ast_customizer_data_type_export'] ) ) {
				$process_type = sanitize_text_field( $_POST['ast_customizer_data_type_export'] );
			}

			// Get options from the Customizer API.
			if ( 'header' === $process_type && ! empty( Astra_Customizer::$customizer_header_configs ) ) {
				$file_constants = 'header-';
				$header_settings = array_intersect_key( $astra_settings, array_fill_keys( Astra_Customizer::$customizer_header_configs, null ) );
				$theme_options['customizer-settings']['astra-settings'] = $header_settings;

			} elseif ( 'footer' === $process_type && ! empty( Astra_Customizer::$customizer_footer_configs ) ) {
				$file_constants = 'footer-';
				$footer_settings = array_intersect_key( $astra_settings, array_fill_keys( Astra_Customizer::$customizer_footer_configs, null ) );
				$theme_options['customizer-settings']['astra-settings'] = $footer_settings;

			} else {
				$file_constants  = '';
				$theme_options['customizer-settings']['astra-settings'] = $astra_settings;

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
			}

			$theme_options = apply_filters( 'astra_export_data', $theme_options );
			nocache_headers();
			header( 'Content-Type: application/json; charset=utf-8' );
			header( 'Content-Disposition: attachment; filename=astra-settings-' . $file_constants . 'export-' . gmdate( 'm-d-Y' ) . '.json' );
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
