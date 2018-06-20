<?php
namespace mvc\c;

class home_c
{

    public function action($act='')
    {
      $this->homepage();
    }

    public function homepage()
    {
      $user=\app::c('user');
      $path="./app/pages/guest.php";
      if(!$user->isGuest())
      {
        $path="./app/pages/user.php";
        if($user->isAdmin()) {$path="./app/pages/admin.php";}
        if(is_readable($path))
        {
          include($path);
        } else {
          \app::log('err','404');
        }
      } else {
        if(is_readable($path))
        {
          include($path);
        } else {
          \app::log('err','404');
        }
      }
    }

}
