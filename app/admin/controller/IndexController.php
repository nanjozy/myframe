<?php

namespace app\admin\controller;


class IndexController extends AdminBase
{
    public function index()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'GET') {
            display('index.html');
        }
    }

    public function logout()
    {
        session_destroy();
        redirect('admin/login/login');
    }
}