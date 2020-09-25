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

class BargainLaunchClose
{
    public function handle($params){
        $bargain = new Bargain();
        $res = $bargain->cronCloseBargainLaunch($params['relate_id']);
        return $res;
    }
}