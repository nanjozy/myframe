<?php

namespace core\lib;
class Route
{
    public $module;
    public $controller;
    public $action;

    public function __construct()
    {
        //获取URL的参数部分
        if (isset($_SERVER['REQUEST_URI'])) {
            $url = trim($_SERVER['REQUEST_URI'], '/');
            $url = ltrimStr($url, 'index.php');
        } else {
            $url = '';
        }
        if ($url == '404' || $url == '404.php') {
            jumpto('404.html');
        } else if ($url == '404.html') {
            $this->module = 'admin';
            $this->controller = 'error';
            $this->action = 'notfound';
        } else if ($url == 'admin' || $url == 'admin.php') {
            jumpto('admin.html');
        } else if ($url == 'admin.html') {
            $this->module = 'admin';
            $this->controller = 'index';
            $this->action = 'index';
        } else if ($url == 'index' || $url == 'index.php') {
            jumpto('index.html');
        } else if ($url == 'index.html') {
            $this->module = 'index';
            $this->controller = 'index';
            $this->action = 'index';
        } else if ($url) {
            $mask = -1;
            //base64
            $temp = explode('&', $url, 2);
            $temp = explode('?', ltrim($temp[0], '?'), 2);
            $temp2 = base64_decode($temp[0]);
            if (is_int(stripos($temp2, VAR_MODULE . '=')) || is_int(stripos($temp2, VAR_ACTION . '=')) || is_int(stripos($temp2, VAR_CONTROLLER . '='))) {
                unset($_GET[$temp[0]]);
                $mask = 2;
                $path = $temp2;
                if (isset($temp[1]) && $temp[1]) {
                    $add = '&' . $temp[1];
                    $path .= $add;
                }
            }
            //伪静态
            if ($mask == -1) {

                $temp = explode('?', $url, 2);
                $temp2 = $temp[0];
                unset($_GET[str_replace('.', '_', $temp2)]);
                if ($temp2 && stripos($temp2, 'statics/') === 0) {
                    $temp3 = str_replace_last('_', '.', $temp2);
                    $path = U('publics/static/gets/' . ltrimStr($temp3, 'statics/'), [], 0);
                } else if ($temp2 && $mask == -1) {
                    $path = U(rtrimStr(rtrimStr($temp2, '.php'), '.html'), [], 0);
                }
                if ($temp2 && isset($temp[1]) && $temp[1] && is_int(stripos($temp[1], '='))) {
                    $add = '&' . $temp[1];
                    $path .= $add;
                }
            }
            if (isset($path)) {
                $path = ltrim($path, '?');
            } else {
                $path = ltrim($url, '?');
            }
            if ($path && $path != '/') {
                $patharr = explode('&', ltrim(trim($path, '/'), '?'));
                if (isset($patharr[0]) && is_int(stripos($patharr[0], VAR_MODULE . '='))) {
                    $this->module = ltrimStr($patharr[0], VAR_MODULE . '=');
                    array_splice($patharr, 0, 1);
                } else {
                    $this->module = DEFAULT_MODULE;
                }
                if (isset($patharr[0]) && is_int(stripos($patharr[0], VAR_CONTROLLER . '='))) {
                    $this->controller = ltrimStr($patharr[0], VAR_CONTROLLER . '=');
                    array_splice($patharr, 0, 1);
                } else {
                    $this->controller = DEFAULT_CONTROLLER;
                }
                if (isset($patharr[0]) && is_int(stripos($patharr[0], VAR_ACTION . '='))) {
                    $this->action = ltrimStr($patharr[0], VAR_ACTION . '=');
                    array_splice($patharr, 0, 1);
                } else {
                    $this->action = DEFAULT_ACTION;
                }
                //url多余部分转换成GET参数
                if (strstr($this->action, '-')) {
                    $temph = explode('-', $this->action, 2);
                    $this->action = $temph[0];
                    $_GET['id'] = $temph[1];
                }
                $count = count($patharr);
                for ($i = 0; $i < $count; $i++) {
                    $temp = explode('=', $patharr[$i]);
                    if (isset($temp[0]) && (!is_null($temp[0]))) {
                        $_GET[$temp[0]] = $temp[1];
                    }
                }
            }
        } else {
            $this->module = DEFAULT_MODULE;
            $this->controller = DEFAULT_CONTROLLER;
            $this->action = DEFAULT_ACTION;
        }
        $this->controller = ucfirst(strtolower($this->controller));
    }
}