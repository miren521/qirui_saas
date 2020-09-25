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


namespace addon\goodscircle\admin\controller;

use app\admin\controller\BaseAdmin;
use addon\goodscircle\model\Config as ConfigModel;

/**
 * 满减控制器
 */
class Config extends BaseAdmin
{
	protected $replace = [];    //视图输出字符串内容替换    相当于配置文件中的'view_replace_str'
	
	public function __construct()
	{
		parent::__construct();
		$this->replace = [
			'CIRCLE_CSS' => __ROOT__ . '/addon/goodscircle/admin/view/public/css',
			'CIRCLE_JS' => __ROOT__ . '/addon/goodscircle/admin/view/public/js',
			'CIRCLE_IMG' => __ROOT__ . '/addon/goodscircle/admin/view/public/img',
		];
	}
	
	
    public function index()
    {
        $config_model = new ConfigModel();
        if (request()->isAjax()) {
            $data = [];
            $is_use = input('is_use', 0);
            return $config_model->setGoodscircleConfig($data, $is_use);
        } else {
            $config = $config_model->getGoodscircleConfig();
            $this->assign('config', $config['data']);
            return $this->fetch("config/index",[],$this->replace);
        }
    }
}