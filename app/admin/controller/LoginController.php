<?php

namespace app\admin\controller;

use app\admin\model\User;
use core\lib\Controller;

class LoginController extends Controller
{
    public function __construct()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'GET') {
            if (isset($_SESSION['user']) && isset($_SESSION['user']['password']) && isset($_SESSION['user']['username'])) {
                $user = [];
                $user['password'] = $_SESSION['user']['password'];
                $user['username'] = $_SESSION['user']['username'];
                $u = new User();
                $result = $u->checkLogin($user);
                if ($result) {
                    redirect('admin/index/index');
                }
            }
        }
    }

    public function login()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'GET') {
            display();
        } else if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $u = new User();
            $u->login();
        }
    }

    public function logout()
    {
        $u = new User();
        $u->logout();
    }

    public function doimg()
    {
        doimg();
    }
}