<?php

namespace app\home\controller;

use think\Controller;
use think\Cookie;

class Index extends Outh
{
    protected $local = "主页";
    public function index(){
        return $this->fetch();
    }
    public function loginExit(){
        Cookie::delete('userUid');
    }
}
