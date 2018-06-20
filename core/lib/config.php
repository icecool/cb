<?php
/*
This class for working with the configuration file,
the configuration file itself is stored separately (in the app directory)
*/
class config
{

	private $config=array();

	function __construct($opt=array())
	{
		if(isset($opt['path']))
		{
			$path='./app/config/'.$opt['path']; // default config path
			if($path)
			{
				require($path);
				$this->config=$cfg;
			} else {
				echo 'Configuration is not found.';
				exit;
			}
		} else {
			echo 'Please specify configuration path.';
			exit;
		}
	}

	public function set($key,$value)
	{
		$this->config[$key]=$value;
	}

	public function get($key)
	{
		if(isset($this->config[$key]))
		{
			return $this->config[$key];
		} else {
			return '';
		}
	}

	public function get_options($prefix='',$clean=false)
	{
		$options=array();
		if($prefix!='')
		{
			$prefix.='_';
			$len=strlen($prefix);
			foreach ($this->config as $key => $value)
			{
				if(substr($key,0,$len)==$prefix)
				{
					$options[$key]=$value;
					if($clean) unset($this->config[$key]);
				}
			}
		}
		return $options;
	}

}
