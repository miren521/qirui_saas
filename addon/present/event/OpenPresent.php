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
 * 启动活动
 */
class OpenPresent
{

	public function handle($params)
	{
	    $pintuan = new Present();
	    $res= $pintuan->cronOpenPresent($params['relate_id']);
        return $res;
	}
}