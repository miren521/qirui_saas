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


namespace addon\supply\supply\controller;

use app\Controller;
use app\model\order\OrderCommon as OrderCommonModel;

/**
 * 打印
 * Class Printer
 * @package addon\supply\supply\controller
 */
class Printer extends Controller
{

    /**
     * 批量打印发货单
     * @return mixed
     */
    public function batchPrintOrder()
    {
        $order_id = input('order_id', 0);
        $order_common_model = new OrderCommonModel();
        $order_detail_result = $order_common_model->getOrderDetail($order_id);
        $order_detail = $order_detail_result["data"];
        $this->assign("order_detail", $order_detail);
        return $this->fetch('order/batch_print_order');
    }
}
