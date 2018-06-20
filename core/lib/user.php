<?php
class user
{
    private $uid=0; // user id
    private $gids=array(0); // IDs of groups
    private $name=''; // username
    private $pid=0; // profile id
    public $session=null;
    public $profile=null;

    function __construct()
    {
      $this->session = new usession();
      $this->init();
      // app::log('debug','uid='.$this->uid().', gid='.$this->gid().', gids='.print_r($this->gids(),true));
    }

    private function init()
    {
      if($this->session->is_started())
      {
        $this->session->check();
        $uid = (int) $this->session->get('u1');
        $gids = $this->session->get('u2');
        $pid = (int) $this->session->get('u3');
        $name = htmlspecialchars($this->session->get('u4'));
        if($uid>0 && count($gids)>0)
        {
            $this->uid = $uid;
            $this->gids = $gids;
            $this->name = $name;
            $this->pid = $pid;
        }
      }
    }

    public function uid()
    {
      return $this->uid;
    }

    public function gid()
    {
      if(isset($this->gids[0]))
      {
        return $this->gids[0]; // 0 - primary group id
      } else {
        return 0;
      }
    }

    public function gids()
    {
      return $this->gids;
    }

    public function pid()
    {
      return $this->pid;
    }

    public function name()
    {
      return $this->name;
    }

    public function isGuest()
    {
        return $this->uid() > 0 ? false : true;
    }

    public function isAdmin()
    {
        return $this->gid()==1 ? true : false;
    }

    public function ac($c,$act) // access control
    {
      $access=false;
      // pre-defined:
      $acl['user']['signin']['g']=[0]; // for guests
      $acl['user']['restore']['g']=[0]; // for guests
      $acl['*']['*']['g']=[1]; // for admins
      $acl['home']['']['g']=['*']; // homepage for all
      $acl['user']['signout']['g']=[1,2]; // for users
      // file based ACL:
      if($c!='home' && !$this->isAdmin())
      {
        $acl_path='./app/acl.php';
        if(is_readable($acl_path)) {
          //\app::log('debug','Loading ACL-file.');
          include($acl_path);
        }
      }
      //\app::log('debug','Checking user access.');
      // check groups
      if(isset($acl['*']['*']['g'])) { $access=$this->acc('g',$acl['*']['*']['g']); if($access) return $access; }
      if(isset($acl[$c]['*']['g'])) { $access=$this->acc('g',$acl[$c]['*']['g']); if($access) return $access; }
      if(isset($acl[$c][$act]['g'])) { $access=$this->acc('g',$acl[$c][$act]['g']); if($access) return $access; }
      // check user id
      if(isset($acl['*']['*']['u'])) { $access=$this->acc('u',$acl['*']['*']['u']); if($access) return $access; }
      if(isset($acl[$c]['*']['u'])) { $access=$this->acc('u',$acl[$c]['*']['u']); if($access) return $access; }
      if(isset($acl[$c][$act]['u'])) { $access=$this->acc('u',$acl[$c][$act]['u']); if($access) return $access; }
      // SignIn modal form for guests or if session exired
      if($this->isGuest() && !$access)
      {
        \mvc\v\user_v::signin_frm_modal();
      }
      // result
      return $access;
    }

    private function acc($type,$a=[])
    {
      $access=false;
      $test_id=0;
      if($type='g')
      {
        $test_id = $this->gids();
        for($i=0,$m=count($a);$i<$m;$i++)
        {
          for($j=0,$n=count($test_id);$i<$n;$i++)
          {
            if($a[$i]==$test_id[$j]) return true;
          }
        }
      }
      if($type='u')
      {
        $test_id = $this->uid();
        for($i=0,$m=count($a);$i<$m;$i++)
        {
          if($a[$i]==$test_id) return true;
        }
      }
      return $access;
    }

}
