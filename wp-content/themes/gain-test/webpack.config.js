// Webpack Config for JS, SCSS, Images, Fonts & SVG's
const webpack = require('webpack');
const path = require('path');
const TerserPlugin = require('terser-webpack-plugin');
const MiniCssExtractPlugin = require('mini-css-extract-plugin');
const globImporter = require('node-sass-glob-importer');
const { CleanWebpackPlugin } = require('clean-webpack-plugin');
const ImageminPlugin = require('imagemin-webpack-plugin').default;
const { WebpackManifestPlugin } = require('webpack-manifest-plugin');
const WebpackBar = require('webpackbar');
const Dotenv = require('dotenv-webpack');

module.exports = (env, argv) => {
    const devMode = argv.mode === 'development';

    const args = {
        entry: {
            app: path.resolve(__dirname, './assets/javascript/app.js'),
            editor: path.resolve(__dirname, './assets/javascript/editor.js'),
        },
        output: {
            filename: devMode ? '[name].js' : '[name].[contenthash].min.js',
            chunkFilename: devMode ? '[name].js' : '[name].[chunkhash].js',
            path: path.resolve(__dirname, './public/dist/'),
        },
        devtool: 'source-map',
        module: {
            rules: [
                {
                    test: /\.jsx?$/,
                    exclude: /node_modules/,
                    use: {
                        loader: 'babel-loader',
                        options: {
                            presets: [
                                [
                                    '@babel/preset-env',
                                    {
                                        targets: {
                                            node: 'current',
                                        },
                                    },
                                ],
                                '@babel/preset-react',
                            ],
                            plugins: [
                                '@babel/plugin-proposal-export-default-from',
                                '@babel/plugin-proposal-class-properties',
                            ],
                        },
                    },
                    resolve: {
                        extensions: ['.js', '.jsx'],
                    },
                },
                {
                    test: /\.s?css$/,
                    use: [
                        MiniCssExtractPlugin.loader,
                        {
                            loader: 'css-loader',
                            options: { sourceMap: true },
                        },
                        {
                            loader: 'postcss-loader',
                            options: { sourceMap: true },
                        },
                        { loader: 'resolve-url-loader' },
                        {
                            loader: 'sass-loader',
                            options: {
                                sourceMap: true,
                                sassOptions: {
                                    importer: globImporter(),
                                },
                            },
                        },
                    ],
                },
                {
                    test: /\.(png|jpe?g|gif|webp)$/i,
                    type: 'asset/resource',
                    generator: {
                        filename: './images/[name][ext]',
                    },
                },
                {
                    test: /\.(svg)$/,
                    type: 'asset/resource',
                    generator: {
                        filename: './svgs/[name][ext]',
                    },
                    use: [
                        {
                            loader: 'svgo-loader',
                            options: {
                                plugins: [
                                    { name: 'removeTitle' },
                                    {
                                        name: 'convertColors',
                                        params: { shorthex: false },
                                    },
                                    { name: 'convertPathData' },
                                ],
                            },
                        },
                    ],
                },
                {
                    test: /\.(woff(2)?|ttf|eot)$/,
                    type: 'asset/resource',
                    generator: {
                        filename: './fonts/[name][ext]',
                    },
                },
            ],
        },
        externals: {
            jquery: 'jQuery',
        },
        plugins: [
            new CleanWebpackPlugin(),
            new MiniCssExtractPlugin({
                filename: devMode
                    ? '[name].css'
                    : '[name].[contenthash].min.css',
                chunkFilename: devMode
                    ? '[id].css'
                    : '[id].[contenthash].min.css',
            }),
            new ImageminPlugin({
                test: /\.(png|jpe?g|gif|svg|webp)$/i,
                cacheFolder: './imgcache',
            }),
            new WebpackManifestPlugin({
                publicPath: '',
            }),
            new webpack.ProvidePlugin({
                $: 'jquery',
                jQuery: 'jquery',
                'window.jQuery': 'jquery',
            }),
            new WebpackBar(),
            new Dotenv({
                path: '../../../.env.webpack',
                systemvars: true,
            }),
        ],
    };

    if (!devMode) {
        args.optimization = {
            minimize: true,
            minimizer: [
                new TerserPlugin({
                    parallel: true,
                    terserOptions: { output: { comments: false } },
                }),
            ],
        };
    }

    return args;
};
