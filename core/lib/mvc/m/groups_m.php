<?php
namespace mvc\m;

class groups_m
{

  public function get_groups()
  {
    $groups=array();
    $db=\app::c('db');
    $groups=$db->get('SELECT * FROM `n-groups` ORDER BY `gp-sort`;');
    return $groups;
  }

}
