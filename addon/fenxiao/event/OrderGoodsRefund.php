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
class OrderGoodsRefund
{

	/**
	 * 活动类型
	 * @return multitype:number unknown
	 */
	public function handle($data)
	{
	    $order_model = new FenxiaoOrder();
	    $res = $order_model->refund($data['order_goods_id']);
	    return $res;
	}
}