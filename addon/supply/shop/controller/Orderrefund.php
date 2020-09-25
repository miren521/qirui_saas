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

use addon\supply\model\Supplier;
use addon\supply\model\order\OrderRefund as OrderRefundModel;

class Orderrefund extends BaseSupplyshop
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
     * 售后列表
     */
    public function lists()
    {
        if (request()->isAjax()) {
            $refund_model = new OrderRefundModel();
            $where          = array(
                ["nop.buyer_shop_id", "=", $this->site_id],
            );
            $refund_status      = input('refund_status', 'all');
            switch ($refund_status) {
//            case "waitpay"://处理中
//                $condition[] = [ "refund_status", "=", 1 ];
//                break;
                default:
                    $where[] = ["nop.refund_status", "<>", 0];
                    break;
            }
            $page = input('page', 1);
            $page_size  = input('page_size', PAGE_LIST_ROWS);
            $res = $refund_model->getRefundOrderGoodsPageList($where, $page, $page_size, "refund_action_time desc");
            return $res;
        } else {
            return $this->fetch("orderrefund/lists", [], $this->replace);
        }
    }


    /**
     * 发起退款
     */
    public function refund()
    {
        if (request()->isAjax()) {
            $order_refund_model = new OrderRefundModel();

            $order_goods_id = input('order_goods_id', 0);
            $refund_type    = input('refund_type', 1);
            $refund_reason  = input('refund_reason', '');
            $refund_remark  = input('refund_remark', '');
            $data           = array(
                "order_goods_id" => $order_goods_id,
                "refund_type"    => $refund_type,
                "refund_reason"  => $refund_reason,
                "refund_remark"  => $refund_remark
            );
            $result         = $order_refund_model->orderRefundApply($data, $this->user_info, $this->site_id);
            return $result;
        } else {
            $order_goods_id     = input('order_goods_id', 0);
            $order_refund_model = new OrderRefundModel();
            $condition          = array(
                ["order_goods_id", "=", $order_goods_id],
                ['buyer_shop_id', '=', $this->site_id]
            );

            $order_goods_info_result = $order_refund_model->getRefundDetail($condition);
            $order_goods_info        = $order_goods_info_result["data"];//订单项信息
            if (empty($order_goods_info))
                $this->error('找不到订单');


            $refund_money       = $order_refund_model->getOrderRefundMoney($order_goods_id);
            $refund_type        = $order_refund_model->getRefundType($order_goods_info);
            $refund_reason_type = $order_refund_model->refund_reason_type;
            $result             = array(
                "order_goods_info"   => $order_goods_info,
                "refund_money"       => $refund_money,
                "refund_type"        => $refund_type,
                "refund_reason_type" => $refund_reason_type
            );
            $this->assign('data', $result);
            return $this->fetch("orderrefund/refund", [], $this->replace);
        }
    }

    /**
     * 取消发起的退款申请
     * @return mixed[]|string[]
     */
    public function cancel()
    {
        if (request()->isAjax()) {
            $order_refund_model = new OrderRefundModel();
            $order_goods_id     = input('order_goods_id', 0);
            $data               = array(
                "order_goods_id" => $order_goods_id
            );
            $res                = $order_refund_model->memberCancelRefund($data, $this->user_info, $this->site_id);
            return $res;
        }
    }

    /**
     * 买家退货
     * @return string
     */
    public function delivery()
    {
        if (request()->isAjax()) {
            $order_refund_model = new OrderRefundModel();

            $order_goods_id         = input('order_goods_id', 0);
            $refund_delivery_name   = input('refund_delivery_name', '');
            $refund_delivery_no     = input('refund_delivery_no', '');
            $refund_delivery_remark = input('refund_delivery_remark', '');

            $data = array(
                "order_goods_id"         => $order_goods_id,
                "refund_delivery_name"   => $refund_delivery_name,
                "refund_delivery_no"     => $refund_delivery_no,
                "refund_delivery_remark" => $refund_delivery_remark
            );
            $res  = $order_refund_model->orderRefundDelivery($data, $this->user_info, $this->site_id);
            return $res;
        } else {
            $this->detail();
            return $this->fetch("orderrefund/delivery", [], $this->replace);
        }
    }

    /**
     * 维权详情
     * @return mixed|void
     */
    public function detail()
    {
        $order_refund_model = new OrderRefundModel();
        $order_goods_id     = input('order_goods_id', 0);

        $order_goods = $order_refund_model->getMemberRefundDetail($order_goods_id, $this->site_id);
        if (!empty($order_goods["data"])) {
            //查询店铺收货地址
            $supplier_model       = new Supplier();
            $supplier_info_result = $supplier_model->getSupplierInfo(
                [["supplier_site_id", "=", $order_goods["data"]['site_id']]]
            );
            $supplier_info        = $supplier_info_result["data"];
            $order_goods["data"]["address"] = $supplier_info["full_address"];
        }
        $this->assign('detail', $order_goods['data']);
        return $this->fetch("orderrefund/detail", [], $this->replace);
    }
}
