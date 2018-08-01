<?php

namespace app\admin\model;

use core\lib\Model;

class Userright extends Model
{
    protected $_table = "userright";

    public function checkright($id, $controller, $fun = null)
    {
        $controller = strtolower($controller);
        $fun = strtolower($fun);
        if (!$id && $id !== 0 && $id !== '0') {
            $id = -1;
        }
        $r = $this->where("status=1")->find($id);
        if (!$r || !$r->dataArr) {
            $r = $this->find()->dataArr;
            foreach ($r as $k => $v) {
                $r[$k] = 0;
            }
        } else {
            $r = $r->dataArr;
        }
        if ($controller == "maincontroller" && $fun == "system") {
            $controller = 'systeminfo';
        }
        if (isset($r[$controller]) && $r[$controller] != 1) {
            return false;
        }
        return true;
    }

    public function getright($id)
    {
        $r = $this->field("systeminfo,usercontroller")->where("status=1")->find($id);
        if ($r) {
            return $r->dataArr;
        }
        return $r;
    }

    public function getright1($id)
    {
        $r = $this->where("id>0")->find($id);
        if (!$r) {
            return $r;
        } else {
            return $r->dataArr;
        }

    }

    public function getright2($id)
    {
        $r = $this->find($id)->dataArr;
        if (!$r) {
            return '未指定';
        } else {
            return $r['name'];
        }

    }

    public function addUser()
    {
        $post = $this->checkform($_POST);
        $re = $this->where("name = '{$post['name']}'")->find();
        if ($re->dataArr) {
            json([
                'code' => -1,
                'msg' => '用户名已存在'
            ]);
        }
        $return = $this->create($post);
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

    public function checkform($post)
    {
        $default1 = [
            'systeminfo' => 0,
            'usercontroller' => 0,
            'msgcontroller' => 0,
            'raidercontroller' => 0,
            'bannercontroller' => 0,
            'contentcontroller' => 0,
            'casecontroller' => 0,
            'classcontroller' => 0,
            'teamcontroller' => 0,
            'abtcontroller' => 0
        ];
        foreach ($post as $k => $v) {
            $default1[$k] = $v;
        }
        return $default1;
    }

    public function getrights()
    {
        $limit = $_POST['limit'];
        $p = $_POST['page'];
        $key = $_POST['key'];
        if ($key) {
            $this->where("name like '%{$key}%'");
        }
        $users = $this->where("id>0")->select();
        $datas = page($users, $p, $limit);
        if (!$datas) {
            $datas = page($users, $p - 1, $limit);
        }
        json([
            'data' => $datas,
            'code' => 0,
            'msg' => '',
            'count' => count($users)
        ]);
    }

    public function switchStatus()
    {
        $id = $_POST['id'];
        if ($id < 1) {
            json([
                'code' => -1,
                'msg' => '无权限',
            ]);
        }
        $status = $this->where("id = $id")->value('status');
        if ($status == 1) {
            if ($this->where("id = $id")->update(['status' => 0])) {
                json([
                    'code' => 0,
                    'msg' => '已禁用',
                ]);
            } else {
                json([
                    'code' => -1,
                    'msg' => '更新失败',
                ]);
            }
        } else {
            if ($this->where("id = $id")->update(['status' => 1])) {
                json([
                    'code' => 1,
                    'msg' => '已启用',
                ]);
            } else {
                json([
                    'code' => -1,
                    'msg' => '更新失败',
                ]);
            }
        }
    }

    public function allrights()
    {
        $re = $this->field("id,name")->where("status=1 and id>0")->select();
        if (!$re) {
            json([
                'code' => -1,
                'msg' => 'error'
            ]);
        }
        json([
            'code' => 1,
            'row' => $re
        ]);
    }

    public function edit()
    {
        $post = $this->checkform($_POST);
        unset($post["id"]);
        $return = $this->where("id = {$_POST['id']}")->update($post);
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

    public function idelete()
    {
        $id = $_POST['id'];
        if ($this->delete($id)) {
            $this->resetStatus($id);
            json([
                'code' => 1,
                'msg' => '删除成功',
            ]);
        } else {
            json([
                'code' => 0,
                'msg' => '删除失败',
            ]);
        }
    }

    public function resetStatus($id)
    {
        $detail = new User();
        $re = $detail->where("rights=$id")->update(["rights" => -1]);
        return $re;
    }

    public function deleteAll()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $ids = $_POST['arrId'];
            $status = $this->delete($ids);
            if ($status) {
                foreach ($ids as $v) {
                    $this->resetStatus($v);
                }
                json([
                    'code' => 1,
                    'msg' => '批量删除成功'
                ]);
            } else if ($status === 0) {
                json([
                    'code' => 0,
                    'msg' => '未找到匹配项'
                ]);
            } else {
                json([
                    'code' => -1,
                    'msg' => '删除数据失败'
                ]);
            }
        }
    }
}