<?php
/**
 * Niushop商城系统 - 团队十年电商经验汇集巨献!
 * =========================================================
 * Copy right 2019-2029 山西牛酷信息科技有限公司, 保留所有权利。
 * ----------------------------------------------
 * 官方网址: https://www.niushop.com
 * 这不是一个自由软件！您只能在不用于商业目的的前提下对程序代码进行修改和使用。
 * 任何企业和个人不允许对程序代码以任何形式任何目的再发布。
 * =========================================================
 */

namespace addon\pointexchange\api\controller;

use app\api\controller\BaseApi;
use addon\pointexchange\model\Order as OrderModel;

/**
 * 积分兑换订单
 */
class Order extends BaseApi
{
	
	/**
	 * 基础信息
	 */
	public function info()
	{
		$token = $this->checkToken();
		if ($token['code'] < 0) return $this->response($token);
		
		$order_id = isset($this->params['order_id']) ? $this->params['order_id'] : 0;
		if (empty($order_id)) {
			return $this->response($this->error('', 'REQUEST_ORDER_ID'));
		}
		$condition = [
			[ 'order_id', '=', $order_id ],
			[ 'member_id', '=', $this->member_id ],
		];
		$field = 'order_id,order_no,member_id,out_trade_no,point,exchange_price,delivery_price,price,express_no,create_time,pay_time,exchange_id,exchange_name,exchange_image,num,order_status,type,type_name,name,mobile,telephone,address,full_address';
		
		$exchange_model = new OrderModel();
		
		$info = $exchange_model->getOrderInfo($condition, $field);
		return $this->response($info);
	}
	
	public function page()
	{
		$token = $this->checkToken();
		if ($token['code'] < 0) return $this->response($token);
		
		$page = isset($this->params['page']) ? $this->params['page'] : 1;
		$page_size = isset($this->params['page_size']) ? $this->params['page_size'] : PAGE_LIST_ROWS;
		$condition = [
			[ 'member_id', '=', $this->member_id ],
		];
		$order = 'create_time desc';
		$field = 'order_id,order_no,member_id,out_trade_no,point,exchange_price,delivery_price,price,express_no,create_time,pay_time,exchange_id,exchange_name,exchange_image,num,order_status,type,type_name';
		
		$exchange_model = new OrderModel();
		$list = $exchange_model->getExchangePageList($condition, $page, $page_size, $order, $field);
		return $this->response($list);
	}

    /**
     * 关闭订单
     * @return false|string
     */
    public function close(){
        $token = $this->checkToken();
        if ($token['code'] < 0) return $this->response($token);

        $order_id = isset($this->params['order_id']) ? $this->params['order_id'] : 0;

        $exchange_model = new OrderModel();
        $condition = array(
            ['order_id', '=', $order_id],
            ['member_id', '=', $this->member_id]
        );
        $result =  $exchange_model->closeOrder($condition);
        return $this->response($result);
    }
	
}