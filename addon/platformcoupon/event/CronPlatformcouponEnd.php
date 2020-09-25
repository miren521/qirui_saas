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

use addon\platformcoupon\model\Platformcoupon;
/**
 * 启动活动
 */
class CronPlatformcouponEnd
{

	public function handle($params=[])
	{
	    $coupon = new Platformcoupon();
	    $res= $coupon->cronPlatformcouponEnd();
        return $res;
	}
}