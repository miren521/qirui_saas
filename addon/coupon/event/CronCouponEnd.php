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

namespace addon\coupon\event;

use addon\coupon\model\Coupon;
/**
 * 启动活动
 */
class CronCouponEnd
{

	public function handle($params=[])
	{
	    $coupon = new Coupon();
	    $res= $coupon->cronCouponEnd();
        return $res;
	}
}