/**
 * WordPress dependencies
 */
import { __ } from '@wordpress/i18n';
import { useBlockProps, InspectorControls } from '@wordpress/block-editor';
import {
	PanelBody,
	PanelRow,
	ToggleControl,
	RadioControl,
	Button,
    Flex,
	Spinner,
	// eslint-disable-next-line @wordpress/no-unsafe-wp-apis
	__experimentalText as Text,
} from '@wordpress/components';
import { useCallback, useMemo } from '@wordpress/element';
import { useDispatch } from '@wordpress/data';
import { dateI18n } from '@wordpress/date';

/**
 * Internal dependencies — sorting helpers
 */
import { getSortableBeginDate } from './utils/sorting';

/**
 * Internal dependencies — utils
 */
import { getOrderedDays, getDefaultHours } from './utils/days';
import { applyFriendlyTwelves, formatTimeWithSiteFormat } from './utils/time';

/**
 * Internal dependencies — hooks
 */
import { useSiteSetting } from './hooks/use-site-setting';
import { useSiteSettings } from './hooks/use-site-settings';
import { useSchedulePreview } from './hooks/use-schedule-preview';

/**
 * Internal dependencies — components
 */
import SeasonEditor from './components/season-editor';
import HolidayEditor from './components/holiday-editor';
import IcalImportButton from './components/ical-import-button';
import WeekView from './components/week-view';
import DayView from './components/day-view';

/**
 * Editor-only styles.
 */
import './editor.scss';

/**
 * Main edit component for the Business Hours Block.
 *
 * @param {Object}   props               Component props.
 * @param {Object}   props.attributes    Block attributes.
 * @param {Function} props.setAttributes Block attribute setter.
 * @return {import('@wordpress/element').WPElement} Rendered component.
 */
export default function Edit( { attributes, setAttributes } ) {
	const {
		displayMode,
		showTodaysDate,
		showReasonClosed,
		friendlyTwelves,
		hideWeekends,
	} = attributes;

	const blockProps = useBlockProps( {
		className: 'telex-hours-block',
	} );

	// Site-wide schedule data.
	const [ seasons, setSeasons, seasonsLoaded ] = useSiteSetting( 'telex_hours_seasons', [] );
	const [ holidays, setHolidays, holidaysLoaded ] = useSiteSetting( 'telex_hours_holidays', [] );
	const hasLoaded = seasonsLoaded && holidaysLoaded;

	const { saveEditedEntityRecord } = useDispatch( 'core' );

	const rawSeasons = Array.isArray( seasons ) ? seasons : [];
	const rawHolidays = Array.isArray( holidays ) ? holidays : [];

	// Sort seasons and holidays by begin date, preserving original indices.
	const sortedSeasons = useMemo( () => {
		return rawSeasons
			.map( ( s, i ) => ( { item: s, originalIndex: i } ) )
			.sort( ( a, b ) => {
				const aKey = getSortableBeginDate( a.item.beginDate );
				const bKey = getSortableBeginDate( b.item.beginDate );
				return aKey.localeCompare( bKey );
			} );
	}, [ rawSeasons ] );

	const sortedHolidays = useMemo( () => {
		return rawHolidays
			.map( ( h, i ) => ( { item: h, originalIndex: i } ) )
			.sort( ( a, b ) => {
				const aKey = getSortableBeginDate( a.item.beginDate );
				const bKey = getSortableBeginDate( b.item.beginDate );
				return aKey.localeCompare( bKey );
			} );
	}, [ rawHolidays ] );

	const currentSeasons = rawSeasons;
	const currentHolidays = rawHolidays;

	const saveSettings = useCallback( () => {
		saveEditedEntityRecord( 'root', 'site' );
	}, [ saveEditedEntityRecord ] );

	// Site display settings.
	const { startOfWeek, timeFormat, dateFormat } = useSiteSettings();

	const orderedDays = useMemo(
		() => getOrderedDays( startOfWeek ),
		[ startOfWeek ]
	);

	// Schedule preview computation.
	const preview = useSchedulePreview( currentSeasons, currentHolidays );

	// Season CRUD handlers.
	const updateSeason = useCallback(
		( idx, newSeason ) => {
			const updated = [ ...currentSeasons ];
			updated[ idx ] = newSeason;
			setSeasons( updated );
			saveSettings();
		},
		[ currentSeasons, setSeasons, saveSettings ]
	);

	const removeSeason = useCallback(
		( idx ) => {
			const updated = currentSeasons.filter( ( _, i ) => i !== idx );
			setSeasons( updated );
			saveSettings();
		},
		[ currentSeasons, setSeasons, saveSettings ]
	);

	const addSeason = useCallback( () => {
		const updated = [
			...currentSeasons,
			{
				name: '',
				beginDate: '',
				endDate: '',
				hours: getDefaultHours(),
			},
		];
		setSeasons( updated );
		saveSettings();
	}, [ currentSeasons, setSeasons, saveSettings ] );

	// Holiday CRUD handlers.
	const updateHoliday = useCallback(
		( idx, newHoliday ) => {
			const updated = [ ...currentHolidays ];
			updated[ idx ] = newHoliday;
			setHolidays( updated );
			saveSettings();
		},
		[ currentHolidays, setHolidays, saveSettings ]
	);

	const removeHoliday = useCallback(
		( idx ) => {
			const updated = currentHolidays.filter( ( _, i ) => i !== idx );
			setHolidays( updated );
			saveSettings();
		},
		[ currentHolidays, setHolidays, saveSettings ]
	);

	const addHoliday = useCallback( () => {
		const updated = [
			...currentHolidays,
			{
				name: '',
				beginDate: '',
				endDate: '',
				slots: [ { open: '', close: '' } ],
			},
		];
		setHolidays( updated );
		saveSettings();
	}, [ currentHolidays, setHolidays, saveSettings ] );

	const importHolidays = useCallback(
		( imported ) => {
			const updated = [ ...currentHolidays, ...imported ];
			setHolidays( updated );
			saveSettings();
		},
		[ currentHolidays, setHolidays, saveSettings ]
	);

	// Time display formatting.
	const formatDisplayTime = useCallback(
		( rawTime ) => {
			if ( ! rawTime ) {
				return rawTime;
			}
			const friendly = applyFriendlyTwelves( rawTime, friendlyTwelves );
			if ( friendly !== rawTime ) {
				return friendly;
			}
			return formatTimeWithSiteFormat( rawTime, timeFormat );
		},
		[ friendlyTwelves, timeFormat ]
	);

	/**
	 * Converts a time string to 24-hour HH:MM format for datetime attributes.
	 *
	 * @param {string} timeStr The input time string.
	 * @return {string} Time in HH:MM format, or empty string.
	 */
	const to24h = useCallback( ( timeStr ) => {
		if ( ! timeStr ) {
			return '';
		}
		const parsed = new Date( '2000-01-01 ' + timeStr );
		if ( isNaN( parsed.getTime() ) ) {
			return '';
		}
		const hh = String( parsed.getHours() ).padStart( 2, '0' );
		const mm = String( parsed.getMinutes() ).padStart( 2, '0' );
		return hh + ':' + mm;
	}, [] );

	/**
	 * Renders an array of time slots as JSX with <time> elements.
	 *
	 * @param {Array} slotsArr Array of slot objects.
	 * @return {Array|null} JSX elements or null if no open slots.
	 */
	const renderSlots = useCallback(
		( slotsArr ) => {
			const openSlots = slotsArr.filter( ( s ) => s.open && s.open.trim() !== '' );
			if ( openSlots.length === 0 ) {
				return null;
			}
			return openSlots.map( ( slot, si ) => (
				<span key={ si }>
					{ si > 0 && <br /> }
					<span className="telex-hours-block__slot">
						<time dateTime={ to24h( slot.open ) }>
							{ formatDisplayTime( slot.open ) }
						</time>
						<span className="telex-hours-block__separator">{ '\u2013' }</span>
						<time dateTime={ to24h( slot.close ) }>
							{ formatDisplayTime( slot.close ) }
						</time>
					</span>
				</span>
			) );
		},
		[ formatDisplayTime, to24h ]
	);

	const formatDate = ( date ) => {
		return dateI18n( dateFormat, date );
	};

	return (
		<>
			<InspectorControls>
				<PanelBody
					title={ __( 'Display Settings', 'telex-hours-block' ) }
					initialOpen={ true }
				>
					<RadioControl
						label={ __( 'Display Mode', 'telex-hours-block' ) }
						selected={ displayMode }
						options={ [
							{
								label: __( 'Full week schedule', 'telex-hours-block' ),
								value: 'week',
							},
							{
								label: __( "Today's hours only", 'telex-hours-block' ),
								value: 'day',
							},
						] }
						onChange={ ( val ) => setAttributes( { displayMode: val } ) }
					/>
					<ToggleControl
						label={ __( "Show today's date", 'telex-hours-block' ) }
						checked={ showTodaysDate }
						onChange={ ( val ) => setAttributes( { showTodaysDate: val } ) }
						__nextHasNoMarginBottom
					/>
					<ToggleControl
						label={ __( 'Show reason when closed', 'telex-hours-block' ) }
						help={ __(
							'Displays the holiday name when closed due to a holiday.',
							'telex-hours-block'
						) }
						checked={ showReasonClosed }
						onChange={ ( val ) => setAttributes( { showReasonClosed: val } ) }
						__nextHasNoMarginBottom
					/>
					<ToggleControl
						label={ __( 'Use "Noon" and "Midnight"', 'telex-hours-block' ) }
						help={ __(
							'Replace 12:00 AM with "Midnight" and 12:00 PM with "Noon".',
							'telex-hours-block'
						) }
						checked={ friendlyTwelves }
						onChange={ ( val ) => setAttributes( { friendlyTwelves: val } ) }
						__nextHasNoMarginBottom
					/>
					<ToggleControl
						label={ __( 'Hide weekend days', 'telex-hours-block' ) }
						help={ __(
							'Hides Saturday and Sunday from the schedule.',
							'telex-hours-block'
						) }
						checked={ hideWeekends }
						onChange={ ( val ) => setAttributes( { hideWeekends: val } ) }
						__nextHasNoMarginBottom
					/>
				</PanelBody>

				<PanelBody
					title={ __( 'Seasons / Semesters', 'telex-hours-block' ) }
					initialOpen={ false }
				>
					<PanelRow>
						<Text variant="muted" size="12">
							{ __(
								'Define periods with specific weekly schedules. These settings are shared across all Business Hours blocks on this site.',
								'telex-hours-block'
							) }
						</Text>
					</PanelRow>
					{ ! hasLoaded && <Spinner /> }
					{ hasLoaded &&
						sortedSeasons.map( ( { item: season, originalIndex } ) => (
							<SeasonEditor
								key={ originalIndex }
								season={ season }
								index={ originalIndex }
								orderedDays={ orderedDays }
								onChange={ updateSeason }
								onRemove={ removeSeason }
							/>
						) ) }
					<PanelRow>
						<Button
							variant="secondary"
							onClick={ addSeason }
							disabled={ ! hasLoaded }
						>
							{ __( 'Add Season', 'telex-hours-block' ) }
						</Button>
					</PanelRow>
				</PanelBody>

				<PanelBody
					title={ __( 'Holidays / Exceptions', 'telex-hours-block' ) }
					initialOpen={ false }
				>
					<PanelRow>
						<Text variant="muted" size="12">
							{ __(
								'Holidays override season hours for specific date ranges. These settings are shared across all Business Hours blocks on this site.',
								'telex-hours-block'
							) }
						</Text>
					</PanelRow>
					{ ! hasLoaded && <Spinner /> }
					{ hasLoaded &&
						sortedHolidays.map( ( { item: holiday, originalIndex } ) => (
							<HolidayEditor
								key={ originalIndex }
								holiday={ holiday }
								index={ originalIndex }
								onChange={ updateHoliday }
								onRemove={ removeHoliday }
							/>
						) ) }
					<PanelRow>
						<Flex direction="column" gap={ 2 }>
							<Button
								variant="secondary"
								onClick={ addHoliday }
								disabled={ ! hasLoaded }
							>
								{ __( 'Add Holiday', 'telex-hours-block' ) }
							</Button>
							<IcalImportButton
								onImport={ importHolidays }
								disabled={ ! hasLoaded }
							/>
						</Flex>
					</PanelRow>
				</PanelBody>
			</InspectorControls>

			<div { ...blockProps }>
				{ showTodaysDate && (
					<p className="telex-hours-block__date">
						{ formatDate( preview.today ) }
					</p>
				) }
				{ displayMode === 'week' ? (
					<WeekView
						hasLoaded={ hasLoaded }
						preview={ preview }
						orderedDays={ orderedDays }
						hideWeekends={ hideWeekends }
						showReasonClosed={ showReasonClosed }
						renderSlots={ renderSlots }
					/>
				) : (
					<DayView
						hasLoaded={ hasLoaded }
						preview={ preview }
						hideWeekends={ hideWeekends }
						showReasonClosed={ showReasonClosed }
						renderSlots={ renderSlots }
					/>
				) }
			</div>
		</>
	);
}
