<?php
//phpinfo();exit;
if(preg_match('/(\.dev|\.loc)$/',$_SERVER['HTTP_HOST'])!==0) {
    // specify error message
    error_reporting(E_ALL);
    ini_set('display_errors', '1');
    // specify development environment
    defined('DEVELOPMENT') or define('DEVELOPMENT', true);
    defined('YII_DEBUG') or define('YII_DEBUG', true);
    // specify how many levels of call stack should be shown in each log message
    defined('YII_TRACE_LEVEL') or define('YII_TRACE_LEVEL', 3);
}else{
    define('YII_DEBUG', false);
    defined('PRODUCTION') or define('PRODUCTION', true);
}
//date_default_timezone_set ('Europe/Moscow');
$yii=dirname(__FILE__).'/protected/vendors/framework/yiilite.php';
$config = require dirname(__FILE__).'/protected/config/main.php';

require_once($yii);
Yii::createWebApplication($config)->run();