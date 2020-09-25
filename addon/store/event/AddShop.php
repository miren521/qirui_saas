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

use addon\store\model\Config;

/**
 *添加店铺门店结算周期
 */
class Addshop
{
    
        public function handle($data)
        {
            $config = new Config();
            $res = $config->addSettlementCron($data['site_id']);
            return $res;
        }
}