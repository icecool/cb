<?php
// for autoloading
set_include_path(get_include_path().PATH_SEPARATOR.'./app/lib/');
set_include_path(get_include_path().PATH_SEPARATOR.CORE.'lib/');
spl_autoload_register();

class app // singleton
{
  protected static $app=null;

  protected function __construct(){}
  protected function __clone(){}

  public static function init()
  {
      if(!isset(static::$app)) { static::$app = new webapp(); }
      return static::$app;
  }

  public static function get()
  {
    return app::init();
  }

  public static function c($name)
  {
    if(isset(static::$app)) return static::$app->get_component($name);
  }

  public static function log($cat,$msg)
  {
      if(isset(static::$app)) {
        app::c('log')->set($cat,$msg);
      }
  }

  public static function data($content,$block='main',$append=true)
  {
      if(isset(static::$app))
      {
        $appdata=app::c('appdata');
        $appdata->set($content,$block,$append);
      }
  }

  public static function user()
  {
    if(isset(static::$app))
    {
      return app::c('user');
    }
  }

  public static function t($s)
	{
		// translation (for further implementation)
		return $s;
	}

  public static function regex($s,$regex='/^[a-zA-Z0-9_]+$/')
	{
		if(preg_match($regex,$s)) { return true; } else { return false; }
	}

}

class mvc_module
{
  private $c=''; // for controller name
	private $act=''; // for action name

  private $model = null;
  private $view = null;
  private $controller = null;

  function __construct($c='',$act='')
	{
    if($c=='' && isset($_GET['c'])) // try user defined controller name
		{
			$c=$_GET['c'];
			if($c!='' && isset($_GET['act'])) { $act=$_GET['act']; } // try user defined action name
		}
		if($c=='') $c='home'; // default controller name
		if(app::regex($c) && (app::regex($act) || $act==''))
		{
			// check user access
			if(app::user()->ac($c,$act))
			{
				$path="\\mvc\\c\\".$c.'_c';
				if(class_exists($path))
				{
					$this->controller = new $path();
          $this->controller->action($act);
					$this->c = $c;
					$this->act = $act;
				} else {
					app::log('err','Module is not found.');
				}

			} else {
				app::log('err','Access denied.');
			}
		} else {
			app::log('err','Bad request.');
		}
	}

  public function get_name()
  {
    return $this->c; // return controller name (alias for mvc_module name)
  }

}
