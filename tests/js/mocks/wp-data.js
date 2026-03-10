/**
 * Mock for @wordpress/data.
 *
 * @package TelexHoursBlock
 */

function useSelect( mapSelect ) {
	return mapSelect( () => ( {
		getEntityRecord: () => ( {} ),
		getEditedEntityRecord: () => ( {} ),
		hasFinishedResolution: () => true,
	} ) );
}

function useDispatch() {
	return {
		editEntityRecord: jest.fn(),
		saveEditedEntityRecord: jest.fn(),
	};
}

module.exports = { useSelect, useDispatch };
