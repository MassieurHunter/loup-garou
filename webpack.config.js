var path = require('path');
var webpack = require('webpack');

module.exports = {
  entry: ["babel-polyfill", './dev/js/main.js'],
  output: {
    path: path.resolve(__dirname, 'public/js/'),
    filename: 'main.js'
  },
  module: {
    rules: [
      {
        test: /\.js$/,
        loader: 'babel-loader',
        exclude: /node_modules/,
        query: {
          presets: ['env']
        }
      }
    ]
  },
  watchOptions: {
    poll: true
  },
  stats: {
    colors: true
  },
  devtool: 'source-map',
  plugins: [
    new webpack.DefinePlugin({
    }),
    new webpack.ProvidePlugin({
      '__DEV__': true,
      $: 'jquery',
      jQuery: 'jquery',
      'window.jQuery': 'jquery',
      Popper: ['popper.js', 'default']
    })
  ]
};