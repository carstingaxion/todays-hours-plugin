/******/ (() => { // webpackBootstrap
/*!*********************!*\
  !*** ./src/view.js ***!
  \*********************/
/**
 * Business Hours Block — Front-End View Script
 *
 * Ensures the current day is highlighted in the weekly schedule
 * and provides any client-side interactivity.
 *
 * @package TelexHoursBlock
 *
 * @see https://developer.wordpress.org/block-editor/reference-guides/block-api/block-metadata/#view-script
 */

(function () {
  'use strict';

  /**
   * Day key map indexed by Date.getDay() value.
   *
   * @type {string[]}
   */
  var dayKeys = ['sun', 'mon', 'tue', 'wed', 'thu', 'fri', 'sat'];

  /**
   * Initializes all Business Hours Block instances on the page.
   *
   * Finds each block wrapper, determines today's day of the week,
   * and applies the '--today' modifier class to the corresponding
   * dt and dd elements for visual highlighting.
   *
   * @return {void}
   */
  function init() {
    var blocks = document.querySelectorAll('.wp-block-telex-block-telex-hours-block');
    var todayIndex = new Date().getDay();
    var todayKey = dayKeys[todayIndex];
    blocks.forEach(function (block) {
      var dts = block.querySelectorAll('dt.telex-hours-block__day');
      var dds = block.querySelectorAll('dd.telex-hours-block__hours');
      dts.forEach(function (dt) {
        if (dt.getAttribute('data-day') === todayKey) {
          dt.classList.add('telex-hours-block__day--today');
        }
      });
      dds.forEach(function (dd) {
        if (dd.getAttribute('data-day') === todayKey) {
          dd.classList.add('telex-hours-block__hours--today');
        }
      });
    });
  }
  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', init);
  } else {
    init();
  }
})();
/******/ })()
;
//# sourceMappingURL=view.js.map