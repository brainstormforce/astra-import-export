// Load the default @wordpress/scripts config object
const path = require( 'path' );
const defaultConfig = require( '@wordpress/scripts/config/webpack.config' );

// Use the defaultConfig but replace the entry and output properties
module.exports = {
	...defaultConfig,
	entry: {
		'dashboard-app': path.resolve(
			__dirname,
			'assets/src/Index.js'
		),
	},
	resolve: {
		alias: {
			...defaultConfig.resolve.alias,
			'@ImportExportDashboardApp': path.resolve( __dirname, 'assets/src/dashboard-app/' ),
		},
	},
	output: {
		filename: '[name].js',
		path: path.resolve( __dirname, 'assets/build' ),
	},
	plugins: [
		// ...defaultConfig.plugins,
		...defaultConfig.plugins.filter( function ( plugin ) {
			if ( plugin.constructor.name === 'LiveReloadPlugin' ) {
				return false;
			}
			return true;
		} ),
	],
};
