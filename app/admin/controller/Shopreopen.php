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


namespace app\admin\controller;

use app\model\shop\ShopReopen as ShopReopenModel;
use app\model\shop\ShopGroup as ShopGroupModel;
use app\model\shop\ShopCategory as ShopCategoryModel;
use app\model\web\WebSite;

/**
 * 店铺续签
 */
class Shopreopen extends BaseAdmin
{
	/******************************* 商家续签列表及相关操作 ***************************/
	
	/**
	 * 商家申请续签列表
	 */
	public function reopen()
	{
		$model = new ShopReopenModel();
		$condition = [];
		if (request()->isAjax()) {
			$page = input('page', 1);
			$page_size = input('page_size', PAGE_LIST_ROWS);
			$site_name = input('site_name', '');//店铺名称
			$category_id = input('category_id', '');//店铺类别id
			$group_id = input('group_id', '');//店铺等级id
			
			$site_id = input('site_id', '');//店铺等级id
			if ($site_id) {
				$condition[] = [ 'r.site_id', '=', $site_id ];
			}
			
			$apply_state = input('apply_state', '');
			if ($apply_state) {
				$condition[] = [ 'r.apply_state', '=', $apply_state ];
			}
			
			if ($site_name) {
				$condition[] = [ 's.site_name', 'like', '%' . $site_name . '%' ];
			}
			if ($category_id) {
				$condition[] = [ 's.category_id', '=', $category_id ];
			}
			if ($group_id) {
				$condition[] = [ 'r.shop_group_id', '=', $group_id ];
			}
			return $model->getApplyReopenPageList($condition, $page, $page_size);
			
		} else {
			$reopen_list = $model->getApplyReopenPageList($condition, 1, PAGE_LIST_ROWS);
			$this->assign('reopen_list', $reopen_list);
			
			//商家分类
			$shop_category_model = new ShopCategoryModel();
			$shop_category_list = $shop_category_model->getCategoryList([], 'category_id, category_name', 'sort asc');
			$this->assign('shop_category_list', $shop_category_list['data']);
			
			//店铺等级
			$shop_group_model = new ShopGroupModel();
			$shop_group_list = $shop_group_model->getGroupList([['is_own','=',0]], 'group_id,is_own,group_name,fee,remark', 'is_own asc,fee asc');
			$this->assign('shop_group_list', $shop_group_list['data']);

            $is_addon_city = addon_is_exit('city');
            $this->assign('is_addon_city',$is_addon_city);

            if($is_addon_city == 1){
                $website_model = new WebSite();
                $website_list = $website_model->getWebsiteList([],'site_id,site_area_name');
                $this->assign('website_list',$website_list['data']);
            }
			return $this->fetch('shopreopen/reopen');
		}
	}
	
	//查看续签详情
	public function reopendetail()
	{
		$model = new ShopReopenModel();
		$id = input('id', '');
		
		$info = $model->getReopenInfo([ [ 'id', '=', $id ] ]);
		$this->assign('info', $info);
		return $this->fetch('shopreopen/reopendetail');
	}
	
	/**
	 * 申请续签通过
	 */
	public function pass()
	{
		if (request()->isAjax()) {
			$model = new ShopReopenModel();
			
			$id = input('id', '');
			$site_id = input('site_id', '');
			$this->addLog("店铺续签审核通过站点id:" . $site_id);
			$result = $model->pass($id, $site_id);
			return $result;
		}
	}
	
	/**
	 * 申请续签失败
	 */
	public function fail()
	{
		if (request()->isAjax()) {
			$model = new ShopReopenModel();
			
			$id = input('id', '');
			$reason = input('reason', '');//拒绝原因
			$this->addLog("店铺续签审核拒绝id:" . $id);
			$result = $model->refuse($id, $reason);
			return $result;
		}
	}
	
}