/**
 * Custom hook for reading WordPress site settings (start_of_week, time_format, date_format).
 *
 * @package TelexHoursBlock
 */

import { useSelect } from '@wordpress/data';

/**
 * Returns an object with common site settings needed by the block.
 *
 * Calls getEntityRecord to trigger the REST API fetch, ensuring the
 * site settings are available.
 *
 * @return {{startOfWeek: number, timeFormat: string, dateFormat: string}} Site settings.
 */
export function useSiteSettings() {
	return useSelect( ( select ) => {
		const store = select( 'core' );
		// Trigger the fetch.
		store.getEntityRecord( 'root', 'site' );
		// Read from the record.
		const siteData = store.getEditedEntityRecord( 'root', 'site' );
		return {
			startOfWeek: siteData?.start_of_week ?? 0,
			timeFormat: siteData?.time_format ?? 'g:i a',
			dateFormat: siteData?.date_format ?? 'F j, Y',
		};
	}, [] );
}
