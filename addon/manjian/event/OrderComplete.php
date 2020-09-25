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



namespace addon\manjian\event;

use addon\manjian\model\Order;

/**
 * 订单完成
 */
class OrderComplete
{

	public function handle($params)
	{
	    if (isset($params['order_id'])) {
            $order = new Order();
            $result = $order->orderComplete($params['order_id']);
            return $result;
        }
	}
}