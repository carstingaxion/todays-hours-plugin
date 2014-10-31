/* 
   Today's Hours Plugin - admin page settings script
   David Baker, Milligan College 2014

*/

jQuery(document).ready(function() {
   /* Add event listeners for delete buttons and submit button
   */
   var submitButton = document.getElementById('submit');
   submitButton.addEventListener('click', handleFormChanges, false);
   
   var seasonObjects = JSON.parse(document.getElementById('seasons').value);
   var numSeasons = seasonObjects.length;
   for (var i = 0; i < numSeasons; i++) {
      var aDeleteButton = document.getElementsByName('seasonDelete_' + i)[0];
      aDeleteButton.addEventListener('click', deleteSeason, false);
   }
   
   var holidayObjects = JSON.parse(document.getElementById('holidays').value);
   var numHolidays = holidayObjects.length;
   for (var i = 0; i < numHolidays; i++) {
      var aDeleteButton = document.getElementsByName('holidayDelete_' + i)[0];
      aDeleteButton.addEventListener('click', deleteHoliday, false);
   }
  
});

function deleteSeason(event) {
   /* remove data from JSON string */
   var buttonName = event.target.name;
   var seasonNumber = buttonName.substr(buttonName.length - 1);
   var seasonObjects = JSON.parse(document.getElementById('seasons').value); 
   seasonObjects.splice(seasonNumber,1);
   document.getElementById('seasons').value = JSON.stringify(seasonObjects);

   /* remove deleted season's form fields */
   var seasonParentDiv = document.getElementById('season' + seasonNumber);
   seasonParentDiv.parentNode.removeChild(seasonParentDiv);
}

function deleteHoliday(event) {
   /* remove data from JSON string */
   var buttonName = event.target.name;
   var holidayNumber = buttonName.substr(buttonName.length - 1);
   var holidayObjects = JSON.parse(document.getElementById('holidays').value); 
   holidayObjects.splice(holidayNumber,1);
   document.getElementById('holidays').value = JSON.stringify(holidayObjects);

   /* remove deleted season's form fields */
   var holidayParentDiv = document.getElementById('holiday' + holidayNumber);
   holidayParentDiv.parentNode.removeChild(holidayParentDiv);
}


/* On submit button click */
function handleFormChanges() {
   var seasonObjects = JSON.parse(document.getElementById('seasons').value);
   var numSeasons = seasonObjects.length;
   var updatedSeasonObjects = [];

   /* update any changes made to existing season textboxes */
   for (var i = 0; i < numSeasons; i++) {
      updatedSeasonObjects.push( createNewSeasonObject(i) );  
   }
   
   /* insert any new data user inputs into blank fields */
   if (document.getElementsByName('seasonName_new')[0].value != '') {
      updatedSeasonObjects.push( createNewSeasonObject('new') ); 
   }
   
   /* Put updates back into hidden field to be POSTed */
   document.getElementById('seasons').value = JSON.stringify(updatedSeasonObjects);
   
   var holidayObjects = JSON.parse(document.getElementById('holidays').value);
   var numHolidays = holidayObjects.length;
   var updatedHolidayObjects = [];
   
   for (var i = 0; i < numHolidays; i++) {
      updatedHolidayObjects.push( createNewHolidayObject(i) );
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
