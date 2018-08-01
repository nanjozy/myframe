<?php

namespace core\lib;

class Upload
{
    public function uploads($name)
    {
        $uni = uniqid();
        $count = count($_FILES[$name]['name']);
        $return = [];
        $files = [];
        for ($i = 0; $i < $count; $i++) {
            $temp = $this->checkUp($name, $i);
            if ($temp['code'] == 0) {
                return ['code' => 0, 'msg' => $temp['msg']];
            }
            $files[$i] = $temp['file'];
        }
        for ($i = 0; $i < $count; $i++) {
            $return['file_path'][$i] = $this->upload($files, $i, $uni);
        }
        $return['code'] = 1;
        return $return;
    }

    public function checkUp($name, $n = 0)
    {
        $file = [];
        if (is_string($name)) {
            $file = $this->getFile($name, $n);
        } else if (is_array($name)) {
            $file = $name[$n];
        }
        $ext = strrchr($file['name'], '.');
        if (!in_array($ext, config('ALLOW_UPLOAD_TYPE'))) {
            return ['code' => 0, 'msg' => '上传文件类型错误'];
        }
        if ($file['size'] > config('ALLOW_UPLOAD_SIZE')) {
            return ['code' => 0, 'msg' => '上传文件超出限制'];
        }
        return ['code' => 1, 'file' => $file];
    }

    public function getFile($name, $n = 0)
    {
        $file = [];
        foreach ($_FILES[$name] as $k => $v) {
            $file[$k] = $v[$n];
        }
        return $file;
    }

    public function upload($name, $n = 0, $uni = NULL)
    {
        $file = [];
        if (is_string($name)) {
            $file = $this->getFile($name, $n);
        } else if (is_array($name)) {
            $file = $name[$n];
        }
        $ext = strrchr($file['name'], '.');
        $date = date('Y-m-d');
        $path = PUBLIC_PATH . "uploads/$date/";
        if (!is_dir($path)) {
            mkdir($path, 0777, true);
        }
        if (!$uni) {
            $uni = uniqid();
        }
        $filename = "$uni" . "_$n" . $ext;
        $real_path = $path . $filename;
        move_uploaded_file($file['tmp_name'], $real_path);
        $file_path = __PUBLIC__ . "/uploads/$date/$filename";
        return $file_path;
    }
}

