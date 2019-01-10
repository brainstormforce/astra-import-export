<?php
/**
 * Home Page Banner options for Astra.
 *
 * @package Home Page Banner for Astra Theme
 * @since 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

	/**
	 * Option: Retina logo selector
	 */
	// Add the export/import setting.
	$wp_customize->add_setting( 'astra-import-export-setting', array(
		'default' => '',
		'type'	  => 'none'
	));

	// Add the export/import control.
	$wp_customize->add_control( new Astra_Import_Export_Control(
		$wp_customize,
		'astra-import-export-setting',
		array(
			'section'	=> 'panel-astra-import-export',
			'priority'	=> 1
		)
	));
