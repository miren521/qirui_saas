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

namespace addon\store\event;

use addon\store\model\StoreOrder;

/**
 * 添加下单用户为门店用户
 */
class OrderComplete
{
	public function handle($order)
	{
        $store_order = new StoreOrder();
        $res = $store_order->orderComplete($order['order_id']);
        return $res;
	}
}