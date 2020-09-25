<?php

namespace app\api\controller;

use app\model\goods\GoodsCollect as GoodsCollectModel;
use app\model\shop\ShopMember as ShopMemberModel;

/**
 * 商品收藏
 * @author Administrator
 *
 */
class Goodscollect extends BaseApi
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
		$sku_name = isset($this->params['sku_name']) ? $this->params['sku_name'] : '';
		$sku_price = isset($this->params['sku_price']) ? $this->params['sku_price'] : '';
		$sku_image = isset($this->params['sku_image']) ? $this->params['sku_image'] : '';
		
		if (empty($goods_id)) {
			return $this->response($this->error('', 'REQUEST_GOODS_ID'));
		}
		if (empty($sku_id)) {
			return $this->response($this->error('', 'REQUEST_SKU_ID'));
		}
		$goods_collect_model = new GoodsCollectModel();
		$data = [
			'member_id' => $token['data']['member_id'],
			'goods_id' => $goods_id,
			'sku_id' => $sku_id,
			'category_id' => $category_id,
			'sku_name' => $sku_name,
			'sku_price' => $sku_price,
			'sku_image' => $sku_image,
			'site_id' => $site_id
		];
		$res = $goods_collect_model->addCollect($data);
		
		//添加店铺关注记录
		$shop_member_model = new ShopMemberModel();
		$shop_member_model->addShopMember($site_id, $token['data']['member_id'], 0);
		return $this->response($res);
	}
	/**
	 * 删除信息
	 */
	public function delete()
	{
		$token = $this->checkToken();
		if ($token['code'] < 0) return $this->response($token);
		
		$goods_id = isset($this->params['goods_id']) ? $this->params['goods_id'] : 0;
		if (empty($goods_id)) {
			return $this->response($this->error('', 'REQUEST_GOODS_ID'));
		}
		$goods_collect_model = new GoodsCollectModel();
		$res = $goods_collect_model->deleteCollect($token['data']['member_id'], $goods_id);
		return $this->response($res);
		
	}
	
	/**
	 * 分页列表信息
	 */
	public function page()
	{
		$token = $this->checkToken();
		if ($token['code'] < 0) return $this->response($token);
		
		$page = isset($this->params['page']) ? $this->params['page'] : 1;
		$page_size = isset($this->params['page_size']) ? $this->params['page_size'] : PAGE_LIST_ROWS;
		$goods_collect_model = new GoodsCollectModel();
		$condition = [
			[ 'member_id', '=', $token['data']['member_id'] ],
		];
		
		$list = $goods_collect_model->getCollectPageList($condition, $page, $page_size);
		return $this->response($list);
		
	}
	
	/**
	 * 是否收藏
	 * @return string
	 */
	public function iscollect()
	{
		$token = $this->checkToken();
		if ($token['code'] < 0) return $this->response($token);
		
		$goods_id = isset($this->params['goods_id']) ? $this->params['goods_id'] : 0;
		if (empty($goods_id)) {
			return $this->response($this->error('', 'REQUEST_GOODS_ID'));
		}
		
		$goods_collect_model = new GoodsCollectModel();
		$res = $goods_collect_model->getIsCollect($goods_id, $token['data']['member_id']);
		return $this->response($res);
	}
	
}