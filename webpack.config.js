var Encore = require('@symfony/webpack-encore');
var CopyWebpackPlugin = require('copy-webpack-plugin');

// Manually configure the runtime environment if not already configured yet by the "encore" command.
// It's useful when you use tools that rely on webpack.config.js file.
if (!Encore.isRuntimeEnvironmentConfigured()) {
    Encore.configureRuntimeEnvironment(process.env.NODE_ENV || 'dev');
}

Encore
    // directory where compiled assets will be stored
    .setOutputPath('public/assets/')
    // public path used by the web server to access the output path
    .setPublicPath('/assets')
    // only needed for CDN's or sub-directory deploy
    //.setManifestKeyPrefix('build/')

    /*
     * ENTRY CONFIG
     *
     * Add 1 entry for each "page" of your app
     * (including one that's included on every page - e.g. "app")
     *
     * Each entry will result in one JavaScript file (e.g. app.js)
     * and one CSS file (e.g. app.css) if your JavaScript imports CSS.
     */
    .addEntry('frontend', './assets/frontend/Main.js')
    .addEntry('frontend.salons', './assets/frontend/Salons/Main.js')
    .addEntry('frontend.catalog', './assets/frontend/Catalog/Main.js')
    .addEntry('frontend.ordering', './assets/frontend/Ordering/Main.js')
    .addEntry('frontend.basket', './assets/frontend/Basket/Basket.js')
    .addEntry('frontend.product', './assets/frontend/Product/Main.js')
    .addEntry('frontend.review', './assets/frontend/Review/Main.js')
    .addEntry('frontend.check-spam', './assets/frontend/CheckSpam/Main.js')
    .addEntry('frontend.quick-order', './assets/frontend/QuickOrder/Main.js')
    .addEntry('frontend.works', './assets/frontend/Works/Main.js')
    .addEntry('frontend.gallery', './assets/frontend/Gallery/Main.js')
    .addEntry('frontend.news', './assets/frontend/News/Main.js')
    .addEntry('frontend.design.project', './assets/frontend/DesignProject/Main.js')
    .addEntry('frontend.quiz', './assets/frontend/Quiz/Main.js')
    .addEntry('ecommerce', './assets/js/ecommerce/Main.js')
    .addEntry('ecommerce.category.tinymce', './assets/js/ecommerce/category/Tinymce.js')
    .addEntry('ecommerce.product.tinymce', './assets/js/ecommerce/product/Tinymce.js')
    .addEntry('cpanel.salon', './assets/js/salon/Main.js')
    .addEntry('cpanel', './assets/js/cpanel.js')
    .addEntry('cpanel.files', './assets/js/cpanel.files.js')
    .addEntry('cpanel.alert.error', './assets/js/cpanel/AlertError.js')
    .addEntry('cpanel.generator.slug', './assets/js/cpanel/GeneratorUrl.js')
    .addEntry('cpanel.tinymce', './assets/js/cpanel.tinymce.js')
    .addEntry('cpanel.gallery', './assets/js/gallery/Main.js')
    .addEntry('cpanel.article', './assets/js/article/Main.js')
    .addEntry('cpanel.works', './assets/js/works/Main.js')
    .addEntry('cpanel.page.tinymce', './assets/js/page/tinymce.js')
    .addEntry('cpanel.works.tinymce', './assets/js/works/tinymce.js')
    .addEntry('cpanel.datetime', './assets/js/cpanel.datetime.js')
    .addEntry('cpanel.order', './assets/js/Order/Main.js')
    .addEntry('cpanel.quiz', './assets/js/Quiz/Main.js')
    .addEntry('ticket.tinymce', './assets/js/ticket.tinymce.js')
    .addEntry('mailer.tinymce', './assets/js/mailer.tinymce.js')
    .addEntry('mailer.files', './assets/js/mailer.files.js')
    .addEntry('mailer', './assets/js/mailer.js')
    .addPlugin(
        new CopyWebpackPlugin({
            patterns: [
                {from: 'node_modules/tinymce/skins', to: 'skins'},
                {from: 'assets/langs', to: 'langs'},
                {from: 'node_modules/tinymce/plugins', to: 'plugins'},
            ],
        })
    )
    //.addEntry('page1', './assets/js/page1.js')
    //.addEntry('page2', './assets/js/page2.js')

    // When enabled, Webpack "splits" your files into smaller pieces for greater optimization.
    .splitEntryChunks()

    // will require an extra script tag for runtime.js
    // but, you probably want this, unless you're building a single-page app
    .enableSingleRuntimeChunk()

    /*
     * FEATURE CONFIG
     *
     * Enable & configure other features below. For a full
     * list of features, see:
     * https://symfony.com/doc/current/frontend.html#adding-more-features
     */
    .cleanupOutputBeforeBuild()
    .enableBuildNotifications()
    .enableSourceMaps(!Encore.isProduction())
    // enables hashed filenames (e.g. app.abc123.css)
    .enableVersioning(Encore.isProduction())

    // enables @babel/preset-env polyfills
    .configureBabel(() => {
    }, {
        useBuiltIns: 'usage',
        corejs: 3
    })

    // enables Sass/SCSS support
    .enableSassLoader()

    // uncomment if you use TypeScript
    //.enableTypeScriptLoader()

    // uncomment to get integrity="..." attributes on your script & link tags
    // requires WebpackEncoreBundle 1.4 or higher
    //.enableIntegrityHashes(Encore.isProduction())

    // uncomment if you're having problems with a jQuery plugin
    .autoProvidejQuery()

// uncomment if you use API Platform Admin (composer req api-admin)
//.enableReactPreset()
//.addEntry('admin', './assets/js/admin.js')
;

module.exports = Encore.getWebpackConfig();
