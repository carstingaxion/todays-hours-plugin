/**
 * Mock for @wordpress/block-editor.
 *
 * @package
 */

const React = require( 'react' );

function useBlockProps( props ) {
	return { ...props, className: props?.className || '' };
}

function InspectorControls( { children } ) {
	return React.createElement(
		'div',
		{ className: 'inspector-controls' },
		children
	);
}

module.exports = { useBlockProps, InspectorControls };
