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



namespace addon\printer\event;

use addon\printer\model\PrinterOrder;
use think\facade\Log;

/**
 * 关闭活动
 */
class PrintOrder
{

	public function handle($params)
	{
        return (new PrinterOrder())->printOrder($params['relate_id']);
	}
}