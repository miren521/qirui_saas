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



namespace addon\bargain\event;

use addon\bargain\model\Bargain;
/**
 * 启动活动
 */
class OpenBargain
{

	public function handle($params)
	{
	    $pintuan = new Bargain();
	    $res= $pintuan->cronOpenBargain($params['relate_id']);
        return $res;
	}
}