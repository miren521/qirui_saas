<?php
// +---------------------------------------------------------------------+
// | NiuCloud | [ WE CAN DO IT JUST NiuCloud ]                |
// +---------------------------------------------------------------------+
// | Copy right 2019-2029 www.niucloud.com                          |
// +---------------------------------------------------------------------+
// | Author | NiuCloud <niucloud@outlook.com>                       |
// +---------------------------------------------------------------------+
// | Repository | https://github.com/niucloud/framework.git          |
// +---------------------------------------------------------------------+

namespace addon\printer\model;

use addon\printer\data\sdk\yilianyun\api\PrintService;
use addon\printer\data\sdk\yilianyun\config\YlyConfig;
use app\model\order\OrderCommon as OrderCommonModel;
use app\model\BaseModel;
use app\model\shop\Shop;
use Exception;

class PrinterOrder extends BaseModel
{

    /************************************************ 正式打印 start ******************************************************************/
    /**
     * 订单打印
     * @param $order_id
     * @return array
     */
    public function printOrder($order_id)
    {
        //获取订单详情
        $order_common_model = new OrderCommonModel();
        $condition = array(
            ['order_id', '=', $order_id]
        );
        $order_detail_result = $order_common_model->getOrderDetail($condition);
        $order_info = $order_detail_result['data'];

        if (empty($order_info)) {
            return $this->success();
        }

        //获取店铺信息
        $shop_model = new Shop();
        $shop = $shop_model->getShopInfo([['site_id', '=', $order_info['site_id']]]);
        $shop_info = $shop['data'];

        //获取打印机列表
        $print_model = new Printer();
        $printer_data = $print_model->getPrinterList([['site_id', '=', $order_info['site_id']], ['order_type', 'like', '%,' . $order_info['order_type'] . ',%']]);
        if (empty($printer_data['data'])) {
            return $this->success();
        }
        foreach ($printer_data['data'] as $v) {

            switch ($v['brand']) {

                case '365'://365打印机

                    break;
                case 'feie'://飞鹅打印机

                    break;
                case 'yilianyun'://易联云打印机

                    $this->Ylyprint($order_info, $v, $shop_info);
                    break;
            }
        }
        return $this->success();
    }

    /**
     * 易联云打印
     * @param $order_info
     * @param $printer
     * @param $shop_info
     */
    public function Ylyprint($order_info, $printer, $shop_info)
    {
        //打印模板
        $print_template_model = new PrinterTemplate();
        $print_template_data = $print_template_model->getPrinterTemplateInfo([['template_id', '=', $printer['template_id']]]);
        $print_template = $print_template_data['data'];

        $config = new YlyConfig($printer['open_id'], $printer['apikey']);

        $printer_model = new Printer();
        $access_token = $printer_model->getYlyToken($config, $printer['site_id']);
        $machine_code = $printer['printer_code'];    //商户授权机器码
        $origin_id = $order_info['order_no'];        //内部订单号(32位以内)

        /**文本接口开始**/
        $print = new PrintService($access_token, $config);
        $content = "<MN>" . $printer['print_num'] . "</MN>";
        //小票名称
        if ($print_template['title'] != '') {
            $content .= "<center>" . $print_template['title'] . "</center>";
            $content .= str_repeat('.', 32);
        }
        //商城名称
        if ($print_template['head'] == 1) {
            $content .= "<FH2><FS><center>" . $print_template['site_name'] . "</center></FS></FH2>";
            $content .= str_repeat('.', 32);
        }
        $content .= "订单时间:" . date("Y-m-d H:i", $order_info['pay_time']) . "\n";
        $content .= "订单编号:" . $order_info['order_no'] . "\n";

        $content .= str_repeat('.', 32);
        $content .= "<table>";
        $content .= "<tr><td>商品名称</td><td></td><td>数量</td><td>金额</td></tr>";
        $content .= "</table>";
        $content .= str_repeat('.', 32);
        $content .= "<table>";
        foreach ($order_info['order_goods'] as $goods) {

            $sku_name_list = $this->r_str_pad_1($goods['sku_name'], 7);
            foreach ($sku_name_list as $index => $value) {
                if ($index == 0) {
                    $content .= "<tr><td>" . $value . "</td><td></td><td>x" . $goods['num'] . "</td><td>￥" . $goods['price'] . "</td></tr>";
                } else {
                    $content .= "<tr><td>" . $value . "</td></tr>";
                }
            }
        }
        $content .= "</table>";
        $content .= str_repeat('.', 32);
        if ($order_info["goods_money"] > 0) {
            $content .= "商品总额：￥" . $order_info["goods_money"] . "\n";
        }
        if ($order_info["coupon_money"] > 0) {
            $content .= "店铺优惠券：￥" . $order_info["coupon_money"] . "\n";
        }
        if ($order_info["promotion_money"] > 0) {
            $content .= "店铺优惠：￥" . $order_info["promotion_money"] . "\n";
        }
//        if ($order_info["point_money"] > 0) {
//            $content .= "积分抵扣：￥" . $order_info["point_money"] . "\n";
//        }
        if ($order_info["adjust_money"] > 0) {
            $content .= "订单调价：￥" . $order_info["adjust_money"] . "\n";
        }
        if ($order_info["delivery_money"] > 0) {
            $content .= "配送费用：￥" . $order_info["delivery_money"] . "\n";
        }
//        if ($order_info["invoice_money"] > 0) {
//            $content .= "发票费用：￥" . $order_info["invoice_money"] . "\n";
//        }
//        if ($order_info["invoice_delivery_money"] > 0) {
//            $content .= "发票邮寄费用：￥" . $order_info["invoice_delivery_money"] . "\n";
//        }
        if ($order_info["goods_num"] > 0) {
            $content .= "订单共" . $order_info['goods_num'] . "件商品，总计: ￥" . $order_info['order_money'] . " \n";
        }
        $content .= str_repeat('.', 32);

        /******************** 备注信息 **************************/
        //买家留言
        if ($print_template['buy_notes'] == 1) {
            $content .= "<FH2>买家留言：" . $order_info["buyer_message"] . "</FH2>\n";
            $content .= str_repeat('.', 32);
        }
        //卖家留言
//        if($print_template['seller_notes'] == 1){
//            $content .= "<FH2>卖家留言：".$order_info["remark"]."</FH2>\n";
//            $content .= str_repeat('.', 32);
//        }

        /******************** 买家信息 **************************/
        //买家姓名
        if ($print_template['buy_name'] == 1) {
            $content .= "" . $order_info["name"] . "\n";
        }
        //联系方式
        if ($print_template['buy_mobile'] == 1) {
            $content .= "" . $order_info["mobile"] . "\n";
        }
        //地址
        if ($print_template['buy_address'] == 1) {
            $content .= "" . $order_info['full_address'] . "-" . $order_info['address'] . "\n";
        }
        if ($print_template['buy_name'] == 1 || $print_template['buy_mobile'] == 1 || $print_template['buy_address'] == 1) {
            $content .= str_repeat('.', 32);
        }
        /******************** 商城信息 **************************/
        //联系方式
        if ($print_template['shop_mobile'] == 1) {
            $content .= "" . $shop_info["mobile"] . "\n";
        }
        //地址
        if ($print_template['shop_address'] == 1) {

            $content .= "" . $shop_info['province_name'] . $shop_info['city_name'] . $shop_info['district_name'] . $shop_info['address'] . "\n";
        }
        if ($print_template['shop_mobile'] == 1 || $print_template['shop_address'] == 1) {
            $content .= str_repeat('.', 32);
        }
        //二维码
        if ($print_template['shop_qrcode'] == 1) {
            $content .= "<QR>" . $print_template['qrcode_url'] . "</QR>";
            $content .= str_repeat('.', 32);
        }

        /******************** 门店信息 **************************/
        if ($order_info['delivery_store_id'] > 0 && !empty($order_info['delivery_store_name']) && !empty($order_info['delivery_store_info'])) {
            $store_info = json_decode($order_info['delivery_store_info'], true);

            $content .= "" . $order_info["delivery_store_name"] . "\n";//门店名称
            $content .= "" . $store_info["telphone"] . "\n";//门店电话
            $content .= "" . $store_info["full_address"] . "\n";//门店地址
            $content .= str_repeat('.', 32);
        }

        //底部内容
        if (!empty($print_template['bottom'])) {

            $content .= "<center>" . $print_template['bottom'] . "</center>";
        }

        try {
            $print->index($machine_code, $content, $origin_id);
        } catch (Exception $e) {
            echo $e->getMessage();
        }
    }


    /**
     * 补齐空格
     * 每n个中文字符长度为一个数组元素
     */
    private function r_str_pad_1($input, $n = 7)
    {
        $string = "";
        $count = 0;
        $c_count = 0;
        $arr = array();
        for ($i = 0; $i < mb_strlen($input, 'UTF-8'); $i++) {
            $char = mb_substr($input, $i, 1, 'UTF-8');
            $string .= $char;
            if (strlen($char) == 3) {
                $count += 2;
                $c_count++;
            } else {
                $count += 1;
            }
            if ($count >= $n * 2) {
                $arr[] = $string;
                $string = '';
                $count = 0;
                $c_count = 0;
            }
        }
        if ($count < $n * 2) {
            $string = str_pad($string, $n * 2 + $c_count);
            $arr[] = $string;
        }
        return $arr;
    }



    /************************************************ 正式打印 end ******************************************************************/


    /************************************************ 测试打印 start ******************************************************************/

    /**
     * 测试打印
     * @param $printer_id
     * @param $site_id
     * @return array
     */
    public function testPrint($printer_id, $site_id)
    {
        //获取打印机列表
        $printer_info = model('printer')->getInfo([['site_id', '=', $site_id], ['printer_id', '=', $printer_id]]);
        if (empty($printer_info)) {
            return $this->success();
        }
        $res = [];
        switch ($printer_info['brand']) {

            case '365'://365打印机

                break;
            case 'feie'://飞鹅打印机

                break;
            case 'yilianyun'://易联云打印机

                $res = $this->testYlyprint($printer_info);
                break;
        }
        return $res;
    }

    /**
     * 测试
     * @param $printer
     * @return array
     */
    public function testYlyprint($printer)
    {
        $config = new YlyConfig($printer['open_id'], $printer['apikey']);

        $printer_model = new Printer();
        $access_token = $printer_model->getYlyToken($config, $printer['site_id']);
        $machine_code = $printer['printer_code'];    //商户授权机器码
        $origin_id = date('YmdHis').rand(1,999);        //内部订单号(32位以内)

        /**文本接口开始**/
        $print = new PrintService($access_token, $config);
        $content = "<MN>" . $printer['print_num'] . "</MN>";

        $content .= "<center>小票名称</center>";
        $content .= str_repeat('.', 32);
        $content .= "<FH2><FS><center>商城名称</center></FS></FH2>";
        $content .= str_repeat('.', 32);

        $content .= "订单时间:" . date("Y-m-d H:i") . "\n";
        $content .= "订单编号:" . $origin_id . "\n";

        $content .= str_repeat('.', 32);
        $content .= "<table>";
        $content .= "<tr><td>商品名称</td><td></td><td>数量</td><td>金额</td></tr>";
        $content .= "</table>";
        $content .= str_repeat('.', 32);

        $content .= "<table>";
        $content .= "<tr><td>烤土豆(超级辣)</td><td></td><td>x3</td><td>5</td></tr>";
        $content .= "<tr><td>烤豆干(超级辣)</td><td></td><td>x2</td><td>10</td></tr>";
        $content .= "<tr><td>烤鸡翅(超级辣)</td><td></td><td>x3</td><td>15</td></tr>";
        $content .= "</table>";
        $content .= str_repeat('.', 32);

        $content .= "商品总额：￥30 \n";
        $content .= "订单共8件商品，总计: ￥30 \n";
        $content .= str_repeat('.', 32);

        /******************** 备注信息 **************************/
        //买家留言

        $content .= "<FH2>买家留言：微辣，多放孜然</FH2>\n";
        $content .= str_repeat('.', 32);

        $content .= "<center>谢谢惠顾，欢迎下次光临</center>";

        try {
            $res = $print->index($machine_code, $content, $origin_id);
            if($res -> error == 0){
                return $this->success();
            }else{
                return $this->error('',$res->error_description);
            }
        } catch (Exception $e) {
            return $this->error('',$e->getMessage());
        }
    }


}