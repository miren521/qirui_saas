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

namespace addon\pointexchange\event;

use addon\pointexchange\model\Order;
/**
 * 积分兑换订单关闭
 */
class CronExchangeOrderClose
{
    
	// 行为扩展的执行入口必须是run
	public function handle($data)
	{
        $order = new Order();
        $condition = array(
            ['order_id', '=', $data['relate_id']]
        );
        $result = $order->closeOrder($condition);
        return $result;
	}
	
}