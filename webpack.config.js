const path = require('path');
const MiniCssExtractPlugin = require("mini-css-extract-plugin");

module.exports = {
	mode: 'development',
	entry: './www/assets/js/index.js', // Entry point for your JavaScript files
	output: {
		filename: 'bundle.js', // Name of file after compilation
		path: path.resolve(__dirname, 'www/dist'), // Destination folder for compiled files
	},
	module: {
		rules: [
			{
				test: /\.s[ac]ss$/i, // Regex to identify .sass and .scss files
				use: [
					MiniCssExtractPlugin.loader, // Creates `style` nodes from JS strings
					'css-loader', // Converts CSS to CommonJS
					'sass-loader',  // Compiles SASS to CSS
				],
			},
		],
	},
	plugins: [
		new MiniCssExtractPlugin({
			filename: 'css/main.css', // Path and name of the generated CSS file
		}),
	],
};