<?php
return [
    //开启调试模式
    'DEBUG' => true,
    //开启地址栏转码
    'URL_MASK' => 2,
    //路由参数
    'VAR_MODULE' => 'm',
    'VAR_CONTROLLER' => 'c',
    'VAR_ACTION' => 'f',
    'DEFAULT_MODULE' => 'index',
    'DEFAULT_CONTROLLER' => 'index',
    'DEFAULT_ACTION' => 'index',
    'DEFAULT_TIME_ZONE_SET' => 'PRC',
    //DB
    'DB_SERVERNAME' => 'localhost',
    'DB_USERNAME' => 'framedemo',
    'DB_PASSWORD' => '###',
    'DB_NAME' => 'framedemo',
    'ALLOW_UPLOAD_TYPE' => 'image/*',
    'ALLOW_UPLOAD_SIZE' => 1024 * 1024 * 2,
    //后台信息
    'ADMIN_VERSION' => 'ALPHA 1.0',
    'ADMIN_AUTHOR' => 'Nanjozy',
    'LIMIT' => 5,
    //CDN
    "__VENDOR__" => ''/*"vendorurl"*/,
    "__IMG__" => "imageurl",
    "__UPLOAD__" => "uploadurl",
    'ACCESSKEY' => '七牛accesskey',
    'SECRETKEY' => '七牛secretkey'
];