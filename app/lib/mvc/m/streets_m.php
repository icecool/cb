<?php
namespace mvc\m;

class streets_m
{

  public function ajax($q)
  {
    $streets=[];
    if($q!='') 
    {
        $db=\app::c('db');
        if($db->connected())
        {
            $sql="SELECT * FROM `streets` WHERE `street-name` LIKE ? AND `city-id`=1 ORDER BY `street-name`;"; // moderated
            $stmt=$db->h->prepare($sql);
            $stmt->execute([$q."%"]);
            $db->q();
            if($stmt->rowCount()>0)
            {
                while($r=$stmt->fetch())
                {
                    $streets[]=$r['street-name'];
                }
            }
        }
    }
    if(count($streets)>0) echo json_encode($streets);
    exit;
  }

}
