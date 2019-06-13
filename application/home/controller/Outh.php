<?php

namespace app\home\controller;

use app\home\model\Menus;
use app\home\model\System;
use app\home\model\Users;
use think\Controller;
use think\Cookie;

class Outh extends Controller
{
    public function _initialize()
    {
        if (!Cookie::has('userUid')) $this->redirect('Login/index',302);
        $userSql = new Users();
        $check = $userSql->where('uid','eq',Cookie::get('userUid'))->find();
        if($check['token'] != Cookie::get('token')){
            Cookie::delete('token');
            Cookie::delete('userUid');
            $this->redirect('Login/index',302);
        }
        $sql = new System();
        $title = $sql->where('name','eq','systemName')->find();
        $version = $sql->where('name','eq','version')->find();
        $this->assign('title',$title['status']);
        $this->assign('version',$version['status']);
        $this->getuid();
        $this->mens();

    }
    /*
     * 权限管理
     *poslition
     * 0 教师 1 教研室主任 2 教务人员 3 部长 4> 超管
     */
    private function mens(){
        $sql = new Menus();
        $menus = $sql->where('status','neq',30)->select();
        $sql = new Users();
        $res = $sql->where('uid','eq',Cookie::get('userUid'))->find();
        foreach ($menus as $k=>$v){
            if($v['outh'] > $res['position']){
                unset($menus[$k]);
            }
        }
        $menus = array_values($menus);
        $list = array();
        $listHeader = array('id','name','icon','urls','urlType');
        for ($i = 0;$i<count($menus);$i++){
            $id = $menus[$i]['id'];

            foreach ($listHeader as $j){
                $list[$id][$j] = $menus[$i][$j];
            }
            if($menus[$i]['root'] > 0){
                    if(!isset($list[$menus[$i]['root']])) {
                        unset($list[$id]);
                    }else {
                        $list[$menus[$i]['root']]['child'][] = $list[$id];
                        $list[$menus[$i]['root']]['checkChild'] = 1;
                        unset($list[$id]);
                    }
            }

        }
        $list = array_values($list);
        $this->assign('menus',$list);
    }
    private function getuid(){
        if(!Cookie::has('userUid')){
            return false;
        }
        $uid = Cookie::get('userUid');
        $sql = new Users();
        $user = $sql->where('uid','eq',$uid)->find();
        if(!$user){
            return false;
        }
        $name = splitName($user['name']);
        $this->assign('name',$name[0].'老师');

    }
}
