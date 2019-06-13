<?php

namespace app\home\controller;

use app\common\lib\exception\ApiException;
use app\home\model\Token;
use app\home\model\Users;
use Firebase\JWT\JWT;
use think\Controller;
use think\Cookie;
use think\Db;
use think\Session;

class Login extends Controller
{
    public function index()
    {
        if(Cookie::has('userUid')){
            $this->redirect('index/index');
        }
        return $this->fetch();
    }
    public function checklogin(){
        $username = input('post.username');
        $pwd = input('post.pwd');
        $errorNum = Session::get('errorNum');
        if($errorNum > 5){
            Session::delete('checkTo');
        }
        if(Session::get('checkTo') !=  md5($username)) {
            $yzmCode = input('post.yzm');
            $captcha = new \think\captcha\Captcha();
            $result = $captcha->check($yzmCode);
            if ($result == false) {
                throw new ApiException('50100', -1, 400, "验证码错误");
            }
        }
            try {
                $sql = new Users();
                $checkres = $sql->where('phone', 'eq', $username)->find();
            }catch (\Exception $e){
                throw new ApiException('50000',-1,500,"数据库错误，请联系管理员");
            }
            if ($checkres['status'] == config('code.login_status_disable')) {
                throw new ApiException('50200', -1, 400, "用户已禁用，请联系管理员");
            } else if ($checkres['status'] == config('code.login_status_limit')) {
                throw new ApiException('50201', -1, 400, "用户被限制登录");
            }
        try {
            if (!password_verify($pwd, $checkres['pwd'])) {
                Session::set('errorNum', $errorNum + 1);
                $error = array();
                if ($errorNum >= 5) {
                    $error['to'] = 1;
                }
                $sql->where('uid', 'eq', $checkres['uid'])->setInc('errorNum');
                $this->addLoginLog(request()->ip(),-1,$checkres['uid']);
                return show('50202', -1, '用户名或密码错误', $error, 200);
            }
            $nowtime = time();//获取当前的时间
            $token = array(
                "iat" => $nowtime,
                "nbf" => $nowtime + 10,
                "exp" => $nowtime + 21600,
                "data" => [
                    'uid'=>$checkres['uid'],
                ]
            );
            $key = config('setting.login_key');//通过配置文件获取key来加密上面的信息
            $jwt = JWT::encode($token, $key);//生成token
            Cookie::set('userUid', $checkres['uid'], 21600);//cookie过期时间为6个小时，单位为秒
            Cookie::set('token', $jwt, 21600);//cookie过期时间为6个小时，单位为秒
            $sql->where('uid', 'eq', $checkres['uid'])->update(['lasttime' => time(), 'lastip' => request()->ip(), 'errorNum' => 0,'token'=>$jwt]);
            }catch (\Exception $e){
            throw new ApiException('50000',-1,500,"数据库错误，请联系管理员");
        }
        $this->addLoginLog(request()->ip(),1,$checkres['uid']);
            return show('10200','1','登录成功','',200);
//        return show('10200','-1','登陆失败','',500);
    }
    public function checkusername(){
        $username = input('get.username');
        $sql = new Users();
        $res = $sql->where('phone','eq',$username)->field('lastip,errorNum,lasttime,uid')->find();
//        $checkSuccess = Db::table('loginLog')->where('uid','eq',$res['uid'])->where('status','eq',1)->count();
//        $checkError = Db::table('loginLog')->where('uid','eq',$res['uid'])->where('status','eq',-1)->count();
//        $checkCity = Db::table('loginLog')->where('uid','eq',$res['uid'])->where('status','eq',-1)->count();
        Session::set('errorNum', $res['errorNum']);
        if($res['errorNum'] < 5){
            Session::set('checkTo',md5($username));
        }else{
            return json(array('status'=>-1));
        }
        http_response_code(204);
    }
    private function addLoginLog($ip,$status,$uid){
        if(request()->ip() == '0.0.0.0'){
            return false;
        }
        ignore_user_abort(true); // 后台运行，这个只是运行浏览器关闭，并不是直接就中止返回200状态。
        set_time_limit(60);
        $url = "http://ip.taobao.com/service/getIpInfo.php?ip=".$ip;
        $ch = curl_init($url);
        curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
        $res = curl_exec($ch);
        $httpCode = curl_getinfo($ch,CURLINFO_HTTP_CODE);
        if($httpCode != 200){
            return false;
        }
        curl_close($ch);
        $data = array('isp'=>$res['isp'],'local'=>$res['city'],'ip'=>$ip,'status'=>$status,'time'=>date('Y-m-d H-i-s',time()),'uid'=>$uid);
        Db::table('loginLog')->insert($data);
        return $res;
    }


}
