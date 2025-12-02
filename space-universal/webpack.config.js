const Encore = require('@symfony/webpack-encore');

// Manually configure the runtime environment if not already configured
if (!Encore.isRuntimeEnvironmentConfigured()) {
    Encore.configureRuntimeEnvironment(process.env.NODE_ENV || 'dev');
}

Encore
    .setOutputPath('public/build/')
    .setPublicPath('/build')
    .addEntry('app', './assets/app.js') // Main app entry point
    .addEntry('cosmic-background', './assets/js/cosmic-background.js') // Cosmic background script
    .enableSingleRuntimeChunk()
    .cleanupOutputBeforeBuild()
    .enableSourceMaps(!Encore.isProduction())
    .enableVersioning(Encore.isProduction())
    .configureBabel((config) => {
        config.presets = []; // Clear existing presets
        config.presets.push(['@babel/preset-env', { corejs: '3', useBuiltIns: 'usage' }]);
    })
    .enablePostCssLoader((options) => {
        options.postcssOptions = {
            plugins: [
                require('tailwindcss')('./tailwind.config.js'),
                require('autoprefixer'),
            ],
        };
    });

module.exports = Encore.getWebpackConfig();
