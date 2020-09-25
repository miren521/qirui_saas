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


namespace addon\membersignin\admin\controller;

use addon\membersignin\model\Signin;
use app\admin\controller\BaseAdmin;

/**
 * 会员签到
 */
class Config extends BaseAdmin
{
	
	public function index()
	{
		$config_model = new Signin();
		if (request()->isAjax()) {
			$data = input("json", "{}");
			$is_use = input("is_use", 0);//是否启用
			$data = json_decode($data);
			$res = $config_model->setConfig($data, $is_use);
			$this->addLog("设置会员签到奖励");
			return $res;
		} else {
			$config_result = $config_model->getConfig();
			$this->assign('config', $config_result['data']);
			return $this->fetch('config/index');
		}
	}
	
}