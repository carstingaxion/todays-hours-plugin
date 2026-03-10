/**
 * Mock for @wordpress/components.
 *
 * Provides minimal pass-through components for testing.
 *
 * @package TelexHoursBlock
 */

const React = require( 'react' );

const identity = ( { children, ...props } ) =>
	React.createElement( 'div', props, children );

module.exports = {
	PanelBody: identity,
	PanelRow: identity,
	ToggleControl: identity,
	RadioControl: identity,
	TextControl: identity,
	Button: identity,
	Flex: identity,
	FlexBlock: identity,
	FlexItem: identity,
	Icon: identity,
	Spinner: () => React.createElement( 'div', { className: 'spinner' } ),
	__experimentalText: identity,
};
