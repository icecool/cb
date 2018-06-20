<?php
class usession
{
  private $is_started=false;
  private $extend=true;

  function __construct()
  {
    $this->start();
  }

  public function is_started()
  {
    return $this->is_started;
  }

  public function start()
  {
    if(session_status()==PHP_SESSION_NONE)
    {
      session_start();
      $this->is_started=true;
    }
  }

  public function set($key,$v)
  {
      if($this->is_started) $_SESSION[AL.'_'.$key]=$v;
  }

  public function get($key)
  {
    $v='';
    if($this->is_started)
    {
      if(isset($_SESSION[AL.'_'.$key]))
      {
        $v=$_SESSION[AL.'_'.$key];
      }
    }
    return $v;
  }

  public function unset($key)
  {
      if($this->is_started && isset($_SESSION[AL.'_'.$key]))
      {
        unset($_SESSION[AL.'_'.$key]);
      }
  }

  public function close()
  {
    $len=strlen(AL);
    foreach($_SESSION as $k => $v)
    {
        if(substr($k,0,$len)==AL)
        {
          unset($_SESSION[$k]);
        }
    }
    $this->extended_close();
  }

  public function check()
  {
    $this->extended_check();
  }

  private function extended_close()
  {
    if(isset($_COOKIE[AL.'_es']))
    {
      unset($_COOKIE[AL.'_es']);
      setcookie(AL.'_es', null, -1, '/');
      $db=\app::c('db');
      if($db->connected())
      {
        $user=\app::c('user');
        $uid=$user->uid();
        $stmt=$db->h->prepare('DELETE FROM `user-sessions` WHERE `ses-uid`=:uid;'); // all user ext records! (AND ses-hash)
        $stmt->execute(array('uid'=>$uid));
        $db->q();
      }
    }
  }

  public function extended_create($usr) // extended session
  {
    if($this->extend)
    {
      $uid=$usr['uid'];
      $db=\app::c('db');
      $h=md5(microtime().$uid);
      if($db->connected())
      {
        // remove old records
        $stmt=$db->h->prepare('DELETE FROM `user-sessions` WHERE `ses-uid`=:uid;');
        $stmt->execute(array('uid'=>$uid));
        $db->q();
        $stmt=$db->h->prepare('INSERT INTO `user-sessions` SET `ses-id`=:id,`ses-uid`=:uid,`ses-pulse`=NOW(),`ses-data`=:data;');
        $stmt->execute(array('id'=>$h,'uid'=>$uid,'data'=>json_encode($usr)));
        $db->q();
      }
      setcookie(AL.'_es', $h, strtotime(date('Y-m-d 23:59:00')), "/"); // until the end of day
    }
  }

  public function str_clean($string)
  {
    $string = str_replace(' ', '-', $string);
    return preg_replace('/[^A-Za-z0-9\-]/', '', $string);
  }

  public function extended_check()
  {
    if($this->extend)
    {
      $uid = (int) $this->get('u1');
      if($uid==0)
      {
        if(isset($_COOKIE[AL.'_es']))
        {
          $h=$this->str_clean($_COOKIE[AL.'_es']);
          $db=\app::c('db');
          if($db->connected())
          {
            $stmt=$db->h->prepare('SELECT * FROM `user-sessions` WHERE `ses-id`=:id;');
            $stmt->execute(array('id'=>$h));
            $db->q();
            if($stmt->rowCount()==1)
            {
              $r=$stmt->fetch();
              $usr=json_decode($r['ses-data'],true);
              $this->start();
              $this->set('u1',$usr['uid']);
              $this->set('u2',$usr['gids']);
              $this->set('u3',$usr['pid']);
              $this->set('u4',$usr['name']);
              \app::log('debug','[user] session extended');
            } else {
              unset($_COOKIE[AL.'_es']);
              setcookie(AL.'_es', null, -1, '/');
            }
          }
        }
      }
    }
  }

}
