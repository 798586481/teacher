<?php
namespace app\home\validate;
use think\Validate;
class User extends Validate{
    protected $rule = [
//        ['id','require|number' ,'非法访问|非法传入'],
        ['info','require|in:xb,position,userStatus','获取值不能为空|info传入值错误'],
        ['name','require|chs|length:2,5','请填写姓名|姓名格式不正确|姓名格式不准确'],
        ['position','require|between:0,5','请选择职位|职位格式不正确'],
        ['xb','require|between:0,4','请选择学部|学部格式不正确'],
        ['bz','require|between:0,1','请选择编制|编制格式不正确'],
        ['phone','require|number','请填写手机号|手机号格式不正确'],
        ['password','require','请填写密码'],
        ['status','require|number|in:10,20,40','请填写密码|请选择正确的状态|请选择正确的状态'],['name','require|chs|length:2,5','请填写姓名|姓名格式不正确|姓名格式不准确'],
        ['M-position','between:0,5','职位格式不正确'],
        ['M-name','chs|length:2,5','姓名格式不正确|姓名格式不准确'],
        ['M-xb','between:0,4','学部格式不正确'],
        ['M-bz','between:0,1','编制格式不正确'],
        ['M-phone','number','手机号格式不正确'],
        ['M-status','number|in:10,20,40','请选择正确的状态|请选择正确的状态'],
//        ['type','require|in:bkllk,zdsy,zzfd,ztfdk,zdjx,zdbkskcsj,zdbksbysj,pybysj,zdxscxsj,bk,qthjgzl','type不能为空|传入值错误']
    ];
    protected $scene = [
        'getUserInfo' => ['info'],
        'addUser' => ['name','position','phone','xb','bz','password'],
//        'updateUser' => ['name','position','phone','xb','bz','password'],
        'updateUser' => ['M-name','M-position','M-phone','M-xb','M-bz','M-status'],
    ];
}