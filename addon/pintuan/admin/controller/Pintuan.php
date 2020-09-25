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


namespace addon\pintuan\admin\controller;

use addon\pintuan\model\PintuanOrder;
use app\admin\controller\BaseAdmin;
use addon\pintuan\model\Pintuan as PintuanModel;
use addon\pintuan\model\PintuanGroup as PintuanGroupModel;

/**
 * 限时折扣控制器
 */
class Pintuan extends BaseAdmin
{
	
	/*
	 *  拼团活动列表
	 */
	public function lists()
	{
		$model = new PintuanModel();
		$condition = [];
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
			
			$goods_name = input('goods_name', '');
			if ($goods_name) {
				$condition[] = [ 'g.goods_name', 'like', '%' . $goods_name . '%' ];
			}
			
			$start_time = input('start_time', '');
			$end_time = input('end_time', '');
			if (!empty($start_time) && empty($end_time)) {
				$condition[] = [ "p.start_time", ">=", date_to_time($start_time) ];
			} elseif (empty($start_time) && !empty($end_time)) {
				$condition[] = [ "p.end_time", "<=", date_to_time($end_time) ];
			} elseif (!empty($start_time) && !empty($end_time)) {
				$condition[] = [ "p.start_time", ">=", date_to_time($start_time) ];
				$condition[] = [ "p.end_time", "<=", date_to_time($end_time) ];
			}
			
			$site_name = input('site_name', '');
			if ($site_name) {
				$condition[] = [ 'p.site_name', 'like', '%' . $site_name . '%' ];
			}
			
			$page = input('page', 1);
			$page_size = input('page_size', PAGE_LIST_ROWS);
			$list = $model->getPintuanPageList($condition, $page, $page_size, 'p.pintuan_id desc');
			return $list;
		} else {
			$page_size = input('page_size', PAGE_LIST_ROWS);
			
			$list = $model->getPintuanPageList($condition, 1, $page_size, 'p.pintuan_id desc');
			$this->assign('list', $list);
		}
		return $this->fetch("pintuan/lists");
	}
	
	/*
	 *  开团团队列表
	 */
	public function group()
	{
		$model = new PintuanGroupModel();
		$pintuan_id = input('pintuan_id', '');
		
		$condition[] = [ 'pg.pintuan_id', '=', $pintuan_id ];
		
		$condition = [];
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
			
			$list = $model->getPintuanGroupPageList($pintuan_id, $condition, 1, PAGE_LIST_ROWS, 'pg.group_id desc');
			$this->assign('list', $list);
		}
		
		return $this->fetch("pintuan/group");
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
			$list = $model->getPintuanOrderPageList($condition, 1, PAGE_LIST_ROWS, 'ppo.id desc');
			$this->assign('list', $list);
			
			return $this->fetch("pintuan/group_order");
		}
	}
	
}