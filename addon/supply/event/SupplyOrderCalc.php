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

// +---------------------------------------------------------------------+
// | NiuCloud | [ WE CAN DO IT JUST NiuCloud ]                |
// +---------------------------------------------------------------------+
// | Copy right 2019-2029 www.niucloud.com                          |
// +---------------------------------------------------------------------+
// | Author | NiuCloud <niucloud@outlook.com>                       |
// +---------------------------------------------------------------------+
// | Repository | https://github.com/niucloud/framework.git          |
// +---------------------------------------------------------------------+
declare (strict_types = 1);

namespace addon\supply\event;

use addon\supply\model\SupplyOrderCalc as SupplyOrderCalcModel;
use app\model\system\Stat;
/**
 * 订单支付后供应商点单计算
 */
class SupplyOrderCalc
{
	/**
	 * 传入订单信息
	 * @param unknown $data
	 */
	public function handle($data)
	{
	    $supply_order_calc = new SupplyOrderCalcModel();
	    $res = $supply_order_calc->calculate($data['order_id']);
	    //添加统计
	    $stat = new Stat();
	    $data = [
	        'site_id' => $data['site_id'],
	        'order_total' => $data['order_money'] - $data['adjust_money'],
	        'shipping_total' => $data['delivery_money'],
	        'order_pay_count'    => 1,
	        'goods_pay_count'    => $data['goods_num'],
	    ];
	    $stat->addShopStat($data);
	    return $res;
	}
	
}