<?php
namespace mvc\c;

class streets_c
{

  public function action($act="")
  {
    switch ($act) {
      default:
        $this->model = new \mvc\m\streets_m;
        $q='';
        if(isset($_GET['q'])) $q=$_GET['q'];
        $this->model->ajax($q);
        break;
    }
  }

}
