<?php
return [
    "systeminfo" => [
        "title" => "系统基本参数",
        "icon" => "&#xe631;",
        "href" => "/admin/main/system",
        "spread" => false
    ],
    "usercontroller" => [
        "title" => "用户控制",
        "icon" => "&#xe770;",
        "href" => "",
        "spread" => false,
        "children" => [
            [
                "title" => "用户",
                "icon" => "&#xe66f;",
                "href" => "/admin/user/alluser",
                "spread" => false
            ],
            [
                "title" => "角色权限",
                "icon" => "&#xe672;",
                "href" => "/admin/user/allright",
                "spread" => false
            ]
        ]
    ]
];