<?php

namespace app\admin\controller;

use core\lib\Controller;

class ErrorController extends Controller
{
    public function notfound()
    {
        display('404.html');
    }

    public function noright()
    {
        display('noright.html');
    }
}