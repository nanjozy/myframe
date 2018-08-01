<?php

namespace app\admin\controller;

use app\admin\model\Systeminfo;
use app\admin\model\Userright;

class MainController extends AdminBase
{
    public function main()
    {
        $db = new Systeminfo();
        if ($_SERVER['REQUEST_METHOD'] == 'GET') {
            $rig = $this->init();
            $re = $db->get();
            $p = $_SESSION['sysParm'];
            $p['message'] = nl2br($re['message']);
            $p['rightname'] = $rig;
            display('main.html', $p);
        }
    }

    public function init()
    {
        //后台信息
        $db = new Systeminfo();
        $re = $db->get();
        $db2 = new Userright;
        $rig = $db2->getright2($_SESSION['user']['rights']);
        foreach ($re as $k => $v) {
            if ($v || is_int($v)) {
                $_SESSION['sysParm'][$k] = $v;
            }
        }
        define('ADMIN_VERSION', config('ADMIN_VERSION'));
        define('ADMIN_AUTHOR', config('ADMIN_AUTHOR'));
        define('ADMIN_HOME', $_SERVER['SERVER_NAME']);
        define('ADMIN_SOFT', php_uname('s') . php_uname('r') . ' ' . $_SERVER['SERVER_SOFTWARE'] . ' php' . PHP_VERSION);
        define('SQL_VERSION', 'MySQL ' . query("select VERSION()")[0]["VERSION()"]);
        return $rig;
    }

    public function system()
    {
        $db = new Systeminfo();
        if ($_SERVER['REQUEST_METHOD'] == 'GET') {
            $this->init();
            $p = $_SESSION['sysParm'];
            display('system.html', $p);
        } else if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $db->edit();
        }
    }

    public function safemode()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $db = new Systeminfo();
            $db->safemode();
        }
    }
}