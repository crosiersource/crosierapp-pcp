/* eslint-disable */
const Encore = require('@symfony/webpack-encore');
const path = require('path');

// Manually configure the runtime environment if not already configured yet by the "encore" command.
// It's useful when you use tools that rely on webpack.config.js file.
if (!Encore.isRuntimeEnvironmentConfigured()) {
  Encore.configureRuntimeEnvironment(process.env.NODE_ENV || 'dev');
}

// noinspection NpmUsedModulesInstalled
const webpack = require('webpack');

const CopyWebpackPlugin = require('copy-webpack-plugin');

// noinspection JSValidateTypes
Encore
  .setOutputPath('public/build/')
  .setPublicPath('/build')
  .autoProvidejQuery()
  .addPlugin(new CopyWebpackPlugin({
    patterns: [
      {from: "./assets/static", to: "static"},
    ],
  }))


  // --------------------------------------------
  .addEntry('tipoInsumoList', './assets/js/tipoInsumoList.js')
  .addEntry('tipoArtigoList', './assets/js/tipoArtigoList.js')
  
  
  .addEntry('fichaTecnicaList', './assets/js/fichaTecnicaList.js')
  .addEntry('loteProducaoList', './assets/js/loteProducaoList.js')
  .addEntry('loteProducaoForm', './assets/js/loteProducaoForm.js')
  .addEntry('fichaTecnica', './assets/js/fichaTecnica.js')
  .addEntry('fichaTecnica_clonar', './assets/js/fichaTecnica_clonar.js')
  .addEntry('fichaTecnicaItemForm', './assets/js/fichaTecnicaItemForm.js')

  .addEntry('cliente/list', './assets/js/Cliente/list.js')
  .addEntry('cliente/form', './assets/js/Cliente/form.js')

  .addEntry('insumo/list', './assets/js/Insumo/list.js')
  .addEntry('insumo/form', './assets/js/Insumo/form.js')
  .addEntry('Insumo/alteracaoLote', './assets/js/Insumo/alteracaoLote.js')
  // --------------------------------------------

  .splitEntryChunks()

  // se deixar habilitado não funciona o datatables e o select2 (parece que começa a fazer 2 chamadas para montá-los no código)
  .disableSingleRuntimeChunk()

  .cleanupOutputBeforeBuild()
  .enableBuildNotifications()
  .enableSourceMaps(!Encore.isProduction())
  .enableVersioning(Encore.isProduction())
  .configureBabelPresetEnv((config) => {
    config.useBuiltIns = 'usage';
    config.corejs = 3;
  })
  .configureBabel((config) => {
    config.plugins.push('@babel/plugin-proposal-class-properties');
  })
  .enableVueLoader(function (options) {
    options.loaders = {
      // vue: {loader: 'babel-loader'}
    };
  }, {version: 3})
  .addAliases({
    '@': path.resolve(__dirname, 'assets', 'js'),
    styles: path.resolve(__dirname, 'assets', 'scss'),
  })
  .enableEslintLoader({
    configFile: "./.eslintrc.js",
  })
  .configureCssLoader((config) => {
    if (!Encore.isProduction() && config.modules) {
      config.modules.localIdentName = '[name]_[local]_[hash:base64:5]';
    }
  })
  .enableSassLoader()
  .addLoader({
    test: /\.js$/,
    loader: 'babel-loader',
    options: {
      plugins: [require("@babel/plugin-proposal-optional-chaining")]
    },
    exclude: file => (
      /node_modules/.test(file) &&
      !/\.vue\.js/.test(file)
    )
  })
;


config = Encore.getWebpackConfig();
config.watchOptions = {
  aggregateTimeout: 1500,
};

module.exports = config;

