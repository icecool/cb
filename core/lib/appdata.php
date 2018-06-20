<?php
class appdata // store application data here, then render via ui
{
  function __construct()
  {
    // initial values
    $this->set(BRAND,'title');
  }

  // including blocks for html template and others
  private $blocks=array(
    'meta'=>'',
    'link'=>'',
    'title'=>'',
    'js'=>'',
    'main'=>''
  );

  public function get_blocks()
  {
    return $this->blocks;
  }

  public function set($s='',$block='main',$append=true)
  {
    if(isset($this->blocks[$block]))
    {
      if($append) {$this->blocks[$block].=$s;} else {$this->blocks[$block]=$s;}
    } else {
      $this->blocks[$block]=$s;
    }
  }

  public function get($block='main')
  {
    $s='';
    if(isset($this->blocks[$block])) $s=$this->blocks[$block];
    return $s;
  }

}
