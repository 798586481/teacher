<?php
namespace app\home\validate;
use think\Validate;
class Teacher extends Validate{
    protected $rule = [
//        ['id','require|number' ,'非法访问|非法传入'],
        ['K','require|between:1,1.25|number' ,'课程系数不能为空|课程系数错误，请重新选择|课程系数错误，请重新选择'],
        ['M','require|in:1,3|number' ,'命题工作量不能为空|命题工作量错误，请重新选择|命题工作量错误，请重新选择'],
        ['J','require|in:1,0|number' ,'监考工作量不能为空|监考工作量错误，请重新选择|监考工作量错误，请重新选择'],
        ['C','require|in:1,0.8|number' ,'重复班系数不能为空|重复班系数错误，请重新选择|重复班系数错误，请重新选择'],
        ['id','require|number' ,'非法访问|非法传入'],
        ['type','require|in:bkllk,zdsy,zzfd,ztfdk,zdjx,zdbkskcsj,zdbksbysj,pybysj,zdxscxsj,bk,qthjgzl','type不能为空da|传入值错误']
    ];
    protected $scene = [
        'saveCourse' => ['K','M','J','C','id'],
        'delete' => ['id','type'],
        'getName' =>['type'],
        'update' =>['id','type','K','M','J'],
        'pass' =>['id','type'],
    ];
}