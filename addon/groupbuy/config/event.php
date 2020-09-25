<?php
// 事件定义文件
return [
    'bind'      => [
        
    ],

    'listen'    => [
        //展示活动
        'ShowPromotion' => [
            'addon\groupbuy\event\ShowPromotion',
        ],
        
        'PromotionType' => [
            'addon\groupbuy\event\PromotionType',
        ],

        //关闭团购
        'CloseGroupbuy' => [
            'addon\groupbuy\event\CloseGroupbuy',
        ],

        //开启团购
        'OpenGroupbuy' => [
            'addon\groupbuy\event\OpenGroupbuy',
        ],
        //PC默认导航
        'InitPcNav' => [
            'addon\groupbuy\event\InitPcNav',
        ],

	    //默认广告位
	    'InitAdv' => [
		    'addon\groupbuy\event\InitAdv',
	    ],
        // 商品列表
        'GoodsListPromotion' => [
            'addon\groupbuy\event\GoodsListPromotion',
        ],
    ],

    'subscribe' => [
    ],
];
