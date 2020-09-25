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


namespace addon\supply\model\order;

use app\model\system\Config as ConfigModel;
use app\model\BaseModel;

/**
 * 订单交易设置
 */
class Config extends BaseModel
{
	/**
	 * 获取供应商订单交易设置
	 */
	public function getOrderTradeConfig()
	{
		$config = new ConfigModel();
		$res = $config->getConfig([ [ 'site_id', '=', 0 ], [ 'app_module', '=', 'admin' ], [ 'config_key', '=', 'SUPPLY_ORDER_TRADE_CONFIG' ] ]);
		if (empty($res['data']['value'])) {
			$res['data']['value'] = [
				'auto_close' => 30,//订单未付款自动关闭时间 数字 单位(天)
				'auto_take_delivery' => 14,//订单发货后自动收货时间 数字 单位(天)
				'auto_complete' => 7,//订单收货后自动完成时间 数字 单位(天)
			];
		}
		return $res;
	}
	
	/**
	 * 设置供应商订单交易设置
	 */
	public function setOrderTradeConfig($data)
	{
		$config = new ConfigModel();
		$res = $config->setConfig($data, '供应商订单交易设置', 1, [ [ 'site_id', '=', 0 ], [ 'app_module', '=', 'admin' ], [ 'config_key', '=', 'SUPPLY_ORDER_TRADE_CONFIG' ] ]);
		return $res;
	}
	


}