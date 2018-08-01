<?php

namespace app\admin\controller;

use app\admin\model\Userright;

class NavController extends AdminBase
{
    public function getnav()
    {
        $db = new Userright();
        $right = $db->getright($_SESSION['user']['rights']);
        if ($right) {
            $arr = [];
            $nav = require_once CONFIG_PATH . 'nav.php';
            foreach ($nav as $k => $v) {
                if (isset($right[$k])) {
                    if ($right[$k] == 1) {
                        $arr[] = $v;
                    }
                } else {
                    $arr[] = $v;
                }
            }
            json($arr);
        } else {
            json([]);
        }

    }
}