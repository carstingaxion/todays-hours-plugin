/**
 * Mock for @wordpress/i18n.
 *
 * @package TelexHoursBlock
 */

function __( text ) {
	return text;
}

function _x( text ) {
	return text;
}

function _n( single, plural, number ) {
	return number === 1 ? single : plural;
}

function sprintf( format, ...args ) {
	let i = 0;
	return format.replace( /%s/g, () => args[ i++ ] || '' );
}

module.exports = { __, _x, _n, sprintf };
