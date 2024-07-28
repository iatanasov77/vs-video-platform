var Encore = require( '@symfony/webpack-encore' );

/**
 *  AdminPanel Default Theme
 */
// const themePath         = './vendor/vankosoft/application/src/Vankosoft/ApplicationBundle/Resources/themes/default';
// const adminPanelConfig  = require( themePath + '/webpack.config' );

//=================================================================================================

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
    //adminPanelConfig,
    adminPanelVelzonConfig,
    VideoPlatformThemeConfig
];
