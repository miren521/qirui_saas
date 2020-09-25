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

namespace addon\memberrecharge\event;

use addon\memberrecharge\model\MemberrechargeOrder;
/**
 * 订单支付回调
 */
class MemberRechargeOrderClose
{

	public function handle($params)
	{
	    $order = new MemberrechargeOrder();
	    $res = $order->cronMemberRechargeOrderClose($params['relate_id']);
        return $res;
	}
}