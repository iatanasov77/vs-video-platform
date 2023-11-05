const Encore = require('@symfony/webpack-encore');
const path = require('path');

Encore
    .setOutputPath( 'public/shared_assets/build/video-platform-theme/' )
    .setPublicPath( '/build/video-platform-theme/' )
  
    .disableSingleRuntimeChunk()
    .cleanupOutputBeforeBuild()
    .enableSourceMaps(!Encore.isProduction())
    .enableVersioning(Encore.isProduction())
    
    .enableSassLoader(function(sassOptions) {}, {
        resolveUrlLoader: true
    })
    
    /**
     * Add Entries
     */
     .autoProvidejQuery()
     .configureFilenames({
        js: '[name].js?[contenthash]',
        css: '[name].css?[contenthash]',
        assets: '[name].[ext]?[hash:8]'
    })
    
    .addAliases({
        '@': path.resolve( __dirname, '../../vendor/vankosoft/application/src/Vankosoft/ApplicationBundle/Resources/themes/default/assets' ),
        '@@': path.resolve( __dirname, '../../vendor/vankosoft/payment-bundle/lib/Resources/assets' )
    })
    
    .copyFiles({
         from: './themes/VideoPlatformTheme/assets/vendor/themeforest-flixgo-online-movies/icon',
         to: 'icon/[path][name].[ext]',
     })
     
     .copyFiles({
         from: './themes/VideoPlatformTheme/assets/vendor/themeforest-flixgo-online-movies/img',
         to: 'img/[path][name].[ext]',
     })
     
    .addStyleEntry( 'css/main', './themes/VideoPlatformTheme/assets/css/main.scss' )
    
    .addEntry( 'js/app', './themes/VideoPlatformTheme/assets/js/app.js' )
    .addEntry( 'js/static-page', './themes/VideoPlatformTheme/assets/js/pages/static-page.js' )
    .addEntry( 'js/home', './themes/VideoPlatformTheme/assets/js/pages/home.js' )
    .addEntry( 'js/contact-page', './themes/VideoPlatformTheme/assets/js/pages/contact-page.js' )
    .addEntry( 'js/help-center', './themes/VideoPlatformTheme/assets/js/pages/help-center.js' )
    .addEntry( 'js/pricing-plans', './themes/VideoPlatformTheme/assets/js/pages/pricing-plans.js' )
    .addEntry( 'js/actors', './themes/VideoPlatformTheme/assets/js/pages/actors.js' )
    .addEntry( 'js/actors-details', './themes/VideoPlatformTheme/assets/js/pages/actors-details.js' )
    .addEntry( 'js/profile', './themes/VideoPlatformTheme/assets/js/pages/profile.js' )
    .addEntry( 'js/movies', './themes/VideoPlatformTheme/assets/js/pages/movies.js' )
    .addEntry( 'js/movies-details', './themes/VideoPlatformTheme/assets/js/pages/movies-details.js' )
;

const config = Encore.getWebpackConfig();
config.name = 'VideoPlatformTheme';

module.exports = config;
