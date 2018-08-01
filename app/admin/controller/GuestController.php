<?php

namespace app\admin\controller;

use app\admin\model\User;

class GuestController extends AdminBase
{
    public function changePwd()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'GET') {
            $user = $_SESSION['user'];
            display('changePwd.html', $user);
        } else if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $_POST['uid'] = $_SESSION['user']['id'];
            $db = new User();
            $db->changePwd();
        }
    }


    public function edit()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'GET') {
            display('edit.html', $_SESSION['user']);
        } else if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $_POST['id'] = $_SESSION['user']['id'];
            unset($_POST['rights']);
            $db = new User();
            $db->edit();
        }
    }
}