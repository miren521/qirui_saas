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

namespace addon\discount\event;

use addon\discount\model\Discount;
/**
 * 启动活动
 */
class OpenDiscount
{

	public function handle($params)
	{
	    $discount = new Discount();
	    $res= $discount->cronOpenDiscount($params['relate_id']);
        return $res;
	}
}