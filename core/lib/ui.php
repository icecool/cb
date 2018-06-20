<?php
class ui
{

private $opt=array();
private $template='';
public $widgets=array();

function __construct()
{
  $this->opt=\app::c('config')->get_options('ui');
  $this->widgets['menu'] = new widgets\menu(['path'=>'./app/menu_items.php']);
}

public function get_template()
{
  if($this->template=='')
  {
    if(isset($this->opt['ui_template']) && is_readable($this->opt['ui_template']))
    {
      $this->template = $this->opt['ui_template'];
    }
  }
  return $this->template;
}

public function set_template($template='')
{
  if(is_readable($template)) $this->template = $template;
}

public function app_messages()
{
  $log=\app::c('log');
  $err=$log->get('err');
  $info=$log->get('info');
  $debug=$log->get('debug');
  if($err!='')
  {
    $err='<div class="alert alert-danger alert-dismissible fade in" role="alert">
    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
    <span aria-hidden="true">×</span></button>
    '.htmlspecialchars($err).'
    </div>';
    \app::data($err,'messages');
  }
  if($info!='')
  {
    $info='<div class="alert alert-info alert-dismissible fade in" role="alert">
    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
    <span aria-hidden="true">×</span></button>
    '.htmlspecialchars($info).'
    </div>';
    \app::data($info,'messages');
  }
  if($debug!='')
  {
    $user=\app::c('user');
    //if($user->isAdmin()) // show debug for admins only !
    //{
      $debug='<pre>'.htmlspecialchars($debug).'</pre>';
      \app::data($debug,'messages');
    //}
  }
}

public function render($template='')
{
  $result='';
  if($template!='')
  {
    $this->set_template($template);
  } else {
    $template=$this->get_template();
  }
  $appdata=\app::c('appdata');
  if($template!='')
  {
    // here define some initial values
    \app::data(BRAND,'brand');
    // render
    $this->app_messages(); // load app messages to appdata (err,debug,etc)
    $blocks=$appdata->get_blocks();
    $result=file_get_contents($template);
    foreach($blocks as $alias => $content)
    {
        $tag="<!--@$alias-->";
        $result=str_replace($tag,$content,$result);
    }
  } else {
    echo 'Template is not found.';
  }
  echo $result;
}

}
