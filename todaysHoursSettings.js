/* 
   Today's Hours Plugin - admin page settings script
   David Baker, Milligan College 2014

*/

jQuery(document).ready(function() {
   /* Add event listener for submit button */
   var submitButton = document.getElementById('submit');
   submitButton.addEventListener('click', handleFormChanges, false);

   /* Add jquery ui to date and time fields */
   jQuery(".datepicker").datepicker({changeMonth:true,changeYear:true});
   jQuery(".timepicker").timepicker({showPeriod:true,showDeselectButton:true,deselectButtonText:'Clear Field',defaultTime:''});
});


/* On submit button click */
function handleFormChanges() {

   /*  S E A S O N S  */
   var seasonObjects = JSON.parse(document.getElementById('seasons').value);
   var numSeasons = seasonObjects.length;
   var updatedSeasonObjects = [];

   /* update any changes made to existing seasons - ignore if checked for deletion */
   for (var i = 0; i < numSeasons; i++) {
      if (document.getElementsByName('seasonDelete_' + i)[0].checked == false) {
         updatedSeasonObjects.push( createNewSeasonObject(i) );  
      }
   }
   
   /* insert any new data user inputs into blank fields */
   if (document.getElementsByName('seasonName_new')[0].value != '') {
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
      if (document.getElementsByName('holidayDelete_' + 1)[0].checked == false) {
         updatedHolidayObjects.push( createNewHolidayObject(i) );
      }
   }

   if (document.getElementsByName('holidayName_' + i)[0].value != '') {
      updatedHolidayObjects.push( createNewHolidayObject(i) );
   }
   
   /* Store JSON string to be POSTed by PHP */
   document.getElementById('holidays').value = JSON.stringify(updatedHolidayObjects);
   
   
   /* Control now goes to options.php as defined in the form action attribute */
   
} /* END handleFormChanges function */


function createNewSeasonObject(j) {
   newSeasonObject = {
      name       : document.getElementsByName('seasonName_' + j)[0].value,
      begin_date : document.getElementsByName('seasonBegin_' + j)[0].value,
      end_date   : document.getElementsByName('seasonEnd_' + j)[0].value,
      su_open    : document.getElementsByName('seasonSuOpen_' + j)[0].value,
      su_close   : document.getElementsByName('seasonSuClose_' + j)[0].value,
      mo_open    : document.getElementsByName('seasonMoOpen_' + j)[0].value,
      mo_close   : document.getElementsByName('seasonMoClose_' + j)[0].value,
      tu_open    : document.getElementsByName('seasonTuOpen_' + j)[0].value,
      tu_close   : document.getElementsByName('seasonTuClose_' + j)[0].value,
      we_open    : document.getElementsByName('seasonWeOpen_' + j)[0].value,
      we_close   : document.getElementsByName('seasonWeClose_' + j)[0].value,
      th_open    : document.getElementsByName('seasonThOpen_' + j)[0].value,
      th_close   : document.getElementsByName('seasonThClose_' + j)[0].value,
      fr_open    : document.getElementsByName('seasonFrOpen_' + j)[0].value,
      fr_close   : document.getElementsByName('seasonFrClose_' + j)[0].value,
      sa_open    : document.getElementsByName('seasonSaOpen_' + j)[0].value,
      sa_close   : document.getElementsByName('seasonSaClose_' + j)[0].value
   };
   
   return newSeasonObject;
}

function createNewHolidayObject(j) {
   newHolidayObject = {
      name      : document.getElementsByName('holidayName_' + j)[0].value,
      begin_date: document.getElementsByName('holidayBegin_' + j)[0].value,
      end_date  : document.getElementsByName('holidayEnd_' + j)[0].value,
      open_time : document.getElementsByName('holidayOpen_' + j)[0].value,
      close_time: document.getElementsByName('holidayClose_' + j)[0].value
   };

   return newHolidayObject;
}
