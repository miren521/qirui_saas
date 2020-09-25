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

use addon\supply\model\order\OrderCommon as OrderCommonModel;
use addon\supply\model\order\Order as OrderModel;
use think\facade\Config;
use phpoffice\phpexcel\Classes\PHPExcel;
use phpoffice\phpexcel\Classes\PHPExcel\Writer\Excel2007;

/**
 * 订单
 * Class Order
 * @package addon\supply\supply\controller
 */
class Order extends BaseSupply
{

    public function __construct()
    {
        //执行父类构造函数
        parent::__construct();
        if ($this->supply_info['expire_time'] < time() && $this->supply_info['expire_time'] != 0) {
            $this->error("店铺已过期，请进行续签!", addon_url('supply/reopen/index'));
        }

    }

    /**
     * 快递订单列表
     */
    public function lists()
    {
        $order_label_list = array(
            "order_no" => "订单号",
            "out_trade_no" => "外部单号",
            "name" => "收货人姓名",
            "mobile" => "收货人手机号",
            "order_name" => "商品名称",
        );
        $order_status = input("order_status", "");//订单状态
        $order_name = input("order_name", '');
        $pay_type = input("pay_type", '');
        $order_from = input("order_from", '');
        $start_time = input("start_time", '');
        $end_time = input("end_time", '');
        $order_label = !empty($order_label_list[input("order_label")]) ? input("order_label") : "";
        $search_text = input("search", '');
        $order_type = input("order_type", 'all');//营销类型
        $order_common_model = new OrderCommonModel();
        if (request()->isAjax()) {
            $page_index = input('page', 1);
            $page_size = input('page_size', PAGE_LIST_ROWS);
            $condition = [
//                ["order_type", "=", 1],
                ["site_id", "=", $this->supply_id]
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
            if($order_type != 'all'){
                $condition[] = ["order_type", "=", $order_type];
            }

            if (!empty($start_time) && empty($end_time)) {
                $condition[] = ["create_time", ">=", date_to_time($start_time)];
            } elseif (empty($start_time) && !empty($end_time)) {
                $condition[] = ["create_time", "<=", date_to_time($end_time)];
            } elseif (!empty($start_time) && !empty($end_time)) {
	            $condition[] = [ 'create_time', 'between', [ date_to_time($start_time), date_to_time($end_time) ] ];
            }
            if ($search_text != "") {
                $condition[] = [$order_label, 'like', "%$search_text%"];
            }
            $list = $order_common_model->getOrderPageList($condition, $page_index, $page_size, "create_time desc");
            return $list;
        } else {

            $order_type_list = $order_common_model->getOrderTypeStatusList();
            $this->assign("order_type_list", $order_type_list);
            $this->assign("order_label_list", $order_label_list);
            $order_model = new OrderModel();
            $order_status_list = $order_model->order_status;
            $this->assign("order_status_list", $order_status_list);//订单状态
            //订单来源 (支持端口)
            $order_from = Config::get("app_type");
            $this->assign('order_from_list', $order_from);

            $pay_type = $order_common_model->getPayType();
            $this->assign("pay_type_list", $pay_type);

            return $this->fetch('order/lists');
        }

    }

    /**
     * 快递订单详情
     */
    public function detail()
    {
        $order_id = input("order_id", 0);
        $order_common_model = new OrderCommonModel();
        $order_detail_result = $order_common_model->getOrderDetail($order_id);
        $order_detail = $order_detail_result["data"];
        $this->assign("order_detail", $order_detail);

        switch ($order_detail["order_type"]) {
            case 1 :
                $template = "order/detail";
                break;
        }
        return $this->fetch($template);
    }


    /**
     * 订单关闭
     * @return mixed
     */
    public function close()
    {
        if (request()->isAjax()) {
            $order_id = input("order_id", 0);
            $order_common_model = new OrderCommonModel();
            $result = $order_common_model->orderClose($order_id);
            return $result;
        }
    }

    /**
     * 订单调价
     * @return mixed
     */
    public function adjustPrice()
    {
        if (request()->isAjax()) {
            $order_id = input("order_id", 0);
            $adjust_money = input("adjust_money", 0);
            $delivery_money = input("delivery_money", 0);
            $order_common_model = new OrderCommonModel();
            $result = $order_common_model->orderAdjustMoney($order_id, $adjust_money, $delivery_money);
            return $result;
        }
    }

    /**
     * 订单发货
     * @return mixed
     */
    public function delivery()
    {
        if (request()->isAjax()) {
            $order_id = input("order_id", 0);
            $order_goods_ids = input("order_goods_ids", '');
            $express_company_id = input("express_company_id", 0);
            $delivery_no = input("delivery_no", '');
            $delivery_type = input("delivery_type", 0);
            $order_model = new OrderModel();
            $data = array(
                "type" => input('type','manual'),//发货方式（手动发货、电子面单）
                "order_goods_ids" => $order_goods_ids,
                "express_company_id" => $express_company_id,
                "delivery_no" => $delivery_no,
                "order_id" => $order_id,
                "delivery_type" => $delivery_type,
                "site_id" => $this->supply_id,
                "template_id" => input('template_id',0)//电子面单模板id
            );
            $result = $order_model->orderGoodsDelivery($data);
            return $result;
        } else {
            $order_id = input("order_id", 0);
            $delivery_status = input("delivery_status", '');
            $order_common_model = new OrderCommonModel();
            $condition = array(
                ["order_id", "=", $order_id],
                ["site_id", "=", $this->supply_id],
            );
            if ($delivery_status === '') {
                $condition[] = ["delivery_status", "=", $delivery_status];
            }
            $field = "order_goods_id, order_id, site_id, site_name, sku_name, sku_image, sku_no, is_virtual, price, cost_price, num, goods_money, cost_money, delivery_status, delivery_no, goods_id, delivery_status_name";
            $order_goods_list_result = $order_common_model->getOrderGoodsList($condition, $field, '', null, "delivery_no");
            $order_goods_list = $order_goods_list_result["data"];
            $this->assign("order_goods_list", $order_goods_list);
            return $this->fetch("order/delivery");
        }
    }

    /**
     * 获取订单项列表
     */
    public function getOrderGoodsList()
    {
        if (request()->isAjax()) {
            $order_id = input("order_id", 0);
            $delivery_status = input("delivery_status", '');
            $order_common_model = new OrderCommonModel();
            $condition = array(
                ["order_id", "=", $order_id],
                ["site_id", "=", $this->supply_id],
                ["refund_status", "<>", 3],
            );
            if ($delivery_status != '') {
                $condition[] = ["delivery_status", "=", $delivery_status];
            }
            $field = "order_goods_id, order_id, site_id, site_name, sku_name, sku_image, sku_no, is_virtual, price, cost_price, num, goods_money, cost_money, delivery_status, delivery_no, goods_id, delivery_status_name";
            $result = $order_common_model->getOrderGoodsList($condition, $field, '', null, "");
            return $result;
        }
    }

    /**
     * 订单修改收货地址
     * @return mixed
     */
    public function editAddress()
    {
        $order_id = input("order_id", 0);
        if (request()->isAjax()) {
            $order_model = new OrderModel();
            $province_id = input("province_id");
            $city_id = input("city_id");
            $district_id = input("district_id");
            $community_id = input("community_id");
            $address = input("address");
            $full_address = input("full_address");
            $longitude = input("longitude");
            $latitude = input("latitude");
            $mobile = input("mobile");
            $telephone = input("telephone");
            $name = input("name");
            $data = array(
                "province_id" => $province_id,
                "city_id" => $city_id,
                "district_id" => $district_id,
                "community_id" => $community_id,
                "address" => $address,
                "full_address" => $full_address,
                "longitude" => $longitude,
                "latitude" => $latitude,
                "mobile" => $mobile,
                "telephone" => $telephone,
                "name" => $name,
            );
            $condition = array(
                ["order_id", "=", $order_id],
                ["site_id", "=", $this->supply_id]
            );
            $result = $order_model->orderAddressUpdate($data, $condition);
            return $result;
        }
    }

    /**
     * 获取订单信息
     */
    public function getOrderInfo()
    {
        if (request()->isAjax()) {
            $order_id = input("order_id", 0);
            $order_common_model = new OrderCommonModel();
            $condition = array(
                ["order_id", "=", $order_id],
                ["site_id", "=", $this->supply_id],
            );
            $result = $order_common_model->getOrderInfo($condition);
            return $result;
        }
    }

    /**
     * 获取订单 订单项内容
     */
    public function getOrderDetail()
    {
        if (request()->isAjax()) {
            $order_id = input("order_id", 0);
            $order_common_model = new OrderCommonModel();
            $result = $order_common_model->getOrderDetail($order_id);
            return $result;
        }
    }

    /**
     * 卖家备注
     */
    public function orderRemark()
    {
        if (request()->isAjax()) {
            $order_id = input("order_id", 0);
            $remark = input("remark", 0);
            $order_common_model = new OrderCommonModel();
            $condition = array(
                ["order_id", "=", $order_id],
                ["site_id", "=", $this->supply_id],
            );
            $data = array(
                "remark" => $remark
            );
            $result = $order_common_model->orderUpdate($data, $condition);
            return $result;
        }
    }

    /**
     * 线下支付
     */
//    public function offlinePay(){
//        $order_id = input('order_id', 0);
//        $order_common_model = new OrderCommonModel();
//        $result = $order_detail_result = $order_common_model->orderOfflinePay($order_id);
//        return $result;
//    }
}