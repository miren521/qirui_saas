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


declare(strict_types=1);

namespace addon\servicer\event;

/**
 * 活动类型
 */
class PromotionType
{
    /**
     * 活动类型
     * @return array
     */
    public function handle()
    {
        return ["name" => "客服", "type" => "servicer"];
    }
}
