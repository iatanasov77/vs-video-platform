var Encore = require( '@symfony/webpack-encore' );

/**
 *  AdminPanel Velzon Theme
 */
Encore.reset();
const adminPanelVelzonConfig	= require( './themes/AdminPanel_VelzonChild/webpack.config' );

//=================================================================================================

/**
 *  Video Platform Theme
 */
Encore.reset();
const VideoPlatformThemeConfig   = require('./themes/VideoPlatformTheme/webpack.config');
//=================================================================================================

module.exports = [
    adminPanelVelzonConfig,
    VideoPlatformThemeConfig
];
