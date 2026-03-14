import * as __WEBPACK_EXTERNAL_MODULE__wordpress_interactivity_8e89b257__ from "@wordpress/interactivity";
/******/ var __webpack_modules__ = ({

/***/ "@wordpress/interactivity"
/*!*******************************************!*\
  !*** external "@wordpress/interactivity" ***!
  \*******************************************/
(module) {

module.exports = __WEBPACK_EXTERNAL_MODULE__wordpress_interactivity_8e89b257__;

/***/ }

/******/ });
/************************************************************************/
/******/ // The module cache
/******/ var __webpack_module_cache__ = {};
/******/ 
/******/ // The require function
/******/ function __webpack_require__(moduleId) {
/******/ 	// Check if module is in cache
/******/ 	var cachedModule = __webpack_module_cache__[moduleId];
/******/ 	if (cachedModule !== undefined) {
/******/ 		return cachedModule.exports;
/******/ 	}
/******/ 	// Create a new module (and put it into the cache)
/******/ 	var module = __webpack_module_cache__[moduleId] = {
/******/ 		// no module.id needed
/******/ 		// no module.loaded needed
/******/ 		exports: {}
/******/ 	};
/******/ 
/******/ 	// Execute the module function
/******/ 	if (!(moduleId in __webpack_modules__)) {
/******/ 		delete __webpack_module_cache__[moduleId];
/******/ 		var e = new Error("Cannot find module '" + moduleId + "'");
/******/ 		e.code = 'MODULE_NOT_FOUND';
/******/ 		throw e;
/******/ 	}
/******/ 	__webpack_modules__[moduleId](module, module.exports, __webpack_require__);
/******/ 
/******/ 	// Return the exports of the module
/******/ 	return module.exports;
/******/ }
/******/ 
/************************************************************************/
/******/ /* webpack/runtime/make namespace object */
/******/ (() => {
/******/ 	// define __esModule on exports
/******/ 	__webpack_require__.r = (exports) => {
/******/ 		if(typeof Symbol !== 'undefined' && Symbol.toStringTag) {
/******/ 			Object.defineProperty(exports, Symbol.toStringTag, { value: 'Module' });
/******/ 		}
/******/ 		Object.defineProperty(exports, '__esModule', { value: true });
/******/ 	};
/******/ })();
/******/ 
/************************************************************************/
var __webpack_exports__ = {};
// This entry needs to be wrapped in an IIFE because it needs to be isolated against other modules in the chunk.
(() => {
/*!*********************!*\
  !*** ./src/view.js ***!
  \*********************/
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _wordpress_interactivity__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @wordpress/interactivity */ "@wordpress/interactivity");
/**
 * Business Hours Block — Front-End View (Interactivity API)
 *
 * Re-computes the current day key from the visitor's browser clock
 * and toggles "--today" modifier classes on the correct <dt>/<dd>
 * elements. This ensures the today highlight is always accurate,
 * even when the page is served from an HTML cache.
 *
 * The server already renders "--today" classes as the initial state
 * (using the server's clock), so there is no flash on non-cached
 * page loads. On cached pages the Interactivity API corrects the
 * highlight during hydration.
 *
 * @package
 *
 * @see https://developer.wordpress.org/block-editor/reference-guides/interactivity-api/
 */



/**
 * Maps JS Date.getDay() index to the day key used in the block markup.
 */
const DAY_KEYS = ['sun', 'mon', 'tue', 'wed', 'thu', 'fri', 'sat'];

/**
 * Returns the current day key based on the visitor's local clock.
 *
 * @return {string} Day key string (e.g. 'mon').
 */
function getClientDayKey() {
  return DAY_KEYS[new Date().getDay()];
}
(0,_wordpress_interactivity__WEBPACK_IMPORTED_MODULE_0__.store)('telex/hours-block', {
  state: {
    /**
     * The current day key, re-evaluated client-side on each access
     * so that cached pages self-correct.
     *
     * @return {string} The current day key.
     */
    get currentDayKey() {
      return getClientDayKey();
    },
    /**
     * Whether the current element's day context matches the current day.
     * Used by data-wp-class directives on both <dt> and <dd>.
     *
     * Reads the dayKey from the element's data-wp-context and compares
     * it against the browser's current day.
     *
     * @return {boolean} True if this element represents today.
     */
    get isDayToday() {
      const ctx = (0,_wordpress_interactivity__WEBPACK_IMPORTED_MODULE_0__.getContext)();
      return ctx.dayKey === getClientDayKey();
    },
    /**
     * Alias for isDayToday — used on <dd> elements.
     * Both <dt> and <dd> share the same context-based logic.
     *
     * @return {boolean} True if this element represents today.
     */
    get isHoursToday() {
      const ctx = (0,_wordpress_interactivity__WEBPACK_IMPORTED_MODULE_0__.getContext)();
      return ctx.dayKey === getClientDayKey();
    }
  }
});
})();


//# sourceMappingURL=view.js.map