const Encore = require('@symfony/webpack-encore');
const path = require('path');

Encore
    .setOutputPath( 'public/shared_assets/build/application-simple-theme/' )
    .setPublicPath( '/build/application-simple-theme/' )
  
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
        '@': path.resolve( __dirname, '../../vendor/vankosoft/application/src/Vankosoft/ApplicationBundle/Resources/themes/default/assets' )
    })
    
    .copyFiles({
         from: './themes/ApplicationSimpleTheme/assets/images',
         to: 'images/[path][name].[ext]',
     })
     
    .addStyleEntry( 'css/login', './themes/ApplicationSimpleTheme/assets/css/login.css' )
    .addStyleEntry( 'css/main', './themes/ApplicationSimpleTheme/assets/css/main.scss' )
    
    .addEntry( 'js/login', './themes/ApplicationSimpleTheme/assets/js/login.js' )
    .addEntry( 'js/home', './themes/ApplicationSimpleTheme/assets/js/pages/home.js' )
    
    .addEntry( 'js/youtube-player', './themes/ApplicationSimpleTheme/assets/js/pages/youtube-player.js' )
    .addEntry( 'js/video-player', './themes/ApplicationSimpleTheme/assets/js/pages/video-player.js' )
;

const config = Encore.getWebpackConfig();
config.name = 'applicationTheme_2';

module.exports = config;
