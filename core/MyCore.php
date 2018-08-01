<?php

namespace core;

use core\lib\Route;

final class MyCore
{
    //加载控制器
    public function run()
    {
        $this->_route();
        $this->_setDefine();
        if (is_file(CONTROLLER_PATH) && method_exists(CONTROLLER_SPACE, ACTION_NAME)) {
            $c = CONTROLLER_SPACE;
            $a = ACTION_NAME;
            $control = new  $c();
            echo $control->$a();
        } else {
            redirect(404);
        }
    }

    private function _route()
    {
        $route = new Route();
        $module = $route->module;
        $controller = $route->controller;
        $action = $route->action;
        define('MODULE_NAME', $module);
        define('CONTROLLER_NAME', lcfirst($controller));
        define('ACTION_NAME', $action);
        define('CONTROLLER_CLASS', $controller . 'Controller');
        define('MODULE_SPACE', MODULES . '\\' . $module);
        define('CONTROLLER_SPACE', '\\' . MODULE_SPACE . '\\controller\\' . CONTROLLER_CLASS);
        define('MODULE_PATH', APP_PATH . MODULE_NAME . '/');
        define('CONTROLLER_PATH', MODULE_PATH . 'controller/' . CONTROLLER_CLASS . '.php');


    }

    private function _setDefine()
    {
        define('MODEL_PATH', MODULE_PATH . 'model/');
        define('VIEW_PATH', MODULE_PATH . 'view/' . CONTROLLER_NAME . '/');
        define('__APP__', ($_SERVER['HTTPS'] == 'on' ? 'https://' : 'http://') . $_SERVER['SERVER_NAME']);
        define('__PUBLIC__', __APP__ . '/');
        define('__STATICS__', __PUBLIC__ . "static/");
        define('__STATIC__', __STATICS__ . MODULE_NAME . "/");
        define('__IMG__', config('__IMG__'));
        define('__VENDOR__', config('__VENDOR__') ? config('__VENDOR__') : (__STATICS__ . "vendor/"));
        define('__UPLOAD__', config('__UPLOAD__'));
        $db = lib\Db::getInstance();
        $sysparm = $db->getSys();
        unset($sysparm['id']);
        foreach ($sysparm as $k => $v) {
            if ($v === null || $v === '') {
                $sysparm[$k] = config($k);
            }
        }
        $_SESSION['sysParm'] = $sysparm;
    }
}