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

// +---------------------------------------------------------------------+
// | NiuCloud | [ WE CAN DO IT JUST NiuCloud ]                |
// +---------------------------------------------------------------------+
// | Copy right 2019-2029 www.niucloud.com                          |
// +---------------------------------------------------------------------+
// | Author | NiuCloud <niucloud@outlook.com>                       |
// +---------------------------------------------------------------------+
// | Repository | https://github.com/niucloud/framework.git          |
// +---------------------------------------------------------------------+
declare (strict_types = 1);

namespace app\event;

use app\model\web\DiyView as DiyViewModel;


/**
 * 增加默认自定义数据：网站主页、底部导航
 */
class AddShopDiyView
{
	
	public function handle($param)
	{
		if (!empty($param['site_id'])) {
			
			$diy_view_model = new DiyViewModel();
			$page = $diy_view_model->getPage();
			$index_value = json_encode([
				"global" => [
					"title" => "店铺主页",
					"openBottomNav" => false,
					"bgColor" => "#ffffff",
					"bgUrl" => ""
				],
				"value" => [
					[
						"addon_name" => "",
						"type" => "IMAGE_ADS",
						"name" => "图片广告",
						"controller" => "ImageAds",
						"selectedTemplate" => "carousel-posters",
						"imageClearance" => 0,
						"height" => 0,
						"list" => [
							[
								"imageUrl" => "upload/default/diy_view/posters.png",
								"title" => "",
								"link" => []
							]
						]
					],
					[
						"addon_name" => "",
						"type" => "SHOP_INFO",
						"name" => "店铺信息",
						"controller" => "ShopInfo",
						"color" => "#0a020a"
					],
					[
						"addon_name" => "",
						"type" => "SHOP_SEARCH",
						"name" => "店内搜索",
						"controller" => "ShopSearch"
					],
					[
						"height" => 10,
						"backgroundColor" => "#f4f4f4",
						"addon_name" => "",
						"type" => "HORZ_BLANK",
						"name" => "辅助空白",
						"controller" => "HorzBlank"
					],
					[
						"sources" => "default",
						"skuId" => "",
						"categoryId" => 0,
						"goodsCount" => 12,
						"addon_name" => "",
						"type" => "GOODS_LIST",
						"name" => "商品列表",
						"controller" => "GoodsList"
					],
					[
						"height" => 10,
						"backgroundColor" => "#f4f4f4",
						"addon_name" => "",
						"type" => "HORZ_BLANK",
						"name" => "辅助空白",
						"controller" => "HorzBlank"
					],
					[
						"addon_name" => "",
						"type" => "SHOP_STORE",
						"name" => "门店",
						"controller" => "ShopStore"
					],
				]
			]);
			
			$goods_category_value = json_encode([
				"global" => [
					"title" => "商品分类",
					"openBottomNav" => false,
					"bgColor" => "#ffffff",
					"bgUrl" => ""
				],
				"value" => [
					[
						"addon_name" => "",
						"type" => "GOODS_CATEGORY",
						"name" => "商品分类",
						"controller" => "GoodsCategory",
						"level" => 2,
						"template" => 2
					]
				]
			]);
			
			// 网站主页
			$data = [ [
				'site_id' => $param['site_id'],
				'title' => '店铺主页',
				'name' => $page['shop']['index']['name'],
				'type' => $page['shop']['port'],
				'value' => $index_value
			], [
				'site_id' => $param['site_id'],
				'title' => '商品分类',
				'name' => $page['shop']['goods_category']['name'],
				'type' => $page['shop']['port'],
				'value' => $goods_category_value
			] ];
			
			$res = $diy_view_model->addSiteDiyViewList($data);
			
			// 底部导航
			$value = json_encode([
				"type" => 1,
				"list" => [
					[
						"iconPath" => "upload/default/diy_view/bottom/shop_index.png",
						"selectedIconPath" => "upload/default/diy_view/bottom/shop_index_selected.png",
						"text" => "首页",
						"link" => [
							"addon_name" => "",
							"addon_title" => null,
							"name" => "SHOP_INDEX",
							"title" => "店铺首页",
							"web_url" => "",
							"wap_url" => "/otherpages/shop/index/index",
							"icon" => "",
							"addon_icon" => null,
							"selected" => false
						]
					],
					[
						"iconPath" => "upload/default/diy_view/bottom/shop_category.png",
						"selectedIconPath" => "upload/default/diy_view/bottom/shop_category_selected.png",
						"text" => "分类",
						"link" => [
							"addon_name" => "",
							"addon_title" => null,
							"name" => "SHOP_CATEGORY",
							"title" => "店铺商品分类",
							"web_url" => "",
							"wap_url" => "/otherpages/shop/category/category",
							"icon" => "",
							"addon_icon" => null,
							"selected" => false
						]
					],
					[
						"iconPath" => "upload/default/diy_view/bottom/shop_list.png",
						"selectedIconPath" => "upload/default/diy_view/bottom/shop_list_selected.png",
						"text" => "宝贝",
						"link" => [
							"addon_name" => "",
							"addon_title" => null,
							"name" => "SHOP_LIST",
							"title" => "店铺商品列表",
							"web_url" => "",
							"wap_url" => "/otherpages/shop/list/list",
							"icon" => "",
							"addon_icon" => null,
							"selected" => false
						]
					],
					[
						"iconPath" => "upload/default/diy_view/bottom/shop_introduce.png",
						"selectedIconPath" => "upload/default/diy_view/bottom/shop_introduce_selected.png",
						"text" => "介绍",
						"link" => [
							"addon_name" => "",
							"addon_title" => null,
							"name" => "SHOP_INTRODUCE",
							"title" => "店铺介绍",
							"web_url" => "",
							"wap_url" => "/otherpages/shop/introduce/introduce",
							"icon" => "",
							"addon_icon" => null,
							"selected" => false
						]
					]
				],
				"backgroundColor" => "#ffffff",
				"textColor" => "#333333",
				"textHoverColor" => "#ff0036",
				"bulge" => false
			]);
			$res = $diy_view_model->setShopBottomNavConfig($value, $param['site_id']);
			return $res;
			
		}
		
	}
	
}