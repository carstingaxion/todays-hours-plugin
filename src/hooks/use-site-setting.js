/**
 * Custom hook for reading and writing a site setting via the core data store.
 *
 * @package TelexHoursBlock
 */

import { useCallback } from '@wordpress/element';
import { useSelect, useDispatch } from '@wordpress/data';

/**
 * Reads and writes a site-level setting using WordPress core data store.
 *
 * Calls getEntityRecord to trigger the REST API fetch, then reads
 * from getEditedEntityRecord to include any local edits.
 *
 * @param {string} settingKey The setting key (e.g. 'telex_hours_seasons').
 * @param {*}      fallback   Fallback value before the entity resolves.
 * @return {Array} Tuple of [currentValue, setValue, hasResolved].
 */
export function useSiteSetting( settingKey, fallback ) {
	const { editedValue, hasResolved } = useSelect(
		( select ) => {
			const store = select( 'core' );
			// Trigger the fetch by calling getEntityRecord.
			store.getEntityRecord( 'root', 'site' );
			// Read from the edited record to pick up local changes.
			const record = store.getEditedEntityRecord( 'root', 'site' );
			const resolved = store.hasFinishedResolution( 'getEntityRecord', [ 'root', 'site' ] );
			return {
				editedValue: record ? record[ settingKey ] : undefined,
				hasResolved: resolved,
			};
		},
		[ settingKey ]
	);

	const { editEntityRecord } = useDispatch( 'core' );

	const setValue = useCallback(
		( newValue ) => {
			editEntityRecord( 'root', 'site', undefined, {
				[ settingKey ]: newValue,
			} );
		},
		[ settingKey, editEntityRecord ]
	);

	const currentValue = hasResolved && editedValue !== undefined ? editedValue : fallback;

	return [ currentValue, setValue, hasResolved ];
}
