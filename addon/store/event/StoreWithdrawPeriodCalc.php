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

namespace addon\store\event;

use addon\store\model\Settlement;
use Carbon\Carbon;

/**
 * 门店结算
 */
class StoreWithdrawPeriodCalc
{
    public function handle($params)
    {
        $model = new Settlement();
        $time = Carbon::today()->timestamp;
        $res = $model->settlement($params['relate_id'], $time);

        return $res;
    }
}