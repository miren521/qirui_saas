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


namespace addon\pintuan\shop\controller;

use addon\pintuan\model\PintuanOrder;
use app\model\shop\Shop as ShopModel;
use app\shop\controller\BaseShop;
use addon\pintuan\model\Pintuan as PintuanModel;
use addon\pintuan\model\PintuanGroup as PintuanGroupModel;

/**
 * 拼团控制器
 */
class Pintuan extends BaseShop
{
	
	/*
	 *  拼团活动列表
	 */
	public function lists()
	{
		$model = new PintuanModel();
		$condition[] = [ 'p.site_id', '=', $this->site_id ];
		//获取续签信息
		if (request()->isAjax()) {
			$status = input('status', '');//拼团状态
			if ($status) {
				if ($status == 6) {
					$condition[] = [ 'p.status', '=', 0 ];
				} else {
					$condition[] = [ 'p.status', '=', $status ];
				}
				
			}
			$pintuan_name = input('pintuan_name', '');
			if ($pintuan_name) {
				$condition[] = [ 'p.pintuan_name', 'like', '%' . $pintuan_name . '%' ];
			}
			$page = input('page', 1);
			$page_size = input('page_size', PAGE_LIST_ROWS);
			$list = $model->getPintuanPageList($condition, $page, $page_size, 'p.pintuan_id desc');
			return $list;
		} else {
			$page_size = input('page_size', PAGE_LIST_ROWS);
			
			$list = $model->getPintuanPageList($condition, 1, $page_size, 'p.pintuan_id desc');
			$this->assign('list', $list);
			
			$this->forthMenu();
		}
		return $this->fetch("pintuan/lists");
	}
	
	/**
	 * 添加活动
	 */
	public function add()
	{
		if (request()->isAjax()) {
			$site_model = new ShopModel();
			$site_info = $site_model->getShopInfo([ [ 'site_id', '=', $this->site_id ] ], 'site_name');
			$pintuan_data = [
				'site_id' => $this->site_id,
				'site_name' => $site_info['data']['site_name'],
				'pintuan_name' => input('pintuan_name', ''),//活动名称
				'goods_id' => input('goods_id', ''),//商品ID
				'is_virtual_goods' => input('is_virtual_goods', ''),//是否是虚拟商品
				'pintuan_num' => input('pintuan_num', ''),//参团人数
				'pintuan_time' => input('pintuan_time', ''),//拼团有效期
				'remark' => input('remark', ''),//备注
				'is_recommend' => input('is_recommend', ''),//是否推荐
				'start_time' => input('start_time', ''),//开始时间
				'end_time' => input('end_time', ''),//结束时间
				'buy_num' => input('buy_num', ''),//拼团限制购买
				'is_single_buy' => input('is_single_buy', ''),//是否单独购买
				'is_virtual_buy' => input('is_virtual_buy', ''),//是否虚拟成团
				'is_promotion' => input('is_promotion', ''),//是否团长优惠
			];
			
			$sku_list = input('sku_list', '');
			$pintuan_model = new PintuanModel();
			return $pintuan_model->addPintuan($pintuan_data, $sku_list);
		} else {
			return $this->fetch("pintuan/add");
		}
	}
	
	/**
	 * 编辑活动
	 */
	public function edit()
	{
		$pintuan_model = new PintuanModel();
		$site_id = $this->site_id;
		if (request()->isAjax()) {
			$pintuan_data = [
				'site_id' => $site_id,
				'pintuan_name' => input('pintuan_name', ''),//活动名称
				'goods_id' => input('goods_id', ''),//商品ID
				'is_virtual_goods' => input('is_virtual_goods', ''),//是否是虚拟商品
				'pintuan_num' => input('pintuan_num', ''),//参团人数
				'pintuan_time' => input('pintuan_time', ''),//拼团有效期
				'remark' => input('remark', ''),//备注
				'is_recommend' => input('is_recommend', ''),//是否推荐
				'start_time' => input('start_time', ''),//开始时间
				'end_time' => input('end_time', ''),//结束时间
				'buy_num' => input('buy_num', ''),//拼团限制购买
				'is_single_buy' => input('is_single_buy', ''),//是否单独购买
				'is_virtual_buy' => input('is_virtual_buy', ''),//是否虚拟成团
				'is_promotion' => input('is_promotion', ''),//是否团长优惠
			];
			
			$pintuan_id = input('pintuan_id', '');
			$sku_list = input('sku_list', '');
			
			return $pintuan_model->editPintuan($pintuan_id, $site_id, $pintuan_data, $sku_list);
			
		} else {
			$pintuan_id = input('pintuan_id', '');
			//获取拼团信息
			$pintuan_info = $pintuan_model->getPintuanDetail($pintuan_id);
			$this->assign('pintuan_info', $pintuan_info);
			return $this->fetch("pintuan/edit");
		}
	}
	
	/*
	 *  拼团详情
	 */
	public function detail()
	{
		$pintuan_model = new PintuanModel();
		
		$pintuan_id = input('pintuan_id', '');
		//获取拼团信息
		$pintuan_info = $pintuan_model->getPintuanDetail($pintuan_id);
		$this->assign('pintuan_info', $pintuan_info);
		return $this->fetch("pintuan/detail");
	}
	
	/*
	 *  删除拼团活动
	 */
	public function delete()
	{
		$pintuan_id = input('pintuan_id', '');
		$site_id = $this->site_id;
		
		$pintuan_model = new PintuanModel();
		return $pintuan_model->deletePintuan($pintuan_id, $site_id);
	}
	
	/*
	 *  拼团活动失效
	 */
	public function invalid()
	{
		$pintuan_id = input('pintuan_id', '');
		$site_id = $this->site_id;
		
		$pintuan_model = new PintuanModel();
		return $pintuan_model->invalidPintuan($pintuan_id, $site_id);
	}
	
	/**********************************  开团团队    ******************************************************/
	
	/*
	 *  开团团队列表
	 */
	public function group()
	{
		$model = new PintuanGroupModel();

        $condition[] = [ 'pg.site_id', '=', $this->site_id ];
		$pintuan_id = input('pintuan_id', '');
		if ($pintuan_id) {
			$condition[] = [ 'pg.pintuan_id', '=', $pintuan_id ];
		}
		//获取续签信息
		if (request()->isAjax()) {
			
			$status = input('status', '');//拼团状态
			if ($status) {
				if ($status == 6) {
					$condition[] = [ 'pg.status', '=', 0 ];
				} else {
					$condition[] = [ 'pg.status', '=', $status ];
				}
			}
			$page = input('page', 1);
			$page_size = input('page_size', PAGE_LIST_ROWS);
			$list = $model->getPintuanGroupPageList($pintuan_id, $condition, $page, $page_size, 'pg.group_id desc');
			return $list;
			
		} else {
			
			$page_size = input('page_size', PAGE_LIST_ROWS);
			$list = $model->getPintuanGroupPageList($pintuan_id, $condition, 1, $page_size, 'pg.group_id desc');
			$this->assign('list', $list);
			
			$this->forthMenu();
			return $this->fetch("pintuan/group");
		}
		
	}
	
	/*
	 *  拼团组成员订单列表
	 */
	public function groupOrder()
	{
		$model = new PintuanOrder();
		
		$condition = [];
		$condition[] = [ 'pintuan_status', 'in', '2,3' ];
		$group_id = input('group_id', '');
		if ($group_id) {
			$condition[] = [ 'ppo.group_id', '=', $group_id ];
		}
		//获取续签信息
		if (request()->isAjax()) {
			
			$page = input('page', 1);
			$page_size = input('page_size', PAGE_LIST_ROWS);
			$list = $model->getPintuanOrderPageList($condition, $page, $page_size, 'ppo.id desc');
			return $list;
			
		} else {
            $this->assign('group_id', $group_id);
			
			return $this->fetch("pintuan/group_order");
		}
	}
	
}