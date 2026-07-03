import path from 'path';
import { fileURLToPath } from 'url';
import MiniCssExtractPlugin from 'mini-css-extract-plugin';
import RemoveEmptyScripts from 'webpack-remove-empty-scripts';

const __dirname = path.dirname(fileURLToPath(import.meta.url));

const config = {
  mode: process.env.NODE_ENV,
  entry: [
    path.resolve(__dirname, './../Resources/Private/Assets/Scss/backend.scss'),
  ],
  output: {
    path: path.resolve(__dirname, './../Resources/Public'),
  },
  plugins: [
    new RemoveEmptyScripts(),
    new MiniCssExtractPlugin({
      filename: './StyleSheets/backend.css',
    }),
  ],
  module: {
    rules: [
      {
        test: /\.s[ac]ss$/i,
        use: [
          MiniCssExtractPlugin.loader,
          {
            loader: 'css-loader',
            options: {
              sourceMap: true,
              importLoaders: 2,
            },
          },
          {
            loader: 'sass-loader',
            options: {
              sourceMap: true,
            },
          },
        ],
      },
    ],
  },
};

if ('development' === process.env.NODE_ENV) {
  config.devtool = 'source-map';
  config.watch = true;
}

export default config;
