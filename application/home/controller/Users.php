<?php

namespace app\home\controller;

use app\common\lib\exception\ApiException;
use think\Controller;
use think\Cookie;
use think\Loader;

class Users extends Outh
{
    /*
     * department 0理工学部，1人文学部，2商学部，3领导层
     */
    public function index(){
        $user = new \app\home\model\Users();
        $uid = Cookie::get('userUid');
        $sql = new \app\home\model\Users();
        $res = $sql->where('uid','eq',$uid)->find();
        if($res['position'] == 3){
            $res = $user->where('department','eq',$res['department'])->field('name,phone,preparedBy,position,department,lasttime,status,uid')->select();
        }elseif($res['position'] == 4) {
            $res = $user->field('name,phone,preparedBy,position,department,lasttime,status,uid')->select();
        }else{
            $this->assign('message',"权限不够，请检查");
            return $this->fetch('/error');
        }
        foreach ($res as $k=>$v){
            $res[$k]['department'] = config('setting.xb')[$v['department']];
            $res[$k]['position'] = config('setting.position')[$v['position']];
            $res[$k]['lasttime'] = date("Y-m-d H:i:s",$v['lasttime']);
            $res[$k]['status'] = config('setting.userStatus')[$v['status']];
        }
        $this->assign('user',$res);
        return $this->fetch("user/index");
    }
    public function getUserInfo(){
        $info = input('get.info');
        $validate = Loader::validate('User');
        if(!$validate->scene('getUserInfo')->check(['info'=>$info])){
            throw new ApiException('50001',-1,400,$validate->getError());
        }
        $res = config('setting.'.$info);
        return show(10000,1,'ok',$res,201);
    }
    public function add(){
        $name = input("post.name");
        $password = input("post.password");
        $phone = input("post.phone");
        $position = input("post.position");
        $xb = input("post.xb");
        $bz = input("post.bz");
        $validate = Loader::validate("User");
        if(!$validate->scene('addUser')->check(['name'=>$name,'phone'=>$phone,'position'=>$position,'xb'=>$xb,'bz'=>$bz,'password'=>$password])){
            throw new ApiException('50001',-1,400,$validate->getError());
        }
        $password = password_hash($password,PASSWORD_BCRYPT,['cost' => 10]);
        $sql = new \app\home\model\Users();
        $check = $sql->where('phone','eq',$phone)->find();
        if($check){
            return show('50004',-1,'添加失败,存在相同的手机号用户，请检查','',200);
        }
        $res = $sql->data(array('name'=>$name,'phone'=>$phone,'position'=>$position,'department'=>$xb,'preparedBy'=>$bz,'pwd'=>$password,'uid'=>uniqid()))->save();
        if($res){
            return show('10001',1,'添加成功','',200);
        }else{
            return show('50004',-1,'添加失败,请刷新再试','',200);
        }
    }
    public function delete(){
        $id = input('delete.uid');
        $usersql = new \app\home\model\Users();
        $res = $usersql->where('uid','eq',$id)->delete();
        if($res){
            return show('10001',1,'删除成功','',200);
        }else{
            return show('50004',-1,'删除失败','',200);
        }
    }
    public function getUser(){
        $uid = input('get.uid');
        $usersql = new \app\home\model\Users();
        $res = $usersql->where('uid','eq',$uid)->find();
        return show(10000,1,'ok',$res,201);
    }
    public function update(){
        $id = input('post.uid');
        $name = input('post.name');
        $phone = input('post.phone');
        $pwd = input('post.pwd');
        $position = input('post.position');
        $xb = input('post.xb');
        $bz = input('post.bz');
        $status = input('post.status');
        if(strlen($pwd)>0){
            $pwd = password_hash($pwd,PASSWORD_BCRYPT,['cost' => 10]);
        }
        $data = array('name'=>$name,'phone'=>$phone,'pwd'=>$pwd,'position'=>$position,'department'=>$xb,'status'=>$status,'preparedBy'=>$bz);
        $validate = Loader::validate("User");
        if(!$validate->scene('updateUser')->check(['M-name'=>$name,'M-phone'=>$phone,'M-position'=>$position,'M-xb'=>$xb,'M-bz'=>$bz,'M-status'=>$status])){
            throw new ApiException('50001',-1,400,$validate->getError());
        }
        foreach ($data as $k=>$v){
            if(strlen($v)<=0){
                unset($data[$k]);
            }
        }
        $sql = new \app\home\model\Users();
        $res = $sql->save($data,['uid'=>$id]);
        if($res){
            return show('10001',1,'更新成功','',200);
        }else{
            return show('50004',-1,'更新失败','',200);
        }
    }

}
