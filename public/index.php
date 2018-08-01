<?php
session_start();

use core\MyCore;

//定义路径常量
define('ROOT_PATH', __DIR__ . '/../');
define('MODULES', 'app');
define('APP_PATH', ROOT_PATH . MODULES . '/');
define('CORE_PATH', ROOT_PATH . 'core/');
define('COMMON_PATH', CORE_PATH . 'common/');
define('CONFIG_PATH', ROOT_PATH . 'config/');
define('EXTEND_PATH', ROOT_PATH . 'extend/');
define('PUBLIC_PATH', ROOT_PATH . 'public/');
define('VENDOR_PATH', ROOT_PATH . 'vendor/');
//composer autoload
require VENDOR_PATH . '/autoload.php';
//加载函数库文件
require_once COMMON_PATH . 'function.php';
set_default();
//开启调试模式
if (DEBUG) {
    //打开显示错误的开关
    ini_set('display_error', 'On');
    $whoops = new \Whoops\Run;
    $whoops->pushHandler(new \Whoops\Handler\PrettyPageHandler);
    $whoops->register();
} else {
    error_reporting(0);
    ini_set('display_error', 'Off');
}
//加载框架的核心文件
require_once CORE_PATH . '/MyCore.php';
date_default_timezone_set(config('DEFAULT_TIME_ZONE_SET'));
$core = new MyCore;
$core->run();