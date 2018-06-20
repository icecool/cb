$(document).ready(function(){

  $("body").addClass("bg_home");

  function validateEmail(email) {
    var re = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
    return re.test(String(email).toLowerCase());
  }
  
  //$("#micro-carousel").microcarousel();

  var xStreet = new autoComplete({
    selector: '#street',
    minChars: 2,
    source: function(term, response){
      try { xhr.abort(); } catch(e){}
      xhr = $.getJSON('./?c=streets', { q: term }, function(data){ response(data); });
    }
  });

});
