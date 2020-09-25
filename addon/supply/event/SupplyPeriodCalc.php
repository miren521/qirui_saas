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
declare (strict_types=1);

namespace addon\supply\event;

use addon\supply\model\SupplySettlement;
use Carbon\Carbon;

/**
 * 供应商
 */
class SupplyPeriodCalc
{
    // 行为扩展的执行入口必须是run
    public function handle($data)
    {
        $model = new SupplySettlement();
        $time = Carbon::today()->timestamp;
        $res = $model->supplySettlement($time);
        return $res;
    }

}