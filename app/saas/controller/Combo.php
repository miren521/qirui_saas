<?php
declare (strict_types = 1);

namespace app\saas\controller;

use think\Request;

class Combo extends BaseSaas
{
    /**
     * 基础方法
     * User: mi
     * Date: 2020-09-25
     */
    public function index(){

        if (request()->isAjax()) {

        } else {
            return $this->fetch('combo/index');
        }
    }
}
