/**
 * Jest configuration for Business Hours Block JavaScript tests.
 *
 * Uses @wordpress/scripts default preset for proper Babel/JSX support,
 * with custom module name mappings for WordPress package mocks.
 *
 * @package TelexHoursBlock
 */

module.exports = {
	testEnvironment: 'jsdom',
	setupFiles: [ '<rootDir>/tests/js/setup.js' ],
	testMatch: [ '<rootDir>/tests/js/**/*.test.js' ],
	transform: {
		'\\.[jt]sx?$': 'babel-jest',
	},
	transformIgnorePatterns: [
		'/node_modules/(?!@wordpress/).+\\.js$',
	],
	moduleNameMapper: {
		'\\.(scss|css)$': '<rootDir>/tests/js/mocks/style-mock.js',
		'^@wordpress/i18n$': '<rootDir>/tests/js/mocks/wp-i18n.js',
		'^@wordpress/date$': '<rootDir>/tests/js/mocks/wp-date.js',
		'^@wordpress/element$': '<rootDir>/tests/js/mocks/wp-element.js',
		'^@wordpress/data$': '<rootDir>/tests/js/mocks/wp-data.js',
		'^@wordpress/components$': '<rootDir>/tests/js/mocks/wp-components.js',
		'^@wordpress/block-editor$': '<rootDir>/tests/js/mocks/wp-block-editor.js',
		'^@wordpress/api-fetch$': '<rootDir>/tests/js/mocks/wp-api-fetch.js',
	},
};
