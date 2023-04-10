const webpack = require("webpack");
const paths = require("./config/paths");
const path = require("path");
const { CleanWebpackPlugin } = require("clean-webpack-plugin");
const HTMLWebpackPlugin = require("html-webpack-plugin");
const CopyWebpackPlugin = require("copy-webpack-plugin");
const { merge } = require("webpack-merge");
const loadPreset = require("./config/presets/loadPreset");
const loadConfig = (mode) => require(`./config/webpack.${mode}.js`)(mode);

module.exports = function (env) {
  const { mode = "production" } = env || {};
  return merge(
    {
      mode,
      entry: `${paths.srcPath}/index.js`,
      output: {
        path: paths.distPath,
        filename: "[name].bundle.js",
        publicPath: "/",
      },
      module: {
        rules: [
          {
            test: /\.js$/,
            use: ["babel-loader"],
            exclude: path.resolve(__dirname, "node_modules"),
          },
          // Images: Copy image files to build folder
          { test: /\.(?:ico|gif|png|jpg|jpeg)$/i, type: "asset/resource" },

          // Fonts and SVGs: Inline files
          { test: /\.(woff(2)?|eot|ttf|otf|svg|)$/, type: "asset/inline" },
        ],
      },
      resolve: {
        modules: [paths.srcPath, "node_modules"],
        extensions: [".js", ".jsx", ".json"],
      },
      plugins: [
        // If building for production, delete all files in dist folder before rebuilding
        mode === 'production' ? new CleanWebpackPlugin() : () => {},

        // Copies files from target to destination folder
        new CopyWebpackPlugin({
          patterns: [
            {
              from: paths.publicPath,
              to: "assets",
              globOptions: {
                ignore: ["*.DS_Store"],
              },
              noErrorOnMissing: true,
            },
          ],
        }),
        new HTMLWebpackPlugin({
          template: `${paths.publicPath}/index.html`,
        }),
        new webpack.ProgressPlugin(),
        new webpack.ProvidePlugin({
          "React": "react",
        })
      ],
    },
    loadConfig(mode),
    loadPreset(env)
  );
};
