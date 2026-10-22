/**
 * Webpack config for the Gutenberg blocks bundle (dist/blocks.js).
 *
 * The main webpack.config.js builds the admin SPA into build/main.js.
 * This config builds src/blocks_src/blocks.js into dist/blocks.js,
 * which is what the block editor enqueues (see
 * wpsp-blocks/classes/class-bsf-sp-init-blocks.php).
 *
 * Usage: npx webpack --config webpack.blocks.config.js
 */
process.env.NODE_ENV = process.env.NODE_ENV || 'production';

const defaultConfig = require('@wordpress/scripts/config/webpack.config');
const path = require('path');

// *.lazy.scss files are consumed through the style-loader lazy API
// (styles.use()/unuse()), so they must not go through the default
// extract-based SCSS rule.
const defaultRules = defaultConfig.module.rules.map((rule) => {
	if (rule.test && String(rule.test).includes('sc|sa')) {
		return {
			...rule,
			exclude: rule.exclude
				? [/\.lazy\.scss$/].concat(rule.exclude)
				: [/\.lazy\.scss$/],
		};
	}
	return rule;
});

module.exports = {
	...defaultConfig,
	entry: {
		blocks: path.resolve(__dirname, 'src/blocks_src/blocks.js'),
	},
	output: {
		...defaultConfig.output,
		path: path.resolve(__dirname, 'dist'),
		// Chunk URLs carry no ?ver= query, so without a hash a plugin
		// update can serve a previous version's cached chunk from the
		// same URL, which crashes the block with a ChunkLoadError.
		// A content hash makes every build's chunk URLs unique.
		chunkFilename: '[name].[contenthash:8].js',
	},
	module: {
		...defaultConfig.module,
		rules: [
			{
				test: /\.lazy\.scss$/,
				use: [
					{
						loader: 'style-loader',
						options: {
							injectType: 'lazyStyleTag',
							attributes: { 'data-wpsp': 'editor-lazy-style' },
							// With Block API v3 the editor canvas is an
							// iframe — styles must land in its document,
							// not the parent page.
							insert: function insertIntoEditorCanvas(element) {
								const canvas = document.querySelector(
									'iframe[name="editor-canvas"]'
								);
								const targetDocument =
									canvas && canvas.contentDocument
										? canvas.contentDocument
										: document;
								targetDocument.head.appendChild(element);
							},
						},
					},
					'css-loader',
					'sass-loader',
				],
			},
			...defaultRules,
		],
	},
	resolve: {
		...defaultConfig.resolve,
		alias: {
			...defaultConfig.resolve.alias,
			'@Controls': path.resolve(__dirname, 'wpsp-config/controls'),
			'@Components': path.resolve(__dirname, 'src/blocks_src/components/'),
		},
	},
};
