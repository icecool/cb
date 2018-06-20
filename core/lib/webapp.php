<?php
class webapp
{
	private $components=[]; // application components (from core)
	private $modules=[]; // modules container

	public function load_component($name,$opt=[])
	{
		if(!isset($this->components[$name]))
		{
			$this->components[$name] = new $name($opt);
		}
	}

	public function get_component($name)
	{
		if(isset($this->components[$name]))
		{
			return $this->components[$name];
		}
	}

	public function load_module($name,$opt=[])
	{
		if(!isset($this->modules[$name]))
		{
			$this->modules[$name] = new $name($opt);
		}
	}

	public function get_module($name)
	{
		if(isset($this->modules[$name]))
		{
			return $this->modules[$name];
		}
	}

	private function stop()
	{
		// here close db connection if needed
		$db=$this->get_module('db');
		if($db!==null) $db->close();
		// echo '<pre>'.print_r(get_included_files(),true).'</pre>'; // here for debug
	}

	private function router()
	{
		$module = new mvc_module();
		if(!isset($this->modules[$module->get_name()]))
		{
			$this->modules[$module->get_name()] = $module;
		}
	}

	public function run($config='config.php')
	{
		$this->load_component('config',['path'=>$config]);
		$this->load_component('log');
		$this->load_component('db');
		$this->load_component('appdata');
		$this->load_component('user');
		$this->load_component('ui');
		$this->router();
		$this->get_component('ui')->render();
		$this->stop();
	}

}
