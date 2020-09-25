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

namespace addon\pintuan\event;
use addon\pintuan\model\PintuanOrder;
/**
 * 活动展示
 */
class OrderPay
{

    /**
     * 活动展示
     * 
     * @return multitype:number unknown
     */
	public function handle($param)
	{
	    if($param['promotion_type'] == 'pintuan')
	    {
	        $pintuan_order = new PintuanOrder();
	        $res = $pintuan_order->orderPay($param);
	        return $res;
	    }else{
	        return '';
	    }
	}
}