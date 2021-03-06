<?php

namespace Qiniu\Tests;

use Qiniu\Config;
use Qiniu\Storage\FormUploader;
use Qiniu\Storage\UploadManager;

class FormUpTest extends \PHPUnit_Framework_TestCase
{
    protected $bucketName;
    protected $auth;
    protected $cfg;

    public function testData()
    {
        $token = $this->auth->uploadToken($this->bucketName);
        list($ret, $error) = FormUploader::put($token, 'formput', 'hello world', $this->cfg, null, 'text/plain', null);
        $this->assertNull($error);
        $this->assertNotNull($ret['hash']);
    }

    public function testData2()
    {
        $upManager = new UploadManager();
        $token = $this->auth->uploadToken($this->bucketName);
        list($ret, $error) = $upManager->put($token, 'formput', 'hello world', null, 'text/plain', null);
        $this->assertNull($error);
        $this->assertNotNull($ret['hash']);
    }

    public function testFile()
    {
        $key = 'formPutFile';
        $token = $this->auth->uploadToken($this->bucketName, $key);
        list($ret, $error) = FormUploader::putFile($token, $key, __file__, $this->cfg, null, 'text/plain', null);
        $this->assertNull($error);
        $this->assertNotNull($ret['hash']);
    }

    public function testFile2()
    {
        $key = 'formPutFile';
        $token = $this->auth->uploadToken($this->bucketName, $key);
        $upManager = new UploadManager();
        list($ret, $error) = $upManager->putFile($token, $key, __file__, null, 'text/plain', null);
        $this->assertNull($error);
        $this->assertNotNull($ret['hash']);
    }

    protected function setUp()
    {
        global $bucketName;
        $this->bucketName = $bucketName;

        global $testAuth;
        $this->auth = $testAuth;
        $this->cfg = new Config();
    }
}
