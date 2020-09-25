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


namespace addon\store\store\controller;

use addon\store\model\StoreMember;
use app\model\order\OrderCommon as OrderCommonModel;
use app\model\order\Order as OrderModel;
use app\model\order\OrderCommon;
use app\model\order\OrderExport;
use app\model\order\StoreOrder;
use think\facade\Config;
use app\model\system\Promotion as PromotionModel;
use phpoffice\phpexcel\Classes\PHPExcel;
use phpoffice\phpexcel\Classes\PHPExcel\Writer\Excel2007;

/**
 * 订单
 * Class Order
 * @package app\shop\controller
 */
class Order extends BaseStore
{

    public function __construct()
    {
        //执行父类构造函数
        parent::__construct();

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
        $promotion_type = input("promotion_type", '');//营销类型
        $order_common_model = new OrderCommonModel();
        if (request()->isAjax()) {
            $page_index = input('page', 1);
            $page_size = input('page_size', PAGE_LIST_ROWS);
            $condition = [
                ["site_id", "=", $this->site_id],
                ['delivery_store_id', '=', $this->store_id],
                ['order_type', '=', 2]
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
                if($promotion_type == 'empty'){
                    $condition[] = ["promotion_type", "=", ''];
                }else{
                    $condition[] = ["promotion_type", "=", $promotion_type];
                }
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
            $this->assign("order_label_list", $order_label_list);
            $order_model = new StoreOrder();
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
            return $this->fetch('order/lists', [], $this->replace);
        }
    }

    /**
     * 快递订单详情
     */
    public function detail()
    {
        $order_id = input("order_id", 0);
        $order_common_model = new OrderCommonModel();
        $condition = array(
            ['order_id', '=', $order_id],
            ['delivery_store_id', '=', $this->store_id]
        );
        $order_detail_result = $order_common_model->getOrderDetail($condition);
        $order_detail = $order_detail_result["data"];
        $this->assign("order_detail", $order_detail);
        return $this->fetch('order/detail', [], $this->replace);
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

            $close_condition = array(
                ['order_id', '=', $order_id],
                ['delivery_store_id', '=', $this->store_id]
            );
            $result = $order_common_model->orderClose($close_condition);
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
            $condition = array(
                ['order_id', '=', $order_id],
                ['delivery_store_id', '=', $this->store_id]
            );
            $result = $order_common_model->orderAdjustMoney($condition, $adjust_money, $delivery_money);
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
                ["site_id", "=", $this->site_id]
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
                ["site_id", "=", $this->site_id],
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
            $condition = array(
                ['order_id', '=', $order_id],
                ['delivery_store_id', '=', $this->store_id]
            );
            $result = $order_common_model->getOrderDetail($condition);
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
                ["site_id", "=", $this->site_id],
            );
            $data = array(
                "remark" => $remark
            );
            $result = $order_common_model->orderUpdate($data, $condition);
            return $result;
        }
    }

    /**
     * 订单导出（已订单为主）
     */
    public function exportOrder()
    {
        $order_status = input("order_status", "");//订单状态
        $order_name = input("order_name", '');
        $pay_type = input("pay_type", '');
        $order_from = input("order_from", '');
        $start_time = input("start_time", '');
        $end_time = input("end_time", '');
        $order_label = !empty($order_label_list[input("order_label")]) ? input("order_label") : "";
        $search_text = input("search", '');
        $promotion_type = input("promotion_type", '');

        $condition[] = ["order_type", "=", 2];
        $condition[] = ["site_id", "=", $this->site_id];
        $condition[] = ['delivery_store_id', '=', $this->store_id];
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
            if($promotion_type == 'empty'){
                $condition[] = ["promotion_type", "=", ''];
            }else{
                $condition[] = ["promotion_type", "=", $promotion_type];
            }
        }
        if (!empty($start_time) && !empty($end_time)) {
            $condition[] = ["create_time", "between", [date_to_time($start_time), date_to_time($end_time)]];
        }
        if ($search_text != "") {
            $condition[] = [$order_label, 'like', "%$search_text%"];
        }
        $order_common_model = new OrderCommonModel();

        //接收需要展示的字段
        $input_field = input('field', '');
        $order = $order_common_model->getOrderList($condition, $input_field, 'id desc');
        $header_arr = array(
            'A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z',
            'AA', 'AB', 'AC', 'AD', 'AE', 'AF', 'AG', 'AH', 'AI', 'AJ', 'AK', 'AL', 'AM', 'AN', 'AO', 'AP', 'AQ', 'AR', 'AS', 'AT', 'AU', 'AV', 'AW', 'AX', 'AY', 'AZ',
            'BA', 'BB', 'BC', 'BD', 'BE', 'BF', 'BG', 'BH', 'BI', 'BJ', 'BK', 'BL', 'BM', 'BN', 'BO', 'BP', 'BQ', 'BR', 'BS', 'BT', 'BU', 'BV', 'BW', 'BX', 'BY', 'BZ'
        );

        $input_field = explode(',', $input_field);
        //处理数据
        $order_export_model = new OrderExport();
        if (!empty($shop['data'])) {
            $order_list = $order_export_model->handleData($order['data'], $input_field);
        }

        $count = count($input_field);
        // 实例化excel
        $phpExcel = new \PHPExcel();

        $phpExcel->getProperties()->setTitle("订单信息-订单维度");
        $phpExcel->getProperties()->setSubject("订单信息-订单维度");
        //单独添加列名称
        $phpExcel->setActiveSheetIndex(0);

        $field = $order_export_model->order_field;
        for ($i = 0; $i < $count; $i++) {
            $phpExcel->getActiveSheet()->setCellValue($header_arr[$i] . '1', $field[$input_field[$i]]);
        }

        if (!empty($order_list)) {
            foreach ($order_list as $k => $v) {
                $start = $k + 2;
                for ($i = 0; $i < $count; $i++) {

                    $phpExcel->getActiveSheet()->setCellValue($header_arr[$i] . $start, $v[$input_field[$i]]);
                }
            }
        }

        // 重命名工作sheet
        $phpExcel->getActiveSheet()->setTitle('订单信息-订单维度');
        // 设置第一个sheet为工作的sheet
        $phpExcel->setActiveSheetIndex(0);
        // 保存Excel 2007格式文件，保存路径为当前路径，名字为export.xlsx
        $objWriter = \PHPExcel_IOFactory::createWriter($phpExcel, 'Excel2007');
        $file = date('Y年m月d日-订单信息', time()) . '.xlsx';
        $objWriter->save($file);

        header("Content-type:application/octet-stream");

        $filename = basename($file);
        header("Content-Disposition:attachment;filename = " . $filename);
        header("Accept-ranges:bytes");
        header("Accept-length:" . filesize($file));
        readfile($file);
        unlink($file);
        exit;

    }

    /**
     * 订单导出（已订单商品为主）
     */
    public function exportOrderGoods()
    {
        $order_status = input("order_status", "");//订单状态
        $order_name = input("order_name", '');
        $pay_type = input("pay_type", '');
        $order_from = input("order_from", '');
        $start_time = input("start_time", '');
        $end_time = input("end_time", '');
        $order_label = !empty($order_label_list[input("order_label")]) ? input("order_label") : "";
        $search_text = input("search", '');
        $promotion_type = input("promotion_type", '');
        $order_type = input("order_type", 2);

        $condition[] = ["o.order_type", "=", $order_type];
        $condition[] = ["o.site_id", "=", $this->site_id];
        $condition[] = ['o.delivery_store_id', '=', $this->store_id];

        //订单状态
        if ($order_status != "") {
            $condition[] = ["o.order_status", "=", $order_status];
        }
        //订单内容 模糊查询
        if ($order_name != "") {
            $condition[] = ["o.order_name", 'like', "%$order_name%"];
        }
        //订单来源
        if ($order_from != "") {
            $condition[] = ["o.order_from", "=", $order_from];
        }
        //订单支付
        if ($pay_type != "") {
            $condition[] = ["o.pay_type", "=", $pay_type];
        }
        //营销类型
        if ($promotion_type != "") {
            if($promotion_type == 'empty'){
                $condition[] = ["o.promotion_type", "=", ''];
            }else{
                $condition[] = ["o.promotion_type", "=", $promotion_type];
            }
        }
        if (!empty($start_time) && !empty($end_time)) {
            $condition[] = ["o.create_time", "between", [date_to_time($start_time), date_to_time($end_time)]];
        }
        if ($search_text != "") {
            $condition[] = ['o.' . $order_label, 'like', "%$search_text%"];
        }
        $order_common_model = new OrderCommonModel();
        $order_export_model = new OrderExport();

        $field = array_merge($order_export_model->order_field, $order_export_model->order_goods_field);
        //接收需要展示的字段
        $input_field = input('field', implode(',',array_keys($field)));
        $order = $order_common_model->getOrderGoodsDetailList($condition);
        $header_arr = array(
            'A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z',
            'AA', 'AB', 'AC', 'AD', 'AE', 'AF', 'AG', 'AH', 'AI', 'AJ', 'AK', 'AL', 'AM', 'AN', 'AO', 'AP', 'AQ', 'AR', 'AS', 'AT', 'AU', 'AV', 'AW', 'AX', 'AY', 'AZ',
            'BA', 'BB', 'BC', 'BD', 'BE', 'BF', 'BG', 'BH', 'BI', 'BJ', 'BK', 'BL', 'BM', 'BN', 'BO', 'BP', 'BQ', 'BR', 'BS', 'BT', 'BU', 'BV', 'BW', 'BX', 'BY', 'BZ'
        );

        $input_field = explode(',', $input_field);
        //处理数据

        if (!empty($order['data'])) {
            $order_list = $order_export_model->handleData($order['data'], $input_field);
        }

        $count = count($input_field);
        // 实例化excel
        $phpExcel = new \PHPExcel();

        $phpExcel->getProperties()->setTitle("订单信息-商品维度");
        $phpExcel->getProperties()->setSubject("订单信息-商品维度");
        //单独添加列名称
        $phpExcel->setActiveSheetIndex(0);


        for ($i = 0; $i < $count; $i++) {
            $phpExcel->getActiveSheet()->setCellValue($header_arr[$i] . '1', $field[$input_field[$i]]);
        }

        if (!empty($order_list)) {
            foreach ($order_list as $k => $v) {
                $start = $k + 2;
                for ($i = 0; $i < $count; $i++) {

                    $phpExcel->getActiveSheet()->setCellValue($header_arr[$i] . $start, $v[$input_field[$i]]);
                }
            }
        }

        // 重命名工作sheet
        $phpExcel->getActiveSheet()->setTitle('订单信息-商品维度');
        // 设置第一个sheet为工作的sheet
        $phpExcel->setActiveSheetIndex(0);
        // 保存Excel 2007格式文件，保存路径为当前路径，名字为export.xlsx
        $objWriter = \PHPExcel_IOFactory::createWriter($phpExcel, 'Excel2007');
        $file = date('Y年m月d日-订单信息', time()) . '.xlsx';
        $objWriter->save($file);

        header("Content-type:application/octet-stream");

        $filename = basename($file);
        header("Content-Disposition:attachment;filename = " . $filename);
        header("Accept-ranges:bytes");
        header("Accept-length:" . filesize($file));
        readfile($file);
        unlink($file);
        exit;

    }


    /**
     * 订单导出（维权订单）
     */
    public function exportRefundOrder()
    {
        $refund_status = input("refund_status", "");//退款状态
        $sku_name = input("sku_name", '');//商品名称
        $refund_type = input("refund_type", '');//退款方式
        $start_time = input("start_time", '');//开始时间
        $end_time = input("end_time", '');//结束时间
        $order_no = input("order_no", '');//订单编号
        $delivery_status = input("delivery_status", '');//物流状态
        $refund_no = input("refund_no", '');//退款编号

        $delivery_no = input("delivery_no", '');//物流编号
        $refund_delivery_no = input("refund_delivery_no", '');//退款物流编号

        $order_common_model = new OrderCommonModel();

        $condition[] = ['og.site_id', '=', $this->site_id];
        //退款状态
        if ($refund_status != "") {
            $condition[] = ["og.refund_status", "=", $refund_status];
        } else {
            $condition[] = ["og.refund_status", "<>", 0];
        }
        //物流状态
        if ($delivery_status != "") {
            $condition[] = ["og.delivery_status", "=", $delivery_status];
        }
        //商品名称
        if ($sku_name != "") {
            $condition[] = ["og.sku_name", "like", "%$sku_name%"];
        }
        //退款方式
        if ($refund_type != "") {
            $condition[] = ["og.refund_type", "=", $refund_type];
        }
        //退款编号
        if ($refund_no != "") {
            $condition[] = ["og.refund_no", "like", "%$refund_no%"];
        }
        //订单编号
        if ($order_no != "") {
            $condition[] = ["og.order_no", "like", "%$order_no%"];
        }
        //物流编号
        if ($delivery_no != "") {
            $condition[] = ["og.delivery_no", "like", "%$delivery_no%"];
        }
        //退款物流编号
        if ($refund_delivery_no != "") {
            $condition[] = ["og.refund_delivery_no", "like", "%$refund_delivery_no%"];
        }

        if (!empty($start_time) && empty($end_time)) {
            $condition[] = ["og.refund_action_time", ">=", date_to_time($start_time)];
        } elseif (empty($start_time) && !empty($end_time)) {
            $condition[] = ["og.refund_action_time", "<=", date_to_time($end_time)];
        } elseif (!empty($start_time) && !empty($end_time)) {
	        $condition[] = [ 'og.refund_action_time', 'between', [ date_to_time($start_time), date_to_time($end_time) ] ];
        }

        $order_export_model = new OrderExport();
        $field = array_merge($order_export_model->order_field, $order_export_model->order_goods_field);
        //接收需要展示的字段
        $input_field = input('field', implode(',',array_keys($field)));
        $order = $order_common_model->getOrderGoodsDetailList($condition);
        $header_arr = array(
            'A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z',
            'AA', 'AB', 'AC', 'AD', 'AE', 'AF', 'AG', 'AH', 'AI', 'AJ', 'AK', 'AL', 'AM', 'AN', 'AO', 'AP', 'AQ', 'AR', 'AS', 'AT', 'AU', 'AV', 'AW', 'AX', 'AY', 'AZ',
            'BA', 'BB', 'BC', 'BD', 'BE', 'BF', 'BG', 'BH', 'BI', 'BJ', 'BK', 'BL', 'BM', 'BN', 'BO', 'BP', 'BQ', 'BR', 'BS', 'BT', 'BU', 'BV', 'BW', 'BX', 'BY', 'BZ'
        );

        $input_field = explode(',', $input_field);
        //处理数据
        
        if (!empty($order['data'])) {
            $order_list = $order_export_model->handleData($order['data'], $input_field);
        }

        $count = count($input_field);
        // 实例化excel
        $phpExcel = new \PHPExcel();

        $phpExcel->getProperties()->setTitle("退款维权订单");
        $phpExcel->getProperties()->setSubject("退款维权订单");
        //单独添加列名称
        $phpExcel->setActiveSheetIndex(0);


        for ($i = 0; $i < $count; $i++) {
            $phpExcel->getActiveSheet()->setCellValue($header_arr[$i] . '1', $field[$input_field[$i]]);
        }

        if (!empty($order_list)) {
            foreach ($order_list as $k => $v) {
                $start = $k + 2;
                for ($i = 0; $i < $count; $i++) {

                    $phpExcel->getActiveSheet()->setCellValue($header_arr[$i] . $start, $v[$input_field[$i]]);
                }
            }
        }

        // 重命名工作sheet
        $phpExcel->getActiveSheet()->setTitle('退款维权订单');
        // 设置第一个sheet为工作的sheet
        $phpExcel->setActiveSheetIndex(0);
        // 保存Excel 2007格式文件，保存路径为当前路径，名字为export.xlsx
        $objWriter = \PHPExcel_IOFactory::createWriter($phpExcel, 'Excel2007');
        $file = date('Y年m月d日-退款维权订单', time()) . '.xlsx';
        $objWriter->save($file);

        header("Content-type:application/octet-stream");

        $filename = basename($file);
        header("Content-Disposition:attachment;filename = " . $filename);
        header("Accept-ranges:bytes");
        header("Accept-length:" . filesize($file));
        readfile($file);
        unlink($file);
        exit;
    }

    /**
     * 导出字段
     * @return array
     */
    public function getPrintingField()
    {
        $model = new OrderExport();
        $data = [
            'order_field' => $model->order_field,
            'order_goods_field' => $model->order_goods_field
        ];

        return success('1', '', $data);
    }


    public function printOrder() {
        $order_id = input('order_id', 0);
        $order_common_model = new OrderCommonModel();
        $condition = array(
            ['order_id', '=', $order_id],
            ['delivery_store_id', '=', $this->store_id]
        );
        $order_detail_result = $order_common_model->getOrderDetail($condition);
        $order_detail = $order_detail_result["data"];
        $this->assign("order_detail", $order_detail);
        return $this->fetch('order/print_order');
    }

}