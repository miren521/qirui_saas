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


namespace app\admin\controller;

use app\model\order\OrderCommon as OrderCommonModel;
use app\model\order\LocalOrder as LocalOrderModel;
use think\facade\Config;
use app\model\system\Promotion as PromotionModel;

/**
 * 外卖订单管理 控制器
 */
class Localorder extends BaseAdmin
{

    /**
     * 外卖订单列表
     */
    public function lists()
    {
        $order_label_list = array(
            "order_no" => "订单号",
            "out_trade_no" => "外部单号",
            "name" => "收货人姓名",
            "order_name" => "商品名称",
            "site_name" => "店铺名称",
        );
        $order_status = input("order_status", "");//订单状态
        $order_name = input("order_name", '');
        $pay_type = input("pay_type", '');
        $order_from = input("order_from", '');
        $start_time = input("start_time", '');
        $end_time = input("end_time", '');
        $site_id = input("site_id", "");
        $order_label = !empty($order_label_list[ input("order_label") ]) ? input("order_label") : "";
        $search_text = input("search", '');
        $promotion_type = input("promotion_type", '');
        $order_common_model = new OrderCommonModel();
        if (request()->isAjax()) {
            $page_index = input('page', 1);
            $page_size = input('limit', PAGE_LIST_ROWS);
            $condition = [
                ["order_type", "=", 3]
            ];
            //订单状态
            if ($order_status != "") {
                $condition[] = ["order_status", "=", $order_status];
            }
            //订单内容 模糊查询
            if ($order_name != "") {
                $condition[] = ["order_name", 'like', "%$order_name%"];
            }
            //订单来源
            if ($order_from != "") {
                $condition[] = ["order_from", "=", $order_from];
            }
            //订单支付
            if ($pay_type != "") {
                $condition[] = ["pay_type", "=", $pay_type];
            }
            //营销类型
            if ($promotion_type != "") {
                if ($promotion_type == 'empty') {
                    $condition[] = ["promotion_type", "=", ''];
                } else {
                    $condition[] = ["promotion_type", "=", $promotion_type];
                }
            }
            if (!empty($start_time) && empty($end_time)) {
                $condition[] = ["create_time", ">=", date_to_time($start_time)];
            } elseif (empty($start_time) && !empty($end_time)) {
                $condition[] = ["create_time", "<=", date_to_time($end_time)];
            } elseif (!empty($start_time) && !empty($end_time)) {
                $condition[] = ['create_time', 'between', [date_to_time($start_time), date_to_time($end_time)]];
            }
            if ($search_text != "") {
                $condition[] = [$order_label, 'like', "%$search_text%"];
            }
            if (!empty($site_id)) {
                $condition[] = ["site_id", "=", $site_id];
            }
            $list = $order_common_model->getOrderPageList($condition, $page_index, $page_size);
            return $list;
        } else {
            $this->assign("order_label_list", $order_label_list);
            $order_model = new LocalOrderModel();
            $order_status_list = $order_model->order_status;
            $this->assign("order_status_list", $order_status_list);//订单状态
            //订单来源 (支持端口)
            $order_from = Config::get("app_type");
            $this->assign('order_from_list', $order_from);

            $pay_type = $order_common_model->getPayType();
            $this->assign("pay_type_list", $pay_type);
            //营销活动类型
            $promotion_model = new PromotionModel();
            $promotion_type = $promotion_model->getPromotionType();
            $this->assign("promotion_type", $promotion_type);
            return $this->fetch('localorder/lists');
        }
    }

    /**
     * 外卖订单详情
     */
    public function detail()
    {
        $order_id = input("order_id", 0);
        $order_common_model = new OrderCommonModel();
        $condition = array(
            ['order_id', '=', $order_id],
        );
        $order_detail_result = $order_common_model->getOrderDetail($condition);
        $order_detail = $order_detail_result[ "data" ];
        $this->assign("order_detail", $order_detail);
        return $this->fetch('localorder/detail');
    }
}