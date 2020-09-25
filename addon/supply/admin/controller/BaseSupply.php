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


namespace addon\supply\admin\controller;

use app\admin\controller\BaseAdmin;
use think\facade\Config;

class BaseSupply extends BaseAdmin
{

    protected $replace = [];    //视图输出字符串内容替换    相当于配置文件中的'view_replace_str'

    public function __construct()
    {
        parent::__construct();
        $this->replace = [
            'ADMIN_SUPPLY_CSS' => __ROOT__ . '/addon/supply/admin/view/public/css',
            'ADMIN_SUPPLY_JS'  => __ROOT__ . '/addon/supply/admin/view/public/js',
            'ADMIN_SUPPLY_IMG' => __ROOT__ . '/addon/supply/admin/view/public/img',
        ];

        $tpl_replace_string         = array_merge(config('view.tpl_replace_string'), $this->replace);
        $view['tpl_replace_string'] = $tpl_replace_string;
        Config::set($view, 'view');
    }


}