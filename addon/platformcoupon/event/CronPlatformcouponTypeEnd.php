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

namespace addon\platformcoupon\event;

use addon\platformcoupon\model\PlatformcouponType;

/**
 * 优惠券定时结束
 */
class CronPlatformcouponTypeEnd
{

	public function handle($params=[])
	{
	    $coupon = new PlatformcouponType();
	    $res= $coupon->platformcouponCronEnd($params['relate_id']);
        return $res;
	}
}