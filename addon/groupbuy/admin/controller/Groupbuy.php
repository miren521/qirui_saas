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


namespace addon\groupbuy\admin\controller;

use app\admin\controller\BaseAdmin;

use addon\groupbuy\model\Groupbuy as GroupbuyModel;

/**
 * 团购控制器
 */
class Groupbuy extends BaseAdmin
{
	/*
	 *  团购活动列表
	 */
	public function lists()
	{
		
		$model = new GroupbuyModel();
		$goods_name = input('goods_name', '');
		$site_name = input('site_name', '');
		$condition = [];
		
		//获取续签信息
		if (request()->isAjax()) {
			$status = input('status', '');//团购状态
			if ($status) {
				
				$condition[] = [ 'status', '=', $status ];
			}
			$condition[] = [ 'goods_name', 'like', '%' . $goods_name . '%' ];
			$condition[] = [ 'site_name', 'like', '%' . $site_name . '%' ];
			$page = input('page', 1);
			$page_size = input('page_size', PAGE_LIST_ROWS);
			$list = $model->getGroupbuyPageList($condition, $page, $page_size, 'groupbuy_id desc');
			return $list;
		} else {
			
			$list = $model->getGroupbuyPageList($condition, 1, PAGE_LIST_ROWS, 'groupbuy_id desc');
			$this->assign('list', $list);
		}
		return $this->fetch("groupbuy/lists");
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
	
	/**
	 * 删除团购
	 */
	public function delete()
	{
		if (request()->isAjax()) {
			$groupbuy_id = input('groupbuy_id', '');
			$site_id = input('site_id', '');
			$this->addLog("删除团购id:" . $groupbuy_id);
			$groupbuy_model = new GroupbuyModel();
			return $groupbuy_model->deleteGroupbuy($groupbuy_id, $site_id);
		}
	}
	
	/**
	 * 结束团购
	 */
	public function close()
	{
		if (request()->isAjax()) {
			$groupbuy_id = input('groupbuy_id', 0);
			$site_id = input('site_id', '');
			$this->addLog("结束团购id:" . $groupbuy_id);
			$groupbuy_model = new GroupbuyModel();
			return $groupbuy_model->finishGroupbuy($groupbuy_id, $site_id);
		}
	}
	
}