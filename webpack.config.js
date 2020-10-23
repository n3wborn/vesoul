var Encore = require('@symfony/webpack-encore');
const CopyWebpackPlugin = require('copy-webpack-plugin');

// Manually configure the runtime environment if not already configured yet by the "encore" command.
// It's useful when you use tools that rely on webpack.config.js file.
if (!Encore.isRuntimeEnvironmentConfigured()) {
    Encore.configureRuntimeEnvironment(process.env.NODE_ENV || 'dev');
}

Encore

    .setOutputPath('public/build/')
    .setPublicPath('/build')
    //.setManifestKeyPrefix('build/')

    // Activer SASS
    .enableSassLoader()
    // Activer TS
    .enableTypeScriptLoader()
    // Activer jQuery
    .autoProvideVariables({
        $: 'jquery',
        jQuery: 'jquery',
        'window.jQuery': 'jquery',
    })

    .configureBabel( function(babelConfig){
        babelConfig.plugins.push('@babel/plugin-transform-async-to-generator');
        babelConfig.plugins.push('@babel/plugin-transform-runtime');
        babelConfig.plugins.push('babel-plugin-syntax-async-functions');
    })

    .addEntry('front', ['./assets/front/js/layout-front.js'])
    .addEntry('admin', './assets/back/js/layout-back.js')
    .addPlugin(new CopyWebpackPlugin(
        {
            patterns: [
                { from: './assets/front/static', to: 'images' },
            ]
        }
    ))

    .enableSingleRuntimeChunk()
    .cleanupOutputBeforeBuild()
    .enableBuildNotifications()
    .enableSourceMaps(!Encore.isProduction())
    .enableVersioning(Encore.isProduction())

;

// Use polling instead of inotify
const config = Encore.getWebpackConfig();
config.watchOptions = {
    poll: true,
};

// Export the final configuration
module.exports = config;