<?php
/**
 * Created by PhpStorm.
 * User: looker
 * Date: 2018-01-08
 * Time: 11:06
 */

namespace app\wqun\controller;


use think\Controller;

class BaseController extends Controller
{
    protected $id;
    protected $openid;


    public function getImg(){
        $qun_users = new QunUser();
    }

}