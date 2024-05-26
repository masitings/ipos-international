let AliasPlugin = require('enhanced-resolve/lib/AliasPlugin');
let path = require('path');
let Encore = require('@symfony/webpack-encore');
let rootDir = path.resolve(__dirname + '/../../..');
let fs = require('fs');

let publicDirectoryPath = fs.existsSync(rootDir + '/web') ? rootDir + '/web' : rootDir + '/public';
let appCustomizedFrontendBuilds = fs.existsSync(publicDirectoryPath + '/var/portal-engine/portal-configs/json/customized-frontend-builds.json')
    ? require( publicDirectoryPath + '/var/portal-engine/portal-configs/json/customized-frontend-builds.json')
    :  []
;

let nodeEnv = process.env.NODE_ENV || 'dev';

if (!Encore.isRuntimeEnvironmentConfigured()) {
    Encore.configureRuntimeEnvironment(nodeEnv);
}



// Base Config for Bundle & App
const entryFiles = ['asset-list', 'asset-detail', 'data-object', 'data-object-list', 'content', 'download-cart', 'collection-list', 'collection-detail', 'search-list', 'search-detail', 'public-share-overview', 'public-share-list'];


let webpackConfigs = [];

['Bundle', 'Customized'].map(function(build) {
    Encore.reset();
    let buildFolder = build.toLowerCase();
    // Bundle config
    Encore
        .setOutputPath(__dirname + '/src/Resources/public/build/' + buildFolder + '/')
        .setPublicPath('/bundles/pimcoreportalengine/build/' + buildFolder + '/')
        .setManifestKeyPrefix('bundles/pimcoreportalengine/build/' + buildFolder + '/')
        .enableVersioning()
        .disableSingleRuntimeChunk()
        .enableReactPreset()
        .cleanupOutputBeforeBuild()
        .enableSassLoader()
        .addStyleEntry('portal-engine-style', __dirname + '/assets/styles/index.scss')
        .addStyleEntry('portal-engine-editmode', __dirname + '/assets/styles/editmode.scss')
        .addStyleEntry('portal-engine-email', __dirname + '/assets/styles/email.scss')
        .configureBabel(function(babelConfig) {
            babelConfig.plugins.push('@babel/plugin-transform-async-to-generator');
            babelConfig.plugins.push('@babel/plugin-transform-runtime');
        })
    ;


    if(nodeEnv !== 'production') {
        Encore.enableSourceMaps();
    }

// Entry files
    entryFiles.map(function (fileName) {
        Encore.addEntry(fileName, __dirname + '/assets/scripts/pages/' + fileName + '.js');
    });

    let portalEngineBundleConfig = Encore.getWebpackConfig();
    portalEngineBundleConfig.name = 'portalEngine' + build;

    if(build === 'Bundle') {
        portalEngineBundleConfig.resolve.alias = {
            'portal-engine-bundle': __dirname + '/assets',
            '~portal-engine': __dirname + '/assets'
        };
    } else {
        portalEngineBundleConfig.resolve = {
            plugins: [new AliasPlugin('described-resolve', [
                {
                    name: 'portal-engine-bundle',
                    alias: [
                        __dirname + '/assets',
                    ]
                },
                {
                    name: '~portal-engine',
                    alias: [
                        __dirname + '/assets-customized',
                        __dirname + '/assets',
                    ]
                },
            ], 'resolve')]
        };
    }

    portalEngineBundleConfig.node = {
        child_process: 'empty',
    };

    webpackConfigs.push(portalEngineBundleConfig);
});


appCustomizedFrontendBuilds.map(function (frontendBuild) {

    Encore.reset();

// App config
    Encore
        .setOutputPath(publicDirectoryPath + '/portal-engine/build/' + frontendBuild + '/')
        .setPublicPath('/portal-engine/build/' + frontendBuild )
        .setManifestKeyPrefix('portal-engine/build/' + frontendBuild )
        .enableVersioning()
        .disableSingleRuntimeChunk()
        .enableReactPreset()
        .splitEntryChunks()
        .cleanupOutputBeforeBuild()
        .enableSassLoader()
        .configureBabel(function(babelConfig) {
            babelConfig.plugins.push('@babel/plugin-transform-async-to-generator');
            babelConfig.plugins.push('@babel/plugin-transform-runtime');
        });

    [
        {styleEntry: 'portal-engine-style', file: '/styles/index.scss'},
        {styleEntry: 'portal-engine-editmode', file: '/styles/editmode.scss'},
        {styleEntry: 'portal-engine-email', file: '/styles/email.scss'}].map(function(entry){

        let file = fs.existsSync(publicDirectoryPath + '/portal-engine/' + frontendBuild + entry.file)
            ? publicDirectoryPath + '/portal-engine/' + frontendBuild + entry.file
            : __dirname + '/assets' + entry.file;

        Encore.addStyleEntry(entry.styleEntry, file)
    });

// Entry files
    entryFiles.map(function (fileName) {

        let file = fs.existsSync(publicDirectoryPath + '/portal-engine/' + frontendBuild + '/scripts/pages/' + fileName + '.js')
            ? publicDirectoryPath + '/portal-engine/' + frontendBuild + '/scripts/pages/' + fileName + '.js'
            : __dirname + '/assets/scripts/pages/' + fileName + '.js';


        Encore.addEntry(fileName, file);
    });

    if(nodeEnv !== 'production') {
        Encore.enableSourceMaps();
    }

    let portalEngineAppPortalConfig = Encore.getWebpackConfig();
    portalEngineAppPortalConfig.name = 'portalEngineApp_' + frontendBuild;

    let alias = [
        publicDirectoryPath + '/portal-engine/' + frontendBuild,
        __dirname + '/assets'
    ];

    portalEngineAppPortalConfig.resolve = {
        modules: [rootDir + '/node_modules', 'node_modules'],
        plugins: [new AliasPlugin('described-resolve', [
            {name: 'portal-engine-bundle', alias: __dirname + '/assets'},
            {
                name: '~portal-engine', alias: alias
            },
        ], 'resolve')]
    };

    portalEngineAppPortalConfig.node = {
        child_process: 'empty',
    };

    webpackConfigs.push(portalEngineAppPortalConfig);

});

module.exports = webpackConfigs;