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


namespace addon\fenxiao\shop\controller;

use addon\fenxiao\model\FenxiaoOrder as FenxiaoOrderModel;
use addon\fenxiao\model\FenxiaoOrder;
use app\model\order\Order as OrderModel;
use app\shop\controller\BaseShop;


/**
 * 分销订单控制器
 */
class Order extends BaseShop
{

    /*
     *  分销订单列表
     */
    public function lists()
    {
        if(request()->isAjax()) {
            $order_model = new FenxiaoOrderModel();
            $page_index = input('page_index', 1);
            $page_size = input('page_size', PAGE_LIST_ROWS);
            $order_label = input('order_label', 'goods');
            $search_text = input('search_text', '');
            $order_status = input('order_status', '');
            $is_settlement = input('is_settlement', '');
            $start_time = input('start_time','');
            $end_time = input('end_time','');
            $condition = [['fo.site_id', '=', $this->site_id]];
            if (!empty($search_text)) {
                $condition[] = ['fo.sku_name|fo.order_no|fo.member_mobile|fo.member_name', 'LIKE', "%{$search_text}%"];
                if ($order_label == 'goods') {
                    $condition[] = ['fo.sku_name', 'LIKE', "%{$search_text}%"];
                }
                if ($order_label == 'order') {
                    $condition[] = ['fo.order_no', 'LIKE', "%{$search_text}%"];
                }
                if ($order_label == 'user') {
                    $condition[] = ['fo.member_mobile|fo.member_name', 'LIKE', "%{$search_text}%"];
                }
            }
            if (!empty($order_status)) {
                $condition[] = ['o.order_status', '=', $order_status];
            }
            if ($is_settlement == 3) {
                $condition[] = ['fo.is_refund', '=', 1];
            }
            if (in_array($is_settlement, [1, 2])) {
                $condition[] = ['fo.is_settlement', '=', $is_settlement-1];
            }
            if(!empty($start_time) && empty($end_time)){
                $condition[] = ['o.create_time','>=',date_to_time($start_time)];
            }elseif(empty($start_time) && !empty($end_time)){
                $condition[] = ['o.create_time','<=',date_to_time($end_time)];
            }elseif(!empty($start_time) && !empty(date_to_time($end_time))){
                $condition[] = ['o.create_time','between',[date_to_time($start_time),date_to_time($end_time)]];
            }

            $list = $order_model->getFenxiaoOrderPageList($condition, $page_index, $page_size, 'fenxiao_order_id DESC');
            return $list;
        }

        $order_model = new OrderModel();
        $order_status_list = $order_model->order_status;
        $this->assign("order_status_list", $order_status_list);//订单状态
        return $this->fetch('order/lists');
    }

    public function detail() {
        $fenxiao_order_model = new FenxiaoOrder();
        $fenxiao_order_id = input('fenxiao_order_id', '');
        $order_info = $fenxiao_order_model->getFenxiaoOrderDetail([['fenxiao_order_id', '=', $fenxiao_order_id]]);
        $this->assign('order_info', $order_info['data']);
        return $this->fetch('order/detail');
    }

}