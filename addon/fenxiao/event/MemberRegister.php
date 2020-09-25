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
 * 活动展示
 */
class MemberRegister
{
    /**
     * 会员注册
     * @param unknown $order
     * @return multitype:
     */
	public function handle($param)
	{
		if (isset($param['member_id']) && !empty($param['member_id'])) {
			$fenxiao = new Fenxiao();
			$fenxiao->memberRegister($param['member_id']);
		}
	}
}