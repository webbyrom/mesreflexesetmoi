const path = require('path');
const isDevelopment = process.env.NODE_ENV === 'development';
const MiniCssExtractPlugin = require('mini-css-extract-plugin');
const { BundleAnalyzerPlugin } = require('webpack-bundle-analyzer');
const { https } = require('follow-redirects');
const { Stats } = require('webpack');

module.exports = {
    mode: isDevelopment ? 'development' : 'production',
    entry: './src/index.js', // Point d'entrée pour le JS
    output: {
        path: path.resolve(__dirname, 'build'), // Chemin de sortie des fichiers compilés
        filename: '[name].js', // Nom du fichier de sortie JS
    },
    module: {
        rules: [
            {
                test: /\.scss$/,
                use: [
                    MiniCssExtractPlugin.loader,
                    {
                        loader: 'css-loader',
                        options: {
                            importLoaders: 2, // Conservez cette option si vous l'aviez ajoutée
                        },
                    },
                    {
                        loader: 'postcss-loader',
                        options: {
                            postcssOptions: {
                                plugins: [
                                    require('autoprefixer'),
                                ],
                            },
                        },
                    },
                    'sass-loader',
                ],
            },
            {
                test: /\.svg$/,
                use: ['svg-loader'],
            },
            {
                test: /\.(png|jpg|jpeg|gif)$/i, // Gérer les fichiers PNG, JPG, JPEG, GIF
                type: 'asset/resource',
                generator: {
                    filename: 'images/[name][hash][ext]',
                }// Utilise file-loader ou url-loader selon  besoins
            },
            {
                test: /\.js$/,
                exclude: /node_modules/,
                use: {
                    loader: 'babel-loader',
                    options: {
                        presets: ['@babel/preset-env'],
                    },
                },
            },
            {
                test: /\.(woff2?|ttf|eot|otf)$/i, // Simplification et ? pour woff2 optionnel
                include: path.resolve(__dirname, 'src/assets/fonts'), // Assurez-vous que ce chemin est correct
                type: 'asset/resource',
                generator: {
                    filename: 'fonts/[name][ext]',
                },
            },
        ],
    },
    plugins: [
        new MiniCssExtractPlugin({
            filename: 'style.css', // Le fichier CSS sera sauvegardé comme style.css
        }),
        new BundleAnalyzerPlugin(),
    ],
    devtool: isDevelopment ? 'eval-source-map' : 'source-map',
    devServer: {
        server: 'https', // Utilise HTTPS
        static: path.join(__dirname, 'build'),
        compress: true,
        port: 9000,
        hot: true,
        open: true,
        watchFiles: ['src/**/*', 'src/**/*.scss'],
        devMiddleware: {
            writeToDisk: true, // Écrit les fichiers sur le disque
        },
    },
    stats: { 
        errorDetails: true,
    },
};
