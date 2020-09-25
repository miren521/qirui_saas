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


namespace addon\Pointexchange\admin\controller;

use app\admin\controller\BaseAdmin;
use addon\pointexchange\model\Order as ExchangeOrderModel;

/**
 * 礼品发放订单
 */
class Pointexchange extends BaseAdmin
{
	
	/**
	 * 兑换订单列表
	 * @return mixed
	 */
	public function lists()
	{
		
		$exchange_id = input('exchange_id', '');
		if (request()->isAjax()) {
			$page = input('page', 1);
			$page_size = input('page_size', PAGE_LIST_ROWS);
			$search_text = input('search_text', '');
			$condition = [];
			if ($search_text) {
				$condition[] = [ 'exchange_name', 'like', '%' . $search_text . '%' ];
			}
			
			$type = input('type', '');
			if ($type) {
				$condition[] = [ 'type', '=', $type ];
			}
			
			if ($exchange_id) {
				$condition[] = [ 'exchange_id', '=', $exchange_id ];
			}
			
			$order = 'create_time desc';
			$field = '*';
			
			$exchange_order_model = new ExchangeOrderModel();
			return $exchange_order_model->getExchangePageList($condition, $page, $page_size, $order, $field);
		} else {
			$this->assign('exchange_id', $exchange_id);
			$this->forthMenu();
			return $this->fetch("exchange_order/lists");
		}
		
	}
	
	/**
     * 订单详情
	 * @return mixed
	 */
	public function detail()
	{
		$order_id = input('order_id', 0);
		$order_model = new ExchangeOrderModel();
		$order_info = $order_model->getOrderInfo([ [ 'order_id', '=', $order_id ] ]);
		$order_info = $order_info["data"];
		if(!empty($order_info)){
            if($order_info['order_status'] == 0){
                $order_status_name = '待支付';
            }else if($order_info['order_status'] == 1){
                $order_status_name = '已完成';
            }else{
                $order_status_name = '已关闭';
            }
            $order_info['order_status_name'] = $order_status_name ?? '';
        }

		$this->assign("order_info", $order_info);
		return $this->fetch('exchange_order/detail');
	}

//    /**
//     *兑换发货
//     */
//    public function express()
//    {
//        if(request()->isAjax())
//        {
//            $order_id = input('order_id', 0);
//            $data = [
//                'express_no' => input('express_no',''),//配送编码
//                'express_company_name' => input('express_company_name',''),//物流公司名称
//                'express_time' => time(),
//                'express_status' => 1
//            ];
//            $gift_order_model = new ExchangeOrderModel();
//            return $gift_order_model->editOrder($data, $order_id);
//        }
//    }

}