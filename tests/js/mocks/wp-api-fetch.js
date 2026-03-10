/**
 * Mock for @wordpress/api-fetch.
 *
 * @package TelexHoursBlock
 */

const apiFetch = jest.fn( () => Promise.resolve( { holidays: [] } ) );

module.exports = apiFetch;
