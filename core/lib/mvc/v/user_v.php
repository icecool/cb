<?php
namespace mvc\v;

class user_v
{

  public static function signin_frm($opt=array())
  {
    $opt['show_last_user']=true;
    $opt['forgot']=false;
    $frm=array(
      'last_user'=>'',
      'title'=>\app::t('Вход в систему'),
      'prefix_body'=>'',
      'forgot'=>'',
      'submit_text'=>\app::t('Войти'),
      'body'=>'',
      'postfix_body'=>'',
    );
    if(isset($opt['show_last_user']) && isset($_COOKIE[AL.'_lu']))
    {
      $frm['last_user']=htmlspecialchars(base64_decode(strrev(base64_decode($_COOKIE[AL.'_lu']))));
    }
    $frm['pre_body']=PHP_EOL.'<div class="col-md-12">
<div id="si_box" style="width:320px;margin:auto;">
<h3 class="form-signin-heading" style="margin-bottom:14px;">'.$frm['title'].'</h3>
<form id="SiFrm" action="./?c=user&act=signin" method="post">'.PHP_EOL;
    if($opt['forgot'])
    {
    $frm['forgot']='<div style="margin-top:14px;"><small><a href="./?c=user&act=restore">Forgot password?</a></small></div>'.PHP_EOL;
    }
    $frm['body']=PHP_EOL.'<div class="input-group">
  <span class="input-group-addon"><i class="glyphicon glyphicon-envelope"></i></span>
  <input type="text" class="form-control" id="s_u" name="s_u" value="'.$frm['last_user'].'" placeholder="'.\app::t('email').'" style="font-size:120%;">
</div>
<div class="input-group" style="margin-top:14px;">
  <span class="input-group-addon"><i class="glyphicon glyphicon-lock"></i></span>
  <input type="password" class="form-control" id="s_p" name="s_p" value="" placeholder="'.\app::t('password').'" style="font-size:120%;">
</div>'.PHP_EOL.$frm['forgot'].PHP_EOL;
  $frm['post_body']='<div style="text-align:right; margin-top:14px;">
  <input type="submit" id="btn_s" class="btn btn-primary" style="font-size:120%;" value="'.$frm['submit_text'].'">
</div>
</form>
</div>
</div>'.PHP_EOL;
    if(isset($opt['as_array']))
    {
      return $frm;
    } else {
      $js='
$(document).ready(function(){

  if($("#s_u").val()!="") { $("#s_p").focus(); } else { $("#s_u").focus(); }

});
';
      \app::data('<script type="text/javascript">'.$js.'</script>'."\n",'js');
      return $frm['pre_body'].$frm['body'].$frm['post_body'];
    }
  }

  public static function signin_frm_modal()
  {
    $frm=user_v::signin_frm(array('as_array'=>true));
    $modal_opt=array(
      'id' => 'SiFrmModal',
      'title' => $frm['title'],
      'body' => $frm['body'],
      'submit_text' => $frm['submit_text'],
      'dialog_attr' => ' style="width:320px;"',
    );
    \app::data(\html::modal($modal_opt));
    $js='
$(document).ready(function(){

  $("#SiFrmModal").modal("show");

  $("#SiFrmModal").on("shown.bs.modal", function () {
    if($("#s_u").val()!="") { $("#s_p").focus(); } else { $("#s_u").focus(); }
  })

  $("#submit_SiFrmModal").click(function(e){
    e.preventDefault();
    var s_u = $("#s_u").val();
    var s_p = $("#s_p").val();
    if(s_u!="" && s_p!="")
    {
      $.post( "./?c=user&act=signin", { s_u: s_u, s_p: s_p })
        .done(function() {
          location.reload();
      });
    }
  });

});
    ';
    \app::data('<script type="text/javascript">'.$js.'</script>'."\n",'js');
  }

  

}
