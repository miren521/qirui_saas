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
/**
 * 订单项退款后后店铺订单计算
 */
class SupplyOrderRefundCalc
{
	/**
	 * 传入订单信息
	 * @param unknown $data
	 */
	public function handle($data)
	{
	    $supply_order_calc = new SupplyOrderCalcModel();
	    $res = $supply_order_calc->refundCalculate($data);
	    return $res;
	}
	
}