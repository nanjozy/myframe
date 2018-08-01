<?php

namespace app\admin\controller;

use app\admin\model\User;
use app\admin\model\Userright;
use core\lib\Controller;

class AdminBase extends Controller
{
    public function __construct()

    {
        if (isset($_SESSION['user']) && isset($_SESSION['user']['password']) && isset($_SESSION['user']['username'])) {
            $user = [];
            $user['id'] = $_SESSION['user']['id'];
            $user['password'] = $_SESSION['user']['password'];
            $user['username'] = $_SESSION['user']['username'];
            $u = new User();
            $result = $u->checkLogin($user);
            if (!$result) {
                redirect('admin/login/login');
            } else {
                $db = new Userright();
                $right = $db->checkright($_SESSION['user']['rights'], CONTROLLER_CLASS, ACTION_NAME);
                if (!$right) {
                    redirect('admin/error/noright');
                }
            }
        } else {
            redirect('admin/login/login');
        }
    }
}