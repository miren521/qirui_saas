<?php

namespace app\api\controller;

use app\model\goods\GoodsAttribute as GoodsAttributeModel;
use app\model\goods\GoodsCategory as GoodsCategoryModel;
use app\model\goods\Goods as GoodsModel;

/**
 * 商品分类
 * Class Goodscategory
 * @package app\api\controller
 */
class Goodscategory extends BaseApi
{

	/**
	 * 树状结构信息
	 */
	public function tree()
	{
		$level = isset($this->params[ 'level' ]) ? $this->params[ 'level' ] : 3; // 分类等级 1 2 3
		$template = isset($this->params[ 'template' ]) ? $this->params[ 'template' ] : 2; // 模板 1：无图，2：有图，3：有商品

		$goods = new GoodsModel();
		$goods_category_model = new GoodsCategoryModel();
		$condition = [
			[ 'is_show', '=', 1 ],
			[ 'level', '<=', $level ]
		];
		$field = "category_id,category_name,short_name,pid,level,image,category_id_1,category_id_2,category_id_3,image_adv";
		$order = "sort desc,category_id desc";

		$list = $goods_category_model->getCategoryTree($condition, $field, $order);
		// 查询商品
		if ($level == 3 && $template == 3) {
			$goods_field = 'gs.sku_id,gs.discount_price,gs.price,gs.market_price,gs.sale_num,gs.sku_image,gs.goods_name,gs.site_id,gs.website_id,gs.is_free_shipping,gs.introduction,gs.promotion_type,gs.goods_spec_format,gs.is_virtual';

			$alias = 'gs';
			$join = [
				[ 'goods g', 'gs.sku_id = g.sku_id', 'inner' ]
			];
			foreach ($list[ 'data' ] as $k => $v) {
				if (!empty($v[ 'child_list' ])) {
					foreach ($v[ 'child_list' ] as $second_k => $second_v) {
						if (!empty($second_v[ 'child_list' ])) {
							foreach ($second_v[ 'child_list' ] as $third_k => $third_v) {
								$goods_condition = [
									[ 'gs.goods_state', '=', 1 ],
									[ 'gs.verify_state', '=', 1 ],
									[ 'gs.is_delete', '=', 0 ],
									[ 'gs.is_delete', '=', 0 ],
									[ 'gs.category_id_3', '=', $third_v[ 'category_id' ] ]
								];
                                //todo  店铺状态商品 只查看处于开启状态的店铺
                                $join[] = [ 'shop s', 's.site_id = gs.site_id', 'inner'];
                                $condition[] = ['s.shop_status', '=', 1];
								$goods_list = $goods->getGoodsSkuPageList($goods_condition, 1, 3, 'gs.sort desc,gs.create_time desc', $goods_field, $alias, $join);
								$goods_list = $goods_list[ 'data' ][ 'list' ];
								$list[ 'data' ][ $k ][ 'child_list' ][ $second_k ][ 'child_list' ][ $third_k ][ 'goods_list' ] = $goods_list;
							}
						} else {
							$list[ 'data' ][ $k ][ 'child_list' ][ $second_k ][ 'child_list' ] = [];
						}
					}
				} else {
					$list[ 'data' ][ $k ][ 'child_list' ] = [];
				}
			}
		}

		return $this->response($list);
	}


	public function info()
	{
		$category_id = isset($this->params[ 'category_id' ]) ? $this->params[ 'category_id' ] : 0; //分类id
		$goods_category_model = new GoodsCategoryModel();
		$condition = [
			[ 'category_id', '=', $category_id ],
			[ 'is_show', '=', 1 ],
		];
		$field = "category_id,category_name,short_name,pid,level,image,category_id_1,category_id_2,category_id_3,category_full_name,image_adv";
		$info = $goods_category_model->getCategoryInfo($condition, $field);
		return $this->response($info);
	}

	/**
	 * 根据商品分类查询关联商品类型，查询关联品牌、属性
	 * @return string
	 */
	public function relevanceinfo()
	{
		$category_id = isset($this->params[ 'category_id' ]) ? $this->params[ 'category_id' ] : 0; //分类id

		if (empty($category_id)) {
			return $this->response($this->error('', 'REQUEST_CATEGORY_ID'));
		}
		$goods_category_model = new GoodsCategoryModel();
		$category_info = $goods_category_model->getCategoryInfo([ [ 'category_id', '=', $category_id ] ], 'attr_class_id');
		$category_info = $category_info[ 'data' ];

		$goods_attribute_model = new GoodsAttributeModel();

		//商品类型关联品牌
		$brand_list = $goods_attribute_model->getAttrClassBrandList([ [ 'ngacb.attr_class_id', '=', $category_info[ 'attr_class_id' ] ] ]);
		$brand_list = $brand_list[ 'data' ];
		$brand_initial_list = [];
		foreach ($brand_list as $item) {
			if (!in_array($item[ 'brand_initial' ], $brand_initial_list) && !empty($item[ 'brand_initial' ])) {
				$brand_initial_list[] = $item[ 'brand_initial' ];
			}
		}

		//商品类型关联属性
		$attribute_list = $goods_attribute_model->getAttributeList([ [ 'attr_class_id', '=', $category_info[ 'attr_class_id' ] ], [ 'is_query', '=', 1 ] ], 'attr_id,attr_name,attr_class_id,sort,is_query,is_spec,attr_value_list,attr_value_list,attr_type,site_id,attr_value_format');
		$attribute_list = $attribute_list[ 'data' ];
		if (!empty($attribute_list)) {
			foreach ($attribute_list as $k => $v) {
				$attribute_list[ $k ][ 'child' ] = json_decode($attribute_list[ $k ][ 'attr_value_format' ], true);
			}
		}

		$res = [
			'brand_list' => $brand_list,
			'attribute_list' => $attribute_list,
			'brand_initial_list' => $brand_initial_list
		];
		return $this->response($this->success($res));
	}
}
