var ExtractTextPlugin = require("extract-text-webpack-plugin");
const UglifyJsPlugin    = require('uglifyjs-webpack-plugin');
var path = require("path");

module.exports = {
  entry: {
    app: "./src/index.jsx"
  },
  output: {
    path: path.resolve(__dirname, "dist"),
    filename: "adminscript.js"
  },
  module: {
    rules: [
      {
        test: /\.scss$|\.css$/,
        use: ExtractTextPlugin.extract({
          fallback: "style-loader",
          use: ["css-loader", "sass-loader"],
          publicPath: "dist"
        })
      },
      {
        test: /\.jsx?$/,
        exclude: /node_modules/,
        use:[
                {
                  loader: 'babel-loader',
                  options: {
                    presets: ["@babel/env", "@babel/react"],
                    plugins:  ["@babel/plugin-proposal-class-properties"],
                  }
                }
            ],
      },
      {
        test: /\.(jpe?g|png|gif|svg)$/i,
        use: [
          "file-loader?name=[name].[ext]&outputPath=images/&publicPath=",
          "image-webpack-loader"
        ]
      },
      {
        test: /\.(woff2?|svg)$/,
        loader: "url-loader?limit=10000&name=fonts/[name].[ext]"
      },
      {
        test: /\.(ttf|eot)$/,
        loader: "file-loader?name=fonts/[name].[ext]"
      }
    ]
  },
  resolve: {
    extensions: [".css",".js", ".jsx"]
  },
  plugins: [
    new ExtractTextPlugin({
      filename: "style.css",
      allChunks: true
    }),
    new UglifyJsPlugin({
      sourceMap: true,
      cache: true,
      parallel: true,
      uglifyOptions: {
        warnings: false,
        parse: {},
        compress: {},
		mangle: {
			reserved: ['__'],
		},
        output: null
      }
    })
  ]
};
