const path = require("path");

const paths = {
  srcPath: path.resolve(__dirname, "./src"),
  distPath: path.resolve(__dirname, "./dist"),
  publicPath: path.resolve(__dirname, "./public"),
  nodeModulesPath: path.resolve(__dirname, "./node_modules"),
};

const defaultConfig = require("../../config/webpack/webpack.default.js");

module.exports = (env) => defaultConfig(env, paths);
