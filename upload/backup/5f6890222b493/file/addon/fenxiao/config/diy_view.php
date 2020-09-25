<?php
/**
 * Niushop商城系统 - 团队十年电商经验汇集巨献!
 * =========================================================
 * Copy right 2019-2029 山西牛酷信息科技有限公司, 保留所有权利。
 * ----------------------------------------------
 * 官方网址: https://www.niushop.com
 * 这不是一个自由软件！您只能在不用于商业目的的前提下对程序代码进行修改和使用。
 * 任何企业和个人不允许对程序代码以任何形式任何目的再发布。
 * =========================================================
 */
return [
	'template' => [
	],
	'util' => [
		[
			'name' => 'FENXIAO_GOODS_LIST',
			'title' => '分销商品',
			'type' => 'OTHER',
			'controller' => 'FenxiaoGoodsList',
			'value' => '{}',
			'sort' => '10000',
			'support_diy_view' => 'DIY_FENXIAO_MARKET',
			'max_count' => 1
		]
	],
	'link' => [
		[
			'name' => 'FENXIAO_GOODS_LIST',
			'title' => '分销商品列表',
			'wap_url' => '/otherpages/fenxiao/goods_list/goods_list',
			'web_url' => '',
			'support_diy_view' => 'DIY_VIEW_INDEX',
		],
	],
];