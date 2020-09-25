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


namespace addon\membersignin\model;

use app\model\system\Config as ConfigModel;
use app\model\BaseModel;

/**
 * 会员签到
 */
class Signin extends BaseModel
{
	/**
	 * 会员签到奖励设置
	 * @param $data
	 * @param $is_use
	 * @return array
	 */
	public function setConfig($data, $is_use)
	{
		$config = new ConfigModel();
		$res = $config->setConfig($data, '会员签到奖励设置', $is_use, [ [ 'site_id', '=', 0 ], [ 'app_module', '=', 'admin' ], [ 'config_key', '=', 'MEMBER_SIGNIN_REWARD_CONFIG' ] ]);
		return $res;
	}
	
	/**
	 * 会员签到奖励设置
	 */
	public function getConfig()
	{
		$config = new ConfigModel();
		$res = $config->getConfig([ [ 'site_id', '=', 0 ], [ 'app_module', '=', 'admin' ], [ 'config_key', '=', 'MEMBER_SIGNIN_REWARD_CONFIG' ] ]);
		if (empty($res['data']['value'])) {
			$res['data']['value'] = [
				[
					"point" => 0,
//					"balance" => 0,
					"growth" => 0,
					'coupon' => 0,
					"day" => 1
				]
			];
		}
		return $res;
	}
	
	public function memberSignin()
	{
	
	}
	
}