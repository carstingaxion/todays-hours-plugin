/**
 * Mock for @wordpress/api-fetch.
 *
 * @package
 */

const apiFetch = jest.fn( () => Promise.resolve( { holidays: [] } ) );

module.exports = apiFetch;
