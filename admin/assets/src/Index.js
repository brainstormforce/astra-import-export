import React from 'react';
import { __ } from '@wordpress/i18n';

import Import from '@ImportExportDashboardApp/Import';
import Export from '@ImportExportDashboardApp/Export';

/**
 * Insert Import Export customizer setting on new Astra admin panel.
 *
 * @returns html
 */
wp.hooks.addFilter(
	'astra_dashboard.settings_screen_after_global-settings',
	'astra_addon/dashboard_app',
	function( prevComponent ){
		prevComponent = <> <Import/> <Export/> </>
		return prevComponent;
	}
);
