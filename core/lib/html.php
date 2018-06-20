<?php
class html
{

  public static function select($array,$opt=array())
  {
    $result='';
    if(!isset($opt['key'])) $opt['key']='';
    if(!isset($opt['attr'])) $opt['attr']='';
    if(!isset($opt['select'])) $opt['select']=0;
    if(!isset($opt['zero_text'])) $opt['zero_text']='';
    if(count($array)>0)
    {
        $result.='<select'.$opt['attr'].'>'.PHP_EOL;
        if($opt['zero_text']!='')
        {
            if($opt['select']<=0)
            {
                $result.='<option value="0" selected="selected">'.$opt['zero_text'].'</option>'.PHP_EOL;
            } else {
                $result.='<option value="0">'.$opt['zero_text'].'</option>'.PHP_EOL;
            }
        }
        if($opt['key']=='')
        {
            foreach ($array as $k => $v)
            {
                if($opt['select']===$k){$s=' selected="selected"';} else {$s='';}
                $result.='<option value="'.$k.'"'.$s.'>'.htmlspecialchars($v).'</option>'.PHP_EOL;
            }
        } else {
            foreach ($array as $k => $v)
            {
                if($opt['select']===$k){$s=' selected="selected"';} else {$s='';}
                $result.='<option value="'.$k.'"'.$s.'>'.htmlspecialchars($v[$opt['key']]).'</option>'.PHP_EOL;
            }
        }
        $result.='</select>'.PHP_EOL;
    }
    return $result;
  }

  public static function modal($opt)
  {
    if(!isset($opt['id'])) $opt['id']='modal1';
    if(!isset($opt['title'])) $opt['title']='Modal title';
    if(!isset($opt['body'])) $opt['body']='...';
    if(!isset($opt['frm_attr'])) $opt['frm_attr']='';
    if(!isset($opt['submit_text'])) $opt['submit_text']='Submit';
    if(!isset($opt['dialog_attr'])) $opt['dialog_attr']='';
    if(!isset($opt['js'])) $opt['js']='';
    if(!isset($opt['jquery_tpl'])) $opt['jquery_tpl']=true;
    if($opt['js']!='')
    {
      if($opt['jquery_tpl'])
      {
        $opt['js']=PHP_EOL.'$(document).ready(function(){'.PHP_EOL.$opt['js'].PHP_EOL.'});'.PHP_EOL;
      }
      \app::data(PHP_EOL.'<script type="text/javascript">'.$opt['js'].'</script>'.PHP_EOL,'js');
    }
    return '
<!-- Modal -->
<div class="modal fade" id="'.$opt['id'].'" tabindex="-1" role="dialog" aria-labelledby="label_'.$opt['id'].'" aria-hidden="true">
<div class="modal-dialog"'.$opt['dialog_attr'].'>
  <div class="modal-content">
  <form'.$opt['frm_attr'].'>
    <div class="modal-header">
      <button type="button" class="close" data-dismiss="modal" aria-label="Close">
      <span aria-hidden="true">&times;</span></button>
      <h3 class="modal-title" id="label_'.$opt['id'].'">'.$opt['title'].'</h3>
    </div>
    <div id="body_'.$opt['id'].'" class="modal-body">
      '.$opt['body'].'
    </div>
    <div class="modal-footer">
      <input type="submit" id="submit_'.$opt['id'].'" class="btn btn-primary" value="'.\app::t($opt['submit_text']).'">
    </div>
  </form>
  </div>
</div>
</div>
<!-- /Modal -->
';
// <button type="button" class="btn btn-default" data-dismiss="modal">'.\app::t('Close').'</button>
  }

  public static function modal_trigger($modal_id='modal1',$text='Show Modal',$attr='')
  {
    return '<button id="show_'.$modal_id.'" type="button" class="btn btn-success btn-xs" data-toggle="modal" data-target="#'.$modal_id.'"'.$attr.'>'.$text.'</button>';
  }

  public static function msg($cat='')
  {
    $result='';
    switch ($cat) {
      case 'norecords':
        $result='<div class="well" style="margin-top:20px;margin-bottom:20px;">No records found in the database.</div>'.PHP_EOL;
        break;
    }
    return $result;
  }

}
