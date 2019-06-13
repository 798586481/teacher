<?php

namespace app\home\model;

use think\Model;

class Experiment extends Model
{
    public function experiment(){
        return $this->belongsToMany('Classes','ExperimentClasses','classesId','experimentId');
    }
}
