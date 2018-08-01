<?php
//验证码类
namespace extend;
class Captcha
{
    public $charset = 'abcdefghkmnprstuvwxyzABCDEFGHKMNPRSTUVWXYZ23456789'; //随机因子
    public $code;       //验证码
    public $codelen;     //验证码长度
    public $width;     //宽度
    public $height;     //高度
    public $img;        //图形资源句柄
    public $font;        //指定的字体
    public $fontsize;    //指定字体大小
    public $fontcolor;      //指定字体颜色

    //构造方法初始化
    public function __construct($width = 110, $height = 34, $codelen = 4, $fontsize = 20)
    {
        $this->width = $width;
        $this->height = $height;
        $this->codelen = $codelen;
        $this->fontsize = $fontsize;
        $this->font = PUBLIC_PATH . 'static/admin/fonts/msyhbd.ttf';
    }

    //生成随机码

    public function doimg()
    {
        $this->createBg();
        $this->createCode();
        $this->createLine();
        $this->createFont();
        $_SESSION['verifycode'] = strtolower($this->code);
        $this->outPut();
    }

    //生成背景

    private function createBg()
    {
        $this->img = imagecreatetruecolor($this->width, $this->height);
        $color = imagecolorallocate($this->img, mt_rand(157, 255), mt_rand(157, 255), mt_rand(157, 255));
        imagefilledrectangle($this->img, 0, $this->height, $this->width, 0, $color);
    }

    //生成文字

    private function createCode()
    {
        $_len = strlen($this->charset) - 1;
        for ($i = 0; $i < $this->codelen; $i++) {
            $this->code .= $this->charset[mt_rand(0, $_len)];
        }
    }

    //生成线条、雪花

    private function createLine()
    {
        for ($i = 0; $i < 6; $i++) {
            $color = imagecolorallocate($this->img, mt_rand(0, 156), mt_rand(0, 156), mt_rand(0, 156));
            imageline($this->img, mt_rand(0, $this->width), mt_rand(0, $this->height), mt_rand(0, $this->width), mt_rand(0, $this->height), $color);
        }
        for ($i = 0; $i < 100; $i++) {
            $color = imagecolorallocate($this->img, mt_rand(200, 255), mt_rand(200, 255), mt_rand(200, 255));
            imagestring($this->img, mt_rand(1, 5), mt_rand(0, $this->width), mt_rand(0, $this->height), '*', $color);
        }
    }

    //输出

    private function createFont()
    {
        $_x = $this->width / $this->codelen;
        for ($i = 0; $i < $this->codelen; $i++) {
            $this->fontcolor = imagecolorallocate($this->img, mt_rand(0, 156), mt_rand(0, 156), mt_rand(0, 156));
            imagettftext($this->img, $this->fontsize, mt_rand(-30, 30), $_x * $i + mt_rand(1, 5), $this->height / 1.4, $this->fontcolor, $this->font, $this->code[$i]);
        }
    }

    //对外生成

    private function outPut()
    {
        ob_clean();
        header('Content-type:image/png');
        imagepng($this->img);
        imagedestroy($this->img);
    }
}