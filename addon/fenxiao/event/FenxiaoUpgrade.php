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

namespace addon\fenxiao\event;

use addon\fenxiao\model\Fenxiao;

/**
 * 分销商升级
 */
class FenxiaoUpgrade
{
    /**
     * 分销商升级
     * @param unknown $order
     * @return multitype:
     */
	public function handle($fenxiao_id)
	{
		if (!empty($fenxiao_id)) {
			$fenxiao = new Fenxiao();
			$fenxiao->fenxiaoUpgrade($fenxiao_id);
		}
	}
}