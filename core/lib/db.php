<?php
class db
{
    private $connected = false;
    private $cfg = array();
    public $h = null; // db handle
    private $queries_counter=0;

    function __construct($cfg=array())
    {
      if(count($cfg)==0) $this->cfg=\app::c('config')->get_options('db',true); // get and clean
    }

    private function cfg_is_ok()
    {
      // checking db options/config
      $valid=true;
      if(count($this->cfg)>0)
      {
        if(!isset($this->cfg['db_server'])) $valid=false;
        if(!isset($this->cfg['db_name'])) $valid=false;
        if(!isset($this->cfg['db_user'])) $valid=false;
        if(!isset($this->cfg['db_pass'])) $valid=false;
        if(!isset($this->cfg['db_charset'])) $valid=false;
      } else {
        $valid=false;
      }
      return $valid;
    }

    private function connect()
    {
        if($this->cfg_is_ok()){
            try {
                $dsn='mysql:host='.$this->cfg['db_server'].';dbname='.$this->cfg['db_name'];
                $opt=array(
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false,
                );
                $this->h = new PDO($dsn,$this->cfg['db_user'],$this->cfg['db_pass'],$opt);
                $this->h->query('SET NAMES '.$this->cfg['db_charset']);
                $this->connected=true;
                // app::log('debug','[db]: connected');
                unset($this->cfg);
            } catch(PDOException $e) {
                app::log('err','DB connection problems');
                app::log('debug','[db]: '.$e->getMessage());
            }
        } else {
            app::log('err','Problems with DB config');
        }
    }

    public function connected()
    {
    	if(!$this->connected) $this->connect();
    	return $this->connected;
    }

    public function close()
    {
    	if($this->connected && $this->h!=null)
    	{
	        $this->h=null;
	        $this->connected=false;
          // app::log('debug','[db]: connection closed');
	    }
    }

    public function get($sql,$opt=array())
    {
        $records=array();
        if($this->connected())
        {
          $sth=$this->h->prepare($sql);
          if(count($opt)>0)
          {
            $sth->execute($opt);
          } else {
            $sth->execute();
          }
          $this->q();
          if($sth->rowCount()>0){
              $records=$sth->fetchAll();
          }
        }
        return $records;
    }

    public function fetch_records($sql,$key='',$opt=array())
    {
        $records=array();
        if($this->connected())
        {
          $sth=$this->h->prepare($sql);
          if(count($opt)>0)
          {
            $sth->execute($opt);
          } else {
            $sth->execute();
          }
          $this->q();
          if($sth->rowCount()>0){
            if($key!='')
            {
              while($r=$sth->fetch()){
  	        		$records[$r[$key]]=$r;
  	        	}
            } else {
              $records=$sth->fetchAll();
            }
	        }
        }
        return $records;
    }

    public function q()
    {
      $this->queries_counter++;
    }

    public function q_number()
    {
      return $this->queries_counter;
    }

}
