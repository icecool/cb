<?php
/*
$t=[
  ['id'=>'27','name'=>'TestStreet27'],
  ['id'=>'28','name'=>'TestStreet28'],
  ['id'=>'29','name'=>'TestStreet29'],
  ['id'=>'30','name'=>'TestStreet30'],
  ['id'=>'31','name'=>'TestStreet31'],
];
*/
$result='<div class="row">

  <div class="col-md-7" style="padding-left:74px;padding-top:90px;">
    <h1 style="font-size:58px;">УПРАВЛЯЙТЕ <br> ВАШИМИ "НАЛОГАМИ"</h1>
    <div id="home_slider">
    <ul id="micro-carousel">
      <li>
        <h2>Как это работает?</h2>
        <p style="font-size:18px;">Этот сервис поможет Вам осознано и разумно управлять <br> собранными средствами.</p>
      </li>
    </ul>      
    </div>
  </div>

  <div class="col-md-3" style="padding-top:100px;text-align:center;">

    <div id="loginbox">

      <!-- Nav tabs -->
      <ul class="nav nav-tabs" role="tablist">
        <li role="presentation" class="active mytablabel"><a href="#signin_box" aria-controls="signin_box" role="tab" data-toggle="tab">Вход</a></li>
        <li role="presentation" class="mytablabel"><a href="#signup_box" aria-controls="signup_box" role="tab" data-toggle="tab">Регистрация</a></li>
      </ul>

      <!-- Tab panes -->
      <div class="tab-content mytabbox">

        <!-- SignIn -->
        <div role="tabpanel" class="tab-pane active" id="signin_box">

        <form action="./?c=user&act=signin" method="post">
          <div class="form-group">
            <label for="s_u">E-mail</label>
            <input type="text" class="form-control" id="s_u" name="s_u" placeholder="email">
          </div>
          <div class="form-group">
            <label for="s_p">Пароль</label>
            <input type="password" class="form-control" id="s_p" name="s_p" placeholder="password">
          </div>
          <div class="form-group text-center">
            <button type="submit" id="cb_go_signin" class="btn btn-mygreen"> ВОЙТИ </button>
          </div>
        </form>

        </div>

        <!-- SignUp -->

        <div role="tabpanel" class="tab-pane" id="signup_box">
        <form action="./?c=user&act=signup" method="post">

          <div class="form-group">
            <label for="cb_city">Aдрес</label>
            <select class="form-control" id="city" name="city">
              <option value="1">г. Киев</option>
            </select>
          </div>

          <div class="form-group">
            <input class="form-control" type="text" id="street" name="street" placeholder="улица/проспект">
          </div>

          <div class="row">
            <div class="col-xs-12 col-sm-4">
              <div class="form-group">
                <input class="form-control" type="text" id="building" name="building" placeholder="№ дома">
              </div>
            </div>
            <div class="col-xs-12 col-sm-4">
              <div class="form-group">
              <input class="form-control" type="text" id="apartment" name="apartment" placeholder="№ кв.">
              </div>
            </div>
          </div>

          <div class="form-group">
            <select class="form-control" id="group" name="group">
              <option value="1">житель</option>
              <option value="2">управдом</option>
            </select>
          </div>

          <div class="row">
            <div class="col-xs-12 col-sm-6">
              <div class="form-group">
                <label for="firstname">Имя</label>
                <input class="form-control" type="text" id="firstname" name="firstname" placeholder="Имя">
              </div>
            </div>
            <div class="col-xs-12 col-sm-6">
              <div class="form-group">
              <label for="lastname">Фамилия</label>
              <input class="form-control" type="text" id="lastname" name="lastname" placeholder="Фамилия">
              </div>
            </div>
          </div>          

          <div class="form-group">
            <label for="email">E-mail</label>
            <input type="text" class="form-control" id="email"  name="email" placeholder="email">
          </div>

          <div class="form-group">
            <label for="new_pwd">Новый пароль</label>
            <input type="password" class="form-control" id="new_pwd" name="new_pwd" placeholder="password">
          </div>

          <div class="form-group">
            <!--<div class="g-recaptcha" data-sitekey="6LfHY1sUAAAAAN2dV5NZP4_q55f4MIKK6WdhkPFb"></div>-->
          </div>
          
          <div class="form-group text-center">
            <button type="submit" id="go_signup" class="btn btn-mygreen"> ЗАРЕГИСТРИРОВАТЬСЯ </button>
          </div>

        </form>
        </div>

      </div>

    </div>

  </div>

</div>
';
///$result=json_encode($t);
//\app::data("<script src='https://www.google.com/recaptcha/api.js'></script>",'link');
\app::data($result);
\app::data('<link href="./ui/css/auto-complete.css" rel="stylesheet">'.PHP_EOL,'link');
\app::data('<script type="text/javascript" src="./ui/js/auto-complete.min.js"></script>'.PHP_EOL,'js');
\app::data('<script type="text/javascript" src="./ui/js/jquery.microcarousel.min.js"></script>'.PHP_EOL,'js');
\app::data('<script type="text/javascript" src="./ui/js/main.js"></script>'.PHP_EOL,'js');
