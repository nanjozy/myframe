<?php

namespace core\lib;

class Controller
{

    public function display($file = null, $var = [])
    {
        $fl = debug_backtrace();
        $functionname = $fl[1]['function'] == "display" ? $fl[2]['function'] : $fl[1]['function'];
        $file = $file ? $file : $functionname . '.html';
        $smarty = new \Smarty();
        if (is_int(stripos($file, '/'))) {
            $path = VIEW_PATH . trim(rtrimStr($file, trim(strrchr($file, '/'), '/')), '/') . '/';
        } else {
            $path = VIEW_PATH;
        }
        $smarty->setTemplateDir($path);
        if ($var && is_array($var)) {
            foreach ($var as $k => $v) {
                $smarty->assign($k, $v);
            }
        }
        $smarty->left_delimiter = '{?';
        $smarty->right_delimiter = '?}';
        $smarty->display($file);
    }

    public function fetch($file = null, $var = [])
    {
        $fl = debug_backtrace();
        $functionname = $fl[1]['function'] == "fetch" ? $fl[2]['function'] : $fl[1]['function'];
        $file = $file ? $file : $functionname . '.html';
        $smarty = new \Smarty();
        if (is_int(stripos($file, '/'))) {
            $path = VIEW_PATH . trim(rtrimStr($file, trim(strrchr($file, '/'), '/')), '/') . '/';
        } else {
            $path = VIEW_PATH;
        }
        $smarty->setTemplateDir($path);
        if ($var && is_array($var)) {
            foreach ($var as $k => $v) {
                $smarty->assign($k, $v);
            }
        }
        $smarty->left_delimiter = '{?';
        $smarty->right_delimiter = '?}';
        return $smarty->fetch($file);
    }

    public function redirect($dist = '', $params = [])
    {
        if ($dist == '404') {
            echo "<script>window.location.href='/404.html';</script>";
            die;
        }
        $path = $this->U($dist, $params);
        echo "<script>window.location.href='$path';</script>";
        die;
    }

    public function U($dist = '', $params = [], $x = URL_MASK)
    {
        $par2 = '';
        if ($dist) {
            $temp0 = explode('?', $dist, 2);
            $arr = explode('/', $temp0[0]);
            if (isset($temp0[1]) && $temp0[1]) {
                $par2 = $temp0[1];
            }
        } else {
            $arr = [];
        }
        $count = count($arr);
        $temp = [];
        for ($i = 0; $i < (3 - $count); $i++) {
            switch ($i) {
                case 0:
                    $temp[$i] = MODULE_NAME;
                    break;
                case 1:
                    $temp[$i] = CONTROLLER_NAME;
                    break;
                case 2:
                    $temp[$i] = ACTION_NAME;
            }
        }
        $arr = array_merge($temp, $arr);
        $module = trim($arr[0]) ? $arr[0] : MODULE_NAME;
        $controller = trim($arr[1]) ? $arr[1] : CONTROLLER_NAME;
        $action = trim($arr[2]) ? $arr[2] : ACTION_NAME;
        $count = count($arr);
        if ($count > 3) {
            $arr2 = '';
            for ($i = 3; $i < $count; $i++) {
                $arr2 .= $arr[$i] . '/';
            }
            $_GET['URL_PATH'] = rtrim($arr2, "/");
        }
        if (is_array($params) && $params) {
            $param_str = '';
            if (count($params) != 0) {
                foreach ($params as $k => $v) {
                    $param_str .= '&' . $k . '=' . $v;
                }
            }
        } else if (is_string($params)) {
            $param_str = '&' . trim(trim($params), '/\\?=&');
        } else if (is_bool($params)) {
            if ($params) {
                $param_str = '&t=' . time();
            }
        } else {
            $param_str = '';
        }
        if ($par2) {
            if ($param_str) {
                $param_str .= "&$par2";
            } else {
                $param_str = "$par2";
            }
        }
        if ($x == 2) {
            $path = "/$module/$controller/$action" . ".html";
            if ($param_str) {
                $path .= '?' . trim($param_str, '&');
            }
        } else {
            $path = '?' . config('VAR_MODULE') . '=' . $module . '&' . config('VAR_CONTROLLER') . '=' . $controller . '&' . config('VAR_ACTION') . '=' . $action;
            if ($param_str) {
                $path .= '&' . trim($param_str, '&');
            }
        }
        if ($x == 1) {
            $path = base64_encode($path);
        }
        if ($x === 0) {
            return $path;
        } else {
            return '/' . ltrim($path, '/');
        }
    }

    public function jumpto($dist)
    {
        echo "<script>window.location.href='/$dist';</script>";
        die;
    }

}