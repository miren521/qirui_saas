<?php

namespace app\api\controller;

use app\model\goods\GoodsBrowse as GoodsBrowseModel;

/**
 * 商品浏览历史
 * @package app\api\controller
 */
class Goodsbrowse extends BaseApi
{
	/**
	 * 添加信息
	 */
	public function add()
	{
		$token = $this->checkToken();
		if ($token['code'] < 0) return $this->response($token);
		
		$goods_id = isset($this->params['goods_id']) ? $this->params['goods_id'] : 0;
		$sku_id = isset($this->params['sku_id']) ? $this->params['sku_id'] : 0;
		$site_id = isset($this->params['site_id']) ? $this->params['site_id'] : 0;
		$category_id = isset($this->params['category_id']) ? $this->params['category_id'] : 0;
		$category_id_1 = isset($this->params['category_id_1']) ? $this->params['category_id_1'] : 0;
		$category_id_2 = isset($this->params['category_id_2']) ? $this->params['category_id_2'] : 0;
		$category_id_3 = isset($this->params['category_id_3']) ? $this->params['category_id_3'] : 0;
		
		if (empty($goods_id)) {
			return $this->response($this->error('', 'REQUEST_GOODS_ID'));
		}
		
		if (empty($sku_id)) {
			return $this->response($this->error('', 'REQUEST_SKU_ID'));
		}
		
		if (empty($category_id)) {
			return $this->response($this->error('', 'REQUEST_CATEGORY_ID'));
		}
		$goods_browse_model = new GoodsBrowseModel();
		$data = [
			'member_id' => $token['data']['member_id'],
			'goods_id' => $goods_id,
			'sku_id' => $sku_id,
			'site_id' => $site_id,
			'category_id' => $category_id,
			'category_id_1' => $category_id_1,
			'category_id_2' => $category_id_2,
			'category_id_3' => $category_id_3,
		];
		$res = $goods_browse_model->addBrowse($data);
		return $this->response($res);
		
	}
	
	/**
	 * 删除信息
	 */
	public function delete()
	{
		$token = $this->checkToken();
		if ($token['code'] < 0) return $this->response($token);
		
		$id = isset($this->params['id']) ? $this->params['id'] : 0;
		if (empty($id)) {
			return $this->response($this->error('', 'REQUEST_ID'));
		}
		$goods_browse_model = new GoodsBrowseModel();
		$res = $goods_browse_model->deleteBrowse($id, $token['data']['member_id']);
		return $this->response($res);
	}
	
	/**
	 * 分页列表
	 */
	public function page()
	{
		$token = $this->checkToken();
		if ($token['code'] < 0) return $this->response($token);
		
		$page = isset($this->params['page']) ? $this->params['page'] : 1;
		$page_size = isset($this->params['page_size']) ? $this->params['page_size'] : PAGE_LIST_ROWS;
		$goods_browse_model = new GoodsBrowseModel();
		$condition = [
			[ 'ngb.member_id', '=', $token['data']['member_id'] ]
		];
		
		$field = 'ngb.id,ngb.member_id,ngb.browse_time,ngb.sku_id,ngb.category_id,ngb.category_id_1,ngb.category_id_2,ngb.category_id_3,ngs.sku_image,ngs.discount_price,ngs.sku_name,ng.goods_id,ng.goods_name,ng.goods_image';
		$alias = 'ngb';
		$join = [
			[
				'goods ng',
				'ngb.goods_id = ng.goods_id',
				'inner'
			],
			[
				'goods_sku ngs',
				'ngb.sku_id = ngs.sku_id',
				'inner'
			]
		];
		
		$list = $goods_browse_model->getBrowsePageList($condition, $page, $page_size, 'ngb.browse_time desc', $field, $alias, $join);
		return $this->response($list);
	}
	
}