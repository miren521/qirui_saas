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


namespace addon\fenxiao\admin\controller;

use addon\fenxiao\model\FenxiaoAccount;
use addon\fenxiao\model\FenxiaoApply;
use addon\fenxiao\model\FenxiaoData;
use addon\fenxiao\model\FenxiaoLevel;
use addon\fenxiao\model\FenxiaoLevel as FenxiaoLevelModel;
use addon\fenxiao\model\FenxiaoOrder;
use app\admin\controller\BaseAdmin;
use addon\fenxiao\model\Fenxiao as FenxiaoModel;
use app\model\system\Menu as MenuModel;
use addon\fenxiao\model\Config as ConfigModel;


/**
 *  分销设置
 */
class Fenxiao extends BaseAdmin
{
	/**
	 * 分销概况
	 */
	public function index()
	{
		//获取分销商账户统计
		$fenxiao_data_model = new FenxiaoData();
		$account_data = $fenxiao_data_model->getFenxiaoAccountData();
		$this->assign('account_data', $account_data);
		//累计佣金
		$fenxiao_account = number_format($account_data['account'] + $account_data['account_withdraw'], 2, '.', '');
		$this->assign('fenxiao_account', $fenxiao_account);
		//获取申请人数
		$fenxiao_apply_num = $fenxiao_data_model->getFenxiaoApplyCount();
		$this->assign('fenxiao_apply_num', $fenxiao_apply_num);
		//分销商人数
		$fenxiao_num = $fenxiao_data_model->getFenxiaoCount();
		$this->assign('fenxiao_num', $fenxiao_num);
		return $this->fetch('fenxiao/index');
	}
	
	/**
	 * 分销商列表
	 */
	public function lists()
	{
		$model = new FenxiaoModel();
		if (request()->isAjax()) {
			
			$condition = [];
			
			$fenxiao_name = input('fenxiao_name', '');
			if ($fenxiao_name) {
				$condition[] = [ 'f.fenxiao_name', 'like', '%' . $fenxiao_name . '%' ];
			}
			
			$parent_name = input('parent_name', '');
			if ($parent_name) {
				$condition[] = [ 'pf.fenxiao_name', 'like', '%' . $parent_name . '%' ];
			}
			
			$level_id = input('level_id', '');
			if ($level_id) {
				$condition[] = [ 'f.level_id', '=', $level_id ];
			}
			$start_time = input('start_time', '');
			$end_time = input('end_time', '');
			if ($start_time && $end_time) {
				$condition[] = [ 'f.create_time', 'between', [ date_to_time($start_time), date_to_time($end_time) ] ];
			} elseif (!$start_time && $end_time) {
				$condition[] = [ 'f.create_time', '<=', date_to_time($end_time) ];
				
			} elseif ($start_time && !$end_time) {
				$condition[] = [ 'f.create_time', '>=', date_to_time($start_time) ];
			}
			
			$status = input('status', '');
			if (!empty($status)) {
				$condition[] = [ 'f.status', '=', $status ];
			}
			$page = input('page', 1);
			$page_size = input('page_size', PAGE_LIST_ROWS);
			$list = $model->getFenxiaoPageList($condition, $page, $page_size, 'f.create_time desc');
			return $list;
			
		} else {
			$level_model = new FenxiaoLevel();
			$level_list = $level_model->getLevelList([ [ 'status', '=', 1 ] ], 'level_id,level_name');
			$this->assign('level_list', $level_list['data']);
			
			$config_model = new ConfigModel();
			$basics = $config_model->getFenxiaoBasicsConfig();
			$this->assign("basics_info", $basics['data']['value']);
			$this->forthMenu();
			return $this->fetch('fenxiao/lists');
		}
	}
	
	/**
	 * 详情
	 */
	public function detail()
	{
		$fenxiao_id = input('fenxiao_id', '');
		$model = new FenxiaoModel();
		$fenxiao_leve_model = new FenxiaoLevelModel();
		$condition[] = [ 'f.fenxiao_id', '=', $fenxiao_id ];
		$info = $model->getFenxiaoDetailInfo($condition);
		$fenxiao_level = $fenxiao_leve_model->getLevelInfo([ [ 'level_id', '=', $info['data']['level_id'] ] ]);
		$this->assign('status', $model->fenxiao_status_zh);
		$this->assign('level', $fenxiao_level['data']);
		$this->assign('info', $info['data']);
		
		$this->fiveMenu([ 'fenxiao_id' => $fenxiao_id ]);
		return $this->fetch('fenxiao/fenxiao_detail');
	}
	
	/**
	 * 分销账户信息
	 */
	public function account()
	{
		$model = new FenxiaoModel();
		$fenxiao_id = input('fenxiao_id', '');
		
		$condition[] = [ 'f.fenxiao_id', '=', $fenxiao_id ];
		$info = $model->getFenxiaoDetailInfo($condition);
		$account = $info['data']['account'] - $info['data']['account_withdraw_apply'];
		$info['data']['account'] = number_format($account, 2, '.', '');
		$this->assign('fenxiao_info', $info['data']);
		
		if (request()->isAjax()) {
			
			$account_model = new FenxiaoAccount();
			$page = input('page', 1);
			$status = input('status', '');
			
			$fenxiao_id = input('fenxiao_id', '');
			$list_condition[] = [ 'fenxiao_id', '=', $fenxiao_id ];
			if ($status) {
				if ($status == 1) {
					$list_condition[] = [ 'money', '>', 0 ];
				} else {
					$list_condition[] = [ 'money', '<', 0 ];
				}
			}
			
			$start_time = input('start_time', '');
			$end_time = input('end_time', '');
			if ($start_time && $end_time) {
				$list_condition[] = [ 'create_time', 'between', [ $start_time, $end_time ] ];
			} elseif (!$start_time && $end_time) {
				$list_condition[] = [ 'create_time', '<=', $end_time ];
				
			} elseif ($start_time && !$end_time) {
				$list_condition[] = [ 'create_time', '>=', $start_time ];
			}
			
			$page_size = input('page_size', PAGE_LIST_ROWS);
			$list = $account_model->getFenxiaoAccountPageList($list_condition, $page, $page_size);
			return $list;
		}
		$this->assign('fenxiao_id', $fenxiao_id);
		
		$this->fiveMenu([ 'fenxiao_id' => $fenxiao_id ]);
		return $this->fetch('fenxiao/fenxiao_account');
	}
	
	/**
	 * 订单管理
	 */
	public function order()
	{
		$model = new FenxiaoOrder();
		
		$fenxiao_id = input('fenxiao_id', '');
		if (request()->isAjax()) {
			
			$page_index = input('page', 1);
			$page_size = input('page_size', PAGE_LIST_ROWS);
			$fenxiao_id = input('fenxiao_id', '');
			
			$condition[] = [ 'one_fenxiao_id|two_fenxiao_id|three_fenxiao_id', '=', $fenxiao_id ];
			
			$search_text_type = input('search_text_type', "goods_name");//订单编号/店铺名称/商品名称
			$search_text = input('search_text', "");
			if (!empty($search_text)) {
				$condition[] = [ 'fo.' . $search_text_type, 'like', '%' . $search_text . '%' ];
			}
			
			//下单时间
			$start_time = input('start_time', '');
			$end_time = input('end_time', '');
			if (!empty($start_time) && empty($end_time)) {
				$condition[] = [ 'o.create_time', '>=', date_to_time($start_time) ];
			} elseif (empty($start_time) && !empty($end_time)) {
				$condition[] = [ 'o.create_time', '<=', date_to_time($end_time) ];
			} elseif (!empty($start_time) && !empty(date_to_time($end_time))) {
				$condition[] = [ 'o.create_time', 'between', [ date_to_time($start_time), date_to_time($end_time) ] ];
			}
			
			$list = $model->getFenxiaoOrderPageList($condition, $page_index, $page_size);
			return $list;
			
		} else {
			//订单状态
			$this->assign('fenxiao_id', $fenxiao_id);
			$this->fiveMenu([ 'fenxiao_id' => $fenxiao_id ]);
			return $this->fetch('fenxiao/order_lists');
		}
	}
	
	/**
	 * 订单详情
	 */
	public function orderDetail()
	{
		$fenxiao_order_model = new FenxiaoOrder();
		$fenxiao_order_id = input('fenxiao_order_id', '');
		$order_info = $fenxiao_order_model->getFenxiaoOrderDetail([ [ 'fenxiao_order_id', '=', $fenxiao_order_id ] ]);
		$this->assign('order_info', $order_info['data']);
		return $this->fetch('fenxiao/order_detail');
	}
	
	/**
	 * 冻结
	 */
	public function frozen()
	{
		$fenxiao_id = input('fenxiao_id', '');
		
		$model = new FenxiaoModel();
		
		return $model->frozen($fenxiao_id);
	}
	
	/**
	 * 恢复正常
	 */
	public function unfrozen()
	{
		$fenxiao_id = input('fenxiao_id', '');
		
		$model = new FenxiaoModel();
		
		return $model->unfrozen($fenxiao_id);
	}
	
	
	/**
	 * 分销商申请列表
	 */
	public function apply()
	{
		$model = new FenxiaoApply();
		if (request()->isAjax()) {
			
			$condition[] = [ 'status', '=', 1 ];
			
			$fenxiao_name = input('fenxiao_name', '');
			if ($fenxiao_name) {
				$condition[] = [ 'fenxiao_name', 'like', '%' . $fenxiao_name . '%' ];
			}
			$mobile = input('mobile', '');
			if ($mobile) {
				$condition[] = [ 'mobile', 'like', '%' . $mobile . '%' ];
			}
			$level_id = input('level_id', '');
			if ($level_id) {
				$condition[] = [ 'level_id', '=', $level_id ];
			}
			$create_start_time = input('create_start_time', '');
			$create_end_time = input('create_end_time', '');
			if ($create_start_time && $create_end_time) {
				$condition[] = [ 'create_time', 'between', [ strtotime($create_start_time), strtotime($create_end_time) ] ];
			} elseif (!$create_start_time && $create_end_time) {
				$condition[] = [ 'create_time', '<=', strtotime($create_end_time) ];
				
			} elseif ($create_start_time && !$create_end_time) {
				$condition[] = [ 'create_time', '>=', strtotime($create_start_time) ];
			}
			
			$rg_start_time = input('rg_start_time', '');
			$rg_end_time = input('rg_end_time', '');
			if ($rg_start_time && $rg_end_time) {
				$condition[] = [ 'reg_time', 'between', [ strtotime($rg_start_time), strtotime($rg_end_time) ] ];
			} elseif (!$rg_start_time && $rg_end_time) {
				$condition[] = [ 'reg_time', '<=', strtotime($rg_end_time) ];
				
			} elseif ($rg_start_time && !$rg_end_time) {
				$condition[] = [ 'reg_time', '>=', strtotime($rg_start_time) ];
			}
			
			$page = input('page', 1);
			$page_size = input('page_size', PAGE_LIST_ROWS);
			$list = $model->getFenxiaoApplyPageList($condition, $page, $page_size, 'create_time desc', '*');
			return $list;
			
		} else {
			
			$level_model = new FenxiaoLevel();
			$level_list = $level_model->getLevelList([ [ 'status', '=', 1 ] ], 'level_id,level_name');
			$this->assign('level_list', $level_list['data']);
			
			$this->forthMenu();
			return $this->fetch('fenxiao/apply');
		}
	}
	
	/**
	 * 分销商申请通过
	 */
	public function applyPass()
	{
		$apply_id = input('apply_id');
		
		$model = new FenxiaoApply();
		$res = $model->pass($apply_id);
		return $res;
	}
	
	/**
	 * 分销商申请通过
	 */
	public function applyRefuse()
	{
		$apply_id = input('apply_id');
		
		$model = new FenxiaoApply();
		$res = $model->refuse($apply_id);
		return $res;
	}
	
	
	/**
	 * 四级菜单
	 * @param unknown $params
	 */
	protected function fiveMenu($params = [])
	{
		$url = strtolower($this->url);
		$menu_model = new MenuModel();
		$menu_info = $menu_model->getMenuInfo([ [ 'url', "=", $url ], [ 'level', '=', 5 ] ], 'parent');
		
		if (!empty($menu_info['data'])) {
			$menus = $menu_model->getMenuList([ [ 'app_module', "=", $this->app_module ], [ 'is_show', "=", 1 ], [ 'parent', '=', $menu_info['data']['parent'] ] ], '*', 'sort asc');
			foreach ($menus['data'] as $k => $v) {
				$menus['data'][ $k ]['parse_url'] = addon_url($menus['data'][ $k ]['url'], $params);
				if ($menus['data'][ $k ]['url'] == $url) {
					$menus['data'][ $k ]['selected'] = 1;
				} else {
					$menus['data'][ $k ]['selected'] = 0;
				}
			}
			$this->assign('forth_menu', $menus['data']);
		}
	}

    /**
     * 分销商下级团队
     */
    public function team()
    {
        $fenxiao_id = input('fenxiao_id', 0);
        $fenxiao_model = new FenxiaoModel();
        if (request()->isAjax()) {

            $condition[] = [ 'status', '=', 1 ];

            $level = input('level', 0);


            $page = input('page', 1);
            $page_size = input('page_size', PAGE_LIST_ROWS);
            $list = $fenxiao_model->getFenxiaoTeam($level, $fenxiao_id, $page, $page_size);
            return $list;

        } else {
            $this->assign('fenxiao_id', $fenxiao_id);
            $this->fiveMenu([ 'fenxiao_id' => $fenxiao_id ]);
            return $this->fetch('fenxiao/team');
        }
    }


}