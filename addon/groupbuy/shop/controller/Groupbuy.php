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


namespace addon\groupbuy\shop\controller;

use app\model\goods\Goods as GoodsModel;
use app\model\shop\Shop as ShopModel;
use app\shop\controller\BaseShop;
use addon\groupbuy\model\Groupbuy as GroupbuyModel;

/**
 * 团购控制器
 */
class Groupbuy extends BaseShop
{
	
	/*
	 *  团购活动列表
	 */
	public function lists()
	{
		$model = new GroupbuyModel();
		$goods_name = input('goods_name', '');
		
		$condition[] = [ 'site_id', '=', $this->site_id ];
		//获取续签信息
		if (request()->isAjax()) {
			$status = input('status', '');//团购状态
			if ($status) {
				
				$condition[] = [ 'status', '=', $status ];
			}
			$condition[] = [ 'goods_name', 'like', '%' . $goods_name . '%' ];
			$page = input('page', 1);
			$page_size = input('page_size', PAGE_LIST_ROWS);
			$list = $model->getGroupbuyPageList($condition, $page, $page_size, 'groupbuy_id desc');
			return $list;
		} else {
			
			$list = $model->getgroupbuyPageList($condition, 1, PAGE_LIST_ROWS, 'groupbuy_id desc');
			$this->assign('list', $list);
		}
		return $this->fetch("groupbuy/lists");
	}
	
	/**
	 * 添加活动
	 */
	public function add()
	{
		if (request()->isAjax()) {
			$site_model = new ShopModel();
			$site_info = $site_model->getShopInfo([ [ 'site_id', '=', $this->site_id ] ], 'site_name');
			//获取商品信息
			$goods_id = input('goods_id', '');
			$goods_model = new GoodsModel();
			$goods = $goods_model->getGoodsInfo([ [ 'goods_id', '=', $goods_id ] ], 'goods_name,goods_image,price');
			$goods_info = $goods['data'];
			$groupbuy_data = [
				'site_id' => $this->site_id,
				'site_name' => $site_info['data']['site_name'],
				'goods_id' => $goods_id,
				'goods_name' => $goods_info['goods_name'],
				'goods_image' => $goods_info['goods_image'],
				'goods_price' => $goods_info['price'],
				'sku_id' => input('sku_id', ''),
				'groupbuy_price' => input('groupbuy_price', ''),
				'buy_num' => input('buy_num', ''),
				'start_time' => strtotime(input('start_time', '')),
				'end_time' => strtotime(input('end_time', '')),
			];
			
			$groupbuy_model = new GroupbuyModel();
			return $groupbuy_model->addGroupbuy($groupbuy_data);
		} else {
			return $this->fetch("groupbuy/add");
		}
	}
	
	/**
	 * 编辑活动
	 */
	public function edit()
	{
		$groupbuy_model = new GroupbuyModel();
		
		if (request()->isAjax()) {
			//获取商品信息
			$goods_id = input('goods_id', '');
			$goods_model = new GoodsModel();
			$goods = $goods_model->getGoodsInfo([ [ 'goods_id', '=', $goods_id ] ], 'goods_name,goods_image,price');
			$goods_info = $goods['data'];
			$groupbuy_data = [
				'goods_id' => $goods_id,
				'goods_name' => $goods_info['goods_name'],
				'goods_image' => $goods_info['goods_image'],
				'goods_price' => $goods_info['price'],
				'sku_id' => input('sku_id', ''),
				'groupbuy_price' => input('groupbuy_price', ''),
				'buy_num' => input('buy_num', ''),
				'start_time' => strtotime(input('start_time', '')),
				'end_time' => strtotime(input('end_time', '')),
			];
			
			$groupbuy_id = input('groupbuy_id', '');
			
			return $groupbuy_model->editGroupbuy($groupbuy_id, $this->site_id, $groupbuy_data);
			
		} else {
			$groupbuy_id = input('groupbuy_id', '');
			//获取团购信息
			$groupbuy_info = $groupbuy_model->getGroupbuyInfo([ [ 'groupbuy_id', '=', $groupbuy_id ] ]);
			$this->assign('groupbuy_info', $groupbuy_info);
			return $this->fetch("groupbuy/edit");
		}
	}
	
	/*
	 *  团购详情
	 */
	public function detail()
	{
		$groupbuy_model = new GroupbuyModel();
		
		$groupbuy_id = input('groupbuy_id', '');
		//获取团购信息
		$groupbuy_info = $groupbuy_model->getGroupbuyInfo([ [ 'groupbuy_id', '=', $groupbuy_id ] ]);
		$this->assign('groupbuy_info', $groupbuy_info);
		return $this->fetch("groupbuy/detail");
	}
	
	/*
	 *  删除团购活动
	 */
	public function delete()
	{
		$groupbuy_id = input('groupbuy_id', '');
		$site_id = $this->site_id;
		
		$groupbuy_model = new GroupbuyModel();
		return $groupbuy_model->deleteGroupbuy($groupbuy_id, $site_id);
	}
	
	/*
	 *  结束团购活动
	 */
	public function finish()
	{
		$groupbuy_id = input('groupbuy_id', '');
		$site_id = $this->site_id;
		
		$groupbuy_model = new GroupbuyModel();
		return $groupbuy_model->finishGroupbuy($groupbuy_id, $site_id);
	}
	
}