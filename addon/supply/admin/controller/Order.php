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


namespace addon\supply\admin\controller;

use addon\supply\model\order\Config as ConfigModel;
use addon\supply\model\order\Order as OrderModel;
use addon\supply\model\order\OrderCommon as OrderCommonModel;
use think\facade\Config;

/**
 * 订单管理 控制器
 */
class Order extends BaseSupply
{
    /**
     * 快递订单列表
     */
    public function lists()
    {
        $order_label_list   = array(
            "order_no"     => "订单号",
            "out_trade_no" => "外部单号",
            "name"         => "收货人姓名",
            "mobile"       => "收货人手机号",
            "order_name"   => "商品名称",
            "site_name"    => "供应商名称",
        );
        $order_status       = input("order_status", "");//订单状态
        $order_name         = input("order_name", '');
        $pay_type           = input("pay_type", '');
        $order_from         = input("order_from", '');
        $start_time         = input("start_time", '');
        $end_time           = input("end_time", '');
        $site_id            = input("site_id", "");
        $order_label        = !empty($order_label_list[input("order_label")]) ? input("order_label") : "";
        $search_text        = input("search", '');
        $order_type         = input("order_type", 'all');//订单类型
        $order_common_model = new OrderCommonModel();
        if (request()->isAjax()) {
            $page_index = input('page', 1);
            $page_size  = input('limit', PAGE_LIST_ROWS);
            $condition  = [
//                ["order_type", "=", 1]
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
            //订单类型
            if ($order_type != 'all') {
                $condition[] = ["order_type", "=", $order_type];
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
                $condition[] = ["site_id", '=', $site_id];
            }
            $list = $order_common_model->getOrderPageList($condition, $page_index, $page_size, "create_time desc");
            return $list;
        } else {
            $this->assign("order_label_list", $order_label_list);
            $order_model       = new OrderModel();
            $order_status_list = $order_model->order_status;
            $this->assign("order_status_list", $order_status_list);//订单状态
            //订单来源 (支持端口)
            $order_from = Config::get("app_type");
            $this->assign('order_from_list', $order_from);

            $pay_type = $order_common_model->getPayType();
            $this->assign("pay_type_list", $pay_type);

            $order_type_list = $order_common_model->getOrderTypeStatusList();
            $this->assign("order_type_list", $order_type_list);

            return $this->fetch('order/lists');
        }
    }

    /**
     * 快递订单详情
     */
    public function detail()
    {
        $order_id            = input("order_id", 0);
        $order_common_model  = new OrderCommonModel();
        $order_detail_result = $order_common_model->getOrderDetail($order_id);
        $order_detail        = $order_detail_result["data"];
        $this->assign("order_detail", $order_detail);

        switch ($order_detail["order_type"]) {
            case 1:
                $template = "order/detail";
                break;
        }

        return $this->fetch($template);
    }

    /**
     * 订单设置
     */
    public function config()
    {
        if (request()->isAjax()) {
            //订单事件时间设置数据
            $order_event_time_config_data = [
                'auto_close'         => input('order_auto_close_time', 0),//订单未付款自动关闭时间 数字 单位(天)
                'auto_take_delivery' => input('order_auto_take_delivery_time', 0),//订单发货后自动收货时间 数字 单位(天)
                'auto_complete'      => input('order_auto_complete_time', 0),//订单收货后自动完成时间 数字 单位(天)
            ];
            $config_model                 = new ConfigModel();
            $res                          = $config_model->setOrderTradeConfig($order_event_time_config_data);

            return $res;
        } else {
            $config_model = new ConfigModel();

            //订单事件时间设置
            $order_event_time_config = $config_model->getOrderTradeConfig();
            $this->assign('order_event_time_config', $order_event_time_config['data']['value']);

            return $this->fetch('order/config');
        }
    }
}
