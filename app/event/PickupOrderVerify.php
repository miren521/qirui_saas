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

use app\model\order\OrderCommon;
use app\model\order\StoreOrder;
/**
 * 门店订单提货
 */
class PickupOrderVerify
{
    
	public function handle($data)
	{
	    if($data['verify_type'] == 'pickup')
	    {

	        $store_order = new StoreOrder();
	        $store_order->verify($data['verify_code']);
	    }
	    return '';
	}
	
}