const path = require('path');
const HtmlWebpackPlugin = require('html-webpack-plugin');
module.exports = (env, argv) => {
  const isDev = argv.mode === "development";

  return {
    entry: [
      './src/index.js',
    ],
    output: {
      path: path.join(__dirname, '/dist'),
      filename: 'bundle.js',
      publicPath: isDev ? 'http://vore.test:9000/vote-app/dist/' : '/vote-app/dist/',
    },
    devServer: {
      headers: {
        "Access-Control-Allow-Origin": "*",
        "Access-Control-Allow-Headers": "X-Requested-With, content-type, Authorization"
      },
      host: 'vore.test',
      port: 8081,
    },
    module: {
      rules: [
        {
          test: /\.jsx?$/,
          exclude: /node_modules/,
          loader: 'babel-loader',
        },
        {
          test: /\.css$/,
          use: [ 'style-loader', 'css-loader' ]
        }
      ]
    },
    plugins: [],
    target: isDev ? "web" : "browserslist:modern",
  };
}
