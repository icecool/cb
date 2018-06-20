$(document).ready(function(){

  // mydate widget

  $("#year_switcher").change(function(){
    location.href = "./?c=" + $(this).attr('rel') + "&year=" + $(this).val();
  });

});
