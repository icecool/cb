<?php
namespace mvc\c;

class page_c
{

    public function action($act='')
    {
      if($act!='')
      {
        \app::data($this->static_page($act));
      } else {
        \app::log('err','404');
      }

    }

    public function static_page($alias)
    {
      $result=''; $path='';
      if(preg_match('/^[a-zA-Z0-9_]+$/',$alias))
      {
        $path=APP.'pages/'.$alias.'.php';
        if(is_readable($path))
        {
            $result=file_get_contents($path);
        } else {
            \app::log('err','404');
        }
      } else {
        \app::log('err','404');
      }
      return $result;
    }

}
