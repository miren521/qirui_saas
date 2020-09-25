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

use addon\coupon\model\CouponType;

/**
 * 优惠券定时结束
 */
class CronCouponTypeEnd
{

	public function handle($params=[])
	{
	    $coupon = new CouponType();
	    $res= $coupon->couponCronEnd($params['relate_id']);
        return $res;
	}
}