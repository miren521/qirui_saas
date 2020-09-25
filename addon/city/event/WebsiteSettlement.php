<?php
// +---------------------------------------------------------------------+
// | NiuCloud | [ WE CAN DO IT JUST NiuCloud ]                |
// +---------------------------------------------------------------------+
// | Copy right 2019-2029 www.niucloud.com                          |
// +---------------------------------------------------------------------+
// | Author | NiuCloud <niucloud@outlook.com>                       |
// +---------------------------------------------------------------------+
// | Repository | https://github.com/niucloud/framework.git          |
// +---------------------------------------------------------------------+
declare (strict_types = 1);

namespace addon\city\event;

use addon\city\model\CitySettlement;
use Carbon\Carbon;

/**
 * 分站结算
 */
class WebsiteSettlement
{
	// 行为扩展的执行入口必须是run
	public function handle($data)
	{
        $model = new CitySettlement();
        $time = Carbon::today()->timestamp+60*30;
        $res = $model->citySettlement($time);

        return $res;
	}
	
}