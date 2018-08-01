<?php

namespace app\admin\model;

use core\lib\Model;

class Systeminfo extends Model
{
    protected $_table = "systeminfo";

    public function get()
    {
        $return = $this->find();
        return $return->dataArr;
    }

    public function edit()
    {
        $post = $_POST;
        foreach ($post as $k => $v) {
            if (!$v) {
                unset($post[$k]);
            } else {
                if ($k == "ALLOW_UPLOAD_SIZE") {
                    $post[$k] = $v * 1024;
                }
            }
        }
        $return = $this->where("id > 0")->update($post);
        if ($return) {
            json([
                'code' => 1,
                'msg' => 'success'
            ]);
        }
        json([
            'code' => -1,
            'msg' => 'error'
        ]);
    }

    public function safe()
    {
        $status = $this->value('safemode');
        return $status;
    }

    public function safemode()
    {
        $status = $this->value('safemode');
        if ($status == 1) {
            if ($this->where("id >0")->update(['safemode' => 0])) {
                json([
                    'code' => -1,
                    'msg' => '已禁用',
                ]);
            } else {
                json([
                    'code' => 0,
                    'msg' => '更新失败',
                    'status' => $status
                ]);
            }
        } else {
            if ($this->where("id >0")->update(['safemode' => 1])) {
                json([
                    'code' => 1,
                    'msg' => '已启用',
                ]);
            } else {
                json([
                    'code' => 0,
                    'msg' => '更新失败',
                    'status' => $status
                ]);
            }
        }
    }
}