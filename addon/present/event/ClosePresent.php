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

namespace addon\present\event;

use addon\present\model\Present;
/**
 * 关闭活动
 */
class ClosePresent
{

	public function handle($params)
	{
	    $present = new present();
	    $res = $present->cronClosePresent($params['relate_id']);
        return $res;
	}
}