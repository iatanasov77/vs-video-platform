const Encore = require('@symfony/webpack-encore');
const path = require('path');

Encore
    .setOutputPath( 'public/admin-panel/build/custom-entries/' )
    .setPublicPath( '/build/custom-entries/' )
    
    .autoProvidejQuery()
    .enableSassLoader(function(sassOptions) {}, {
        resolveUrlLoader: true
    })
    .configureFilenames({
        js: '[name].js?[contenthash]',
        css: '[name].css?[contenthash]',
        assets: '[name].[ext]?[hash:8]'
    })
    .enableSingleRuntimeChunk()
    .enableVersioning( Encore.isProduction() )
    .enableSourceMaps( !Encore.isProduction() )
    
    .addAliases({
        '@': path.resolve( __dirname, '../../vendor/vankosoft/application/src/Vankosoft/ApplicationBundle/Resources/themes/default/assets' )
    })

    // Custom Entries
    /////////////////////////////////////////////////////////////////////////////////////////////////
    .addStyleEntry( 'css/custom', './assets/admin-panel/css/custom.css' )
    .addEntry( 'js/videos-categories', './assets/admin-panel/js/pages/videos-categories.js' )
    .addEntry( 'js/videos-categories-edit', './assets/admin-panel/js/pages/videos-categories-edit.js' )
    .addEntry( 'js/videos-genres', './assets/admin-panel/js/pages/videos-genres.js' )
    .addEntry( 'js/videos', './assets/admin-panel/js/pages/videos.js' )
    .addEntry( 'js/videos-edit', './assets/admin-panel/js/pages/videos-edit.js' )
    .addEntry( 'js/videos-upload', './assets/admin-panel/js/pages/videos-upload.js' )
    .addEntry( 'js/actors', './assets/admin-panel/js/pages/actors.js' )
    .addEntry( 'js/actors-edit', './assets/admin-panel/js/pages/actors-edit.js' )
    .addEntry( 'js/video-platform-settings', './assets/admin-panel/js/pages/video-platform-settings.js' )
    .addEntry( 'js/video-platform-settings-edit', './assets/admin-panel/js/pages/video-platform-settings-edit.js' )
    .addEntry( 'js/video-platform-storages', './assets/admin-panel/js/pages/video-platform-storages.js' )
    .addEntry( 'js/video-platform-storages-edit', './assets/admin-panel/js/pages/video-platform-storages-edit.js' )
    .addEntry( 'js/google-projects', './assets/admin-panel/js/pages/google-projects.js' )
    .addEntry( 'js/youtube-channels', './assets/admin-panel/js/pages/youtube-channels.js' )
;

const config = Encore.getWebpackConfig();
config.name = 'adminPanelCusstomEntries';

module.exports = config;
