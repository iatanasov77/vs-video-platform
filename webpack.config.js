var Encore = require( '@symfony/webpack-encore' );

/**
 *  AdminPanel Default Theme
 */
const themePath         = './vendor/vankosoft/application/src/Vankosoft/ApplicationBundle/Resources/themes/default';
const adminPanelConfig  = require( themePath + '/webpack.config' );

//=================================================================================================

/**
 *  AdminPanel Cusstom Entries
 */
Encore.reset();
const adminPanelCusstomEntriesConfig = require('./assets/admin-panel/webpack.config');

//=================================================================================================

/**
 *  Video Platform Theme
 */
Encore.reset();
const VideoPlatformThemeConfig   = require('./themes/VideoPlatformTheme/webpack.config');
//=================================================================================================

module.exports = [
    adminPanelConfig,
    adminPanelCusstomEntriesConfig,
    VideoPlatformThemeConfig
];
