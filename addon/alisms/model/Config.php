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


namespace addon\alisms\model;

use app\model\system\Config as ConfigModel;
use app\model\BaseModel;

/**
 * 支付宝支付配置
 */
class Config extends BaseModel
{
	/**
	 * 设置短信配置
	 * array $data
	 */
	public function setSmsConfig($data)
	{
		$config = new ConfigModel();
		$res = $config->setConfig($data, '阿里云短信配置', $data['status'], [ [ 'site_id', '=', 0 ], [ 'app_module', '=', 'admin' ], [ 'config_key', '=', 'ALI_SMS_CONFIG' ] ]);
		return $res;
	}
	
	/**
	 * 获取短信配置
	 */
	public function getSmsConfig()
	{
		$config = new ConfigModel();
		$res = $config->getConfig([ [ 'site_id', '=', 0 ], [ 'app_module', '=', 'admin' ], [ 'config_key', '=', 'ALI_SMS_CONFIG' ] ]);
		return $res;
	}
}