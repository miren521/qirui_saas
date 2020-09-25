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

namespace addon\groupbuy\event;

use addon\groupbuy\model\Groupbuy;
/**
 * 启动活动
 */
class OpenGroupbuy
{

	public function handle($params)
	{
	    $pintuan = new Groupbuy();
	    $res= $pintuan->cronOpenGroupbuy($params['relate_id']);
        return $res;
	}
}