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


namespace addon\pintuan\api\controller;

use addon\pintuan\model\PintuanOrder as PintuanOrderModel;
use app\api\controller\BaseApi;

/**
 * 拼团订单
 * @author Administrator
 *
 */
class Order extends BaseApi
{
	
	/**
	 * 拼团订单详情
	 * @return string
	 */
	public function detail()
	{
		$token = $this->checkToken();
		if ($token['code'] < 0) return $this->response($token);
		
		$id = isset($this->params['id']) ? $this->params['id'] : 0;//拼团订单主键id
		if (empty($id)) {
			return $this->response($this->error('', 'REQUEST_ID'));
		}
		$pintuan_order_model = new PintuanOrderModel();
		$res = $pintuan_order_model->getPintuanOrderDetail($id, $this->member_id);
		return $this->response($res);
	}
	
	/**
	 * 拼团列表
	 * @return string
	 */
	public function page()
	{
		$token = $this->checkToken();
		if ($token['code'] < 0) return $this->response($token);
		
		$pintuan_order_model = new PintuanOrderModel();
		$condition = array(
			[ "ppo.member_id", "=", $this->member_id ],
		);
		$pintuan_status = isset($this->params['pintuan_status']) ? $this->params['pintuan_status'] : 'all';
		if ($pintuan_status != "all") {
			$condition[] = [ "ppo.pintuan_status", "=", $pintuan_status ];
		} else {
			$condition[] = [ "ppo.pintuan_status", "<>", "0" ];//不查询未支付的拼团
		}
		
		$page_index = isset($this->params['page']) ? $this->params['page'] : 1;
		$page_size = isset($this->params['page_size']) ? $this->params['page_size'] : PAGE_LIST_ROWS;
		$res = $pintuan_order_model->getPintuanOrderPageList($condition, $page_index, $page_size, "o.pay_time desc");
		if (!empty($res['data']['list'])) {
			foreach ($res['data']['list'] as $k => $v) {
				$member_list = $pintuan_order_model->getPintuanOrderList([ [ "group_id", "=", $v["group_id"] ] ], "member_img,nickname");
				$res['data']['list'][ $k ]['member_list'] = $member_list['data'];
			}
			
		}
		return $this->response($res);
	}
}