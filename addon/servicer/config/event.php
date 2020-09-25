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


return [
    'bind'      => [],

    'listen'    => [
        //展示活动
        'ShowPromotion' => [
            'addon\servicer\event\ShowPromotion',
        ],
        'PromotionType' => [
//            'addon\servicer\event\PromotionType',
        ],
    ],

    'subscribe' => [],
];
