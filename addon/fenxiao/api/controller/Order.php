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


namespace addon\fenxiao\api\controller;

use app\api\controller\BaseApi;
use addon\fenxiao\model\FenxiaoOrder as FenxiaoOrderModel;
use addon\fenxiao\model\Fenxiao;

/**
 * 分销订单
 */
class Order extends BaseApi
{
	
	/**
	 * 信息
	 * @return false|string
	 */
	public function info()
	{
		$token = $this->checkToken();
		if ($token['code'] < 0) return $this->response($token);
		
		$model = new Fenxiao();
		$fenxiao_info = $model->getFenxiaoInfo([[ 'member_id', '=', $this->member_id ]], 'fenxiao_id');
		if (empty($fenxiao_info['data'])) return $this->response($this->error('', 'MEMBER_NOT_IS_FENXIAO'));
		
		$fenxiao_order_id = isset($this->params['fenxiao_order_id']) ? $this->params['fenxiao_order_id'] : 0;
		if (empty($fenxiao_order_id)) {
			return $this->response($this->error('', 'REQUEST_FENXIAO_ORDER_ID'));
		}
		$order_model = new FenxiaoOrderModel();
		$condition = [ 
			[ 'one_fenxiao_id|two_fenxiao_id|three_fenxiao_id', '=', $fenxiao_info['data']['fenxiao_id'] ],
			[ 'fenxiao_order_id', '=', $fenxiao_order_id ] 
		];
		$res = $order_model->getFenxiaoOrderDetail($condition);
		if (!empty($res['data'])) {
			if ($res['data']['one_fenxiao_id'] == $fenxiao_info['data']['fenxiao_id']) {
				$res['data']['commission'] = $res['data']['one_commission'];
				$res['data']['commission_rate'] = $res['data']['one_rate'];
				$res['data']['commission_level'] = 1;
			} elseif ($res['data']['two_fenxiao_id'] == $fenxiao_info['data']['fenxiao_id']) {
				$res['data']['commission'] = $res['data']['two_commission'];
				$res['data']['commission_rate'] = $res['data']['two_rate'];
				$res['data']['commission_level'] = 2;
			} elseif ($res['data']['three_fenxiao_id'] == $fenxiao_info['data']['fenxiao_id']) {
				$res['data']['commission'] = $res['data']['three_commission'];
				$res['data']['commission_rate'] = $res['data']['three_rate'];
				$res['data']['commission_level'] = 3;
			}
			$res['data'] = array_diff_key($res['data'], ['member_id' => '','member_name' => '','member_mobile' => '','full_address' => '','one_fenxiao_id' => '','one_rate' => '','one_commission' => '','one_fenxiao_name' => '','two_fenxiao_id' => '','two_rate' => '','two_commission' => '','two_fenxiao_name' => '','three_fenxiao_id' => '','three_rate' => '','three_commission' => '','three_fenxiao_name' => '']);
		}
		return $this->response($res);
	}
	
	/**
	 * 列表
	 */
	public function page()
	{
		$token = $this->checkToken();
		if ($token['code'] < 0) return $this->response($token);
		
		$model = new Fenxiao();
		$fenxiao_info = $model->getFenxiaoInfo([[ 'member_id', '=', $this->member_id ]], 'fenxiao_id');
		if (empty($fenxiao_info['data'])) return $this->response($this->error('', 'MEMBER_NOT_IS_FENXIAO'));
		
		$page = isset($this->params['page']) ? $this->params['page'] : 1;
		$page_size = isset($this->params['page_size']) ? $this->params['page_size'] : PAGE_LIST_ROWS;
		$is_settlement = isset($this->params['is_settlement']) ? $this->params['is_settlement'] : 0;// 结算状态 0 全部 1 待结算 2 已结算 3 已退款
		
		$condition = [
			[ 'fo.one_fenxiao_id|fo.two_fenxiao_id|fo.three_fenxiao_id', '=', $fenxiao_info['data']['fenxiao_id'] ]
		];
		if (!empty($is_settlement)) {
			if ($is_settlement == 3) {
				$condition[] = [ 'fo.is_refund', '=', 1 ];
			}
			if (in_array($is_settlement, [ 1, 2 ])) {
				$condition[] = [ 'fo.is_settlement', '=', $is_settlement - 1 ];
			}
		}
		
		$order_model = new FenxiaoOrderModel();
		$list = $order_model->getFenxiaoOrderPageList($condition, $page, $page_size, 'fo.fenxiao_order_id desc');
		if (!empty($list['data']['list'])) {
			foreach ($list['data']['list'] as $k => $item) {
				if ($item['one_fenxiao_id'] == $fenxiao_info['data']['fenxiao_id']) {
					$list['data']['list'][$k]['commission'] = $item['one_commission'];
					$list['data']['list'][$k]['commission_level'] = 1;
				} elseif ($item['two_fenxiao_id'] == $fenxiao_info['data']['fenxiao_id']) {
					$list['data']['list'][$k]['commission'] = $item['two_commission'];
					$list['data']['list'][$k]['commission_level'] = 2;
				} elseif ($item['three_fenxiao_id'] == $fenxiao_info['data']['fenxiao_id']) {
					$list['data']['list'][$k]['commission'] = $item['three_commission'];
					$list['data']['list'][$k]['commission_level'] = 3;
				}
				$list['data']['list'][$k] = array_diff_key($list['data']['list'][$k], ['one_fenxiao_id' => '','one_rate' => '','one_commission' => '','one_fenxiao_name' => '','two_fenxiao_id' => '','two_rate' => '','two_commission' => '','two_fenxiao_name' => '','three_fenxiao_id' => '','three_rate' => '','three_commission' => '','three_fenxiao_name' => '']);
			}
		}
		return $this->response($list);
	}
	
}