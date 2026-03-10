/**
 * IcalImportButton component — File picker that imports holidays from .ics files.
 *
 * @package
 */

import { __ } from '@wordpress/i18n';
import { Button } from '@wordpress/components';
import { useState, useCallback, useRef } from '@wordpress/element';
import { importIcalFile } from '../utils/ical-import';

/**
 * Renders a button that opens a file picker for .ics files,
 * imports the events via the server-side parser, and calls
 * onImport with the parsed holiday objects.
 *
 * @param {Object}   props          Component props.
 * @param {Function} props.onImport Callback: (holidays) => void.
 * @param {boolean}  props.disabled Whether the button is disabled.
 * @return {import('@wordpress/element').WPElement} Rendered component.
 */
export default function IcalImportButton( { onImport, disabled } ) {
	const [ importing, setImporting ] = useState( false );
	const [ error, setError ] = useState( '' );
	const fileRef = useRef( null );

	const handleClick = useCallback( () => {
		if ( fileRef.current ) {
			fileRef.current.value = '';
			fileRef.current.click();
		}
	}, [] );

	const handleFileChange = useCallback(
		async ( event ) => {
			const file = event.target.files && event.target.files[ 0 ];
			if ( ! file ) {
				return;
			}

			setImporting( true );
			setError( '' );

			try {
				const holidays = await importIcalFile( file );
				if ( holidays.length === 0 ) {
					setError(
						__(
							'No events found in the file.',
							'telex-hours-block'
						)
					);
				} else {
					onImport( holidays );
				}
			} catch ( err ) {
				setError(
					err.message ||
						__( 'Failed to import iCal file.', 'telex-hours-block' )
				);
			} finally {
				setImporting( false );
			}
		},
		[ onImport ]
	);

	return (
		<div>
			<input
				ref={ fileRef }
				type="file"
				accept=".ics,.ical,text/calendar"
				style={ { display: 'none' } }
				onChange={ handleFileChange }
			/>
			<Button
				variant="secondary"
				onClick={ handleClick }
				disabled={ disabled || importing }
				isBusy={ importing }
			>
				{ importing
					? __( 'Importing…', 'telex-hours-block' )
					: __( 'Import from iCal', 'telex-hours-block' ) }
			</Button>
			{ error && (
				<p
					style={ {
						color: '#cc1818',
						fontSize: '12px',
						marginTop: '4px',
					} }
				>
					{ error }
				</p>
			) }
		</div>
	);
}
