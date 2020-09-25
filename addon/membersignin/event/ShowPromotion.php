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

namespace addon\membersignin\event;

/**
 * 活动展示
 */
class ShowPromotion
{
	
	/**
	 * 活动展示
	 *
	 * @return multitype:number unknown
	 */
	public function handle()
	{
		$data = [
			'admin' => [
				[
					//插件名称
					'name' => 'membersignin',
					//展示分类（根据平台端设置，admin（平台营销），shop：店铺营销，member:会员营销, tool:应用工具）
					'show_type' => 'member',
					//展示主题
					'title' => '会员签到',
					//展示介绍
					'description' => '会员签到后奖励设置',
					//展示图标
					'icon' => 'addon/membersignin/icon.png',
					//跳转链接
					'url' => 'membersignin://admin/config/index',
				]
			],
			'shop' => [
			]
		
		];
		return $data;
	}
}