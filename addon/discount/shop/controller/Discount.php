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


namespace addon\discount\shop\controller;


use app\shop\controller\BaseShop;
use addon\discount\model\Discount as DiscountModel;

/**
 * 限时折扣控制器
 */
class Discount extends BaseShop
{
	/**
	 * 添加活动
	 */
	public function add()
	{
		if (request()->isAjax()) {
			$data = [
				'discount_name' => input('discount_name', ''),
				'remark' => input('remark', ''),
				'start_time' => strtotime(input('start_time', '')),
				'end_time' => strtotime(input('end_time', '')),
				'site_id' => $this->site_id,
			];
			
			$discount_model = new DiscountModel();
			return $discount_model->addDiscount($data);
		} else {
			return $this->fetch("discount/add");
		}
	}
	
	/**
	 * 编辑活动
	 */
	public function edit()
	{
		$discount_model = new DiscountModel();
		if (request()->isAjax()) {
			$data = [
				'discount_name' => input('discount_name', ''),
				'remark' => input('remark', ''),
				'start_time' => strtotime(input('start_time', '')),
				'end_time' => strtotime(input('end_time', '')),
				'discount_id' => input('discount_id', 0),
				'site_id' => $this->site_id,
			];
			
			return $discount_model->editDiscount($data);
			
		} else {
			$discount_id = input('discount_id', 0);
			$this->assign('discount_id', $discount_id);
			
			$discount_info = $discount_model->getDiscountInfo($discount_id, $this->site_id);
			$this->assign('discount_info', $discount_info['data']);
			
			return $this->fetch("discount/edit");
		}
		
	}
	
	/**
	 * 限时折扣详情
	 */
	public function detail()
	{
		$discount_model = new DiscountModel();
		if (request()->isAjax()) {
			//活动商品
			$discount_id = input('discount_id', 0);
			$list = $discount_model->getDiscountGoods($discount_id);
			foreach ($list['data'] as $key => $val) {
				if ($val['price'] != 0) {
					$discount_rate = floor($val['discount_price'] / $val['price'] * 100);
				} else {
					$discount_rate = 100;
				}
				$list['data'][ $key ]['discount_rate'] = $discount_rate;
			}
			return $list;
		} else {
			$discount_id = input('discount_id', 0);
			$site_id = $this->site_id;
			$this->assign('discount_id', $discount_id);
			
			//活动详情
			$discount_info = $discount_model->getDiscountInfo($discount_id, $site_id);
			$this->assign('discount_info', $discount_info['data']);
			
			return $this->fetch("discount/detail");
		}
	}
	
	/**
	 * 活动列表
	 */
	public function lists()
	{
		$discount_model = new DiscountModel();
		if (request()->isAjax()) {
			$page = input('page', 1);
			$page_size = input('page_size', PAGE_LIST_ROWS);
			$discount_name = input('discount_name', '');
			$status = input('status', '');
			
			$condition = [];
			if ($status !== "") {
				$condition[] = [ 'status', '=', $status ];
			}
			$condition[] = [ 'site_id', '=', $this->site_id ];
			$condition[] = [ 'discount_name', 'like', '%' . $discount_name . '%' ];
			$order = 'create_time desc';
			$field = 'discount_id, discount_name, start_time, end_time, status, create_time';
			
			$discount_status_arr = $discount_model->getDiscountStatus();
			$res = $discount_model->getDiscountPageList($condition, $page, $page_size, $order, $field);
			foreach ($res['data']['list'] as $key => $val) {
				$res['data']['list'][ $key ]['status_name'] = $discount_status_arr[ $val['status'] ];
			}
			return $res;
			
		} else {
			//限时折扣状态
			$discount_status_arr = $discount_model->getDiscountStatus();
			$this->assign('discount_status_arr', $discount_status_arr);
			
			return $this->fetch("discount/lists");
		}
	}
	
	/**
	 * 关闭活动
	 */
	public function close()
	{
		if (request()->isAjax()) {
			$discount_id = input('discount_id', 0);
			$discount_model = new DiscountModel();
			return $discount_model->closeDiscount($discount_id, $this->site_id);
		}
	}
	
	/**
	 * 删除活动
	 */
	public function delete()
	{
		if (request()->isAjax()) {
			$discount_id = input('discount_id', 0);
			$discount_model = new DiscountModel();
			return $discount_model->deleteDiscount($discount_id, $this->site_id);
		}
	}
	
	/**
	 * 限时折扣商品管理
	 */
	public function manage()
	{
		$discount_model = new DiscountModel();
		if (request()->isAjax()) {
			//限时折扣商品列表
			$discount_id = input('discount_id', 0);
			
			$res = $discount_model->getDiscountGoods($discount_id);
			foreach ($res['data'] as $key => $val) {
				if ($val['price'] != 0) {
					$discount_rate = floor($val['discount_price'] / $val['price'] * 100);
				} else {
					$discount_rate = 100;
				}
				$res['data'][ $key ]['discount_rate'] = $discount_rate;
			}
			return $res;
		} else {
			$discount_id = input('discount_id', 0);
			$this->assign('discount_id', $discount_id);
			
			//活动详情
			$discount_info = $discount_model->getDiscountInfo($discount_id, $this->site_id);
			$this->assign('discount_info', $discount_info['data']);
			
			return $this->fetch("discount/manage");
		}
	}
	
	/**
	 * 添加商品
	 */
	public function addGoods()
	{
		if (request()->isAjax()) {
			$sku_ids = input('sku_ids', '');
			$discount_id = input('discount_id', 0);
			$discount_model = new DiscountModel();
			return $discount_model->addDiscountGoods($discount_id, $this->site_id, $sku_ids);
		}
	}
	
	/**
	 * 修改商品（价格）
	 */
	public function updateGoods()
	{
		if (request()->isAjax()) {
			$discount_id = input('discount_id', 0);
			$sku_id = input('sku_id', '');
			$discount_price = input('discount_price', 0.00);
			$discount_model = new DiscountModel();
			return $discount_model->updateDiscountGoods($discount_id, $sku_id, $this->site_id, $discount_price);
		}
	}
	
	/**
	 * 删除商品
	 */
	public function deleteGoods()
	{
		if (request()->isAjax()) {
			$discount_id = input('discount_id', 0);
			$sku_id = input('sku_id', '');
			$discount_model = new DiscountModel();
			return $discount_model->deleteDiscountGoods($discount_id, $sku_id, $this->site_id);
		}
	}
}