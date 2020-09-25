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

use addon\pintuan\model\Pintuan;
/**
 * 关闭活动
 */
class ClosePintuan
{

	public function handle($params)
	{
	    $pintuan = new Pintuan();
	    $res = $pintuan->cronClosePintuan($params['relate_id']);
        return $res;
	}
}