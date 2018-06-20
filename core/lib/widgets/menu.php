<?php
namespace widgets;

class menu
{

  private $user=null;
  private $path='';
  private $menu=array();

  function __construct($opt=[])
  {
    $this->user=\app::c('user');
    if(isset($opt['path'])) $this->set_path($opt['path']);
    if($this->path!='')
    {
      $this->load_menu_from_file();
    } else {
      // $this->load_menu_from_db();
    }
    $this->render();
  }

  private function set_path($path='')
  {
    if($path!='' && is_readable($path))
    {
      $this->path = $path;
    } else {
      \app::log('debug','Menu source file is not found.');
    }
  }

  public function load_menu_from_file()
  {
    include($this->path);
    if(isset($menu)) $this->menu=$menu;
  }

  public function load_menu_from_db()
  {
    //...
  }

  private function get_items($items)
    {
      $result='';
      $c=count($items);
      for($i=0;$i<$c;$i++)
      {
        if(isset($items[$i]['items']))
        {
          $result.='<li class="dropdown">
<a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">
'.$items[$i]['label'].' <span class="caret"></span></a>
<ul class="dropdown-menu">'.PHP_EOL;
          $result.=$this->get_items($items[$i]['items']);
          $result.='</ul>'.PHP_EOL.'</li>'.PHP_EOL;
        } else {
          if(isset($items[$i]['url']))
          {
            $result.='<li><a href="'.$items[$i]['url'].'">'.$items[$i]['label']."</a></li>".PHP_EOL;
          } else {
            $result.=$items[$i]['label'].PHP_EOL;
          }
        }
      }
      return $result;
    }

  public function render()
  {
    if(count($this->menu)>0)
    {
      foreach ($this->menu as $name => $items) {
        \app::data('<ul class="nav navbar-nav">'.PHP_EOL,$name);
        \app::data($this->get_items($items),$name);
        \app::data('</ul>'.PHP_EOL,$name);
      }
    }
  }

}
