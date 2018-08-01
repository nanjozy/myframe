<?php

namespace app\admin\controller;

use app\admin\model\User;
use app\admin\model\Userright;

class UserController extends AdminBase
{
    private $_db;

    public function alluser()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'GET') {
            $limit = config('LIMIT');
            $user = $_SESSION['user'];
            $user['limit'] = $limit;
            display('allUsers.html', $user);
        }
    }

    public function changePwd()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'GET') {
            if ($_GET['uid'] == $_SESSION['user']['id']) {
                $user = $_SESSION['user'];
            }
            display('changePwd.html', $user);
        } else if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $db = new User();
            $db->changePwd();
        }
    }

    public function getUsers()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $db = new User();
            $db->getUsers();
        }
    }

    public function addUser()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'GET') {
            display('addUser.html');
        } else if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $db = new User();
            $db->addUser();
        }
    }

    public function status()
    {
        $u = new User();
        $u->switchStatus();
    }

    public function edit()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'GET') {
            $db = new User();
            $user = $db->find($_GET['id'])->dataArr;
            if (isset($_GET['self']) && $_GET['self'] == 1) {
                $user['self'] = 1;
            } else {
                $user['self'] = 0;
            }
            display('edit.html', $user);
        } else if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $db = new User();
            $db->edit();
        }
    }


    public function delete()
    {
        $id = $_POST['id'];
        $db = new User();
        if ($db->delete($id)) {
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

    public function deleteAll()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $ids = $_POST['arrId'];
            $db = new User();
            $status = $db->delete($ids);
            if ($status) {
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

    public function allright()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'GET') {
            $limit = config('LIMIT');
            $user = $_SESSION['user'];
            $user['limit'] = $limit;
            display('rightlist.html', $user);
        }
    }

    public function getright()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $db = new Userright();
            $db->getrights();
        }
    }

    public function addright()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'GET') {
            display('addright.html');
        } else if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $db = new Userright();
            $db->addUser();
        }
    }

    public function getright2()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $db = new Userright();
            $db->allrights();
        }
    }

    public function getright1()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $db = new Userright();
            $id = $_POST['id'];
            $re = $db->getright1($id);
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
    }

    public function rightstatus()
    {
        $u = new Userright();
        $u->switchStatus();
    }

    public function editright()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'GET') {
            $re = ["id" => $_GET['id']];
            if (isset($_GET['self']) && $_GET['self'] == 1) {
                $re['self'] = 1;
            } else {
                $re['self'] = 0;
            }
            display('editright.html', $re);
        } else if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $db = new Userright();
            $db->edit();
        }
    }


    public function deleteright()
    {
        $id = $_POST['id'];
        if ($id < 1) {
            json([
                'code' => -1,
                'msg' => '无权限',
            ]);
        }
        $db = new Userright();
        $db->idelete();
    }

    public function deleteAllright()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $ids = $_POST['arrId'];
            if (in_array(0, $ids)) {
                json([
                    'code' => -1,
                    'msg' => '无权限',
                ]);
            }
            $db = new Userright();
            $db->deleteAll();
        }
    }
}