/**
 * ESLint configuration for Business Hours Block.
 *
 * Extends the default WordPress scripts configuration with
 * overrides for test files.
 *
 * @package
 */

module.exports = {
	extends: [ 'plugin:@wordpress/eslint-plugin/recommended' ],
	overrides: [
		{
			files: [ 'tests/js/**/*.js' ],
			env: {
				jest: true,
			},
			rules: {
				'import/no-extraneous-dependencies': 'off',
			},
		},
	],
};
