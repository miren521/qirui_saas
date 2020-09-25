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

use addon\pintuan\model\PintuanGroup;
/**
 * 关闭活动
 */
class ClosePintuanGroup
{

	public function handle($params)
	{
	    $pintuan = new PintuanGroup();
	    $res = $pintuan->cronClosePintuanGroup($params['relate_id']);
        return $res;
	}
}