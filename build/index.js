/******/ (() => { // webpackBootstrap
/******/ 	"use strict";
/******/ 	var __webpack_modules__ = ({

/***/ "./src/components/day-view.js"
/*!************************************!*\
  !*** ./src/components/day-view.js ***!
  \************************************/
(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (/* binding */ DayView)
/* harmony export */ });
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n");
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _wordpress_components__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @wordpress/components */ "@wordpress/components");
/* harmony import */ var _wordpress_components__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_wordpress_components__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _utils_slots__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ../utils/slots */ "./src/utils/slots.js");
/* harmony import */ var _utils_days__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ../utils/days */ "./src/utils/days.js");
/* harmony import */ var react_jsx_runtime__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! react/jsx-runtime */ "react/jsx-runtime");
/* harmony import */ var react_jsx_runtime__WEBPACK_IMPORTED_MODULE_4___default = /*#__PURE__*/__webpack_require__.n(react_jsx_runtime__WEBPACK_IMPORTED_MODULE_4__);
/**
 * DayView component — Renders today's hours only in the editor preview.
 *
 * @package TelexHoursBlock
 */






/**
 * Renders the "today only" view in the editor preview.
 *
 * @param {Object}   props                  Component props.
 * @param {boolean}  props.hasLoaded        Whether settings have loaded.
 * @param {Object}   props.preview          Preview data from useSchedulePreview.
 * @param {boolean}  props.hideWeekends     Whether to hide weekend days.
 * @param {boolean}  props.showReasonClosed Whether to show closed reason.
 * @param {Function} props.renderSlots      Function to render slots as JSX.
 * @return {import('@wordpress/element').WPElement|null} Rendered component.
 */

function DayView({
  hasLoaded,
  preview,
  hideWeekends,
  showReasonClosed,
  renderSlots
}) {
  if (!hasLoaded) {
    return /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_4__.jsx)(_wordpress_components__WEBPACK_IMPORTED_MODULE_1__.Spinner, {});
  }
  const {
    slots,
    holidayName,
    dayKey
  } = preview;
  if (hideWeekends && _utils_days__WEBPACK_IMPORTED_MODULE_3__.WEEKEND_KEYS.includes(dayKey)) {
    return null;
  }
  const hasOpen = (0,_utils_slots__WEBPACK_IMPORTED_MODULE_2__.slotsHaveOpen)(slots);
  if (!hasOpen) {
    let closedText = (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__.__)('Closed Today', 'telex-hours-block');
    if (showReasonClosed && holidayName) {
      closedText = (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__.__)('Closed for ', 'telex-hours-block') + holidayName;
    }
    return /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_4__.jsx)("p", {
      className: "telex-hours-block__today-hours telex-hours-block__today-hours--closed",
      children: closedText
    });
  }
  return /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_4__.jsx)("p", {
    className: "telex-hours-block__today-hours",
    children: renderSlots(slots)
  });
}

/***/ },

/***/ "./src/components/holiday-editor.js"
/*!******************************************!*\
  !*** ./src/components/holiday-editor.js ***!
  \******************************************/
(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (/* binding */ HolidayEditor)
/* harmony export */ });
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n");
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _wordpress_components__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @wordpress/components */ "@wordpress/components");
/* harmony import */ var _wordpress_components__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_wordpress_components__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @wordpress/element */ "@wordpress/element");
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_wordpress_element__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var _utils_dates__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ../utils/dates */ "./src/utils/dates.js");
/* harmony import */ var _utils_slots__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! ../utils/slots */ "./src/utils/slots.js");
/* harmony import */ var _time_slot_row__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! ./time-slot-row */ "./src/components/time-slot-row.js");
/* harmony import */ var _month_day_picker__WEBPACK_IMPORTED_MODULE_6__ = __webpack_require__(/*! ./month-day-picker */ "./src/components/month-day-picker.js");
/* harmony import */ var react_jsx_runtime__WEBPACK_IMPORTED_MODULE_7__ = __webpack_require__(/*! react/jsx-runtime */ "react/jsx-runtime");
/* harmony import */ var react_jsx_runtime__WEBPACK_IMPORTED_MODULE_7___default = /*#__PURE__*/__webpack_require__.n(react_jsx_runtime__WEBPACK_IMPORTED_MODULE_7__);
/**
 * HolidayEditor component — Inspector panel editor for a single holiday.
 *
 * @package TelexHoursBlock
 */









/**
 * Renders the editor for a single holiday in the inspector panel.
 *
 * @param {Object}   props           Component props.
 * @param {Object}   props.holiday   Holiday data object.
 * @param {number}   props.index     Index of this holiday in the array.
 * @param {Function} props.onChange  Callback: (index, updatedHoliday) => void.
 * @param {Function} props.onRemove Callback: (index) => void.
 * @return {import('@wordpress/element').WPElement} Rendered component.
 */

function HolidayEditor({
  holiday,
  index,
  onChange,
  onRemove
}) {
  const [isOpen, setIsOpen] = (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_2__.useState)(false);
  const isRecurring = !(0,_utils_dates__WEBPACK_IMPORTED_MODULE_3__.dateHasYear)(holiday.beginDate) && !(0,_utils_dates__WEBPACK_IMPORTED_MODULE_3__.dateHasYear)(holiday.endDate);
  const updateField = (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_2__.useCallback)((field, value) => {
    onChange(index, {
      ...holiday,
      [field]: value
    });
  }, [index, holiday, onChange]);
  const toggleRecurring = (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_2__.useCallback)(recurring => {
    const updated = {
      ...holiday
    };
    if (recurring) {
      if ((0,_utils_dates__WEBPACK_IMPORTED_MODULE_3__.dateHasYear)(updated.beginDate)) {
        const {
          month,
          day: d
        } = (0,_utils_dates__WEBPACK_IMPORTED_MODULE_3__.parseMonthDay)(updated.beginDate);
        updated.beginDate = month + '-' + d;
      }
      if ((0,_utils_dates__WEBPACK_IMPORTED_MODULE_3__.dateHasYear)(updated.endDate)) {
        const {
          month,
          day: d
        } = (0,_utils_dates__WEBPACK_IMPORTED_MODULE_3__.parseMonthDay)(updated.endDate);
        updated.endDate = month + '-' + d;
      }
    } else {
      const year = new Date().getFullYear();
      if (!(0,_utils_dates__WEBPACK_IMPORTED_MODULE_3__.dateHasYear)(updated.beginDate) && updated.beginDate) {
        updated.beginDate = year + '-' + updated.beginDate;
      }
      if (!(0,_utils_dates__WEBPACK_IMPORTED_MODULE_3__.dateHasYear)(updated.endDate) && updated.endDate) {
        updated.endDate = year + '-' + updated.endDate;
      }
    }
    onChange(index, updated);
  }, [index, holiday, onChange]);
  const holidaySlots = (0,_utils_slots__WEBPACK_IMPORTED_MODULE_4__.normalizeHolidaySlots)(holiday);
  const currentSlots = holidaySlots.length > 0 ? holidaySlots : [{
    open: '',
    close: ''
  }];
  const updateSlotTime = (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_2__.useCallback)((slotIndex, timeKey, value) => {
    const updated = currentSlots.map((s, si) => si === slotIndex ? {
      ...s,
      [timeKey]: value
    } : s);
    onChange(index, {
      ...holiday,
      slots: updated,
      openTime: undefined,
      closeTime: undefined
    });
  }, [index, holiday, currentSlots, onChange]);
  const addSlot = (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_2__.useCallback)(() => {
    const updated = [...currentSlots, {
      open: '',
      close: ''
    }];
    onChange(index, {
      ...holiday,
      slots: updated,
      openTime: undefined,
      closeTime: undefined
    });
  }, [index, holiday, currentSlots, onChange]);
  const removeSlot = (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_2__.useCallback)(slotIndex => {
    const updated = currentSlots.filter((_, si) => si !== slotIndex);
    onChange(index, {
      ...holiday,
      slots: updated.length > 0 ? updated : [{
        open: '',
        close: ''
      }],
      openTime: undefined,
      closeTime: undefined
    });
  }, [index, holiday, currentSlots, onChange]);
  return /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_7__.jsxs)("div", {
    className: "telex-hours-block-inspector__holiday",
    children: [/*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_7__.jsxs)(_wordpress_components__WEBPACK_IMPORTED_MODULE_1__.Flex, {
      align: "center",
      justify: "space-between",
      children: [/*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_7__.jsx)(_wordpress_components__WEBPACK_IMPORTED_MODULE_1__.FlexBlock, {
        children: /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_7__.jsxs)(_wordpress_components__WEBPACK_IMPORTED_MODULE_1__.Button, {
          variant: "link",
          onClick: () => setIsOpen(!isOpen),
          className: "telex-hours-block-inspector__holiday-toggle",
          children: [/*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_7__.jsx)(_wordpress_components__WEBPACK_IMPORTED_MODULE_1__.Icon, {
            icon: isOpen ? 'arrow-up-alt2' : 'arrow-down-alt2'
          }), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_7__.jsx)("span", {
            children: holiday.name || (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__.__)('Holiday', 'telex-hours-block') + ' ' + (index + 1)
          })]
        })
      }), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_7__.jsx)(_wordpress_components__WEBPACK_IMPORTED_MODULE_1__.FlexItem, {
        children: /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_7__.jsx)(_wordpress_components__WEBPACK_IMPORTED_MODULE_1__.Button, {
          variant: "tertiary",
          isDestructive: true,
          size: "small",
          onClick: () => onRemove(index),
          label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__.__)('Remove holiday', 'telex-hours-block'),
          icon: "trash"
        })
      })]
    }), isOpen && /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_7__.jsxs)("div", {
      className: "telex-hours-block-inspector__holiday-details",
      children: [/*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_7__.jsx)(_wordpress_components__WEBPACK_IMPORTED_MODULE_1__.TextControl, {
        label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__.__)('Name', 'telex-hours-block'),
        value: holiday.name,
        onChange: val => updateField('name', val),
        __nextHasNoMarginBottom: true
      }), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_7__.jsx)(_wordpress_components__WEBPACK_IMPORTED_MODULE_1__.ToggleControl, {
        label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__.__)('Repeats every year', 'telex-hours-block'),
        checked: isRecurring,
        onChange: toggleRecurring,
        __nextHasNoMarginBottom: true
      }), isRecurring ? /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_7__.jsxs)(_wordpress_components__WEBPACK_IMPORTED_MODULE_1__.Flex, {
        children: [/*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_7__.jsx)(_wordpress_components__WEBPACK_IMPORTED_MODULE_1__.FlexBlock, {
          children: /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_7__.jsx)(_month_day_picker__WEBPACK_IMPORTED_MODULE_6__["default"], {
            label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__.__)('Begin Date', 'telex-hours-block'),
            value: holiday.beginDate || '01-01',
            onChange: val => updateField('beginDate', val)
          })
        }), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_7__.jsx)(_wordpress_components__WEBPACK_IMPORTED_MODULE_1__.FlexBlock, {
          children: /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_7__.jsx)(_month_day_picker__WEBPACK_IMPORTED_MODULE_6__["default"], {
            label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__.__)('End Date', 'telex-hours-block'),
            value: holiday.endDate || '01-01',
            onChange: val => updateField('endDate', val)
          })
        })]
      }) : /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_7__.jsxs)(_wordpress_components__WEBPACK_IMPORTED_MODULE_1__.Flex, {
        children: [/*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_7__.jsx)(_wordpress_components__WEBPACK_IMPORTED_MODULE_1__.FlexBlock, {
          children: /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_7__.jsx)(_wordpress_components__WEBPACK_IMPORTED_MODULE_1__.TextControl, {
            label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__.__)('Begin Date', 'telex-hours-block'),
            type: "date",
            value: holiday.beginDate,
            onChange: val => updateField('beginDate', val),
            __nextHasNoMarginBottom: true
          })
        }), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_7__.jsx)(_wordpress_components__WEBPACK_IMPORTED_MODULE_1__.FlexBlock, {
          children: /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_7__.jsx)(_wordpress_components__WEBPACK_IMPORTED_MODULE_1__.TextControl, {
            label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__.__)('End Date', 'telex-hours-block'),
            type: "date",
            value: holiday.endDate,
            onChange: val => updateField('endDate', val),
            __nextHasNoMarginBottom: true
          })
        })]
      }), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_7__.jsx)(_wordpress_components__WEBPACK_IMPORTED_MODULE_1__.__experimentalText, {
        variant: "muted",
        size: "11",
        upperCase: true,
        weight: 500,
        children: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__.__)('Leave all slots blank if closed all day', 'telex-hours-block')
      }), currentSlots.map((slot, si) => /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_7__.jsx)(_time_slot_row__WEBPACK_IMPORTED_MODULE_5__["default"], {
        slot: slot,
        slotIndex: si,
        onUpdate: updateSlotTime,
        onRemove: removeSlot,
        canRemove: currentSlots.length > 1
      }, si)), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_7__.jsx)(_wordpress_components__WEBPACK_IMPORTED_MODULE_1__.Button, {
        variant: "link",
        size: "small",
        onClick: addSlot,
        className: "telex-hours-block-inspector__add-slot",
        children: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__.__)('+ Add time slot', 'telex-hours-block')
      })]
    })]
  });
}

/***/ },

/***/ "./src/components/ical-import-button.js"
/*!**********************************************!*\
  !*** ./src/components/ical-import-button.js ***!
  \**********************************************/
(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (/* binding */ IcalImportButton)
/* harmony export */ });
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n");
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _wordpress_components__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @wordpress/components */ "@wordpress/components");
/* harmony import */ var _wordpress_components__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_wordpress_components__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @wordpress/element */ "@wordpress/element");
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_wordpress_element__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var _utils_ical_import__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ../utils/ical-import */ "./src/utils/ical-import.js");
/* harmony import */ var react_jsx_runtime__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! react/jsx-runtime */ "react/jsx-runtime");
/* harmony import */ var react_jsx_runtime__WEBPACK_IMPORTED_MODULE_4___default = /*#__PURE__*/__webpack_require__.n(react_jsx_runtime__WEBPACK_IMPORTED_MODULE_4__);
/**
 * IcalImportButton component — File picker that imports holidays from .ics files.
 *
 * @package TelexHoursBlock
 */






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

function IcalImportButton({
  onImport,
  disabled
}) {
  const [importing, setImporting] = (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_2__.useState)(false);
  const [error, setError] = (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_2__.useState)('');
  const fileRef = (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_2__.useRef)(null);
  const handleClick = (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_2__.useCallback)(() => {
    if (fileRef.current) {
      fileRef.current.value = '';
      fileRef.current.click();
    }
  }, []);
  const handleFileChange = (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_2__.useCallback)(async event => {
    const file = event.target.files && event.target.files[0];
    if (!file) {
      return;
    }
    setImporting(true);
    setError('');
    try {
      const holidays = await (0,_utils_ical_import__WEBPACK_IMPORTED_MODULE_3__.importIcalFile)(file);
      if (holidays.length === 0) {
        setError((0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__.__)('No events found in the file.', 'telex-hours-block'));
      } else {
        onImport(holidays);
      }
    } catch (err) {
      setError(err.message || (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__.__)('Failed to import iCal file.', 'telex-hours-block'));
    } finally {
      setImporting(false);
    }
  }, [onImport]);
  return /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_4__.jsxs)("div", {
    children: [/*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_4__.jsx)("input", {
      ref: fileRef,
      type: "file",
      accept: ".ics,.ical,text/calendar",
      style: {
        display: 'none'
      },
      onChange: handleFileChange
    }), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_4__.jsx)(_wordpress_components__WEBPACK_IMPORTED_MODULE_1__.Button, {
      variant: "secondary",
      onClick: handleClick,
      disabled: disabled || importing,
      isBusy: importing,
      children: importing ? (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__.__)('Importing…', 'telex-hours-block') : (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__.__)('Import from iCal', 'telex-hours-block')
    }), error && /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_4__.jsx)("p", {
      style: {
        color: '#cc1818',
        fontSize: '12px',
        marginTop: '4px'
      },
      children: error
    })]
  });
}

/***/ },

/***/ "./src/components/month-day-picker.js"
/*!********************************************!*\
  !*** ./src/components/month-day-picker.js ***!
  \********************************************/
(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (/* binding */ MonthDayPicker)
/* harmony export */ });
/* harmony import */ var _wordpress_components__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @wordpress/components */ "@wordpress/components");
/* harmony import */ var _wordpress_components__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_wordpress_components__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _utils_dates__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ../utils/dates */ "./src/utils/dates.js");
/* harmony import */ var react_jsx_runtime__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! react/jsx-runtime */ "react/jsx-runtime");
/* harmony import */ var react_jsx_runtime__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(react_jsx_runtime__WEBPACK_IMPORTED_MODULE_2__);
/**
 * MonthDayPicker component — Selects a month and day without year.
 *
 * @package TelexHoursBlock
 */




/**
 * Renders a month/day picker for recurring (yearless) holiday dates.
 *
 * @param {Object}   props          Component props.
 * @param {string}   props.label    The label text.
 * @param {string}   props.value    Current value in MM-DD format.
 * @param {Function} props.onChange Callback with the new MM-DD string.
 * @return {import('@wordpress/element').WPElement} Rendered component.
 */

function MonthDayPicker({
  label,
  value,
  onChange
}) {
  const {
    month,
    day
  } = (0,_utils_dates__WEBPACK_IMPORTED_MODULE_1__.parseMonthDay)(value);
  const monthOptions = (0,_utils_dates__WEBPACK_IMPORTED_MODULE_1__.getMonthOptions)();
  const dayOptions = (0,_utils_dates__WEBPACK_IMPORTED_MODULE_1__.getDayOptions)(month);
  return /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_2__.jsxs)("div", {
    children: [/*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_2__.jsx)(_wordpress_components__WEBPACK_IMPORTED_MODULE_0__.__experimentalText, {
      as: "label",
      size: "11",
      upperCase: true,
      weight: 500,
      className: "components-base-control__label",
      children: label
    }), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_2__.jsxs)(_wordpress_components__WEBPACK_IMPORTED_MODULE_0__.Flex, {
      gap: 2,
      children: [/*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_2__.jsx)(_wordpress_components__WEBPACK_IMPORTED_MODULE_0__.FlexBlock, {
        children: /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_2__.jsx)("select", {
          className: "components-select-control__input",
          value: month,
          onChange: e => {
            const newMonth = e.target.value;
            const newDayOpts = (0,_utils_dates__WEBPACK_IMPORTED_MODULE_1__.getDayOptions)(newMonth);
            const clampedDay = parseInt(day, 10) > newDayOpts.length ? String(newDayOpts.length).padStart(2, '0') : day;
            onChange(newMonth + '-' + clampedDay);
          },
          style: {
            width: '100%',
            minHeight: '36px'
          },
          children: monthOptions.map(opt => /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_2__.jsx)("option", {
            value: opt.value,
            children: opt.label
          }, opt.value))
        })
      }), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_2__.jsx)(_wordpress_components__WEBPACK_IMPORTED_MODULE_0__.FlexItem, {
        children: /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_2__.jsx)("select", {
          className: "components-select-control__input",
          value: day,
          onChange: e => {
            onChange(month + '-' + e.target.value);
          },
          style: {
            minHeight: '36px'
          },
          children: dayOptions.map(opt => /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_2__.jsx)("option", {
            value: opt.value,
            children: opt.label
          }, opt.value))
        })
      })]
    })]
  });
}

/***/ },

/***/ "./src/components/season-editor.js"
/*!*****************************************!*\
  !*** ./src/components/season-editor.js ***!
  \*****************************************/
(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (/* binding */ SeasonEditor)
/* harmony export */ });
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n");
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _wordpress_components__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @wordpress/components */ "@wordpress/components");
/* harmony import */ var _wordpress_components__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_wordpress_components__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @wordpress/element */ "@wordpress/element");
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_wordpress_element__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var _utils_slots__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ../utils/slots */ "./src/utils/slots.js");
/* harmony import */ var _time_slot_row__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! ./time-slot-row */ "./src/components/time-slot-row.js");
/* harmony import */ var react_jsx_runtime__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! react/jsx-runtime */ "react/jsx-runtime");
/* harmony import */ var react_jsx_runtime__WEBPACK_IMPORTED_MODULE_5___default = /*#__PURE__*/__webpack_require__.n(react_jsx_runtime__WEBPACK_IMPORTED_MODULE_5__);
/**
 * SeasonEditor component — Inspector panel editor for a single season.
 *
 * @package TelexHoursBlock
 */







/**
 * Renders the editor for a single season in the inspector panel.
 *
 * @param {Object}   props             Component props.
 * @param {Object}   props.season      Season data object.
 * @param {number}   props.index       Index of this season in the array.
 * @param {Array}    props.orderedDays Ordered array of {key, label} day objects.
 * @param {Function} props.onChange    Callback: (index, updatedSeason) => void.
 * @param {Function} props.onRemove   Callback: (index) => void.
 * @return {import('@wordpress/element').WPElement} Rendered component.
 */

function SeasonEditor({
  season,
  index,
  orderedDays,
  onChange,
  onRemove
}) {
  const [isOpen, setIsOpen] = (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_2__.useState)(false);
  const updateField = (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_2__.useCallback)((field, value) => {
    onChange(index, {
      ...season,
      [field]: value
    });
  }, [index, season, onChange]);
  const updateSlotTime = (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_2__.useCallback)((dayKey, slotIndex, timeKey, value) => {
    const currentSlots = (0,_utils_slots__WEBPACK_IMPORTED_MODULE_3__.normalizeSlots)(season.hours?.[dayKey]);
    const updatedSlots = currentSlots.map((s, si) => si === slotIndex ? {
      ...s,
      [timeKey]: value
    } : s);
    const updatedHours = {
      ...season.hours,
      [dayKey]: updatedSlots
    };
    onChange(index, {
      ...season,
      hours: updatedHours
    });
  }, [index, season, onChange]);
  const addSlot = (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_2__.useCallback)(dayKey => {
    const currentSlots = (0,_utils_slots__WEBPACK_IMPORTED_MODULE_3__.normalizeSlots)(season.hours?.[dayKey]);
    const updatedSlots = [...currentSlots, {
      open: '',
      close: ''
    }];
    const updatedHours = {
      ...season.hours,
      [dayKey]: updatedSlots
    };
    onChange(index, {
      ...season,
      hours: updatedHours
    });
  }, [index, season, onChange]);
  const removeSlot = (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_2__.useCallback)((dayKey, slotIndex) => {
    const currentSlots = (0,_utils_slots__WEBPACK_IMPORTED_MODULE_3__.normalizeSlots)(season.hours?.[dayKey]);
    const updatedSlots = currentSlots.filter((_, si) => si !== slotIndex);
    const updatedHours = {
      ...season.hours,
      [dayKey]: updatedSlots.length > 0 ? updatedSlots : [{
        open: '',
        close: ''
      }]
    };
    onChange(index, {
      ...season,
      hours: updatedHours
    });
  }, [index, season, onChange]);
  return /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_5__.jsxs)("div", {
    className: "telex-hours-block-inspector__season",
    children: [/*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_5__.jsxs)(_wordpress_components__WEBPACK_IMPORTED_MODULE_1__.Flex, {
      align: "center",
      justify: "space-between",
      children: [/*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_5__.jsx)(_wordpress_components__WEBPACK_IMPORTED_MODULE_1__.FlexBlock, {
        children: /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_5__.jsxs)(_wordpress_components__WEBPACK_IMPORTED_MODULE_1__.Button, {
          variant: "link",
          onClick: () => setIsOpen(!isOpen),
          className: "telex-hours-block-inspector__season-toggle",
          children: [/*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_5__.jsx)(_wordpress_components__WEBPACK_IMPORTED_MODULE_1__.Icon, {
            icon: isOpen ? 'arrow-up-alt2' : 'arrow-down-alt2'
          }), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_5__.jsx)("span", {
            children: season.name || (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__.__)('Season', 'telex-hours-block') + ' ' + (index + 1)
          })]
        })
      }), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_5__.jsx)(_wordpress_components__WEBPACK_IMPORTED_MODULE_1__.FlexItem, {
        children: /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_5__.jsx)(_wordpress_components__WEBPACK_IMPORTED_MODULE_1__.Button, {
          variant: "tertiary",
          isDestructive: true,
          size: "small",
          onClick: () => onRemove(index),
          label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__.__)('Remove season', 'telex-hours-block'),
          icon: "trash"
        })
      })]
    }), isOpen && /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_5__.jsxs)("div", {
      className: "telex-hours-block-inspector__season-details",
      children: [/*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_5__.jsx)(_wordpress_components__WEBPACK_IMPORTED_MODULE_1__.TextControl, {
        label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__.__)('Name', 'telex-hours-block'),
        value: season.name,
        onChange: val => updateField('name', val),
        __nextHasNoMarginBottom: true
      }), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_5__.jsxs)(_wordpress_components__WEBPACK_IMPORTED_MODULE_1__.Flex, {
        children: [/*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_5__.jsx)(_wordpress_components__WEBPACK_IMPORTED_MODULE_1__.FlexBlock, {
          children: /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_5__.jsx)(_wordpress_components__WEBPACK_IMPORTED_MODULE_1__.TextControl, {
            label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__.__)('Begin Date', 'telex-hours-block'),
            type: "date",
            value: season.beginDate,
            onChange: val => updateField('beginDate', val),
            __nextHasNoMarginBottom: true
          })
        }), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_5__.jsx)(_wordpress_components__WEBPACK_IMPORTED_MODULE_1__.FlexBlock, {
          children: /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_5__.jsx)(_wordpress_components__WEBPACK_IMPORTED_MODULE_1__.TextControl, {
            label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__.__)('End Date', 'telex-hours-block'),
            type: "date",
            value: season.endDate,
            onChange: val => updateField('endDate', val),
            __nextHasNoMarginBottom: true
          })
        })]
      }), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_5__.jsxs)("div", {
        className: "telex-hours-block-inspector__day-grid",
        children: [/*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_5__.jsx)(_wordpress_components__WEBPACK_IMPORTED_MODULE_1__.__experimentalText, {
          variant: "muted",
          size: "11",
          upperCase: true,
          weight: 500,
          className: "telex-hours-block-inspector__day-grid-hint",
          children: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__.__)('Leave blank for closed', 'telex-hours-block')
        }), orderedDays.map(({
          key,
          label
        }) => {
          const slots = (0,_utils_slots__WEBPACK_IMPORTED_MODULE_3__.normalizeSlots)(season.hours?.[key]);
          return /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_5__.jsxs)("div", {
            className: "telex-hours-block-inspector__day-row",
            children: [/*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_5__.jsx)("div", {
              className: "telex-hours-block-inspector__day-label",
              children: label
            }), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_5__.jsxs)("div", {
              children: [slots.map((slot, si) => /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_5__.jsx)(_time_slot_row__WEBPACK_IMPORTED_MODULE_4__["default"], {
                slot: slot,
                slotIndex: si,
                onUpdate: (slotIdx, timeKey, val) => updateSlotTime(key, slotIdx, timeKey, val),
                onRemove: slotIdx => removeSlot(key, slotIdx),
                canRemove: slots.length > 1
              }, si)), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_5__.jsx)(_wordpress_components__WEBPACK_IMPORTED_MODULE_1__.Button, {
                variant: "link",
                size: "small",
                onClick: () => addSlot(key),
                className: "telex-hours-block-inspector__add-slot",
                children: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__.__)('+ Add time slot', 'telex-hours-block')
              })]
            })]
          }, key);
        })]
      })]
    })]
  });
}

/***/ },

/***/ "./src/components/time-slot-row.js"
/*!*****************************************!*\
  !*** ./src/components/time-slot-row.js ***!
  \*****************************************/
(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (/* binding */ TimeSlotRow)
/* harmony export */ });
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n");
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _wordpress_components__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @wordpress/components */ "@wordpress/components");
/* harmony import */ var _wordpress_components__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_wordpress_components__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var react_jsx_runtime__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! react/jsx-runtime */ "react/jsx-runtime");
/* harmony import */ var react_jsx_runtime__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(react_jsx_runtime__WEBPACK_IMPORTED_MODULE_2__);
/**
 * TimeSlotRow component — Editable row for a single time slot.
 *
 * @package TelexHoursBlock
 */




/**
 * Renders an editable row for a single time slot (open/close pair).
 *
 * @param {Object}   props           Component props.
 * @param {Object}   props.slot      The slot object with open and close strings.
 * @param {number}   props.slotIndex Index of this slot within its parent array.
 * @param {Function} props.onUpdate  Callback: (slotIndex, timeKey, value) => void.
 * @param {Function} props.onRemove  Callback: (slotIndex) => void.
 * @param {boolean}  props.canRemove Whether the remove button should be shown.
 * @return {import('@wordpress/element').WPElement} Rendered component.
 */

function TimeSlotRow({
  slot,
  slotIndex,
  onUpdate,
  onRemove,
  canRemove
}) {
  return /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_2__.jsxs)(_wordpress_components__WEBPACK_IMPORTED_MODULE_1__.Flex, {
    align: "flex-end",
    gap: 2,
    children: [/*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_2__.jsx)(_wordpress_components__WEBPACK_IMPORTED_MODULE_1__.FlexBlock, {
      children: /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_2__.jsx)(_wordpress_components__WEBPACK_IMPORTED_MODULE_1__.TextControl, {
        label: slotIndex === 0 ? (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__.__)('Open', 'telex-hours-block') : '',
        placeholder: "8:00 AM",
        value: slot.open || '',
        onChange: val => onUpdate(slotIndex, 'open', val),
        __nextHasNoMarginBottom: true
      })
    }), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_2__.jsx)(_wordpress_components__WEBPACK_IMPORTED_MODULE_1__.FlexBlock, {
      children: /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_2__.jsx)(_wordpress_components__WEBPACK_IMPORTED_MODULE_1__.TextControl, {
        label: slotIndex === 0 ? (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__.__)('Close', 'telex-hours-block') : '',
        placeholder: "5:00 PM",
        value: slot.close || '',
        onChange: val => onUpdate(slotIndex, 'close', val),
        __nextHasNoMarginBottom: true
      })
    }), canRemove && /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_2__.jsx)(_wordpress_components__WEBPACK_IMPORTED_MODULE_1__.FlexItem, {
      children: /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_2__.jsx)(_wordpress_components__WEBPACK_IMPORTED_MODULE_1__.Button, {
        icon: "minus",
        isDestructive: true,
        size: "small",
        label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__.__)('Remove time slot', 'telex-hours-block'),
        onClick: () => onRemove(slotIndex)
      })
    })]
  });
}

/***/ },

/***/ "./src/components/week-view.js"
/*!*************************************!*\
  !*** ./src/components/week-view.js ***!
  \*************************************/
(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (/* binding */ WeekView)
/* harmony export */ });
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n");
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _wordpress_components__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @wordpress/components */ "@wordpress/components");
/* harmony import */ var _wordpress_components__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_wordpress_components__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _utils_slots__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ../utils/slots */ "./src/utils/slots.js");
/* harmony import */ var _utils_days__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ../utils/days */ "./src/utils/days.js");
/* harmony import */ var _hooks_use_schedule_preview__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! ../hooks/use-schedule-preview */ "./src/hooks/use-schedule-preview.js");
/* harmony import */ var react_jsx_runtime__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! react/jsx-runtime */ "react/jsx-runtime");
/* harmony import */ var react_jsx_runtime__WEBPACK_IMPORTED_MODULE_5___default = /*#__PURE__*/__webpack_require__.n(react_jsx_runtime__WEBPACK_IMPORTED_MODULE_5__);
/**
 * WeekView component — Renders the full weekly schedule preview in the editor.
 *
 * @package TelexHoursBlock
 */







/**
 * Renders the full week schedule in the editor preview.
 *
 * @param {Object}   props                  Component props.
 * @param {boolean}  props.hasLoaded        Whether settings have loaded.
 * @param {Object}   props.preview          Preview data from useSchedulePreview.
 * @param {Array}    props.orderedDays      Ordered day objects.
 * @param {boolean}  props.hideWeekends     Whether to hide weekend days.
 * @param {boolean}  props.showReasonClosed Whether to show closed reason.
 * @param {Function} props.renderSlots      Function to render slots as JSX.
 * @return {import('@wordpress/element').WPElement} Rendered component.
 */

function WeekView({
  hasLoaded,
  preview,
  orderedDays,
  hideWeekends,
  showReasonClosed,
  renderSlots
}) {
  if (!hasLoaded) {
    return /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_5__.jsx)(_wordpress_components__WEBPACK_IMPORTED_MODULE_1__.Spinner, {});
  }
  if (!preview.currentSeason) {
    return /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_5__.jsx)("p", {
      className: "telex-hours-block__message",
      children: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__.__)('No active season for today.', 'telex-hours-block')
    });
  }
  const todayDate = preview.today;
  const todayDayIndex = _utils_days__WEBPACK_IMPORTED_MODULE_3__.ALL_DAY_KEYS.indexOf(preview.dayKey);
  const holidays = preview.holidays || [];

  /**
   * Computes the Date object for a given day key relative to today.
   *
   * @param {string} dk Day key (e.g. 'mon').
   * @return {Date} Date object for that day of the current week.
   */
  function getDateForDay(dk) {
    const targetIndex = _utils_days__WEBPACK_IMPORTED_MODULE_3__.ALL_DAY_KEYS.indexOf(dk);
    const diff = targetIndex - todayDayIndex;
    const d = new Date(todayDate);
    d.setDate(d.getDate() + diff);
    return d;
  }
  return /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_5__.jsx)("dl", {
    className: "telex-hours-block__list",
    children: orderedDays.filter(({
      key: dk
    }) => !hideWeekends || !_utils_days__WEBPACK_IMPORTED_MODULE_3__.WEEKEND_KEYS.includes(dk)).map(({
      key: dk,
      label: dayLabel
    }) => {
      const isToday = dk === preview.dayKey;
      const dayDate = getDateForDay(dk);
      const dayHoliday = (0,_hooks_use_schedule_preview__WEBPACK_IMPORTED_MODULE_4__.findHolidayForDate)(holidays, dayDate);
      let daySlots = [];
      if (dayHoliday) {
        daySlots = (0,_utils_slots__WEBPACK_IMPORTED_MODULE_2__.normalizeHolidaySlots)(dayHoliday);
      } else if (preview.currentSeason.hours?.[dk]) {
        daySlots = (0,_utils_slots__WEBPACK_IMPORTED_MODULE_2__.normalizeSlots)(preview.currentSeason.hours[dk]);
      }
      const hasOpen = (0,_utils_slots__WEBPACK_IMPORTED_MODULE_2__.slotsHaveOpen)(daySlots);
      const isClosed = !hasOpen;
      const dtClasses = ['telex-hours-block__day', isToday ? 'telex-hours-block__day--today' : ''].filter(Boolean).join(' ');
      const ddClasses = ['telex-hours-block__hours', isToday ? 'telex-hours-block__hours--today' : '', isClosed ? 'telex-hours-block__hours--closed' : ''].filter(Boolean).join(' ');
      let closedLabel = (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__.__)('Closed', 'telex-hours-block');
      if (isClosed && showReasonClosed && dayHoliday && dayHoliday.name) {
        closedLabel = (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__.__)('Closed for ', 'telex-hours-block') + dayHoliday.name;
      }
      return /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_5__.jsxs)("div", {
        className: "telex-hours-block__list-row",
        children: [/*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_5__.jsx)("dt", {
          className: dtClasses,
          "data-day": dk,
          children: dayLabel
        }), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_5__.jsx)("dd", {
          className: ddClasses,
          "data-day": dk,
          children: isClosed ? closedLabel : renderSlots(daySlots)
        })]
      }, dk);
    })
  });
}

/***/ },

/***/ "./src/edit.js"
/*!*********************!*\
  !*** ./src/edit.js ***!
  \*********************/
(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (/* binding */ Edit)
/* harmony export */ });
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n");
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _wordpress_block_editor__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @wordpress/block-editor */ "@wordpress/block-editor");
/* harmony import */ var _wordpress_block_editor__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_wordpress_block_editor__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _wordpress_components__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @wordpress/components */ "@wordpress/components");
/* harmony import */ var _wordpress_components__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_wordpress_components__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! @wordpress/element */ "@wordpress/element");
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_3___default = /*#__PURE__*/__webpack_require__.n(_wordpress_element__WEBPACK_IMPORTED_MODULE_3__);
/* harmony import */ var _wordpress_data__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! @wordpress/data */ "@wordpress/data");
/* harmony import */ var _wordpress_data__WEBPACK_IMPORTED_MODULE_4___default = /*#__PURE__*/__webpack_require__.n(_wordpress_data__WEBPACK_IMPORTED_MODULE_4__);
/* harmony import */ var _wordpress_date__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! @wordpress/date */ "@wordpress/date");
/* harmony import */ var _wordpress_date__WEBPACK_IMPORTED_MODULE_5___default = /*#__PURE__*/__webpack_require__.n(_wordpress_date__WEBPACK_IMPORTED_MODULE_5__);
/* harmony import */ var _utils_sorting__WEBPACK_IMPORTED_MODULE_6__ = __webpack_require__(/*! ./utils/sorting */ "./src/utils/sorting.js");
/* harmony import */ var _utils_days__WEBPACK_IMPORTED_MODULE_7__ = __webpack_require__(/*! ./utils/days */ "./src/utils/days.js");
/* harmony import */ var _utils_time__WEBPACK_IMPORTED_MODULE_8__ = __webpack_require__(/*! ./utils/time */ "./src/utils/time.js");
/* harmony import */ var _hooks_use_site_setting__WEBPACK_IMPORTED_MODULE_9__ = __webpack_require__(/*! ./hooks/use-site-setting */ "./src/hooks/use-site-setting.js");
/* harmony import */ var _hooks_use_site_settings__WEBPACK_IMPORTED_MODULE_10__ = __webpack_require__(/*! ./hooks/use-site-settings */ "./src/hooks/use-site-settings.js");
/* harmony import */ var _hooks_use_schedule_preview__WEBPACK_IMPORTED_MODULE_11__ = __webpack_require__(/*! ./hooks/use-schedule-preview */ "./src/hooks/use-schedule-preview.js");
/* harmony import */ var _components_season_editor__WEBPACK_IMPORTED_MODULE_12__ = __webpack_require__(/*! ./components/season-editor */ "./src/components/season-editor.js");
/* harmony import */ var _components_holiday_editor__WEBPACK_IMPORTED_MODULE_13__ = __webpack_require__(/*! ./components/holiday-editor */ "./src/components/holiday-editor.js");
/* harmony import */ var _components_ical_import_button__WEBPACK_IMPORTED_MODULE_14__ = __webpack_require__(/*! ./components/ical-import-button */ "./src/components/ical-import-button.js");
/* harmony import */ var _components_week_view__WEBPACK_IMPORTED_MODULE_15__ = __webpack_require__(/*! ./components/week-view */ "./src/components/week-view.js");
/* harmony import */ var _components_day_view__WEBPACK_IMPORTED_MODULE_16__ = __webpack_require__(/*! ./components/day-view */ "./src/components/day-view.js");
/* harmony import */ var _editor_scss__WEBPACK_IMPORTED_MODULE_17__ = __webpack_require__(/*! ./editor.scss */ "./src/editor.scss");
/* harmony import */ var react_jsx_runtime__WEBPACK_IMPORTED_MODULE_18__ = __webpack_require__(/*! react/jsx-runtime */ "react/jsx-runtime");
/* harmony import */ var react_jsx_runtime__WEBPACK_IMPORTED_MODULE_18___default = /*#__PURE__*/__webpack_require__.n(react_jsx_runtime__WEBPACK_IMPORTED_MODULE_18__);
/**
 * WordPress dependencies
 */







/**
 * Internal dependencies — sorting helpers
 */


/**
 * Internal dependencies — utils
 */



/**
 * Internal dependencies — hooks
 */




/**
 * Internal dependencies — components
 */






/**
 * Editor-only styles.
 */


/**
 * Main edit component for the Business Hours Block.
 *
 * @param {Object}   props               Component props.
 * @param {Object}   props.attributes    Block attributes.
 * @param {Function} props.setAttributes Block attribute setter.
 * @return {import('@wordpress/element').WPElement} Rendered component.
 */

function Edit({
  attributes,
  setAttributes
}) {
  const {
    displayMode,
    showTodaysDate,
    showReasonClosed,
    friendlyTwelves,
    hideWeekends
  } = attributes;
  const blockProps = (0,_wordpress_block_editor__WEBPACK_IMPORTED_MODULE_1__.useBlockProps)({
    className: 'telex-hours-block'
  });

  // Site-wide schedule data.
  const [seasons, setSeasons, seasonsLoaded] = (0,_hooks_use_site_setting__WEBPACK_IMPORTED_MODULE_9__.useSiteSetting)('telex_hours_seasons', []);
  const [holidays, setHolidays, holidaysLoaded] = (0,_hooks_use_site_setting__WEBPACK_IMPORTED_MODULE_9__.useSiteSetting)('telex_hours_holidays', []);
  const hasLoaded = seasonsLoaded && holidaysLoaded;
  const {
    saveEditedEntityRecord
  } = (0,_wordpress_data__WEBPACK_IMPORTED_MODULE_4__.useDispatch)('core');
  const rawSeasons = Array.isArray(seasons) ? seasons : [];
  const rawHolidays = Array.isArray(holidays) ? holidays : [];

  // Sort seasons and holidays by begin date, preserving original indices.
  const sortedSeasons = (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_3__.useMemo)(() => {
    return rawSeasons.map((s, i) => ({
      item: s,
      originalIndex: i
    })).sort((a, b) => {
      const aKey = (0,_utils_sorting__WEBPACK_IMPORTED_MODULE_6__.getSortableBeginDate)(a.item.beginDate);
      const bKey = (0,_utils_sorting__WEBPACK_IMPORTED_MODULE_6__.getSortableBeginDate)(b.item.beginDate);
      return aKey.localeCompare(bKey);
    });
  }, [rawSeasons]);
  const sortedHolidays = (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_3__.useMemo)(() => {
    return rawHolidays.map((h, i) => ({
      item: h,
      originalIndex: i
    })).sort((a, b) => {
      const aKey = (0,_utils_sorting__WEBPACK_IMPORTED_MODULE_6__.getSortableBeginDate)(a.item.beginDate);
      const bKey = (0,_utils_sorting__WEBPACK_IMPORTED_MODULE_6__.getSortableBeginDate)(b.item.beginDate);
      return aKey.localeCompare(bKey);
    });
  }, [rawHolidays]);
  const currentSeasons = rawSeasons;
  const currentHolidays = rawHolidays;
  const saveSettings = (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_3__.useCallback)(() => {
    saveEditedEntityRecord('root', 'site');
  }, [saveEditedEntityRecord]);

  // Site display settings.
  const {
    startOfWeek,
    timeFormat,
    dateFormat
  } = (0,_hooks_use_site_settings__WEBPACK_IMPORTED_MODULE_10__.useSiteSettings)();
  const orderedDays = (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_3__.useMemo)(() => (0,_utils_days__WEBPACK_IMPORTED_MODULE_7__.getOrderedDays)(startOfWeek), [startOfWeek]);

  // Schedule preview computation.
  const preview = (0,_hooks_use_schedule_preview__WEBPACK_IMPORTED_MODULE_11__.useSchedulePreview)(currentSeasons, currentHolidays);

  // Season CRUD handlers.
  const updateSeason = (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_3__.useCallback)((idx, newSeason) => {
    const updated = [...currentSeasons];
    updated[idx] = newSeason;
    setSeasons(updated);
    saveSettings();
  }, [currentSeasons, setSeasons, saveSettings]);
  const removeSeason = (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_3__.useCallback)(idx => {
    const updated = currentSeasons.filter((_, i) => i !== idx);
    setSeasons(updated);
    saveSettings();
  }, [currentSeasons, setSeasons, saveSettings]);
  const addSeason = (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_3__.useCallback)(() => {
    const updated = [...currentSeasons, {
      name: '',
      beginDate: '',
      endDate: '',
      hours: (0,_utils_days__WEBPACK_IMPORTED_MODULE_7__.getDefaultHours)()
    }];
    setSeasons(updated);
    saveSettings();
  }, [currentSeasons, setSeasons, saveSettings]);

  // Holiday CRUD handlers.
  const updateHoliday = (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_3__.useCallback)((idx, newHoliday) => {
    const updated = [...currentHolidays];
    updated[idx] = newHoliday;
    setHolidays(updated);
    saveSettings();
  }, [currentHolidays, setHolidays, saveSettings]);
  const removeHoliday = (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_3__.useCallback)(idx => {
    const updated = currentHolidays.filter((_, i) => i !== idx);
    setHolidays(updated);
    saveSettings();
  }, [currentHolidays, setHolidays, saveSettings]);
  const addHoliday = (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_3__.useCallback)(() => {
    const updated = [...currentHolidays, {
      name: '',
      beginDate: '',
      endDate: '',
      slots: [{
        open: '',
        close: ''
      }]
    }];
    setHolidays(updated);
    saveSettings();
  }, [currentHolidays, setHolidays, saveSettings]);
  const importHolidays = (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_3__.useCallback)(imported => {
    const updated = [...currentHolidays, ...imported];
    setHolidays(updated);
    saveSettings();
  }, [currentHolidays, setHolidays, saveSettings]);

  // Time display formatting.
  const formatDisplayTime = (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_3__.useCallback)(rawTime => {
    if (!rawTime) {
      return rawTime;
    }
    const friendly = (0,_utils_time__WEBPACK_IMPORTED_MODULE_8__.applyFriendlyTwelves)(rawTime, friendlyTwelves);
    if (friendly !== rawTime) {
      return friendly;
    }
    return (0,_utils_time__WEBPACK_IMPORTED_MODULE_8__.formatTimeWithSiteFormat)(rawTime, timeFormat);
  }, [friendlyTwelves, timeFormat]);

  /**
   * Converts a time string to 24-hour HH:MM format for datetime attributes.
   *
   * @param {string} timeStr The input time string.
   * @return {string} Time in HH:MM format, or empty string.
   */
  const to24h = (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_3__.useCallback)(timeStr => {
    if (!timeStr) {
      return '';
    }
    const parsed = new Date('2000-01-01 ' + timeStr);
    if (isNaN(parsed.getTime())) {
      return '';
    }
    const hh = String(parsed.getHours()).padStart(2, '0');
    const mm = String(parsed.getMinutes()).padStart(2, '0');
    return hh + ':' + mm;
  }, []);

  /**
   * Renders an array of time slots as JSX with <time> elements.
   *
   * @param {Array} slotsArr Array of slot objects.
   * @return {Array|null} JSX elements or null if no open slots.
   */
  const renderSlots = (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_3__.useCallback)(slotsArr => {
    const openSlots = slotsArr.filter(s => s.open && s.open.trim() !== '');
    if (openSlots.length === 0) {
      return null;
    }
    return openSlots.map((slot, si) => /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_18__.jsxs)("span", {
      children: [si > 0 && /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_18__.jsx)("br", {}), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_18__.jsxs)("span", {
        className: "telex-hours-block__slot",
        children: [/*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_18__.jsx)("time", {
          dateTime: to24h(slot.open),
          children: formatDisplayTime(slot.open)
        }), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_18__.jsx)("span", {
          className: "telex-hours-block__separator",
          children: '\u2013'
        }), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_18__.jsx)("time", {
          dateTime: to24h(slot.close),
          children: formatDisplayTime(slot.close)
        })]
      })]
    }, si));
  }, [formatDisplayTime, to24h]);
  const formatDate = date => {
    return (0,_wordpress_date__WEBPACK_IMPORTED_MODULE_5__.dateI18n)(dateFormat, date);
  };
  return /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_18__.jsxs)(react_jsx_runtime__WEBPACK_IMPORTED_MODULE_18__.Fragment, {
    children: [/*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_18__.jsxs)(_wordpress_block_editor__WEBPACK_IMPORTED_MODULE_1__.InspectorControls, {
      children: [/*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_18__.jsxs)(_wordpress_components__WEBPACK_IMPORTED_MODULE_2__.PanelBody, {
        title: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__.__)('Display Settings', 'telex-hours-block'),
        initialOpen: true,
        children: [/*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_18__.jsx)(_wordpress_components__WEBPACK_IMPORTED_MODULE_2__.RadioControl, {
          label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__.__)('Display Mode', 'telex-hours-block'),
          selected: displayMode,
          options: [{
            label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__.__)('Full week schedule', 'telex-hours-block'),
            value: 'week'
          }, {
            label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__.__)("Today's hours only", 'telex-hours-block'),
            value: 'day'
          }],
          onChange: val => setAttributes({
            displayMode: val
          })
        }), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_18__.jsx)(_wordpress_components__WEBPACK_IMPORTED_MODULE_2__.ToggleControl, {
          label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__.__)("Show today's date", 'telex-hours-block'),
          checked: showTodaysDate,
          onChange: val => setAttributes({
            showTodaysDate: val
          }),
          __nextHasNoMarginBottom: true
        }), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_18__.jsx)(_wordpress_components__WEBPACK_IMPORTED_MODULE_2__.ToggleControl, {
          label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__.__)('Show reason when closed', 'telex-hours-block'),
          help: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__.__)('Displays the holiday name when closed due to a holiday.', 'telex-hours-block'),
          checked: showReasonClosed,
          onChange: val => setAttributes({
            showReasonClosed: val
          }),
          __nextHasNoMarginBottom: true
        }), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_18__.jsx)(_wordpress_components__WEBPACK_IMPORTED_MODULE_2__.ToggleControl, {
          label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__.__)('Use "Noon" and "Midnight"', 'telex-hours-block'),
          help: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__.__)('Replace 12:00 AM with "Midnight" and 12:00 PM with "Noon".', 'telex-hours-block'),
          checked: friendlyTwelves,
          onChange: val => setAttributes({
            friendlyTwelves: val
          }),
          __nextHasNoMarginBottom: true
        }), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_18__.jsx)(_wordpress_components__WEBPACK_IMPORTED_MODULE_2__.ToggleControl, {
          label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__.__)('Hide weekend days', 'telex-hours-block'),
          help: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__.__)('Hides Saturday and Sunday from the schedule.', 'telex-hours-block'),
          checked: hideWeekends,
          onChange: val => setAttributes({
            hideWeekends: val
          }),
          __nextHasNoMarginBottom: true
        })]
      }), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_18__.jsxs)(_wordpress_components__WEBPACK_IMPORTED_MODULE_2__.PanelBody, {
        title: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__.__)('Seasons / Semesters', 'telex-hours-block'),
        initialOpen: false,
        children: [/*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_18__.jsx)(_wordpress_components__WEBPACK_IMPORTED_MODULE_2__.PanelRow, {
          children: /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_18__.jsx)(_wordpress_components__WEBPACK_IMPORTED_MODULE_2__.__experimentalText, {
            variant: "muted",
            size: "12",
            children: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__.__)('Define periods with specific weekly schedules. These settings are shared across all Business Hours blocks on this site.', 'telex-hours-block')
          })
        }), !hasLoaded && /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_18__.jsx)(_wordpress_components__WEBPACK_IMPORTED_MODULE_2__.Spinner, {}), hasLoaded && sortedSeasons.map(({
          item: season,
          originalIndex
        }) => /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_18__.jsx)(_components_season_editor__WEBPACK_IMPORTED_MODULE_12__["default"], {
          season: season,
          index: originalIndex,
          orderedDays: orderedDays,
          onChange: updateSeason,
          onRemove: removeSeason
        }, originalIndex)), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_18__.jsx)(_wordpress_components__WEBPACK_IMPORTED_MODULE_2__.PanelRow, {
          children: /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_18__.jsx)(_wordpress_components__WEBPACK_IMPORTED_MODULE_2__.Button, {
            variant: "secondary",
            onClick: addSeason,
            disabled: !hasLoaded,
            children: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__.__)('Add Season', 'telex-hours-block')
          })
        })]
      }), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_18__.jsxs)(_wordpress_components__WEBPACK_IMPORTED_MODULE_2__.PanelBody, {
        title: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__.__)('Holidays / Exceptions', 'telex-hours-block'),
        initialOpen: false,
        children: [/*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_18__.jsx)(_wordpress_components__WEBPACK_IMPORTED_MODULE_2__.PanelRow, {
          children: /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_18__.jsx)(_wordpress_components__WEBPACK_IMPORTED_MODULE_2__.__experimentalText, {
            variant: "muted",
            size: "12",
            children: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__.__)('Holidays override season hours for specific date ranges. These settings are shared across all Business Hours blocks on this site.', 'telex-hours-block')
          })
        }), !hasLoaded && /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_18__.jsx)(_wordpress_components__WEBPACK_IMPORTED_MODULE_2__.Spinner, {}), hasLoaded && sortedHolidays.map(({
          item: holiday,
          originalIndex
        }) => /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_18__.jsx)(_components_holiday_editor__WEBPACK_IMPORTED_MODULE_13__["default"], {
          holiday: holiday,
          index: originalIndex,
          onChange: updateHoliday,
          onRemove: removeHoliday
        }, originalIndex)), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_18__.jsx)(_wordpress_components__WEBPACK_IMPORTED_MODULE_2__.PanelRow, {
          children: /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_18__.jsxs)(_wordpress_components__WEBPACK_IMPORTED_MODULE_2__.Flex, {
            direction: "column",
            gap: 2,
            children: [/*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_18__.jsx)(_wordpress_components__WEBPACK_IMPORTED_MODULE_2__.Button, {
              variant: "secondary",
              onClick: addHoliday,
              disabled: !hasLoaded,
              children: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__.__)('Add Holiday', 'telex-hours-block')
            }), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_18__.jsx)(_components_ical_import_button__WEBPACK_IMPORTED_MODULE_14__["default"], {
              onImport: importHolidays,
              disabled: !hasLoaded
            })]
          })
        })]
      })]
    }), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_18__.jsxs)("div", {
      ...blockProps,
      children: [showTodaysDate && /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_18__.jsx)("p", {
        className: "telex-hours-block__date",
        children: formatDate(preview.today)
      }), displayMode === 'week' ? /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_18__.jsx)(_components_week_view__WEBPACK_IMPORTED_MODULE_15__["default"], {
        hasLoaded: hasLoaded,
        preview: preview,
        orderedDays: orderedDays,
        hideWeekends: hideWeekends,
        showReasonClosed: showReasonClosed,
        renderSlots: renderSlots
      }) : /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_18__.jsx)(_components_day_view__WEBPACK_IMPORTED_MODULE_16__["default"], {
        hasLoaded: hasLoaded,
        preview: preview,
        hideWeekends: hideWeekends,
        showReasonClosed: showReasonClosed,
        renderSlots: renderSlots
      })]
    })]
  });
}

/***/ },

/***/ "./src/hooks/use-schedule-preview.js"
/*!*******************************************!*\
  !*** ./src/hooks/use-schedule-preview.js ***!
  \*******************************************/
(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   findHolidayForDate: () => (/* binding */ findHolidayForDate),
/* harmony export */   useSchedulePreview: () => (/* binding */ useSchedulePreview)
/* harmony export */ });
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @wordpress/element */ "@wordpress/element");
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _utils_days__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ../utils/days */ "./src/utils/days.js");
/* harmony import */ var _utils_dates__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ../utils/dates */ "./src/utils/dates.js");
/* harmony import */ var _utils_slots__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ../utils/slots */ "./src/utils/slots.js");
/**
 * Custom hook that computes the schedule preview for the editor.
 *
 * @package TelexHoursBlock
 */






/**
 * Finds a matching holiday for a given Date object.
 *
 * @param {Array} holidays Array of holiday objects.
 * @param {Date}  dateObj  The date to check.
 * @return {Object|null} The matching holiday or null.
 */
function findHolidayForDate(holidays, dateObj) {
  const md = String(dateObj.getMonth() + 1).padStart(2, '0') + '-' + String(dateObj.getDate()).padStart(2, '0');
  for (const h of holidays) {
    const beginStr = h.beginDate || '';
    const endStr = h.endDate || '';
    if (!beginStr || !endStr) {
      continue;
    }
    const beginHasYear = beginStr.length > 5;
    const endHasYear = endStr.length > 5;
    if (beginHasYear && endHasYear) {
      const begin = (0,_utils_dates__WEBPACK_IMPORTED_MODULE_2__.parseDate)(beginStr);
      const end = (0,_utils_dates__WEBPACK_IMPORTED_MODULE_2__.parseDate)(endStr);
      if (begin && end && (0,_utils_dates__WEBPACK_IMPORTED_MODULE_2__.isDateInRange)(dateObj, begin, end)) {
        return h;
      }
    } else if (!beginHasYear && !endHasYear) {
      if (beginStr <= endStr) {
        if (md >= beginStr && md <= endStr) {
          return h;
        }
      } else if (md >= beginStr || md <= endStr) {
        return h;
      }
    }
  }
  return null;
}

/**
 * Computes a preview of today's schedule based on current seasons and holidays.
 *
 * @param {Array} seasons  Array of season objects.
 * @param {Array} holidays Array of holiday objects.
 * @return {Object} Preview data with currentSeason, currentHoliday, slots, holidayName, dayKey, today, and holidays.
 */
function useSchedulePreview(seasons, holidays) {
  return (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.useMemo)(() => {
    const now = new Date();
    const today = new Date(now.getFullYear(), now.getMonth(), now.getDate());
    const dayKey = _utils_days__WEBPACK_IMPORTED_MODULE_1__.ALL_DAY_KEYS[today.getDay()];
    const currentHoliday = findHolidayForDate(holidays, today);
    let currentSeason = null;
    for (const s of seasons) {
      const begin = (0,_utils_dates__WEBPACK_IMPORTED_MODULE_2__.parseDate)(s.beginDate);
      const end = (0,_utils_dates__WEBPACK_IMPORTED_MODULE_2__.parseDate)(s.endDate);
      if (begin && end && (0,_utils_dates__WEBPACK_IMPORTED_MODULE_2__.isDateInRange)(today, begin, end)) {
        currentSeason = s;
        break;
      }
    }
    let slots = [];
    let holidayName = '';
    if (currentHoliday) {
      slots = (0,_utils_slots__WEBPACK_IMPORTED_MODULE_3__.normalizeHolidaySlots)(currentHoliday);
      holidayName = currentHoliday.name || '';
    } else if (currentSeason && currentSeason.hours?.[dayKey]) {
      slots = (0,_utils_slots__WEBPACK_IMPORTED_MODULE_3__.normalizeSlots)(currentSeason.hours[dayKey]);
    }
    return {
      currentSeason,
      currentHoliday,
      slots,
      holidayName,
      dayKey,
      today,
      holidays
    };
  }, [seasons, holidays]);
}

/***/ },

/***/ "./src/hooks/use-site-setting.js"
/*!***************************************!*\
  !*** ./src/hooks/use-site-setting.js ***!
  \***************************************/
(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   useSiteSetting: () => (/* binding */ useSiteSetting)
/* harmony export */ });
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @wordpress/element */ "@wordpress/element");
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _wordpress_data__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @wordpress/data */ "@wordpress/data");
/* harmony import */ var _wordpress_data__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_wordpress_data__WEBPACK_IMPORTED_MODULE_1__);
/**
 * Custom hook for reading and writing a site setting via the core data store.
 *
 * @package TelexHoursBlock
 */




/**
 * Reads and writes a site-level setting using WordPress core data store.
 *
 * Calls getEntityRecord to trigger the REST API fetch, then reads
 * from getEditedEntityRecord to include any local edits.
 *
 * @param {string} settingKey The setting key (e.g. 'telex_hours_seasons').
 * @param {*}      fallback   Fallback value before the entity resolves.
 * @return {Array} Tuple of [currentValue, setValue, hasResolved].
 */
function useSiteSetting(settingKey, fallback) {
  const {
    editedValue,
    hasResolved
  } = (0,_wordpress_data__WEBPACK_IMPORTED_MODULE_1__.useSelect)(select => {
    const store = select('core');
    // Trigger the fetch by calling getEntityRecord.
    store.getEntityRecord('root', 'site');
    // Read from the edited record to pick up local changes.
    const record = store.getEditedEntityRecord('root', 'site');
    const resolved = store.hasFinishedResolution('getEntityRecord', ['root', 'site']);
    return {
      editedValue: record ? record[settingKey] : undefined,
      hasResolved: resolved
    };
  }, [settingKey]);
  const {
    editEntityRecord
  } = (0,_wordpress_data__WEBPACK_IMPORTED_MODULE_1__.useDispatch)('core');
  const setValue = (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.useCallback)(newValue => {
    editEntityRecord('root', 'site', undefined, {
      [settingKey]: newValue
    });
  }, [settingKey, editEntityRecord]);
  const currentValue = hasResolved && editedValue !== undefined ? editedValue : fallback;
  return [currentValue, setValue, hasResolved];
}

/***/ },

/***/ "./src/hooks/use-site-settings.js"
/*!****************************************!*\
  !*** ./src/hooks/use-site-settings.js ***!
  \****************************************/
(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   useSiteSettings: () => (/* binding */ useSiteSettings)
/* harmony export */ });
/* harmony import */ var _wordpress_data__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @wordpress/data */ "@wordpress/data");
/* harmony import */ var _wordpress_data__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_wordpress_data__WEBPACK_IMPORTED_MODULE_0__);
/**
 * Custom hook for reading WordPress site settings (start_of_week, time_format, date_format).
 *
 * @package TelexHoursBlock
 */



/**
 * Returns an object with common site settings needed by the block.
 *
 * Calls getEntityRecord to trigger the REST API fetch, ensuring the
 * site settings are available.
 *
 * @return {{startOfWeek: number, timeFormat: string, dateFormat: string}} Site settings.
 */
function useSiteSettings() {
  return (0,_wordpress_data__WEBPACK_IMPORTED_MODULE_0__.useSelect)(select => {
    const store = select('core');
    // Trigger the fetch.
    store.getEntityRecord('root', 'site');
    // Read from the record.
    const siteData = store.getEditedEntityRecord('root', 'site');
    return {
      startOfWeek: siteData?.start_of_week ?? 0,
      timeFormat: siteData?.time_format ?? 'g:i a',
      dateFormat: siteData?.date_format ?? 'F j, Y'
    };
  }, []);
}

/***/ },

/***/ "./src/index.js"
/*!**********************!*\
  !*** ./src/index.js ***!
  \**********************/
(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _wordpress_blocks__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @wordpress/blocks */ "@wordpress/blocks");
/* harmony import */ var _wordpress_blocks__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_wordpress_blocks__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _style_scss__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./style.scss */ "./src/style.scss");
/* harmony import */ var _edit__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./edit */ "./src/edit.js");
/* harmony import */ var _block_json__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ./block.json */ "./src/block.json");
/**
 * Registers the Business Hours Block.
 *
 * @see https://developer.wordpress.org/block-editor/reference-guides/block-api/block-registration/
 */


/**
 * Lets webpack process CSS, SASS or SCSS files referenced in JavaScript files.
 * All files containing `style` keyword are bundled together.
 *
 * @see https://www.npmjs.com/package/@wordpress/scripts#using-css
 */


/**
 * Internal dependencies
 */



/**
 * Registers the block type using metadata from block.json.
 *
 * @see https://developer.wordpress.org/block-editor/reference-guides/block-api/block-registration/
 */
(0,_wordpress_blocks__WEBPACK_IMPORTED_MODULE_0__.registerBlockType)(_block_json__WEBPACK_IMPORTED_MODULE_3__.name, {
  /**
   * @see ./edit.js
   */
  edit: _edit__WEBPACK_IMPORTED_MODULE_2__["default"]
});

/***/ },

/***/ "./src/utils/dates.js"
/*!****************************!*\
  !*** ./src/utils/dates.js ***!
  \****************************/
(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   dateHasYear: () => (/* binding */ dateHasYear),
/* harmony export */   getDayOptions: () => (/* binding */ getDayOptions),
/* harmony export */   getMonthOptions: () => (/* binding */ getMonthOptions),
/* harmony export */   isDateInRange: () => (/* binding */ isDateInRange),
/* harmony export */   parseDate: () => (/* binding */ parseDate),
/* harmony export */   parseMonthDay: () => (/* binding */ parseMonthDay)
/* harmony export */ });
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n");
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__);
/**
 * Date parsing and comparison utilities.
 *
 * @package TelexHoursBlock
 */



/**
 * Parses a date string to a Date object at midnight.
 *
 * @param {string} dateStr Date string in YYYY-MM-DD format.
 * @return {Date|null} Parsed date or null if invalid.
 */
function parseDate(dateStr) {
  if (!dateStr) {
    return null;
  }
  const parts = dateStr.split('-');
  if (parts.length !== 3) {
    return null;
  }
  return new Date(parseInt(parts[0], 10), parseInt(parts[1], 10) - 1, parseInt(parts[2], 10));
}

/**
 * Checks if a date falls within a range (inclusive).
 *
 * @param {Date} testDate  The date to test.
 * @param {Date} beginDate The start of the range.
 * @param {Date} endDate   The end of the range.
 * @return {boolean} True if testDate is within the range.
 */
function isDateInRange(testDate, beginDate, endDate) {
  const t = testDate.getFullYear() * 10000 + (testDate.getMonth() + 1) * 100 + testDate.getDate();
  const b = beginDate.getFullYear() * 10000 + (beginDate.getMonth() + 1) * 100 + beginDate.getDate();
  const e = endDate.getFullYear() * 10000 + (endDate.getMonth() + 1) * 100 + endDate.getDate();
  return t >= b && t <= e;
}

/**
 * Determines whether a holiday date string has a year.
 *
 * @param {string} dateStr Date string to check.
 * @return {boolean} True if the date string includes a year.
 */
function dateHasYear(dateStr) {
  if (!dateStr) {
    return false;
  }
  return dateStr.length > 5;
}

/**
 * Parses a date value into month and day strings.
 *
 * @param {string} dateStr Date string in YYYY-MM-DD or MM-DD format.
 * @return {{month: string, day: string}} Parsed month and day.
 */
function parseMonthDay(dateStr) {
  if (!dateStr) {
    return {
      month: '01',
      day: '01'
    };
  }
  const parts = dateStr.split('-');
  if (parts.length === 3) {
    return {
      month: parts[1],
      day: parts[2]
    };
  }
  if (parts.length === 2) {
    return {
      month: parts[0],
      day: parts[1]
    };
  }
  return {
    month: '01',
    day: '01'
  };
}

/**
 * Generates month options with localized labels.
 *
 * @return {Array<{value: string, label: string}>} Month options.
 */
function getMonthOptions() {
  return [{
    value: '01',
    label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__.__)('January')
  }, {
    value: '02',
    label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__.__)('February')
  }, {
    value: '03',
    label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__.__)('March')
  }, {
    value: '04',
    label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__.__)('April')
  }, {
    value: '05',
    label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__.__)('May')
  }, {
    value: '06',
    label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__.__)('June')
  }, {
    value: '07',
    label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__.__)('July')
  }, {
    value: '08',
    label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__.__)('August')
  }, {
    value: '09',
    label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__.__)('September')
  }, {
    value: '10',
    label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__.__)('October')
  }, {
    value: '11',
    label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__.__)('November')
  }, {
    value: '12',
    label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__.__)('December')
  }];
}

/**
 * Generates day options for a given month.
 *
 * @param {string} month Two-digit month string (01-12).
 * @return {Array<{value: string, label: string}>} Day options.
 */
function getDayOptions(month) {
  const daysInMonth = {
    '01': 31,
    '02': 29,
    '03': 31,
    '04': 30,
    '05': 31,
    '06': 30,
    '07': 31,
    '08': 31,
    '09': 30,
    '10': 31,
    '11': 30,
    '12': 31
  };
  const count = daysInMonth[month] || 31;
  const options = [];
  for (let d = 1; d <= count; d++) {
    const val = String(d).padStart(2, '0');
    options.push({
      value: val,
      label: String(d)
    });
  }
  return options;
}

/***/ },

/***/ "./src/utils/days.js"
/*!***************************!*\
  !*** ./src/utils/days.js ***!
  \***************************/
(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   ALL_DAY_KEYS: () => (/* binding */ ALL_DAY_KEYS),
/* harmony export */   WEEKEND_KEYS: () => (/* binding */ WEEKEND_KEYS),
/* harmony export */   getDefaultHours: () => (/* binding */ getDefaultHours),
/* harmony export */   getOrderedDays: () => (/* binding */ getOrderedDays)
/* harmony export */ });
/* harmony import */ var _wordpress_date__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @wordpress/date */ "@wordpress/date");
/* harmony import */ var _wordpress_date__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_wordpress_date__WEBPACK_IMPORTED_MODULE_0__);
/**
 * Day key and label utilities.
 *
 * @package TelexHoursBlock
 */



/**
 * All day keys in standard order starting from Sunday (index 0).
 *
 * @type {string[]}
 */
const ALL_DAY_KEYS = ['sun', 'mon', 'tue', 'wed', 'thu', 'fri', 'sat'];

/**
 * Weekend day keys.
 *
 * @type {string[]}
 */
const WEEKEND_KEYS = ['sun', 'sat'];

/**
 * Returns day keys and localized labels ordered according to start_of_week.
 *
 * Uses WordPress dateI18n to get properly localized day names that respect
 * the site's language settings.
 *
 * @param {number} startOfWeek The start of week (0=Sunday, 1=Monday, etc.).
 * @return {Array<{key: string, label: string}>} Ordered array of day objects.
 */
function getOrderedDays(startOfWeek) {
  const days = [];
  const baseSunday = new Date('2024-01-07T12:00:00');
  for (let i = 0; i < 7; i++) {
    const dayOfWeek = (startOfWeek + i) % 7;
    const dayDate = new Date(baseSunday);
    dayDate.setDate(baseSunday.getDate() + dayOfWeek);
    days.push({
      key: ALL_DAY_KEYS[dayOfWeek],
      label: (0,_wordpress_date__WEBPACK_IMPORTED_MODULE_0__.dateI18n)('l', dayDate)
    });
  }
  return days;
}

/**
 * Creates a default hours object with all days having one empty slot.
 *
 * @return {Object} Hours object keyed by day key.
 */
function getDefaultHours() {
  const hours = {};
  ALL_DAY_KEYS.forEach(key => {
    hours[key] = [{
      open: '',
      close: ''
    }];
  });
  return hours;
}

/***/ },

/***/ "./src/utils/ical-import.js"
/*!**********************************!*\
  !*** ./src/utils/ical-import.js ***!
  \**********************************/
(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   importIcalFile: () => (/* binding */ importIcalFile)
/* harmony export */ });
/* harmony import */ var _wordpress_api_fetch__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @wordpress/api-fetch */ "@wordpress/api-fetch");
/* harmony import */ var _wordpress_api_fetch__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_wordpress_api_fetch__WEBPACK_IMPORTED_MODULE_0__);
/**
 * iCal import utility.
 *
 * Reads a .ics file via FileReader and sends its content to the
 * server-side REST endpoint for parsing.
 *
 * @package TelexHoursBlock
 */



/**
 * Reads a File object as text.
 *
 * @param {File} file The file to read.
 * @return {Promise<string>} The file content as a string.
 */
function readFileAsText(file) {
  return new Promise((resolve, reject) => {
    const reader = new FileReader();
    reader.onload = () => resolve(reader.result);
    reader.onerror = () => reject(reader.error);
    reader.readAsText(file);
  });
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
async function importIcalFile(file) {
  const text = await readFileAsText(file);
  const response = await _wordpress_api_fetch__WEBPACK_IMPORTED_MODULE_0___default()({
    path: '/telex-hours-block/v1/import-ical',
    method: 'POST',
    data: {
      ical_text: text
    }
  });
  return response.holidays || [];
}

/***/ },

/***/ "./src/utils/slots.js"
/*!****************************!*\
  !*** ./src/utils/slots.js ***!
  \****************************/
(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   normalizeHolidaySlots: () => (/* binding */ normalizeHolidaySlots),
/* harmony export */   normalizeSlots: () => (/* binding */ normalizeSlots),
/* harmony export */   slotsHaveOpen: () => (/* binding */ slotsHaveOpen)
/* harmony export */ });
/**
 * Time slot normalization utilities.
 *
 * @package TelexHoursBlock
 */

/**
 * Normalizes day data to always be an array of slots.
 * Handles legacy { open, close } format.
 *
 * @param {*} dayData Raw day data from season hours.
 * @return {Array<{open: string, close: string}>} Normalized array of slots.
 */
function normalizeSlots(dayData) {
  if (!dayData) {
    return [{
      open: '',
      close: ''
    }];
  }
  if (Array.isArray(dayData)) {
    if (dayData.length === 0) {
      return [{
        open: '',
        close: ''
      }];
    }
    if (typeof dayData[0] === 'object' && dayData[0] !== null && ('open' in dayData[0] || 'close' in dayData[0])) {
      return dayData;
    }
  }
  if (typeof dayData === 'object' && ('open' in dayData || 'close' in dayData)) {
    return [{
      open: dayData.open || '',
      close: dayData.close || ''
    }];
  }
  return [{
    open: '',
    close: ''
  }];
}

/**
 * Normalizes holiday data to use the 'slots' array format.
 * Handles legacy 'openTime'/'closeTime' fields.
 *
 * @param {Object} holiday Holiday data object.
 * @return {Array<{open: string, close: string}>} Normalized array of slots.
 */
function normalizeHolidaySlots(holiday) {
  if (holiday.slots && Array.isArray(holiday.slots)) {
    return holiday.slots.length > 0 ? holiday.slots : [];
  }
  if (holiday.openTime) {
    return [{
      open: holiday.openTime || '',
      close: holiday.closeTime || ''
    }];
  }
  return [];
}

/**
 * Checks if any slot has a non-empty open time.
 *
 * @param {Array<{open: string, close: string}>} slots Array of slot objects.
 * @return {boolean} True if at least one slot has a non-empty open time.
 */
function slotsHaveOpen(slots) {
  return slots.some(s => s.open && s.open.trim() !== '');
}

/***/ },

/***/ "./src/utils/sorting.js"
/*!******************************!*\
  !*** ./src/utils/sorting.js ***!
  \******************************/
(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   getSortableBeginDate: () => (/* binding */ getSortableBeginDate)
/* harmony export */ });
/**
 * Sorting utilities for seasons and holidays.
 *
 * @package TelexHoursBlock
 */

/**
 * Converts a beginDate string into a sortable key.
 *
 * Handles three formats:
 * - "YYYY-MM-DD" (year-specific) — returned as-is.
 * - "MM-DD" (recurring/yearless) — prefixed with "9999-" so recurring items sort after year-specific ones.
 * - Empty or missing — returns "zzzz" to sort last.
 *
 * @param {string} beginDate The begin date string.
 * @return {string} A string suitable for lexicographic sorting.
 */
function getSortableBeginDate(beginDate) {
  if (!beginDate) {
    return 'zzzz';
  }
  // Year-specific: "YYYY-MM-DD" (length > 5).
  if (beginDate.length > 5) {
    return beginDate;
  }
  // Recurring: "MM-DD" — prefix with 9999 so they sort after dated entries.
  return '9999-' + beginDate;
}

/***/ },

/***/ "./src/utils/time.js"
/*!***************************!*\
  !*** ./src/utils/time.js ***!
  \***************************/
(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   applyFriendlyTwelves: () => (/* binding */ applyFriendlyTwelves),
/* harmony export */   formatTimeWithSiteFormat: () => (/* binding */ formatTimeWithSiteFormat)
/* harmony export */ });
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n");
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__);
/**
 * Time formatting utilities.
 *
 * @package TelexHoursBlock
 */



/**
 * Applies friendly twelve labels to a time string.
 *
 * @param {string}  time            The time string.
 * @param {boolean} friendlyTwelves Whether to apply friendly labels.
 * @return {string} The processed time string.
 */
function applyFriendlyTwelves(time, friendlyTwelves) {
  if (!friendlyTwelves || !time) {
    return time;
  }
  const lower = time.toLowerCase().replace(/\s/g, '');
  if (lower === '12:00am') {
    return (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__.__)('Midnight', 'telex-hours-block');
  }
  if (lower === '12:00pm') {
    return (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__.__)('Noon', 'telex-hours-block');
  }
  return time;
}

/**
 * Formats a time string using the WordPress time_format setting.
 *
 * @param {string} timeStr    The input time string (e.g. "8:00 AM").
 * @param {string} timeFormat The PHP-style time format string.
 * @return {string} The formatted time string.
 */
function formatTimeWithSiteFormat(timeStr, timeFormat) {
  if (!timeStr || !timeFormat) {
    return timeStr;
  }
  const parsed = new Date('2000-01-01 ' + timeStr);
  if (isNaN(parsed.getTime())) {
    return timeStr;
  }
  const hours = parsed.getHours();
  const minutes = parsed.getMinutes();
  const seconds = parsed.getSeconds();
  let result = '';
  let i = 0;
  while (i < timeFormat.length) {
    const ch = timeFormat[i];
    if (ch === '\\' && i + 1 < timeFormat.length) {
      result += timeFormat[i + 1];
      i += 2;
      continue;
    }
    switch (ch) {
      case 'g':
        result += hours % 12 || 12;
        break;
      case 'G':
        result += hours;
        break;
      case 'h':
        result += String(hours % 12 || 12).padStart(2, '0');
        break;
      case 'H':
        result += String(hours).padStart(2, '0');
        break;
      case 'i':
        result += String(minutes).padStart(2, '0');
        break;
      case 's':
        result += String(seconds).padStart(2, '0');
        break;
      case 'a':
        result += hours >= 12 ? 'pm' : 'am';
        break;
      case 'A':
        result += hours >= 12 ? 'PM' : 'AM';
        break;
      default:
        result += ch;
        break;
    }
    i++;
  }
  return result;
}

/***/ },

/***/ "./src/editor.scss"
/*!*************************!*\
  !*** ./src/editor.scss ***!
  \*************************/
(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
// extracted by mini-css-extract-plugin


/***/ },

/***/ "./src/style.scss"
/*!************************!*\
  !*** ./src/style.scss ***!
  \************************/
(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
// extracted by mini-css-extract-plugin


/***/ },

/***/ "react/jsx-runtime"
/*!**********************************!*\
  !*** external "ReactJSXRuntime" ***!
  \**********************************/
(module) {

module.exports = window["ReactJSXRuntime"];

/***/ },

/***/ "@wordpress/api-fetch"
/*!**********************************!*\
  !*** external ["wp","apiFetch"] ***!
  \**********************************/
(module) {

module.exports = window["wp"]["apiFetch"];

/***/ },

/***/ "@wordpress/block-editor"
/*!*************************************!*\
  !*** external ["wp","blockEditor"] ***!
  \*************************************/
(module) {

module.exports = window["wp"]["blockEditor"];

/***/ },

/***/ "@wordpress/blocks"
/*!********************************!*\
  !*** external ["wp","blocks"] ***!
  \********************************/
(module) {

module.exports = window["wp"]["blocks"];

/***/ },

/***/ "@wordpress/components"
/*!************************************!*\
  !*** external ["wp","components"] ***!
  \************************************/
(module) {

module.exports = window["wp"]["components"];

/***/ },

/***/ "@wordpress/data"
/*!******************************!*\
  !*** external ["wp","data"] ***!
  \******************************/
(module) {

module.exports = window["wp"]["data"];

/***/ },

/***/ "@wordpress/date"
/*!******************************!*\
  !*** external ["wp","date"] ***!
  \******************************/
(module) {

module.exports = window["wp"]["date"];

/***/ },

/***/ "@wordpress/element"
/*!*********************************!*\
  !*** external ["wp","element"] ***!
  \*********************************/
(module) {

module.exports = window["wp"]["element"];

/***/ },

/***/ "@wordpress/i18n"
/*!******************************!*\
  !*** external ["wp","i18n"] ***!
  \******************************/
(module) {

module.exports = window["wp"]["i18n"];

/***/ },

/***/ "./src/block.json"
/*!************************!*\
  !*** ./src/block.json ***!
  \************************/
(module) {

module.exports = /*#__PURE__*/JSON.parse('{"$schema":"https://schemas.wp.org/trunk/block.json","apiVersion":3,"name":"telex/block-telex-hours-block","version":"0.1.0","title":"Business Hours Block","category":"widgets","icon":"clock","description":"Displays the current day\'s business hours or a full weekly schedule with customizable seasons and holidays.","example":{"attributes":{"displayMode":"week","showTodaysDate":true,"showReasonClosed":true,"friendlyTwelves":true}},"supports":{"html":false,"color":{"background":true,"text":true},"typography":{"fontSize":true,"lineHeight":true},"spacing":{"margin":true,"padding":true},"align":["wide","full"]},"attributes":{"displayMode":{"type":"string","default":"week","enum":["week","day"]},"showTodaysDate":{"type":"boolean","default":true},"showReasonClosed":{"type":"boolean","default":true},"friendlyTwelves":{"type":"boolean","default":true},"hideWeekends":{"type":"boolean","default":false}},"textdomain":"telex-hours-block","editorScript":"file:./index.js","editorStyle":"file:./index.css","style":"file:./style-index.css","viewScript":"file:./view.js","render":"file:./render.php"}');

/***/ }

/******/ 	});
/************************************************************************/
/******/ 	// The module cache
/******/ 	var __webpack_module_cache__ = {};
/******/ 	
/******/ 	// The require function
/******/ 	function __webpack_require__(moduleId) {
/******/ 		// Check if module is in cache
/******/ 		var cachedModule = __webpack_module_cache__[moduleId];
/******/ 		if (cachedModule !== undefined) {
/******/ 			return cachedModule.exports;
/******/ 		}
/******/ 		// Create a new module (and put it into the cache)
/******/ 		var module = __webpack_module_cache__[moduleId] = {
/******/ 			// no module.id needed
/******/ 			// no module.loaded needed
/******/ 			exports: {}
/******/ 		};
/******/ 	
/******/ 		// Execute the module function
/******/ 		if (!(moduleId in __webpack_modules__)) {
/******/ 			delete __webpack_module_cache__[moduleId];
/******/ 			var e = new Error("Cannot find module '" + moduleId + "'");
/******/ 			e.code = 'MODULE_NOT_FOUND';
/******/ 			throw e;
/******/ 		}
/******/ 		__webpack_modules__[moduleId](module, module.exports, __webpack_require__);
/******/ 	
/******/ 		// Return the exports of the module
/******/ 		return module.exports;
/******/ 	}
/******/ 	
/******/ 	// expose the modules object (__webpack_modules__)
/******/ 	__webpack_require__.m = __webpack_modules__;
/******/ 	
/************************************************************************/
/******/ 	/* webpack/runtime/chunk loaded */
/******/ 	(() => {
/******/ 		var deferred = [];
/******/ 		__webpack_require__.O = (result, chunkIds, fn, priority) => {
/******/ 			if(chunkIds) {
/******/ 				priority = priority || 0;
/******/ 				for(var i = deferred.length; i > 0 && deferred[i - 1][2] > priority; i--) deferred[i] = deferred[i - 1];
/******/ 				deferred[i] = [chunkIds, fn, priority];
/******/ 				return;
/******/ 			}
/******/ 			var notFulfilled = Infinity;
/******/ 			for (var i = 0; i < deferred.length; i++) {
/******/ 				var [chunkIds, fn, priority] = deferred[i];
/******/ 				var fulfilled = true;
/******/ 				for (var j = 0; j < chunkIds.length; j++) {
/******/ 					if ((priority & 1 === 0 || notFulfilled >= priority) && Object.keys(__webpack_require__.O).every((key) => (__webpack_require__.O[key](chunkIds[j])))) {
/******/ 						chunkIds.splice(j--, 1);
/******/ 					} else {
/******/ 						fulfilled = false;
/******/ 						if(priority < notFulfilled) notFulfilled = priority;
/******/ 					}
/******/ 				}
/******/ 				if(fulfilled) {
/******/ 					deferred.splice(i--, 1)
/******/ 					var r = fn();
/******/ 					if (r !== undefined) result = r;
/******/ 				}
/******/ 			}
/******/ 			return result;
/******/ 		};
/******/ 	})();
/******/ 	
/******/ 	/* webpack/runtime/compat get default export */
/******/ 	(() => {
/******/ 		// getDefaultExport function for compatibility with non-harmony modules
/******/ 		__webpack_require__.n = (module) => {
/******/ 			var getter = module && module.__esModule ?
/******/ 				() => (module['default']) :
/******/ 				() => (module);
/******/ 			__webpack_require__.d(getter, { a: getter });
/******/ 			return getter;
/******/ 		};
/******/ 	})();
/******/ 	
/******/ 	/* webpack/runtime/define property getters */
/******/ 	(() => {
/******/ 		// define getter functions for harmony exports
/******/ 		__webpack_require__.d = (exports, definition) => {
/******/ 			for(var key in definition) {
/******/ 				if(__webpack_require__.o(definition, key) && !__webpack_require__.o(exports, key)) {
/******/ 					Object.defineProperty(exports, key, { enumerable: true, get: definition[key] });
/******/ 				}
/******/ 			}
/******/ 		};
/******/ 	})();
/******/ 	
/******/ 	/* webpack/runtime/hasOwnProperty shorthand */
/******/ 	(() => {
/******/ 		__webpack_require__.o = (obj, prop) => (Object.prototype.hasOwnProperty.call(obj, prop))
/******/ 	})();
/******/ 	
/******/ 	/* webpack/runtime/make namespace object */
/******/ 	(() => {
/******/ 		// define __esModule on exports
/******/ 		__webpack_require__.r = (exports) => {
/******/ 			if(typeof Symbol !== 'undefined' && Symbol.toStringTag) {
/******/ 				Object.defineProperty(exports, Symbol.toStringTag, { value: 'Module' });
/******/ 			}
/******/ 			Object.defineProperty(exports, '__esModule', { value: true });
/******/ 		};
/******/ 	})();
/******/ 	
/******/ 	/* webpack/runtime/jsonp chunk loading */
/******/ 	(() => {
/******/ 		// no baseURI
/******/ 		
/******/ 		// object to store loaded and loading chunks
/******/ 		// undefined = chunk not loaded, null = chunk preloaded/prefetched
/******/ 		// [resolve, reject, Promise] = chunk loading, 0 = chunk loaded
/******/ 		var installedChunks = {
/******/ 			"index": 0,
/******/ 			"./style-index": 0
/******/ 		};
/******/ 		
/******/ 		// no chunk on demand loading
/******/ 		
/******/ 		// no prefetching
/******/ 		
/******/ 		// no preloaded
/******/ 		
/******/ 		// no HMR
/******/ 		
/******/ 		// no HMR manifest
/******/ 		
/******/ 		__webpack_require__.O.j = (chunkId) => (installedChunks[chunkId] === 0);
/******/ 		
/******/ 		// install a JSONP callback for chunk loading
/******/ 		var webpackJsonpCallback = (parentChunkLoadingFunction, data) => {
/******/ 			var [chunkIds, moreModules, runtime] = data;
/******/ 			// add "moreModules" to the modules object,
/******/ 			// then flag all "chunkIds" as loaded and fire callback
/******/ 			var moduleId, chunkId, i = 0;
/******/ 			if(chunkIds.some((id) => (installedChunks[id] !== 0))) {
/******/ 				for(moduleId in moreModules) {
/******/ 					if(__webpack_require__.o(moreModules, moduleId)) {
/******/ 						__webpack_require__.m[moduleId] = moreModules[moduleId];
/******/ 					}
/******/ 				}
/******/ 				if(runtime) var result = runtime(__webpack_require__);
/******/ 			}
/******/ 			if(parentChunkLoadingFunction) parentChunkLoadingFunction(data);
/******/ 			for(;i < chunkIds.length; i++) {
/******/ 				chunkId = chunkIds[i];
/******/ 				if(__webpack_require__.o(installedChunks, chunkId) && installedChunks[chunkId]) {
/******/ 					installedChunks[chunkId][0]();
/******/ 				}
/******/ 				installedChunks[chunkId] = 0;
/******/ 			}
/******/ 			return __webpack_require__.O(result);
/******/ 		}
/******/ 		
/******/ 		var chunkLoadingGlobal = globalThis["webpackChunktelex_hours_block"] = globalThis["webpackChunktelex_hours_block"] || [];
/******/ 		chunkLoadingGlobal.forEach(webpackJsonpCallback.bind(null, 0));
/******/ 		chunkLoadingGlobal.push = webpackJsonpCallback.bind(null, chunkLoadingGlobal.push.bind(chunkLoadingGlobal));
/******/ 	})();
/******/ 	
/************************************************************************/
/******/ 	
/******/ 	// startup
/******/ 	// Load entry module and return exports
/******/ 	// This entry module depends on other loaded chunks and execution need to be delayed
/******/ 	var __webpack_exports__ = __webpack_require__.O(undefined, ["./style-index"], () => (__webpack_require__("./src/index.js")))
/******/ 	__webpack_exports__ = __webpack_require__.O(__webpack_exports__);
/******/ 	
/******/ })()
;
//# sourceMappingURL=index.js.map