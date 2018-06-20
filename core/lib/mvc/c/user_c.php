<?php
namespace mvc\c;

class user_c
{

    public function action($act='')
    {
      switch ($act) {

        case 'signin': // login
          $user=\app::c('user');
          if($user->uid()==0)
          {
            if(isset($_POST['s_u']) && isset($_POST['s_p']))
            {
              $this->model = new \mvc\m\user_m();
              $this->model->SignIn(['login'=>$_POST['s_u'],'pwd'=>$_POST['s_p']]);
            } else {
              $this->view = new \mvc\v\user_v();
              \app::data(\app::t('Sign in'),'title', false);
              \app::data($this->view->signin_frm());
            }
          } else {
            \app::log('info',\app::t('You already signed in.'));
          }
          break;

        case 'signout': // logout
          $this->model = new \mvc\m\user_m();
          $this->model->SignOut();
          break;

        case 'signup': // registration
          $this->model = new \mvc\m\user_m();
          $this->model->SignUp();
          break;
        
        case 'verify': // for new user
          $this->model = new \mvc\m\user_m();
          $this->model->verify();
          break;

        case 'restore':
          /*
          if(class_exists('\widgets\user_restore'))
          {
            $this->model = new \mvc\m\user_m();
            $restore = new \widgets\user_restore();
            if($this->model->can_restore())
            {
              $restore->main($this->model);
            } else {
              \app::log('err','This option is not available');
            }
          } else {
            \app::log('err',\app::t('This feature is not available.'));
          }
          */
          break;
      }
    }

}
