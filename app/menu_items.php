<?php
if(!\app::c('user')->isGuest())
{
  $user_menu1='
<div id="house_address">
<strong>Адрес:</strong> ул. Шевченко, 35 <br>
<strong>Квартира:</strong> №24
</div>';
  $user_menu='
<a href="./?c=user&act=logout" id="cb_logout_btn"> Выйти </a>
  ';
  \app::data($user_menu1,'user1');
  \app::data($user_menu,'user');
}
