<?php
namespace app\common\lib\exception;
use think\Cookie;
use think\Db;
use think\exception\Handle;
class ApiHandleException extends Handle{
    public $httpCode = 500;
    public $code = 9999;
    public $info = "服务器出错，请重试";
    public function render(\Exception $e)
    {
        if(config('app_debug') == true){
            return parent::render($e);
        }
        if ($e instanceof ApiException) {
            $this->httpCode = $e->httpCode;
            $this->code = $e->code;
            return show($this->code,-1,$e->getMessage(),[],$this->httpCode);
        }else {
            //记录一些服务器错误
            $sql = Db::table('t_errorLog');
            $data = array('errorInfo' => $e->getMessage(), 'controller' => $e->getFile(), 'times' => date('Y-m-d H-i-s', time()), 'uid' => Cookie::get('userUid'),'lines'=>$e->getLine(),'remarks'=>$_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"]);
            $sql->insert($data);
            return show($this->code,-1,$this->info,[],$this->httpCode);
        }
    }
}