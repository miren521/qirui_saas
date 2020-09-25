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

namespace addon\coupon\event;

/**
 * 店铺活动
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
	                'name' => 'coupon',
	                //展示分类（根据平台端设置，admin（平台营销），shop：店铺营销，member:会员营销, tool:应用工具）
	                'show_type' => 'shop',
	                //展示主题
	                'title' => '优惠券',
	                //展示介绍
	                'description' => '会员优惠券功能',
	                //展示图标
	                'icon' => 'addon/coupon/icon.png',
	                //跳转链接
	                'url' => 'coupon://admin/coupon/lists',
	            ]
	        ],
	        'shop' => [
	            [
	                //插件名称
	                'name' => 'coupon',
	                //展示分类（根据平台端设置，admin（平台营销），shop：店铺营销，member:会员营销, tool:应用工具）
	                'show_type' => 'shop',
	                //展示主题
	                'title' => '优惠券',
	                //展示介绍
	                'description' => '会员优惠券功能',
	                //展示图标
	                'icon' => 'addon/coupon/icon.png',
	                //跳转链接
	                'url' => 'coupon://shop/coupon/lists',
	            ]
	        ]
	
	    ];
	    return $data;
	}
}