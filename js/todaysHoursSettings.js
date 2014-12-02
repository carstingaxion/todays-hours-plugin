/* 
   Today's Hours Plugin - admin page settings script
   David Baker, Milligan College 2014
*/

jQuery(document).ready(function() {
   /* Add event listener for submit button */
   var submitButton = document.getElementById('submit');
   submitButton.addEventListener('click', handleFormChanges, false);
   
   jQuery('#showNewSeason').click(function() {
      jQuery('#addNewSeason').toggleClass('hidden');
      jQuery('#showNewSeason').toggleClass('hidden');
   });
   
   jQuery('#showNewHoliday').click(function() {
      jQuery('#addNewHoliday').toggleClass('hidden');
      jQuery('#showNewHoliday').toggleClass('hidden');
   });
   

   /* Add jquery ui to date and time fields */
   jQuery('.datepicker').datepicker({
         changeMonth:true,
         changeYear:true
      });
   jQuery('.timepicker').timepicker({
         showPeriod:true,
         showDeselectButton:true,
         deselectButtonText:'Clear Field',
         defaultTime:'',
         periodSeparator:'',
         showLeadingZero:false,
         amPmText: ['am', 'pm']
      });
});


/* On submit button click */
function handleFormChanges() {

   /*  S E A S O N S  */
   var seasonObjects = JSON.parse(document.getElementById('seasons').value);
   var numSeasons = seasonObjects.length;
   var updatedSeasonObjects = [];

   /* update any changes made to existing seasons - ignore if checked for deletion */
   for (var i = 0; i < numSeasons; i++) {
      if (document.getElementById('seasonDelete_' + i).checked == false) {
         updatedSeasonObjects.push( createNewSeasonObject(i) );  
      }
   }
   
   /* insert any new data user inputs into blank fields */
   if (document.getElementById('seasonName_new').value != '') {
      updatedSeasonObjects.push( createNewSeasonObject('new') ); 
   }
   
   /* Put updates back into hidden field */
   document.getElementById('seasons').value = JSON.stringify(updatedSeasonObjects);

  
   /*  H O L I D A Y S  */
   var holidayObjects = JSON.parse(document.getElementById('holidays').value);
   var numHolidays = holidayObjects.length;
   var updatedHolidayObjects = [];
   
   /* update any changes made to existing holidays - ignore those marked for deletion */
   for (var i = 0; i < numHolidays; i++) {
      if (document.getElementById('holidayDelete_' + i).checked == false) {
         updatedHolidayObjects.push( createNewHolidayObject(i) );
      }
   }

   if (document.getElementById('holidayName_new').value != '') {
      updatedHolidayObjects.push( createNewHolidayObject('new') );
   }
   
   /* Store JSON string to be POSTed by PHP */
   document.getElementById('holidays').value = JSON.stringify(updatedHolidayObjects);
   
   
   /* Control now goes to options.php as defined in the form action attribute */
   
} /* END handleFormChanges function */


function createNewSeasonObject(j) {
   newSeasonObject = {
      name       : document.getElementById('seasonName_' + j).value,
      begin_date : document.getElementById('seasonBegin_' + j).value,
      end_date   : document.getElementById('seasonEnd_' + j).value,
      su_open    : document.getElementById('seasonSuOpen_' + j).value,
      su_close   : document.getElementById('seasonSuClose_' + j).value,
      mo_open    : document.getElementById('seasonMoOpen_' + j).value,
      mo_close   : document.getElementById('seasonMoClose_' + j).value,
      tu_open    : document.getElementById('seasonTuOpen_' + j).value,
      tu_close   : document.getElementById('seasonTuClose_' + j).value,
      we_open    : document.getElementById('seasonWeOpen_' + j).value,
      we_close   : document.getElementById('seasonWeClose_' + j).value,
      th_open    : document.getElementById('seasonThOpen_' + j).value,
      th_close   : document.getElementById('seasonThClose_' + j).value,
      fr_open    : document.getElementById('seasonFrOpen_' + j).value,
      fr_close   : document.getElementById('seasonFrClose_' + j).value,
      sa_open    : document.getElementById('seasonSaOpen_' + j).value,
      sa_close   : document.getElementById('seasonSaClose_' + j).value
   };
   
   return newSeasonObject;
}

function createNewHolidayObject(j) {
   newHolidayObject = {
      name      : document.getElementById('holidayName_' + j).value,
      begin_date: document.getElementById('holidayBegin_' + j).value,
      end_date  : document.getElementById('holidayEnd_' + j).value,
      open_time : document.getElementById('holidayOpen_' + j).value,
      close_time: document.getElementById('holidayClose_' + j).value
   };

   return newHolidayObject;
}
