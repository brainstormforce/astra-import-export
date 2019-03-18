<?php
/**
 * Astra Import Export
 *
 * @package Astra Import Export for Astra Theme
 * @since 1.0.0
 */

if ( ! class_exists( 'Astra_Import_Export' ) ) {

	/**
	 * Advanced Headers Initial Setup
	 *
	 * @since 1.0.0
	 */
	class Astra_Import_Export {


		/**
		 * Member Variable
		 *
		 * @var object instance
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
		 * Constructor function that initializes required actions and hooks
		 */
		public function __construct() {
			require_once ASTRA_IMPORT_EXPORT_DIR . 'inc/classes/class-astra-import-export-loader.php';
		}
	}

	/**
	 *  Kicking this off by calling 'get_instance()' method
	 */
	Astra_Import_Export::get_instance();

}
