<?php

namespace app\home\model;

use think\Model;

class Course extends Model
{
    public function course(){
        return $this->belongsToMany('Classes','ClassesCourse','classesId','courseId');
    }
}
