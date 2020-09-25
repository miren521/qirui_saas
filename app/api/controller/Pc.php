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


namespace app\api\controller;

use app\model\goods\Goods as GoodsModel;
use app\model\goods\GoodsBrand as GoodsBrandModel;
use app\model\goods\GoodsCategory as GoodsCategoryModel;
use app\model\web\Pc as PcModel;

/**
 * Pc端接口
 * @author Administrator
 *
 */
class Pc extends BaseApi
{
	/**
	 * 获取热门搜索关键词
	 */
	public function hotSearchWords()
	{
		$pc_model = new PcModel();
		$info = $pc_model->getHotSearchWords();
		return $this->response($this->success($info[ 'data' ][ 'value' ]));
	}

	/**
	 * 获取默认搜索关键词
	 */
	public function defaultSearchWords()
	{
		$pc_model = new PcModel();
		$info = $pc_model->getDefaultSearchWords();
		return $this->response($this->success($info[ 'data' ][ 'value' ]));
	}

	/**
	 * 获取首页浮层
	 */
	public function floatLayer()
	{
		$pc_model = new PcModel();
		$info = $pc_model->getFloatLayer();
		return $this->response($this->success($info[ 'data' ][ 'value' ]));
	}

	/**
	 * 楼层列表
	 *
	 * @return string
	 */
	public function floors()
	{
		$pc_model = new PcModel();
		$condition = [
			[ 'state', '=', 1 ]
		];
		$list = $pc_model->getFloorList($condition, 'pf.title,pf.value,fb.name as block_name,fb.title as block_title');
		if (!empty($list[ 'data' ])) {
			$goods_model = new GoodsModel();
			$goods_brand_model = new GoodsBrandModel();
			$goods_category_model = new GoodsCategoryModel();
			foreach ($list[ 'data' ] as $k => $v) {
				$value = $v[ 'value' ];
				if (!empty($value)) {
					$value = json_decode($value, true);
					foreach ($value as $ck => $cv) {
						if (!empty($cv[ 'type' ])) {
							if ($cv[ 'type' ] == 'goods') {
								$field = 'sku_id,sku_name,price,market_price,discount_price,stock,sale_num,sku_image,goods_name,introduction';
								$order = 'sort desc,create_time desc';
								$goods_sku_list = $goods_model->getGoodsSkuList([ [ 'sku_id', 'in', $cv[ 'value' ][ 'sku_ids' ] ] ], $field, $order);
								$goods_sku_list = $goods_sku_list[ 'data' ];
								$value[ $ck ][ 'value' ][ 'list' ] = $goods_sku_list;
							} elseif ($cv[ 'type' ] == 'brand') {
								// 品牌
								$condition = [
									[ 'brand_id', 'in', $cv[ 'value' ][ 'brand_ids' ] ]
								];
								$brand_list = $goods_brand_model->getBrandList($condition, 'brand_id, brand_name, brand_initial,image_url', 'sort desc,create_time desc');
								$brand_list = $brand_list[ 'data' ];
								$value[ $ck ][ 'value' ][ 'list' ] = $brand_list;
							} elseif ($cv[ 'type' ] == 'category') {
								// 商品分类
								$condition = [
									[ 'category_id', 'in', $cv[ 'value' ][ 'category_ids' ] ]
								];
								$category_list = $goods_category_model->getCategoryList($condition, 'category_id,category_name,short_name,level,image,image_adv');
								$category_list = $category_list[ 'data' ];
								$value[ $ck ][ 'value' ][ 'list' ] = $category_list;
							}
						}
					}
					$list[ 'data' ][ $k ][ 'value' ] = $value;
				}
			}
		}
		return $this->response($list);
	}

	/**
	 * 获取导航
	 */
	public function navList()
	{
		$pc_model = new PcModel();
		$data = $pc_model->getNavPageList([ [ 'is_show', '=', 1 ] ], 1, 0, 'sort');
		return $this->response($data);
	}

	/**
	 * 获取友情链接
	 */
	public function friendlyLink()
	{
		$pc_model = new PcModel();
		$data = $pc_model->getlinkPageList([ [ 'is_show', '=', 1 ] ], 1, 0, 'link_sort');
		return $this->response($data);
	}
}
