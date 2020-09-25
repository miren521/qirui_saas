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

namespace app\event;

use app\model\shop\ShopOrderCalc as ShopOrderCalcModel;
use app\model\system\Stat;
/**
 * 订单支付后店铺点单计算
 */
class ShopOrderCalc
{
	/**
	 * 传入订单信息
	 * @param unknown $data
	 */
	public function handle($data)
	{
	    $shop_order_calc = new ShopOrderCalcModel();
	    $res = $shop_order_calc->calculate($data['order_id']);
	    //添加统计
	    $stat = new Stat();
	    $data = [
	        'site_id' => $data['site_id'],
	        'order_total' => $data['order_money'],
	        'shipping_total' => $data['delivery_money'],
	        'order_pay_count'    => 1,
	        'goods_pay_count'    => $data['goods_num'],
	    ];
	    $stat->addShopStat($data);
	    return $res;
	}
	
}