<?php

namespace app\home\controller;

use app\home\model\Bkllk;
use app\home\model\Classes;
use app\home\model\Course;
use app\home\model\Experiment;
use app\home\model\Users;
use app\home\model\Varible;
use app\home\model\Zdsy;
use think\Cookie;
use think\Loader;
use app\common\lib\exception\ApiException;

class Teacher extends Outh
{
    protected $outh = 1;
    protected $beforeActionList = [
        'reviewOuth'  =>  ['only'=>'reject,pass,review'],
    ];
    public function write(){
        $sql = new Varible();
        $kcxs = $sql->where('names','eq','kcxs')->select();
        $this->assign('kcxs',$kcxs);
        return $this->fetch();
    }

    public function views(){
        $sql = new Varible();
        $kcxs = $sql->where('names','eq','kcxs')->select();
        $user = new Users();
        $check = $user->where('uid','eq',Cookie::get('userUid'))->find();
        if($check['position'] == 3){
            $bkllk = bkllk::all();
            $ztfdk = db("ztfdk")->select();
            foreach ($bkllk as $k=>$v){
                $checkTo = $user->where('uid','eq',$v['uid'])->find();
                if($checkTo['department'] != $check['department']){
                    unset($bkllk[$k]);
                }
            }
            foreach ($ztfdk as $k1=>$v1){
                $checkTo = $user->where('uid','eq',$v1['uid'])->find();
                if($checkTo['department'] != $check['department']){
                    unset($ztfdk[$k1]);
                }
            }
        }elseif($check['position'] == 4){
            $bkllk = bkllk::all();
            $ztfdk = db("ztfdk")->select();
        }else{
            $bkllk = bkllk::all(['uid'=>Cookie::get('userUid')]);
            $ztfdk = db("ztfdk")->where('uid','eq',Cookie::get('userUid'))->select();
        }

        $this->assign('bkllk',$bkllk);
        $this->assign('ztfdk',$ztfdk);
        $this->assign('kcxs',$kcxs);
        return $this->fetch();
    }
    public function getExperiment($id = ''){
        $sql = new Zdsy();
        $uid = Cookie::get('userUid');
        $data = array();
        $experiment = Experiment::with('experiment')->where('uid','eq',$uid)->select();
        $res = collection($experiment)->toArray();
        foreach ($res as $v){
            foreach ($v['experiment'] as $v2){
                if($id) if($v2['pivot']['id'] != $id) continue;
                if($v2['pivot']['uid'] != $uid) continue;//排除关联表不是这个老师的课
                $check = $sql->where('experimentClassesId','eq',$v2['pivot']['id'])->find();
                if($check) continue;
                $x = $this->getXyxs($v2['peoples'],'d');
                $data[] = array('name'=>$v2['name'].$v['name'],'people'=>$v2['peoples'],'experimentId'=>$v['id'],'ids'=>$v2['pivot']['id'],'jhxs'=>$v['jhxs'],'sksj'=>$v['sksj'],'X'=>$x);
            }
        }
        return show('10002',1,'success',$data,200);

    }
    private function getXyxs($people = 0,$type = ''){
        //计算效益系数
        $sql3 = new varible();
        $xyxs = $sql3->where('category','eq',$type)->where('names','eq','xyxs')->order('number')->select();
        for ($i=0;$i<count($xyxs);$i++){
            if($xyxs[$i]['number'] >= $people){
                $x = $xyxs[$i]['values'];
                break;
            }
        }
        return $x;

    }
    public function getCourse($id = 0){
        $sql4 = new Bkllk();
        $data = array();
        $uid = Cookie::get('userUid');
        $res = Course::with('course')->where('uid',$uid)->select();
        $res = collection($res)->toArray();
        foreach ($res as $v){
            foreach ($v['course'] as $v2){
                $mtgzl = 3; $jkgzl = 1;
                if($id) if($v2['pivot']['id'] != $id) continue;
                if($v2['pivot']['uid'] != $uid) continue;//排除关联表不是这个老师的课
                $x = $this->getXyxs($v2['peoples'],'b');
                $check = $sql4->where('classesCourseId','eq',$v2['pivot']['id'])->find();
                if($check) continue;
                $check = Bkllk::get(['courseId'=>$v['id']]);
                if($check) {
                    $check = $check->toArray();
                    if ($check['mtgzl'] == 3) $mtgzl = 1;
                    if ($check['jkgzl'] == 1) $jkgzl = 0;
                }
                $data[] = array('type'=>$v['nature'],'name'=>$v['name'].$v2['name'],'people'=>$v2['peoples'],'ids'=>$v2['pivot']['id'],'courseId'=>$v['id'],'jhxs'=>$v['jhxs'],'sksj'=>$v['sksj'],'X'=>$x,'mtgzl'=>$mtgzl,'jkgzl'=>$jkgzl);
            }
        }
        if($id) return json_encode($data);
        return json($data);
    }
    public function saveCourse(){
        if(request()->isPost()) {
            $id = input('post.id');
            $validate = Loader::validate('Teacher');
            if (!$validate->scene('saveCourse')->check(input('post.'))){
                return json(array('code' => '50001', 'status' => -1, 'info' => $validate->getError()));
            }
            $kcxs = (float)input('post.K');
            $mtgzl = (int)input('post.M');
            $jkgzl = (int)input('post.J');
            $cfbxs = (float)input('post.C');
            $course = json_decode($this->getCourse($id),true);
            if (!$course) {
                throw new ApiException(50003, -1, 400, '请不要重复插入');
            }
            $xyxs = (float)$course[0]['X'];
            $jhxs = (int)$course[0]['jhxs'];
            $sum = array('id'=>$id,'kcxs'=>$kcxs,'mtgzl'=>$mtgzl,'jkgzl'=>$jkgzl,'type'=>'add','jhxs'=>$jhxs,'xyxs'=>$xyxs);
            $bzxs = $this->bkllkSum($sum);
            $data = array('kcmc' => $course[0]['name'], 'sksj' => $course[0]['sksj'], 'jhxs' => $jhxs, 'kcxs' => $kcxs, 'xyxs' => $xyxs, 'mtgzl' => $mtgzl, 'jkgzl' => $jkgzl, 'cfbxs' => $cfbxs, 'bzxs' => $bzxs, 'uid' => Cookie::get('userUid'),'classesCourseId'=>$id,'courseId'=>$course[0]['courseId']);
            $sql = new bkllk();
            $check = $sql->where('classesCourseId', 'eq', $id)->count();
            if ($check > 0) throw new ApiException(50003, -1, 400, '请不要重复插入');
            $res = $sql->save($data);
            if ($res) {
                return show('10000', 1, '保存成功', '', 201);
            } else {
                throw new ApiException(50003, -1, 400, '保存失败，请刷新重试');
            }
        }else{
            throw new ApiException(50001, -1, 400, '访问参数不对');
        }
    }
    public function savePhoto(){
        $X = input('post.S');
        if(!is_numeric($X)){
            return json(array('code' => '50001', 'status' => -1, 'info' => '填写信息有误，请重新填写'));
        }
        $sum = $X * 0.4;
        $res = db('ztfdk')->data(array('x'=>$X,'sum'=>$sum,'status'=>'10','uid'=>Cookie::get('userUid')))->insert();
        if($res){
            return show('10000',1,'操作成功','',200);
        }else{
            return show('50003',-1,'操作失败,请刷新再试','',400);
        }
    }
    private function bkllkSum($sum){
        //计算本科理论课工作量
        $id = $sum['id'];
        if($sum['type'] == 'update'){
            $course = Bkllk::get(['classesCourseId'=>$id]);
            $course = $course->toArray();
            $check = db('Bkllk')->where('courseId','eq',$course['courseId'])->where('id','<>',$course['id'])->select();
           foreach ($check as $k=>$v){
               if($v['mtgzl'] == 3 && $sum['mtgzl'] ==3) throw new ApiException('50001',-1,400,"已存在同名课程的命题工作量为命题，请检查");
               if($v['jkgzl'] == 1 && $sum['jkgzl'] ==1) throw new ApiException('50001',-1,400,"已存在同名课程的监考工作量为监考，请检查");
           }
            $xyxs = (float)$course['xyxs'];
            $jhxs = (int)$course['jhxs'];
        }else{
            $xyxs = $sum['xyxs'];
            $jhxs = $sum['jhxs'];
        }
        $cfbxs = config('setting.bkllk_cfbxs');
        $bzxs = ((6 * $xyxs) + ($jhxs * $xyxs * $sum['kcxs'])) * $cfbxs + $sum['mtgzl'] + $sum['jkgzl'];
        return $bzxs;

    }
    public function delete(){
        if(request()->isDelete()){
            $type = input('delete.type');//获取操作的表
            $id = input('delete.id');
            $validate = Loader::validate('Teacher');
            if(!$validate->scene('delete')->check(['id'=>$id,'type'=>$type])){
                throw new ApiException('50001',-1,400,$validate->getError());
            }
            try {
                $res = db($type)->where('uid','eq',Cookie::get('userUid'))->where('id',$id)->delete();
            }catch (\Exception $e){
                throw new ApiException(50000, -1, 500, '数据库错误');
            }
            if($res){
                return show('10001',1,'删除成功','',200);
            }else{
                return show('50004',-1,'删除失败,请刷新再试','',400);
            }
        }else{
            throw new ApiException(50001, -1, 400, '访问参数不对');
        }
    }
    public function updates(){
        $validate = Loader::validate('Teacher');
        if(!$validate->scene('update')->check(input('post.'))){
            throw new ApiException('50001',-1,400,$validate->getError());
        }
        $check = db(input('post.type'))->where('id','eq',input('post.id'))->find();
        if($check['status'] > 10){
            throw new ApiException('50001',-1,400,'该记录已审核，不能修改，请联系教务人员');
        }
        $sumName = config('setting.sumName_'.input('post.type'));
        $sumValue = config('setting.sumValue_'.input('post.type'));
        foreach ($sumName as $k1=>$v1){
            $sumArray[$v1] = input('post.'.$sumValue[$k1]);
        }
        $sumArray['type'] = 'update';
        $func = input('post.type').'Sum';
        $sum = $this->$func($sumArray);
        $request = input('post.');
        $request['sum'] = $sum;
        $saveName = config('setting.saveName_'.input('post.type'));
        $saveValue = config('setting.saveValue_'.input('post.type'));
        foreach ($saveValue as $k=>$v){
            $data[$v] = $request[$saveName[$k]];
        }
        try{
            db(input('post.type'))->where('uid','eq',Cookie::get('userUid'))->where('id','eq',input('post.id'))->setField('status',10);
            $res = db(input('post.type'))->where('uid','eq',Cookie::get('userUid'))->where('id','eq',input('post.id'))->update($data);
        }catch (\Exception $e){
            throw new ApiException(50000, -1, 500, '数据库错误');
        }
        if($res){
            return show('10000',1,'更新成功','',200);
        }else{
            return show('50003',-1,'更新失败,请刷新再试','',400);
        }
    }
    public function getDepartment(){
        $dep = new Varible();
        $res = $dep->where('category','eq','s')->where('names','eq','department')->field('values,remarks')->select();
        return json($res);
    }
    public function getTeacher(){
        $id = input('get.id');
        if(!is_numeric($id)){
            throw new ApiException('50001',-1,400,'缺少关键参数');
        }
        $tea = new Users();
        $res = $tea->where('department','eq',$id)->where('status','eq',10)->field('name,uid')->select();
        return json($res);
    }
    /*
     * 教师工作量审核
     */
    public function review(){
        if($this->outh == -1){
            $this->assign('message',"权限不够，请检查");
            return $this->fetch('/error');
        }

        $uid = Cookie::get('userUid');
        $sql = new \app\home\model\Users();
        $res = $sql->where('uid','eq',$uid)->find();
        $bkllk = Bkllk::all();
        if($res['position'] == 3){
            foreach ($bkllk as $k=>$v){
                $check = $sql->where('uid','eq',$v['uid'])->find();
                if($check['department'] != $res['department']){
                    unset($bkllk[$k]);
                }
            }
        }else{
            $this->assign('message',"权限不够，请检查");
            return $this->fetch('/error');
        }

        $user = new Users();
        foreach ($bkllk as $k=>$v){
            if($v['mtgzl'] == 3){
                $bkllk[$k]['mtgzl'] = '命题';
            }elseif ($v['mtgzl'] == 1){
                $bkllk[$k]['mtgzl'] = '使用题库';
            }
            if($v['jkgzl'] == 1){
                $bkllk[$k]['jkgzl'] = '监考';
            }elseif ($v['jkgzl'] == 0){
                $bkllk[$k]['jkgzl'] = '不监考';
            }
            $res = $user->where('uid','eq',$v['uid'])->find();
            $bkllk[$k]['name'] = $res['name'];
            $bkllk[$k]['xb'] = config('setting.xb')[$res['department']];
        }
        $this->assign('bkllk',$bkllk);

        return $this->fetch();
    }
    public function pass(){
        $id = input('post.id');
        $name = input('post.name');
        $validate = Loader::validate("Teacher");
        if(!$validate->scene('pass')->check(['id'=>$id,'type'=>$name])){
            throw new ApiException('50001',-1,400,$validate->getError());
        }

        try{
            $res = db($name)->where('id','eq',$id)->setField('status',30);
        }catch (\Exception $e){
            throw new ApiException(50000, -1, 500, '数据库错误');
        }
        if($res){
            return show('10000',1,'操作成功','',200);
        }else{
            return show('50003',-1,'操作失败,请刷新再试','',400);
        }

    }
    public function reject(){
        $id = input('post.id');
        $name = input('post.name');
        $validate = Loader::validate("Teacher");
        if(!$validate->scene('pass')->check(['id'=>$id,'type'=>$name])){
            throw new ApiException('50001',-1,400,$validate->getError());
        }

        try{
            $res = db($name)->where('id','eq',$id)->setField('status',0);
        }catch (\Exception $e){
            throw new ApiException(50000, -1, 500, '数据库错误');
        }
        if($res){
            return show('10000',1,'操作成功','',200);
        }else{
            return show('50003',-1,'操作失败,请刷新再试','',400);
        }
    }
    protected function reviewOuth(){
        $uid = Cookie::get('userUid');
        $sql = new Users();
        $res = $sql->where('uid','eq',$uid)->find();
        if($res['position']<2){
            $this->outh = -1;
            $this->assign('message',"权限不够，请检查");
            return $this->fetch('/error');
        }

    }
}
