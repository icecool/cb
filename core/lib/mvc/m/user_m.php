<?php
namespace mvc\m;

class user_m
{
  public function signin($u=['login'=>'','pwd'=>''])
  {
    // processing parameters
    $u['login']=strtolower(trim($u['login']));
    $u['pwd']=trim($u['pwd']);
    // validation
    if(\is_valid::email($u['login']) && $this->valid('pwd',$u['pwd']))
    {
      $db=\app::c('db');
      if($db->connected())
      {
        $stmt=$db->h->prepare('SELECT * FROM `users` WHERE `email` = ?;');
        $stmt->execute([$u['login']]);
        $db->q();
        if($stmt->rowCount()==1)
        {
          $r=$stmt->fetch();
          $salt=$r['salt'];
          $pwd_hash=md5(md5($u['pwd']).$salt);
          if($pwd_hash==$r['pwd'])
          {
            if($r['active']==1) // is active
            {
              $usr=array(
                'uid'=>0,
                'gid'=>0,
                'name'=>''
              );
              $usr['uid']=(int) $r['uid'];
              $usr['gid']=(int) $r['gid'];
              $usr['name']=$r['firstname'];
              // save/store in session
              if(session_status()==PHP_SESSION_NONE){session_start();}
              $user=\app::c('user');
              $user->session->set('u1',$usr['uid']);
              $user->session->set('u2',$usr['gid']);
              $user->session->set('u3',$usr['name']);
              // update login time (lastlogin)
              //$this->update_login_time($usr['uid']);
              // store username in cookie (last_user)
              setcookie(AL.'_lu', base64_encode(strrev(base64_encode($usr['name']))),time()+(86400*7),"/"); // 86400=1 day (*7=1,week)
              // extend session
              $user->session->extended_create($usr);
              header('Location: ./'); exit;
            } else {
              \app::log('err',\app::t('Account is currently inactive.'));
            }
          } else {
            \app::log('err',\app::t('Incorrect username or password.'));
            \app::data(\mvc\v\user_v::signin_frm());
          }
        } else {
          \app::log('err',\app::t('Incorrect username.'));
          \app::data(\mvc\v\user_v::signin_frm());
        }
      }
    }
    else
    {
      \app::log('err',\app::t('Incorrect username or password.'));
      \app::data(\mvc\v\user_v::signin_frm());
    }
  }

  public function signout()
  {
    $user=\app::c('user');
    $uid=$user->uid();
    if($uid>0)
    {
      $user->session->close();
      header('Location: ./');
      exit;
    }
  }

  public function update_login_time($uid)
  {
    $db=\app::c('db');
    if($db->connected())
    {
      $stmt=$db->h->prepare('UPDATE `users` SET `lastlogin` = NOW() WHERE `uid` = :uid;');
      $stmt->execute(array('uid'=>$uid));
      $db->q();
    }
  }

  public function get_name($uid=0)
  {
    $name='';
    if($uid==0){$uid=$user->get('uid');}
    if($uid>0)
    {
      $db=\app::c('db');
      if($db->connected())
      {
        $sql="SELECT `uid`,`firstname` FROM `users` WHERE `uid`=:uid;";
        $stmt=$db->h->prepare($sql);
        $stmt->execute(array('uid'=>$uid));
        $db->q();
        if($stmt->rowCount()==1)
        {
          $r=$stmt->fetch();
          $name = htmlspecialchars($r['firstname']);
        }
      }
    }
    return $name;
  }

  public function generate_salt($n=3)
  {
    $key='';
    $pattern='1234567890abcdefghijklmnopqrstuvwxyz.,*_-=+';
    $counter=strlen($pattern)-1;
    for($i=0;$i<$n;$i++){$key.=$pattern{rand(0,$counter)};}
    return $key;
  }

  public function random_pwd($len_min=8,$len_max=0)
  {
    // testing
    if($len_max>$len_min){
      $len=rand($len_min,$len_max);
    } else {
      $len=(int) $len_min;
    }
      $alphabet = "abcdefghijklmnopqrstuwxyzABCDEFGHIJKLMNOPQRSTUWXYZ0123456789";
      $pass = array(); // remember to declare $pass as an array
      $alphaLength = strlen($alphabet) - 1; // put the length -1 in cache
      for ($i = 0; $i < $len; $i++) {
          $n = rand(0, $alphaLength);
          $pass[] = $alphabet[$n];
      }
      return implode($pass); // turn the array into a string
  }

  public function generate_pwd($pwd='',$min=8,$max=0){
    if($pwd=='') { $pwd=$this->random_pwd($min,$max); }
    $salt=$this->generate_salt();
    $hash=md5(md5($pwd).$salt);
    $p=array(
      'pwd' => $pwd,
      'hash' => $hash,
      'salt' => $salt,
      );
    return $p;
  }

    public function passwd($uid=0,$pwd='')
    {
      $user=\app::c('user');
      if($uid==0){$uid=$user->get('uid');}
      if($uid>0 && $pwd!='')
      {
        if($this->valid('pwd',$pwd))
        {
          $p=$this->generate_pwd($pwd);
          $salt=$p['salt'];
          $hash=$p['hash'];
          $db=\app::c('db');
          if($db->connected())
          {
            $sql="UPDATE `users` SET `pwd`=:hash, `salt`=:salt;";
            $stmt=$db->h->prepare($sql);
            $stmt->execute(array('hash'=>$hash,'salt'=>$salt));
            $db->q();
          }
        } else {
          \app::log('err','Password is not valid');
        }
      }
    }

    public function valid($type,$value)
    {
      $valid=false;
      $len=strlen($value);
      switch ($type) {
        case 'login':
          if(\app::regex($value,'/^[a-z0-9]+$/') && ($len>=3 && $len<128))
          {
            $valid=true;
          }
          break;
        case 'pwd':
          if($len>=8 && $len<256)
          {
            $valid=true;
          }
          break;
          case 'link':
            if(\app::regex($value,'/^[a-zA-Z0-9]+$/'))
            {
              $valid=true;
            }
            break;
      }
      return $valid;
    }

    public function signup()
    {
      ////\app::data('<pre>'.print_r($_POST,true).'</pre>');
      $valid=true;

      // re-captcha
      if(isset($_POST['g-recaptcha-response']))
      {
        $captcha=$_POST['g-recaptcha-response'];
        $captcha_response=file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret=6LfHY1sUAAAAACz4w87bhF2Vn85HPM5j6nrBdpJE&response=".$captcha."&remoteip=".$_SERVER['REMOTE_ADDR']);
        $obj = json_decode($captcha_response);
        if($obj->success == true)
        {
          //passes test
  
        }
        else
        {
          //error handling
          $valid=false;
          \app::log('err','Неправильный код верификации (captcha).');
        }
      }

      if($valid)
      {
        $param=[
          'city'=>'',
          'street'=>'',
          'building'=>'',
          'apartment'=>'',
          'firstname'=>'',
          'lastname'=>'',
          'group'=>'',
          'email'=>'',
          'new_pwd'=>''
        ];
        // checking received params
        foreach ($param as $k => $v) {
          if(!isset($_POST[$k])) {
            $valid=false;
            \app::log('err','Не указан параметр: "'.$k.'". ');
          } else {
            $param[$k]=trim($_POST[$k]);          
          }
        }
        if(!$valid) {\app::log('err','Не все параметры формы регистрации были получены.');}
      }

      if($valid)
      {
        // params validation
        if($param['city']!=1)
        {
          $valid=false;
          \app::log('err','Указанный Вами город, пока не добавлен.');
        }
        if($param['street']=='')
        {
          $valid=false;
          \app::log('err','Необходимо указать название улицы (проспекта или др.).');
        }
        if($param['building']=='')
        {
          $valid=false;
          \app::log('err','Необходимо указать номер дома.');
        }
        if($param['apartment']=='')
        {
          $valid=false;
          \app::log('err','Необходимо указать номер квартиры.'); // means we make it for apartment houses (flats)
        }
        if($param['firstname']=='')
        {
          $valid=false;
          \app::log('err','Необходимо указать Имя.');
        }
        /*
        if($param['lastname']=='')
        {
          $valid=false;
          \app::log('err','Необходимо указать Фамилию.');
        }
        */
        $param['group']=(int) $param['group'];
        if(!\is_valid::email($param['email']))
        {
          $valid=false;
          \app::log('err','Необходимо указать корректный email.');
        }
        if(strlen($param['new_pwd']) < 8)
        {
          $valid=false;
          \app::log('err','Пароль должен содержать не менее 8-ми символов.');
        }        
      }

      if($valid)
      {
        // try to create new user
        // first check if such email is not exist
        $db = \app::c('db');
        if($db->connected())
        {
          $sql="SELECT `uid`,`email` FROM `users` WHERE `email`=:email;";
          $stmt=$db->h->prepare($sql);
          $stmt->execute([':email'=>$param['email']]);
          $db->q();
          if($stmt->rowCount()>0)
          {
            $valid=false;
            \app::log('err','Указанный email уже зарегистрирован в системе.');
          }
          else
          {
            $vericode=md5( $param['email'] . microtime() );
            $p=$this->generate_pwd($param['new_pwd']);
            $u=[
              'gid'=>$param['group'],
              'email'=>$param['email'],
              'pwd'=>$p['hash'],
              'salt'=>$p['salt'],
              'city'=>$param['city'],
              'street'=>$param['street'],
              'building'=>$param['building'],
              'apartment'=>$param['apartment'],
              'firstname'=>$param['firstname'],
              'lastname'=>$param['lastname'],
              'vcode'=>$vericode
            ];
            $link='http://localhost/cb/?c=user&act=verify&vc='.$vericode.'&ve='.base64_encode($param['email']); // then change domain to: http://clever-budget.org/
            // create
            $sql="INSERT INTO `users` (`gid`,`email`,`pwd`,`salt`,`city`,`street-name`,`building`,`apartment`,`firstname`,`lastname`,`vcode`) VALUES
            (:gid,:email,:pwd,:salt,:city,:street,:building,:apartment,:firstname,:lastname,:vcode);";
            $stmt=$db->h->prepare($sql);
            $stmt->execute($u);
            $db->q();
            // street id
            
            // send email for verification
            $body=\widgets\mailbot::template('./app/lib/widgets/mail_tpl/verify.html',['NAME'=>', '.$u['firstname'].',','LINK'=>$link]);
            $body_txt=\widgets\mailbot::template('./app/lib/widgets/mail_tpl/verify.txt',['LINK'=>$link]);
            $message=[
              'to'=>$param['email'],
              'subject'=>'Подтверждение регистрации на clever-budget.org',
              'body'=>$body,
              'body-txt'=>$body_txt
            ];
            \widgets\mailbot::mail($message);
          }
        }
      }

    }

public function verify()
{
  $vcode='';
  $email='';
  $uid=0;
  if(isset($_GET['vc'])) $vcode=$_GET['vc'];
  if(isset($_GET['ve'])) $email=base64_decode($_GET['ve']);
  if($vcode!='' && $email!='')
  {
    $db=\app::c('db');
    if($db->connected())
    {
      $sql="SELECT * FROM `users` WHERE `email`=:email AND `vcode`=:vcode AND `active`=0;";
      $stmt=$db->h->prepare($sql);
      $stmt->execute([':email'=>$email,':vcode'=>$vcode]);
      $db->q();
      if($stmt->rowCount()==1)
      {
        $r=$stmt->fetch();
        $uid=(float) $r['uid'];
        $sql="UPDATE `users` SET `vcode`='verified',`active`=1 WHERE `uid`=:uid;";
        $stmt=$db->h->prepare($sql);
        $stmt->execute([':uid'=>$uid]);
        $db->q();
        \app::data('Процедура подтверждения регистрации пользователя завершена! <a href="./">Войдите в систему.</a>');

      } else {
        \app::log('err','Эта ссылка недействительна или уже ранее использована.');
      }
    }
  }
}

}
