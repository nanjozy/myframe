<?php

namespace app\admin\model;

use core\lib\Model;

class User extends Model
{
    protected $_table = "users";

    public function checkLogin()
    {
        if (isset($_SESSION['user']) && isset($_SESSION['user']['password']) && isset($_SESSION['user']['username'])) {
            $user = [];
            $user['id'] = $_SESSION['user']['id'];
            $user['password'] = $_SESSION['user']['password'];
            $user['username'] = $_SESSION['user']['username'];
            $user['status'] = 1;
            $re = $this->where($user)->find()->dataArr;
            if ($re) {
                $_SESSION['user'] = $re;
                return true;
            } else {
                unset($_SESSION["user"]);
                return false;
            }
        } else {
            unset($_SESSION["user"]);
            return false;
        }
    }

    public function login()
    {
        $code = strtolower($_POST['code']);
        if ($_SESSION['verifycode'] != $code) {
            json([
                'code' => -1,
                'msg' => '验证码错误'
            ]);
        }
        $user = [
            'username' => $_POST['username'],
            'password' => mdd($_POST['password']),
            'status' => 1
        ];
        $user = $this->where($user)->find();
        if ($user->dataArr) {
            $_SESSION['user'] = $user->dataArr;
            json([
                'code' => 1,
                'msg' => '登录成功'
            ]);
        } else {
            $user2 = [
                'username' => $_POST['username'],
                'password' => mdd($_POST['password'])
            ];
            $user = $this->where($user2)->find();
            if ($user->dataArr) {
                json([
                    'code' => -2,
                    'msg' => '用户被禁用'
                ]);
            } else {
                json([
                    'code' => -2,
                    'msg' => '用户名或者密码错误'
                ]);
            }
        }
    }

    public function logout()
    {
        unset($_SESSION["user"]);
        json(['code' => 1, 'msg' => 'finish']);
    }

    public function changePwd()
    {
        $user = [];
        if (!$_POST['newPwd']) {
            json([
                'code' => -1,
                'msg' => '请输入新密码'
            ]);
        }
        if ($_POST['newPwd'] != $_POST['newPwd2']) {
            json([
                'code' => -1,
                'msg' => '两次密码不一致'
            ]);
        }
        $user["id"] = $_POST['uid'];
        $user["password"] = mdd($_POST['oldPwd']);
        $return = $this->where($user)->find();
        if ($return->dataArr) {
            $re = $this->where($user)->update(['password' => mdd($_POST['newPwd'])]);
            if ($re) {
                if ($user["id"] == $_SESSION["user"]["id"]) {
                    $_SESSION["user"]["password"] = mdd($_POST['newPwd']);
                }
                json([
                    'code' => 1,
                    'num' => $re,
                    'msg' => '修改成功'
                ]);
            } else if ($re === 0) {
                json([
                    'code' => 0,
                    'msg' => '未找到匹配项'
                ]);
            } else {
                json([
                    'code' => -2,
                    'msg' => '修改失败'
                ]);
            }
        }
        json([
            'code' => -1,
            'msg' => '密码错误'
        ]);

    }

    public function addUser()
    {
        $user = [];
        if ($_POST['password'] != $_POST['password2']) {
            json([
                'code' => -1,
                'msg' => '两次密码不一致'
            ]);
        }
        if (!$this->checkUser($_POST["username"])) {
            json([
                'code' => -1,
                'msg' => '用户名格式错误'
            ]);
        }
        $re = $this->checkPass($_POST['password']);
        if ($re == -2) {
            json([
                'code' => -1,
                'msg' => '密码格式错误'
            ]);
        } else if ($re == -1) {
            json([
                'code' => -1,
                'msg' => '密码强度不足'
            ]);
        }
        $re = $this->where("username = '{$_POST['username']}'")->find();
        if ($re->dataArr) {
            json([
                'code' => -1,
                'msg' => '用户名已存在'
            ]);
        }
        unset($_POST['password2']);
        $user = $_POST;
        $user["password"] = mdd($user["password"]);
        $return = $this->create($user);
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

    public function checkUser($user)
    {
        if (!preg_match("/^[a-zA-Z][a-zA-Z0-9]{3,8}$/", $user)) {
            return false;
        } else {
            return true;
        }
    }

    public function checkPass($pass)
    {
        if (!preg_match("/^[a-zA-Z0-9]{4,16}$/", $pass)) {
            return -2;
        } else if (!preg_match("/^.*(?=.*\d)(?=.*[a-zA-Z]).*$/", $pass)) {
            return -1;
        } else {
            return 1;
        }
    }

    public function getUsers()
    {
        $limit = $_POST['limit'];
        $p = $_POST['page'];
        $key = $_POST['key'];
        if ($key) {
            $this->where("users.username like '%{$key}%'");
        }
        $users = $this->where("users.id>0")->field("users.*,userright.name as rightname")->leftjoin("userright ON userright.id=users.rights")->select();
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

    public function edit()
    {
        $user = [];
        if (!$_POST["nickname"]) {
            json([
                'code' => -1,
                'msg' => '请输入昵称'
            ]);
        };
        $user = $_POST;
        unset($user["id"]);
        $return = $this->where("id = {$_POST['id']}")->update($user);
        if ($return) {
            if ($_POST['id'] == $_SESSION["user"]["id"]) {
                $u1 = $this->find($_POST['id'])->dataArr;
                $_SESSION["user"] = $u1;
            }
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
}