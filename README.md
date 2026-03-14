# Business Hours Block

**Contributors:**      carstenbach & WordPress Telex  
**Tags:**              business hours, schedule, opening times, block  
**Tested up to:**      7.0  
**Stable tag:**        1.1  
**License:**           GPLv2 or later  
**License URI:**       https://www.gnu.org/licenses/gpl-2.0.html  

Displays the current day's business hours or a full weekly schedule. Supports configurable seasons, holidays, and multiple time slots per day.

[![Playground Demo Link](https://img.shields.io/badge/WordPress_Playground-blue?logo=wordpress&logoColor=%23fff&labelColor=%233858e9&color=%233858e9)](https://playground.wordpress.net/?blueprint-url=https://raw.githubusercontent.com/carstingaxion/todays-hours-plugin/main/.wordpress-org/blueprints/blueprint.json) [![Build, test & measure](https://github.com/carstingaxion/todays-hours-plugin/actions/workflows/build-test-measure.yml/badge.svg?branch=main)](https://github.com/carstingaxion/todays-hours-plugin/actions/workflows/build-test-measure.yml)

## Description

A Gutenberg block that shows business hours on the front end of a WordPress site. Schedule data (seasons and holidays) is stored as site-wide settings and shared across all block instances. Each block instance has its own display settings.

## Features

### Seasons

- Define named time periods (e.g. "Fall Semester", "Summer Break") with start and end dates.
- Each season has a weekly schedule with per-day hours.
- Each day supports multiple time slots (e.g. 8:00 AM -- 11:00 AM and 1:00 PM -- 5:00 PM).
- Leave all slots blank for a day to mark it as closed.

### Holidays / Exceptions

- Override season schedules for specific date ranges.
- Holidays can be year-specific (e.g. 2025-12-25) or recurring every year (month and day only).
- Each holiday supports multiple time slots, or can be left blank to indicate a full-day closure.
- When a holiday applies, its hours replace the season hours for that day.

### iCal Import

- Import holidays from `.ics` (iCal) files directly in the block inspector panel.
- The file is read client-side and parsed server-side via a REST endpoint.
- Parsed events are merged into the existing holidays list.
- The uploaded file is not persisted to the media library.
- All-day events are imported as closed holidays. Timed events are imported with their start and end times as a single time slot.

### Display Modes

- **Full week schedule** -- Shows all seven days of the current season's schedule. Today's row is highlighted. Holidays are checked per-day across the displayed week.
- **Today's hours only** -- Shows only the current day's hours (or a closed message).

### Display Options

- **Show today's date** -- Displays the current date above the schedule, formatted according to the WordPress date format setting.
- **Show reason when closed** -- When a holiday causes a closure, displays the holiday name (e.g. "Closed for Thanksgiving"). Works in both display modes.
- **Use "Noon" and "Midnight"** -- Replaces "12:00 AM" with "Midnight" and "12:00 PM" with "Noon".
- **Hide weekend days** -- Removes Saturday and Sunday from both the week view and the day view.

### Localization

- Respects the WordPress `time_format` setting for time display on the front end.
- Respects the WordPress `date_format` setting for the date display.
- Respects the WordPress `start_of_week` setting for day ordering in both the editor and front end.
- Day names are localized using `wp_date()` (front end) and `dateI18n()` from `@wordpress/date` (editor), so they appear in the site's configured language.
- All plugin strings use the `telex-hours-block` text domain.

### Site-Wide Data

- Seasons and holidays are stored as WordPress site options (`telex_hours_seasons` and `telex_hours_holidays`).
- All block instances on the site share the same schedule data.
- Any block instance's inspector panel can edit the shared seasons and holidays.
- Each block instance retains its own display settings (mode, date visibility, friendly labels, hide weekends).

### Front-End Output

- Server-side rendered on each page load.
- Outputs semantic HTML using `<dl>`/`<dt>`/`<dd>` with `<time>` elements and proper `datetime` attributes.
- Includes a JSON-LD `<script>` block with schema.org `Place` and `OpeningHoursSpecification` structured data, generated from the active season and holidays. Google recommends JSON-LD over inline microdata.
- A small front-end script highlights the current day's row in the week view.
- BEM-structured CSS class names.
- Responsive styles for small screens (below 480px).

### Editor Experience

- Live preview of the schedule in the block editor, matching the front-end output.
- Inspector panel sections for Display Settings, Seasons/Semesters, and Holidays/Exceptions.
- Collapsible season and holiday editors with add/remove controls.
- Per-day time slot management with add/remove slot buttons.
- Month/day picker for recurring holidays (no year).
- Full date picker for year-specific holidays.
- Supports block color, typography, spacing, and alignment settings.

### Architecture

- Main plugin class and renderer class both use the Singleton pattern.
- Editor code is split into separate utility, hook, and component modules.
- Settings are registered with `show_in_rest` schemas and read/written via the WordPress core data store (`useEntityProp` / `editEntityRecord`).
- Sanitization callbacks handle both the current array-of-slots format and a legacy single open/close format.

## Installation

1. Upload the plugin files to `/wp-content/plugins/telex-hours-block`, or install through the WordPress plugins screen.
2. Activate the plugin through the Plugins screen.
3. Add the "Business Hours Block" to any post, page, or widget area via the block editor.
4. Configure seasons, holidays, and display options in the block's inspector panel.

## FAQ

### How do I set up a schedule?

Insert the block, then open the block settings panel on the right. Under "Seasons / Semesters", add a season with a name, date range, and per-day hours. You can add multiple time slots per day.

### What if today does not fall within any season?

The block displays "No active season for today." in week mode, or "Closed Today" in day mode.

### How do recurring holidays work?

When adding a holiday, enable "Repeats every year". The date fields switch to month/day-only pickers. The holiday will match on that month and day regardless of the year.

### Can I have multiple time ranges in one day?

Yes. Each day in a season and each holiday supports multiple time slots. Use the "+ Add time slot" button to add additional open/close pairs.

### Does this work with page caching?

The block renders server-side on each request. If you use full-page caching, the displayed hours will reflect the time the cache was generated. You may need to exclude pages with this block from caching or use a short cache TTL.

### Are seasons and holidays shared across blocks?

Yes. All block instances read from and write to the same site-wide settings. Editing seasons or holidays from one block's inspector panel updates all instances.

### How do I import holidays from an iCal file?

In the block inspector under "Holidays / Exceptions", click "Import from iCal" and select a `.ics` file. The events are parsed and added to the holidays list. The file itself is not stored.


## Changelog

All notable changes to this project will be documented in the [CHANGELOG.md](CHANGELOG.md).


## License

GPLv2 or later. See https://www.gnu.org/licenses/gpl-2.0.html
