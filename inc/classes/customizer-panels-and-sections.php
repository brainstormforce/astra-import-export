<?php
/**
 * Banner - Panels & Sections
 *
 * @package Import Export for Astra Theme
 * @since 1.0.0
 */

/**
 * Layout Panel
 */

require_once ASTRA_IMPORT_EXPORT_DIR . 'inc/classes/sections/class-astra-import-export-control.php';

// Add the export/import section.
$wp_customize->add_section( 'panel-astra-import-export', array(
	'title'	   => __( 'Astra Export/Import', 'customizer-export-import' ),
	'priority' => 200
));


