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


namespace addon\supply\shop\controller;

use app\model\express\ExpressPackage;
use addon\supply\model\order\Order as OrderModel;
use addon\supply\model\order\OrderCommon as OrderCommonModel;
use addon\supply\model\order\OrderRefund as OrderRefundModel;

class Order extends BaseSupplyshop
{

    public function __construct()
    {
        parent::__construct();
        $check_login_result = $this->checkLogin();
        if($check_login_result['code'] < 0){
            echo json_encode($check_login_result);
            exit();
        }
    }

    /**
     * 详情信息
     */
    public function detail()
    {
        $order_common_model = new OrderCommonModel();
        $order_id           = input('order_id', 0);
        $result             = $order_common_model->getMemberOrderDetail($order_id, $this->site_id);
        if(empty($result['data']))
            $this->error('找不到订单');

        $this->assign('detail', $result['data'] ?? []);
        return $this->fetch("order/detail", [], $this->replace);
    }

    /**
     * 列表信息
     */
    public function lists()
    {
        if (request()->isAjax()) {
            $order_common_model = new OrderCommonModel();
            $condition          = array(
                ["buyer_shop_id", "=", $this->site_id],
            );
            $order_status       = input('order_status', 'all');
            switch ($order_status) {
                case "waitpay"://待付款
                    $condition[] = ["order_status", "=", 0];
                    break;
                case "waitsend"://待发货
                    $condition[] = ["order_status", "=", 1];
                    break;
                case "waitconfirm"://待收货
                    $condition[] = ["order_status", "=", 3];
                    break;
                case "waitrate"://待评价
                    $condition[] = ["order_status", "in", [4, 10]];
                    $condition[] = ["is_evaluate", "=", 1];
                    break;
            }

            $page_index = input('page', 1);
            $page_size  = input('page_size', PAGE_LIST_ROWS);
            $res = $order_common_model->getMemberOrderPageList($condition, $page_index, $page_size, "create_time desc");
            return $res;
        } else {
            $status_list = array(
                // 'all' => '全部',
                'waitpay'     => '待付款',
                'waitsend'    => '待发货',
                'waitconfirm' => '待收货',
                'waitrate'    => '待评价',
            );
            $this->assign('status_list', $status_list);
            return $this->fetch("order/lists", [], $this->replace);
        }
    }

    /**
     * 订单评价基础信息
     */
    public function evluateinfo()
    {
        if (request()->isAjax()) {
            $order_id           = input('order_id', 0);
            $order_common_model = new OrderCommonModel();
            if (empty($order_id)) {
                return $order_common_model->error('', 'REQUEST_ORDER_ID');
            }
            $order_info = $order_common_model->getOrderInfo([
                ['order_id', '=', $order_id],
                ['buyer_shop_id', '=', $this->site_id],
                ['order_status', 'in', ('4,10')],
                ['is_evaluate', '=', 1],
            ], 'evaluate_status,evaluate_status_name');
            $res        = $order_info['data'];
            if (!empty($res)) {
                if ($res['evaluate_status'] == 2) {
                    return $order_common_model->error('', '该订单已评价');
                } else {
                    $condition   = [
                        ['order_id', '=', $order_id],
                        ['buyer_shop_id', '=', $this->site_id],
                        ['refund_status', '<>', 3],
                    ];
                    $list = $order_common_model->getOrderGoodsList(
                        $condition,
                        'order_goods_id,order_id,order_no,site_id,site_name,goods_id,sku_id,sku_name,sku_image,price,num'
                    );
                    $list        = $list['data'];
                    $res['list'] = $list;
                    return $order_common_model->success($res);
                }
            } else {
                return $order_common_model->error('', '没有找到该订单');
            }
        } else {
//            return $this->fetch("order/lists");
        }
    }

    /**
     * 订单收货(收到所有货物)
     */
    public function takeDelivery()
    {
        $order_model = new OrderCommonModel();
        $order_id    = input('order_id', 0);
        if (empty($order_id)) {
            return $order_model->error('', 'REQUEST_ORDER_ID');
        }
        $result = $order_model->orderCommonTakeDelivery($order_id, $this->site_id);
        return $result;
    }


    /**
     * 关闭订单
     */
    public function close()
    {
        $order_model = new OrderModel();
        $order_id    = input('order_id', 0);
        if (empty($order_id)) {
            return $order_model->error('', 'REQUEST_ORDER_ID');
        }
        $result = $order_model->orderClose($order_id, $this->site_id);
        return $result;
    }

    /**
     * 获取订单数量
     */
    public function num()
    {
        $order_common_model = new OrderCommonModel();
        $order_status       = input('order_status', '');
        if (empty($order_status)) {
            return $order_common_model->error('', 'REQUEST_ORDER_STATUS');
        }
        $order_refund_model = new OrderRefundModel();
        $data               = [];
        foreach (explode(',', $order_status) as $order_status) {
            $condition = array(
                ["buyer_shop_id", "=", $this->site_id],
            );
            switch ($order_status) {
                case "waitpay"://待付款
                    $condition[] = ["order_status", "=", 0];
                    break;
                case "waitsend"://待发货
                    $condition[] = ["order_status", "=", 1];
                    break;
                case "waitconfirm"://待收货
                    $condition[] = ["order_status", "=", 3];
                    break;
                case "waitrate"://待评价
                    $condition[] = ["order_status", "in", [4, 10]];
                    $condition[] = ["is_evaluate", "=", 1];
                    break;
            }
            if ($order_status == 'refunding') {
                $result              = $order_refund_model->getRefundOrderGoodsCount([
                    ["buyer_shop_id", "=", $this->site_id],
                    ["refund_status", "in", [-1, 1, 2, 4, 5, 6]]
                ]);
                $data[$order_status] = $result['data'];
            } else {
                $result              = $order_common_model->getOrderCount($condition);
                $data[$order_status] = $result['data'];
            }
        }
        return $order_common_model->success($data);
    }

    /**
     * 订单包裹信息
     */
    public function package()
    {

            $order_id              = input('order_id', 0);
            $express_package_model = new ExpressPackage();
            $condition             = array(
                ["member_id", "=", $this->site_id],
                ["order_id", "=", $order_id],
            );
            $result                = $express_package_model->package($condition);

            $this->assign('package', $result);
//        }else{
            return $this->fetch("order/package", [], $this->replace);
//        }

    }

    /**
     * 订单支付
     * @return array
     */
    public function pay()
    {
        $order_common_model = new OrderCommonModel();
        $order_ids          = input('order_ids', '');
        if (empty($order_ids)) {
            return $order_common_model->error('', "订单数据为空");
        }
        $result = $order_common_model->splitOrderPay($order_ids, $this->site_id);
        return $result;
    }
}
