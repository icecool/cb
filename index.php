<?php
ini_set('display_errors',1); ini_set('display_startup_errors',1); error_reporting(E_ALL); // for debug
define('CORE', './core/');
if(is_readable(CORE.'main.php'))
{
  require(CORE.'main.php');
} else {
  echo 'Main script is not found.';
  exit;
}
$app=app::init();
$app->run('config.php');
