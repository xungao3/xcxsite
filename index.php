<?php
define('XG_BIND_APP','home');
define('XG_ROOT',str_replace('\\','/',dirname($_SERVER['PHP_SELF'])).'/');
define('XG_STATIC','/'.trim(XG_ROOT,'/').'/public/');
require './xgphp/xgphp.php';
?>