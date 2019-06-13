<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 流年 <liu21st@gmail.com>
// +----------------------------------------------------------------------

// 应用公共文件
/**
 * 姓名的拆分
 * @param $fullname 完整的姓名
 * @return array
 */
function splitName($fullname){
    $hyphenated = array('欧阳','太史','端木','上官','司马','东方','独孤','南宫','万俟','闻人','夏侯','诸葛','尉迟','公羊','赫连','澹台','皇甫',
        '宗政','濮阳','公冶','太叔','申屠','公孙','慕容','仲孙','钟离','长孙','宇文','城池','司徒','鲜于','司空','汝嫣','闾丘','子车','亓官',
        '司寇','巫马','公西','颛孙','壤驷','公良','漆雕','乐正','宰父','谷梁','拓跋','夹谷','轩辕','令狐','段干','百里','呼延','东郭','南门',
        '羊舌','微生','公户','公玉','公仪','梁丘','公仲','公上','公门','公山','公坚','左丘','公伯','西门','公祖','第五','公乘','贯丘','公皙',
        '南荣','东里','东宫','仲长','子书','子桑','即墨','达奚','褚师');
    $vLength = mb_strlen($fullname, 'utf-8');
    $lastname = '';
    $firstname = '';//前为姓,后为名
    if($vLength > 2){
        $preTwoWords = mb_substr($fullname, 0, 2, 'utf-8');//取命名的前两个字,看是否在复姓库中
        if(in_array($preTwoWords, $hyphenated)){
            $lastname = $preTwoWords;
            $firstname = mb_substr($fullname, 2, 10, 'utf-8');
        }else{
            $lastname = mb_substr($fullname, 0, 1, 'utf-8');
            $firstname = mb_substr($fullname, 1, 10, 'utf-8');
        }
    }else if($vLength == 2){//全名只有两个字时,以前一个为姓,后一下为名
        $lastname = mb_substr($fullname ,0, 1, 'utf-8');
        $firstname = mb_substr($fullname, 1, 10, 'utf-8');
    }else{
        $lastname = $fullname;
    }
    return array($lastname, $firstname);
}

/**
 * @param $code 状态   用于区分操作
 * @param $status 状态码
 * @param $info  提示信息
 * @param array $data  更新的数据   可以为空
 * @param $httpCode  http状态码
 */
function show($code,$status,$info,$data = [],$httpCode){
    $data = [
        'code'=>$code,
        'status'=>$status,
        'info'=>$info,
        'data'=>$data
    ];
    return json($data,$httpCode);
}
