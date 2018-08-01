<?php

use app\admin\model\Systeminfo;
use core\lib\Controller;
use core\lib\Db;
use core\lib\Upload;
use extend\Captcha;

function set_default()
{
    $config = require_once CONFIG_PATH . 'config.php';
    config($config);
    $sysinfo = getSql('systeminfo');
    foreach ($sysinfo as $k => $v) {
        if ($v || $v === '0' || $v === 0) {
            config($k, $v);
            $config[$k] = $v;
        }
    }

    //开启调试模式
    define('DEBUG', isset($config['DEBUG']) ? $config['DEBUG'] : true);
    //开启地址栏转码
    define('URL_MASK', isset($config['URL_MASK']) ? $config['URL_MASK'] : 2);
    //路由参数
    define('VAR_MODULE', isset($config['VAR_MODULE']) ? $config['VAR_MODULE'] : 'm');
    define('VAR_CONTROLLER', isset($config['VAR_CONTROLLER']) ? $config['VAR_CONTROLLER'] : 'c');
    define('VAR_ACTION', isset($config['VAR_ACTION']) ? $config['VAR_ACTION'] : 'f');
    define('DEFAULT_MODULE', isset($config['DEFAULT_MODULE']) ? $config['DEFAULT_MODULE'] : 'index');
    define('DEFAULT_CONTROLLER', isset($config['DEFAULT_CONTROLLER']) ? $config['DEFAULT_CONTROLLER'] : 'index');
    define('DEFAULT_ACTION', isset($config['DEFAULT_ACTION']) ? $config['DEFAULT_ACTION'] : 'index');
    define('DEFAULT_TIME_ZONE_SET', isset($config['DEFAULT_TIME_ZONE_SET']) ? $config['DEFAULT_TIME_ZONE_SET'] : 'RPC');
}

function safem()
{
    $db = new Systeminfo();
    return $db->safe();
}

//config
function config($var = null, $value = null)
{
    static $config = [];

    if (is_array($var)) {
        $config = array_merge($config, array_change_key_case($var, CASE_UPPER));
    } else if (is_string($var)) {
        $var = strtoupper($var);
        if (is_null($value)) {
            if (isset($config[$var])) {
                return $config[$var];
            } else {
                return false;
            }
        } else {
            $config[strtoupper($var)] = $value;
        }
    } else if (is_null($var) && is_null($value)) {
        return $config;
    }
}

//controller
function U($dist = '', $params = [], $x = URL_MASK)
{
    $c = new Controller();
    $result = $c->U($dist, $params, $x);
    return $result;
}

function jumpto($dist)
{
    $c = new Controller();
    $c->jumpto($dist);
}

function redirect($dist = '', $params = [])
{
    $c = new Controller();
    $c->redirect($dist, $params);
}

function display($file = null, $var = [])
{
    $c = new Controller();
    $c->display($file, $var);
}

function fetch($file = null, $var = [])
{
    $c = new Controller();
    $c->fetch($file, $var);
}

//db函数
function query($sql, $n = false)
{
    $db = Db::getInstance();
    $result = $db->query($sql, $n);
    return $result;
}

function getSql($tabName, $id = NULL)
{
    $db = Db::getInstance();
    $result = $db->getSql($tabName, $id);
    return $result;
}

function getSqls($tabName, $id = NULL)
{
    $db = Db::getInstance();
    $results = $db->getSqls($tabName, $id);
    return $results;
}

function inSql($tabName, $message, $n = 0)
{
    $db = Db::getInstance();
    $result = $db->inSql($tabName, $message, $n);
    return $result;
}

//上传
function upload($name)
{
    $upload = new Upload();
    $return = $upload->uploads($name);
    return $return;
}

function copyArrVal($a, $b, $n = 0)
{
    $temp = $a;
    if (is_array($b) && is_array($b)) {
        foreach ($b as $k => $v) {
            if (array_key_exists($k, $temp) || $n == 1) {
                $temp[$k] = $v;
            }
        }
    }
    return $temp;
}

//验证码
function doimg($width = 110, $height = 34, $codelen = 4, $fontsize = 20)
{
    $ca = new Captcha($width, $height, $codelen, $fontsize);
    $ca->doimg();
}

//json
function json($data, $n = true)
{
    echo json_encode($data);
    if ($n) {
        die;
    }
}

//string
function ltrimStr($str, $s)
{
    if ($str && $s) {
        $re = explode($s, $str, 2);
        if (!$re[0]) {
            array_splice($re, 0, 1);
            if (isset($re[0])) {
                $re = $re[0];
            } else {
                $re = '';
            }
        } else {
            $re = $str;
        }
        return $re;
    } else {
        return $str;
    }
}

function rtrimStr($str, $s)
{
    if ($str && $s) {
        $strlen = strlen($str);
        $slen = strlen($s);
        $t = $strlen - $slen;
        if (strripos($str, $s) === $t) {
            $str = substr($str, 0, $t);
        }
    }
    return $str;
}

function trimStr($str, $s)
{
    $re = ltrimStr($str, $s);
    $re = rtrimStr($re, $s);
    return $re;
}

function str_replace_last($s, $r, $arr, $n = 1)
{
    $ar = explode($s, $arr);
    $j = -1;
    $return = '';
    for ($i = 0; $i < count($ar) - $n; $i++) {
        if ($i > 0) {
            $return .= $s;
        }
        $return .= $ar[$i];
        $j = $i;
    }
    for ($k = $j + 1; $k < count($ar); $k++) {
        if ($k > 0) {
            $return .= $r;
        }
        $return .= $ar[$k];
    }
    return $return;
}

function page($datas = [], $page = 1, $limit = null)
{
    if (is_null($limit)) {
        $limit = config('LIMIT');
    }
    $arr = [];
    for ($i = ($page - 1) * $limit; $i < ($page * $limit); $i++) {
        if (isset($datas[$i])) {
            $arr[($i - ($page - 1) * $limit)] = $datas[$i];
        } else {
            break;
        }
    }
    return $arr;
}

function jsonout($a, $n = true)
{
    echo json_encode($a);
    if ($n) {
        die;
    }
}

function mdd($a)
{
    return md5($a);
}

function getFileName()
{
    $arr = date('Y-m-d') . '/' . uniqid();
    return $arr;
}

function arrBr($arr)
{
    if (is_array($arr)) {
        foreach ($arr as $k => $v) {
            $arr[$k] = arrBr($v);
        }
    } else if (is_string($arr)) {
        $arr = nl2br($arr);
    }
    return $arr;
}