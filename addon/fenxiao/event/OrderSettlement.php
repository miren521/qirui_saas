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

declare (strict_types = 1);

namespace addon\fenxiao\event;

use addon\fenxiao\model\FenxiaoOrder;

/**
 * 活动类型
 */
class OrderSettlement
{

	/**
	 * 活动类型
	 * @return multitype:number unknown
	 */
	public function handle($data)
	{
	    $fenxiao_order_model = new FenxiaoOrder();
	    $fenxiao_order_model->settlement($data['order_id']);
	    $res = $fenxiao_order_model->calculateOrder($data['order_id']);
	    return $res;
	}
}