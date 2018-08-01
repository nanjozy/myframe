<?php

namespace app\index\controller;

use core\lib\Controller;

class IndexController extends Controller
{
    public function index()
    {
        return $this->fetch();
    }
}