<?php

namespace app\home\model;

use think\Model;

class Bkllk extends Model
{
    public function getStatusAttr($value,$data){
        $status = [10=>"未审核",20=>"教研室审核通过",30=>"教务员审核通过",0=>"审核驳回"];
        return $status[$data['status']];
    }
}
