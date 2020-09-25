<?php
/**
 * KirySaaS--------||bai T o o Y ||
 * =========================================================
 * ----------------------------------------------
 * User Mack Qin
 * Copy right 2019-2029 kiry 保留所有权利。
 * ----------------------------------------------
 * =========================================================
 */

namespace app\saas\controller;
use app\model\system\User as UserModel;
use app\model\web\Config as ConfigModel;
use think\captcha\facade\Captcha as ThinkCaptcha;
use think\facade\Cache;

/**
 * 首页 控制器
 */
class Index extends BaseSaas
{

    public function index(){
        return view();
    }

}
