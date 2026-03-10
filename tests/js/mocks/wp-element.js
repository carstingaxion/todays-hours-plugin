/**
 * Mock for @wordpress/element — re-exports React hooks.
 *
 * @package TelexHoursBlock
 */

const React = require( 'react' );

module.exports = {
	useState: React.useState,
	useEffect: React.useEffect,
	useCallback: React.useCallback,
	useMemo: React.useMemo,
	useRef: React.useRef,
	createElement: React.createElement,
	Fragment: React.Fragment,
	forwardRef: React.forwardRef,
	createContext: React.createContext,
	useContext: React.useContext,
};
