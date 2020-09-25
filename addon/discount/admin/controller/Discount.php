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


namespace addon\discount\admin\controller;

use app\admin\controller\BaseAdmin;
use addon\discount\model\Discount as DiscountModel;

/**
 * 限时折扣控制器
 */
class Discount extends BaseAdmin
{
	
	/**
	 * 限时折扣列表
	 */
	public function lists()
	{
		$discount_model = new DiscountModel();
		if (request()->isAjax()) {
			$page = input('page', 1);
			$page_size = input('page_size', PAGE_LIST_ROWS);
			$discount_name = input('discount_name', '');
			$site_name = input('site_name', '');
			$status = input('status', '');
			
			$condition = [];
			if ($status !== "") {
				$condition[] = [ 'status', '=', $status ];
			}
			$condition[] = [ 'discount_name', 'like', '%' . $discount_name . '%' ];
			$condition[] = [ 'site_name', 'like', '%' . $site_name . '%' ];
			$order = 'create_time desc';
			$field = 'discount_id, discount_name, start_time, end_time, status, create_time, site_id, site_name';
			
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
	 * 限时折扣详情
	 */
	public function detail()
	{
		if (request()->isAjax()) {
			//活动商品
			$discount_id = input('discount_id', 0);
			$discount_model = new DiscountModel();
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
			$site_id = input('site_id', 0);
			$this->assign('discount_id', $discount_id);
			
			//活动详情
			$discount_model = new DiscountModel();
			$discount_info = $discount_model->getDiscountInfo($discount_id, $site_id);
			$this->assign('discount_info', $discount_info['data']);
			
			return $this->fetch("discount/detail");
		}
	}
	
	/**
	 * 关闭活动
	 */
	public function close()
	{
		if (request()->isAjax()) {
			$discount_id = input('discount_id', 0);
			$site_id = input('site_id', 0);
			$this->addLog("强制关闭限时折扣id:" . $discount_id);
			$discount_model = new DiscountModel();
			return $discount_model->closeDiscount($discount_id, $site_id);
		}
	}
	
	/**
	 * 删除活动
	 */
	public function delete()
	{
		if (request()->isAjax()) {
			$discount_id = input('discount_id', 0);
			$site_id = input('site_id', 0);
			$discount_model = new DiscountModel();
			return $discount_model->deleteDiscount($discount_id, $site_id);
		}
	}
}