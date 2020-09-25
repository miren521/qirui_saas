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


namespace addon\bargain\model;

use app\model\order\OrderCreate;
use app\model\goods\GoodsStock;
use app\model\store\Store;
use think\facade\Cache;
use app\model\express\Express;
use app\model\system\Pay;
use app\model\express\Config as ExpressConfig;
use app\model\order\Config;
use app\model\express\Local;

/**
 * 订单创建（砍价）
 *
 * @author Administrator
 *
 */
class BargainOrderCreate extends OrderCreate
{

    public $goods_money = 0;//商品金额
    public $balance_money = 0;//余额
    public $delivery_money = 0;//配送费用
    public $coupon_money = 0;//优惠券金额
    public $adjust_money = 0;//调整金额
    public $invoice_money = 0;//发票费用
    public $promotion_money = 0;//优惠金额
    public $order_money = 0;//订单金额
    public $pay_money = 0;//支付总价
    public $is_virtual = 0;  //是否是虚拟类订单
    public $order_name = '';  //订单详情
    public $goods_num = 0;  //商品种数
    public $member_balance_money = 0;//会员账户余额(计算过程中会逐次减少)
    public $pay_type = 'ONLINE_PAY';//支付方式

    public $invoice_delivery_money = 0;
    public $error = 0;  //是否有错误
    public $error_msg = '';  //错误描述

    /**
     * 订单创建
     * @param unknown $data
     */
    public function create($data)
    {
        //查询出会员相关信息
        $calculate_data = $this->calculate($data);

        if(isset($calculate_data['code']) && $calculate_data['code'] < 0)
            return $calculate_data;

        if ($this->error > 0)
            return $this->error(['error_code' => $this->error], $this->error_msg);


        $pay = new Pay();
        $out_trade_no = $pay->createOutTradeNo();
        model("order")->startTrans();
        //循环生成多个订单
        try {
            $pay_money = 0;
            $goods_stock_model = new GoodsStock();

            $shop_goods_list = $calculate_data['shop_goods_list'];

            $item_delivery = $shop_goods_list['delivery'] ?? [];
            $delivery_type = $item_delivery['delivery_type'] ?? '';
            $delivery_type_name = Express::express_type[$delivery_type]["title"] ?? '';

            //订单主表
            $order_type = $this->orderType($shop_goods_list, $calculate_data);
            $order_no = $this->createOrderNo($shop_goods_list['site_id']);
            $data_order = [
                'order_no' => $order_no,
                'site_id' => $shop_goods_list['site_id'],
                'site_name' => $shop_goods_list['site_name'],
                'order_from' => $data['order_from'],
                'order_from_name' => $data['order_from_name'],
                'order_type' => $order_type['order_type_id'],
                'order_type_name' => $order_type['order_type_name'],
                'order_status_name' => $order_type['order_status']['name'],
                'order_status_action' => json_encode($order_type['order_status'], JSON_UNESCAPED_UNICODE),
                'out_trade_no' => $out_trade_no,
                'member_id' => $data['member_id'],
                'name' => $calculate_data['member_address']['name'] ?? '',
                'mobile' => $calculate_data['member_address']['mobile'] ?? '',
                'telephone' => $calculate_data['member_address']['telephone'] ?? '',
                'province_id' => $calculate_data['member_address']['province_id'] ?? '',
                'city_id' => $calculate_data['member_address']['city_id'] ?? '',
                'district_id' => $calculate_data['member_address']['district_id'] ?? '',
                'community_id' => $calculate_data['member_address']['community_id'] ?? '',
                'address' => $calculate_data['member_address']['address'] ?? '',
                'full_address' => $calculate_data['member_address']['full_address'] ?? '',
                'longitude' => $calculate_data['member_address']['longitude'] ?? '',
                'latitude' => $calculate_data['member_address']['latitude'] ?? '',
                'buyer_ip' => request()->ip(),
                'goods_money' => $shop_goods_list['goods_money'],
                'delivery_money' => $shop_goods_list['delivery_money'],
                'coupon_id' => isset($shop_goods_list['coupon_id']) ? $shop_goods_list['coupon_id'] : 0,
                'coupon_money' => $shop_goods_list['coupon_money'] ?? 0,
                'adjust_money' => $shop_goods_list['adjust_money'],
                'invoice_money' => $shop_goods_list['invoice_money'],
                'promotion_money' => $shop_goods_list['promotion_money'],
                'order_money' => $shop_goods_list['order_money'],
                'balance_money' => $shop_goods_list['balance_money'],
                'pay_money' => $shop_goods_list['pay_money'],
                'create_time' => time(),
                'is_enable_refund' => 0,
                'order_name' => $shop_goods_list["order_name"],
                'goods_num' => $shop_goods_list['goods_num'],
                'delivery_type' => $delivery_type,
                'delivery_type_name' => $delivery_type_name,
                'delivery_store_id' => $shop_goods_list["delivery_store_id"] ?? 0,
                "delivery_store_name" => $shop_goods_list["delivery_store_name"] ?? '',
                "delivery_store_info" => $shop_goods_list["delivery_store_info"] ?? '',
                "buyer_message" => $shop_goods_list["buyer_message"] ?? '',
                "promotion_type" => "bargain",
                "promotion_type_name" => "砍价",
                "promotion_status_name" => "",

                "website_id" => $shop_goods_list["website_id"],

                'buyer_ask_delivery_time' => $shop_goods_list['buyer_ask_delivery_time'] ?? '',//定时达
                "platform_coupon_id" => $shop_goods_list["platform_coupon_id"] ?? 0,
                "platform_coupon_money" => $shop_goods_list["platform_coupon_money"] ?? 0,
                "platform_coupon_total_money" => $shop_goods_list["platform_coupon_total_money"] ?? 0,
                "platform_coupon_shop_money" => $shop_goods_list["platform_coupon_shop_money"] ?? 0,
            ];
            $order_id = model("order")->add($data_order);
            $pay_money += $shop_goods_list['pay_money'];
            //订单项目表
            foreach ($shop_goods_list['goods_list'] as $k_order_goods => $order_goods) {
                $data_order_goods = array(
                    'order_id' => $order_id,
                    'site_id' => $shop_goods_list['site_id'],
                    'order_no' => $order_no,
                    'member_id' => $data['member_id'],
                    'sku_id' => $order_goods['sku_id'],
                    'sku_name' => $order_goods['sku_name'],
                    'sku_image' => $order_goods['sku_image'],
                    'sku_no' => $order_goods['sku_no'],
                    'is_virtual' => $order_goods['is_virtual'],
                    'goods_class' => $order_goods['goods_class'],
                    'goods_class_name' => $order_goods['goods_class_name'],
                    'price' => $order_goods['price'],
                    'cost_price' => $order_goods['cost_price'],
                    'num' => $order_goods['num'],
                    'goods_money' => $order_goods['goods_money'],
                    'cost_money' => $order_goods['cost_price'] * $order_goods['num'],
                    'goods_id' => $order_goods['goods_id'],
                    'delivery_status' => 0,
                    'delivery_status_name' => "未发货",

                    'real_goods_money' => $order_goods['real_goods_money'] ?? 0,
                    'coupon_money' => $order_goods['coupon_money'] ?? 0,
                    'promotion_money' => $order_goods['promotion_money'] ?? 0,
                    'platform_coupon_money' => $order_goods['platform_coupon_money'] ?? 0
                );
                model("order_goods")->add($data_order_goods);
                //库存变化
                $stock_result = $goods_stock_model->decStock(["sku_id" => $order_goods['sku_id'], "num" => $order_goods['num']]);
                if ($stock_result["code"] != 0) {
                    model("order")->rollback();
                    return $stock_result;
                }
            }

            //扣除余额(统一扣除)
            if ($calculate_data["balance_money"] > 0) {
                $balance_result = $this->useBalance($calculate_data, $this);
                if ($balance_result["code"] < 0) {
                    model("order")->rollback();
                    return $balance_result;
                }
            }

            // 砍价绑定订单id
            $bargain_data = ['order_id' =>  $order_id];
            if ($calculate_data['bargain_info']['buy_type'] == 0) {
                $bargain_data['status'] = 1;
            }
            model('promotion_bargain_launch')->update($bargain_data, [ ['launch_id', '=', $data['id'] ] ]);

            $result_list = event("OrderCreate", ['order_id' => $order_id]);
            if (!empty($result_list)) {
                foreach ($result_list as $k => $v) {
                    if (!empty($v) && $v["code"] < 0) {
                        model("order")->rollback();
                        return $v;
                    }
                }
            }

            //生成整体支付单据
            $pay->addPay($shop_goods_list['site_id'], $out_trade_no, $this->pay_type, $this->order_name, $this->order_name, $this->pay_money, '', 'OrderPayNotify', '');
            $this->addOrderCronClose($order_id, $shop_goods_list['site_id']);//增加关闭订单自动事件
            model("order")->commit();
            return $this->success($out_trade_no);
        } catch (\Exception $e) {
            model("order")->rollback();
            return $this->error('', $e->getMessage());
        }

    }

    /**
     * 订单计算
     * @param unknown $data
     */
    public function calculate($data)
    {
        $data = $this->initMemberAddress($data);
        $data = $this->initMemberAccount($data);//初始化会员账户

        //余额付款
        if ($data['is_balance'] > 0) {
            $this->member_balance_money = $data["member_account"]["balance_total"] ?? 0;
        }
        //查询砍价信息
        $bargain_model = new Bargain();
        $launch_id = $data["id"];
        $bargain_info_result = $bargain_model->getBargainLaunchDetail([ [ "launch_id", "=", $launch_id ] ]);
        $bargain_info = $bargain_info_result["data"];
        $data["bargain_info"] = $bargain_info;

        //商品列表信息
        $shop_goods_list = $this->getOrderGoodsCalculate($data);

        //判断砍价是否成功
        if ($bargain_info["buy_type"] == 1 && $bargain_info["status"] != 1) {
            $this->error = 1;
            $this->error_msg = "该商品您尚未砍价成功!";
        }
        //判断砍价是否已经下单了
        if (!empty($bargain_info["order_id"])) {
            $this->error = 1;
            $this->error_msg = "本次砍价您已下单过了!";
        }

        $data['shop_goods_list'] = $this->shopOrderCalculate($shop_goods_list, $data);

        //总优惠使用
        $data = $this->eachShopOrder($data, false,$this);

        //总结计算
        $data['delivery_money'] = $this->delivery_money;
        $data['coupon_money'] = $this->coupon_money;
        $data['adjust_money'] = $this->adjust_money;
        $data['invoice_money'] = $this->invoice_money;
        $data['promotion_money'] = $this->promotion_money;
        $data['order_money'] = $this->order_money;
        $data['balance_money'] = $this->balance_money;
        $data['pay_money'] = $this->pay_money;
        $data['goods_money'] = $this->goods_money;
        $data['goods_num'] = $this->goods_num;
        $data['is_virtual'] = $this->is_virtual;
        return $data;
    }

    /**
     * 待付款订单
     * @param unknown $data
     */
    public function orderPayment($data)
    {
        $calculate_data = $this->calculate($data);
        if (isset($calculate_data['code']) && $calculate_data['code'] < 0)
            return $calculate_data;

        $shop_goods_list = $calculate_data['shop_goods_list'];
        $calculate_data['shop_goods_list'] = $this->itemPayment($shop_goods_list, $calculate_data, false, $this);
        return $this->success($calculate_data);
    }

    /**
     * 获取商品的计算信息
     * @param unknown $data
     */
    public function getOrderGoodsCalculate($data)
    {
        $shop_goods_list = [];
        $goods_list = $this->getBargainGoodsInfo($data);
        $goods_list['promotion_money'] = 0;
        $shop_goods_list = $goods_list;
        return $shop_goods_list;
    }

    /**
     * 获取团购商品列表信息
     * @param unknown $bl_id
     */
    public function getBargainGoodsInfo($data)
    {
        //组装商品列表
        $field = 'sku_id,sku_name, sku_no,
            price, discount_price, cost_price, stock, weight, volume, sku_image, 
            site_id, goods_state, verify_state, is_virtual, 
            is_free_shipping, shipping_template, goods_class, goods_class_name,goods_id, site_name,sku_spec_format,goods_name,website_id';



        $info = model("goods_sku")->getInfo([['sku_id', '=', $data["bargain_info"]["sku_id"]]], $field);
        $shop_goods_list = [];

        if (!empty($info)) {
            //用于过滤商品
            if ($info['goods_state'] == 0 || $info['verify_state'] == 0) {
                $this->error = 1;
                $this->error_msg = '商品未上架或未通过审核';
            }
            //判断是否是虚拟订单
            if ($info['is_virtual']) {
                $this->is_virtual = 1;
            } else {
                $this->is_virtual = 0;
            }
            $info["num"] = $data["num"];
            $site_id = $info['site_id'];

            $price = $data["bargain_info"]["curr_price"];
            $goods_money = $price * $info['num'];
            $info["price"] = $price;
            $info["goods_money"] = $goods_money;
            $info['real_goods_money'] = $goods_money;//真实商品金额
            $info['coupon_money'] = 0;//优惠券金额
            $info['promotion_money'] = 0;//优惠金额

            $shop_goods_list['site_id'] = $site_id;
            $shop_goods_list['website_id'] = $info['website_id'];
            $shop_goods_list['site_name'] = $info['site_name'];
            $shop_goods_list['goods_money'] = $goods_money;
            $shop_goods_list['goods_list_str'] = $info['sku_id'] . ':' . $info['num'];
            $shop_goods_list['order_name'] = string_split("", ",", $info['sku_name']);
            $shop_goods_list['goods_num'] = $info['num'];
            $shop_goods_list['goods_list'][] = $info;

            $shop_goods_list['coupon_money'] = $info['coupon_money'];
            $shop_goods_list['promotion_money'] = $info['promotion_money'];
        }
        return $shop_goods_list;
    }

    /**
     * 获取店铺订单计算
     * @param unknown $site_id 店铺id
     * @param unknown $goods_money 商品总价
     * @param unknown $goods_list 店铺商品列表
     * @param unknown $data 传输生成订单数据
     */
    public function shopOrderCalculate($shop_goods, $data)
    {

        //查询店铺信息(以及店铺对于商品的相关控制)
        $shop_goods = $this->getShopInfo($shop_goods);
        $site_id = $shop_goods['site_id'];
        //定义计算金额
        $adjust_money = 0;     //调整金额
        $invoice_money = 0;    //发票金额
        //运费计算
        $shop_goods = $this->delivery($shop_goods, $data);

        //是否符合免邮
        $is_free_delivery = $shop_goods['is_free_delivery'] ?? false;
        if ($is_free_delivery) {
            $shop_goods['delivery_money'] = 0;
        }

        $shop_goods['order_money'] = $shop_goods['goods_money'] + $shop_goods['delivery_money'] - $shop_goods['promotion_money'];

        if($shop_goods['order_money'] < 0)
        {
            $shop_goods['order_money'] = 0;
        }

        //发票相关
        $shop_goods = $this->invoice($shop_goods, $data);
        $shop_goods['order_money'] = $shop_goods['order_money'] + $shop_goods['invoice_money'] + $shop_goods['invoice_delivery_money'];
        //买家留言
        if(isset($data['buyer_message']) && isset($data['buyer_message'][$site_id])){
            $item_buyer_message = $data['buyer_message'][$site_id];
            $shop_goods["buyer_message"] = $item_buyer_message;
        }else{
            $shop_goods["buyer_message"] = '';
        }

        //总结计算
        $shop_goods['adjust_money'] = $adjust_money;
        return $shop_goods;
    }

}