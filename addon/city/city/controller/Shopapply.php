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


namespace addon\city\city\controller;

use app\model\shop\ShopCategory as ShopCategoryModel;
use app\model\shop\ShopGroup as ShopGroupModel;
use app\model\shop\ShopApply as ShopApplyModel;

/**
 * 商家申请控制器
 */
class Shopapply extends BaseCity
{
	/******************************* 商家申请列表及相关操作 ***************************/
	
	/**
	 * 商家申请
	 */
	public function apply()
	{
		if (request()->isAjax()) {
			$page = input('page', 1);
			$page_size = input('page_size', PAGE_LIST_ROWS);
			$search_text = input('search_text', '');
			$search_text_user = input('search_text_user', '');
			$category_id = input('category_id', 0);
			$group_id = input('group_id', 0);
			$apply_state = input('apply_state', 0);
			$start_time = input("start_time", '');
			$end_time = input("end_time", '');

            $condition[] = ['website_id','=',$this->site_id];

            $site_id = input('site_id', '');
			if ($site_id) {
				$condition[] = [ 'site_id', '=', $site_id ];
			}
			
			$condition[] = [ 'shop_name', 'like', '%' . $search_text . '%' ];
			$condition[] = [ 'username', 'like', '%' . $search_text_user . '%' ];
			if ($category_id != 0) {
				$condition[] = [ 'category_id', '=', $category_id ];
			}
			if ($group_id != 0) {
				$condition[] = [ 'group_id', '=', $group_id ];
			}
			if ($apply_state != 0) {
			    if ($apply_state == 4) {
			        $condition[] = ['apply_state', '=', '2'];
			        $condition[] = ['paying_money_certificate', '=', ''];
                } else {
                    $condition[] = [ 'apply_state', '=', $apply_state ];
                }
			}
			if (!empty($start_time) && empty($end_time)) {
				$condition[] = [ 'create_time', '>=', date_to_time($start_time) ];
			} elseif (empty($start_time) && !empty($end_time)) {
				$condition[] = [ "create_time", "<=", date_to_time($end_time) ];
			} elseif (!empty($start_time) && !empty($end_time)) {
				$condition[] = [ 'create_time', 'between', [ date_to_time($start_time), date_to_time($end_time) ] ];
			}
			
			$order = 'create_time desc';
			//申请会员 店铺名称 申请状态名称  拒绝理由（apply_message） 入驻时长（apply_year）  分类名称（category_name） 分组名称（group_name）create_time(申请时间)
			$field = 'apply_id,member_id,member_name,cert_id,shop_name,apply_state,apply_message,apply_year,category_name,paying_money_certificate,group_name,audit_time,finish_time,create_time,username,paying_apply,paying_deposit,paying_amount';
			
			$shop_apply_model = new ShopApplyModel();
			$res = $shop_apply_model->getApplyPageList($condition, $page, $page_size, $order, $field);
			
			//处理审核状态
			$apply_state_arr = $shop_apply_model->getApplyState();
			foreach ($res['data']['list'] as $key => $val) {
                if ($apply_state == 2) {
                    if (empty(trim($val['paying_money_certificate']))) {
                        $res['data']['count'] = $res['data']['count'] - 1;
                        unset($res['data']['list'][$key]);
                        continue;
                    }
                }
				$res['data']['list'][ $key ]['apply_state_name'] = $apply_state_arr[ $val['apply_state'] ];
			}
			if ($apply_state == 2) {
			    if (empty($res['data']['count'])) {
                    $res['data']['page_count'] = 0;
                } else {
                    $res['data']['page_count'] = ceil($res['data']['page_count']/$page_size);
                }
            }

			return $res;
			
		} else {
			//商家主营行业
			$shop_category_model = new ShopCategoryModel();
			$shop_category_list = $shop_category_model->getCategoryList([], 'category_id, category_name', 'sort asc');
			$this->assign('shop_category_list', $shop_category_list['data']);
			
			//店铺等级
			$shop_group_model = new ShopGroupModel();
			$shop_group_list = $shop_group_model->getGroupList([['is_own','=',0]], 'group_id,is_own,group_name,fee,remark', 'is_own asc,fee asc');
			$this->assign('shop_group_list', $shop_group_list['data']);
			
			//申请状态
			$shop_apply_model = new ShopApplyModel();
			$apply_state_arr = $shop_apply_model->getApplyState();
			$this->assign('apply_state_arr', $apply_state_arr);
			
			return $this->fetch('shopapply/apply',[],$this->replace);
		}
	}
	
	/**
	 * 申请详情
	 */
	public function applyDetail()
	{
		$apply_id = input('apply_id', 0);
		
		$shop_apply_model = new ShopApplyModel();
		$apply_detail = $shop_apply_model->getApplyDetail([ [ 'nsa.apply_id', '=', $apply_id ] ]);
		$this->assign('apply_detail', $apply_detail['data']);
		
		return $this->fetch('shopapply/apply_detail',[],$this->replace);
	}
	
	/**
	 * 编辑商家申请
	 */
	public function editApply()
	{
		$shop_apply_model = new ShopApplyModel();
		if (request()->isAjax()) {
		    if (empty(input('apply_state'))) {
		        return 0;
            }
		    $apply_state = input('apply_state');
		    if (!in_array($apply_state, [3, -2])) {
		        return 0;
            }
			$data = [
			    'apply_state' => $apply_state,
                'apply_message' => input('apply_message', ''),//审核意见
			];
			$apply_id = input('apply_id', 0);
			if($apply_state == -2) {
                $this->addLog("财务审核拒绝入驻申请ID:" . $apply_id);
            } else {
                $this->addLog("入驻申请通过ID:" . $apply_id);
            }

			if ($apply_state == 3) {
                $this->addLog("开店通过，申请id:" . $apply_id);
                $re = $shop_apply_model->openShop($apply_id,$data['apply_message']);

            }else{
                $re = $shop_apply_model->editApply($data, [ [ 'apply_id', '=', $apply_id ] ]);
            }
            return $re;
		} else {
			$apply_id = input('apply_id', 0);

			//申请信息
			$apply_info = $shop_apply_model->getApplyInfo([ [ 'apply_id', '=', $apply_id ] ]);

			//商家主营行业
			$shop_category_model = new ShopCategoryModel();
			$shop_category = $shop_category_model->getCategoryInfo(['category_id' => $apply_info['data']['category_id']], 'category_name');
			$apply_info['category_name'] = $shop_category['data']['category_name'];
            $this->assign('apply_info', $apply_info['data']);
			return $this->fetch('shopapply/edit_apply',[],$this->replace);
		}
	}
	
	/**
	 * 申请通过
	 */
	public function applyPass()
	{
		$apply_id = input('apply_id', 0);
		$shop_apply_model = new ShopApplyModel();
		$this->addLog("商家申请通过id:" . $apply_id);
		return $shop_apply_model->applyPass($apply_id);
	}
	
	/**
	 * 申请失败
	 */
	public function applyReject()
	{
		$apply_id = input('apply_id', 0);
		$reason = input('reason', '');
		$this->addLog("商家申请拒绝id:" . $apply_id);
		$shop_apply_model = new ShopApplyModel();
		return $shop_apply_model->applyReject($apply_id, $reason);
	}
	
	/**
	 * 入驻通过
	 */
	public function openShop()
	{
		$apply_id = input('apply_id', 0);
		$this->addLog("入驻通过，申请id:" . $apply_id);
		$shop_apply_model = new ShopApplyModel();
		return $shop_apply_model->openShop($apply_id);
	}

}