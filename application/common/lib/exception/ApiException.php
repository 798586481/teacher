<?php
namespace app\common\lib\exception;
use think\Exception;
class ApiException extends Exception{
    public $code = '';
    public $httpCode = 500;
    public $message = '';
    public $status = -1;
    public function __construct($code = '',$status,$httpCode,$message = '')
    {
        $this->code = $code;
        $this->httpCode = $httpCode;
        $this->message = $message;
        $this->status = $status;
    }
}