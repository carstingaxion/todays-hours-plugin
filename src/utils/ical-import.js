/**
 * iCal import utility.
 *
 * Reads a .ics file via FileReader and sends its content to the
 * server-side REST endpoint for parsing.
 *
 * @package TelexHoursBlock
 */

import apiFetch from '@wordpress/api-fetch';

/**
 * Reads a File object as text.
 *
 * @param {File} file The file to read.
 * @return {Promise<string>} The file content as a string.
 */
function readFileAsText( file ) {
	return new Promise( ( resolve, reject ) => {
		const reader = new FileReader();
		reader.onload = () => resolve( reader.result );
		reader.onerror = () => reject( reader.error );
		reader.readAsText( file );
	} );
}

/**
 * Imports holidays from an iCal (.ics) file.
 *
 * Reads the file content client-side and sends it to the server
 * for parsing. Returns the parsed holiday objects.
 *
 * @param {File} file The .ics file to import.
 * @return {Promise<Array>} Array of parsed holiday objects.
 */
export async function importIcalFile( file ) {
	const text = await readFileAsText( file );

	const response = await apiFetch( {
		path: '/telex-hours-block/v1/import-ical',
		method: 'POST',
		data: { ical_text: text },
	} );

	return response.holidays || [];
}
