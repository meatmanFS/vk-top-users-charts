const path = require('path');
const webpack = require('webpack');
module.exports = {
	devtool: 'cheap-module-source-map',
	context: path.join(__dirname, 'resources/assets/js'),
	entry: './app.js',
	output: {
		path: path.join(__dirname, 'public/assets/js'),
		filename: 'app.js',
	},
	// Step 2: Node environment
	plugins: [
		new webpack.DefinePlugin({
			'process.env': {
				'NODE_ENV': JSON.stringify('production')
			}
		}),
		new webpack.optimize.UglifyJsPlugin({
            compress: {
                warnings: false
            }
        })
	],
	module: {
		loaders: [
			{
				test: /\.js$/,
				exclude: /node_modules/,
				loaders: ['babel?presets[]=es2015&presets[]=react']
			}
		]
	}
};
