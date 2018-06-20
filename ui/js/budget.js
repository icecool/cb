$(document).ready(function(){

// here will be some useful code ;)

  $.fn.datepicker.dates['ru'] = {
  		days: ["Воскресенье", "Понедельник", "Вторник", "Среда", "Четверг", "Пятница", "Суббота"],
  		daysShort: ["Вск", "Пнд", "Втр", "Срд", "Чтв", "Птн", "Суб"],
  		daysMin: ["Вс", "Пн", "Вт", "Ср", "Чт", "Пт", "Сб"],
  		months: ["Январь", "Февраль", "Март", "Апрель", "Май", "Июнь", "Июль", "Август", "Сентябрь", "Октябрь", "Ноябрь", "Декабрь"],
  		monthsShort: ["Янв", "Фев", "Мар", "Апр", "Май", "Июн", "Июл", "Авг", "Сен", "Окт", "Ноя", "Дек"],
  		today: "Сегодня",
  		clear: "Очистить",
  		format: "dd.mm.yyyy",
  		weekStart: 1,
      monthsTitle: 'Месяцы'
  	};

  $("body").on("focus",".datepicker",function(){
    $(this).datepicker({
      format: "dd.mm.yyyy",
      language: "ru"
    });
  });

  $("#addsrow").click(function(e){
    e.preventDefault();
    var myID = parseInt($(this).attr("rel"));
    myID++;
    if(myID<150)
    {
      var newRow = '<div class="row">\
        <div class="col-md-6">\
          <div class="form-group">\
            <label for="bt_s'+myID+'">Наименование статьи</label>\
            <input type="text" class="form-control bt_s" id="bt_s'+myID+'" name="bt_s'+myID+'" placeholder="">\
          </div>\
        </div>\
        <div class="col-md-2">\
          <div class="form-group">\
            <label for="bt_cost'+myID+'">Сумма</label>\
            <input type="text" class="form-control bt_cost" id="bt_cost'+myID+'" name="bt_cost'+myID+'" rel="'+myID+'" placeholder="">\
          </div>\
        </div>\
        <div class="col-md-2">\
          <div class="checkbox obcheck">\
            <label>\
              <input type="checkbox" class="bt_ob" id="bt_ob'+myID+'" name="bt_ob'+myID+'"> Обязательный платёж\
            </label>\
          </div>\
        </div>\
      </div>';
      $("#subbox").append(newRow);
      $(this).attr("rel",myID);
    }
  });

  function budget_sum_calc()
  {
    var budget_total = 0;
    var cost = 0;
    $(".bt_cost").each(function() {
      cost = parseFloat($(this).val());
      if(cost>0) budget_total += cost;
    });
    $("#bt_total_span").text(budget_total);
    $("#bt_total").val(budget_total);
  }

  $("body").on("change",".bt_cost",function(){
    budget_sum_calc();
  });

  $("#submit_budget").click(function(e){
    var isValid = true;
    var err_msg = '';
    var bTitle = $("#bt_title").val();
    var bYear = $("#bt_year").val();
    if(bTitle.length==0)
    {
      isValid=false;
      err_msg+="Необходимо указать наименование бюджета. \n";
      $("#bt_title_group").addClass("has-error");
    }
    if(bYear.length==0)
    {
      isValid=false;
      err_msg+="Необходимо указать год для бюджета. \n";
      $("#bt_year_group").addClass("has-error");
    }

    if(isValid)
    {

    } else {
      e.preventDefault();
      alert(err_msg);
    }

  });

});
