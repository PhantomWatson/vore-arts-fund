const path = require("path");
const { HotModuleReplacementPlugin } = require("webpack");

module.exports = (paths) => ({
  devtool: false,
  module: {
    rules: [
      {
        test: /\.css$/,
        use: ["style-loader", "css-loader"],
      },
    ],
  },
  devServer: {
    static: paths.distPath,
    port: 3000,
    compress: true,
  },
  plugins: [new HotModuleReplacementPlugin()],
  target: 'web',
  watchOptions: {
    poll: true // Required for WSL
  },
});
