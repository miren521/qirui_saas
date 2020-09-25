<?php
/**
 * Niushop商城系统 - 团队十年电商经验汇集巨献!
 * =========================================================
 * Copy right 2019-2029 山西牛酷信息科技有限公司, 保留所有权利。
 * ----------------------------------------------
 * 官方网址: https://www.niushop.com
 * 这不是一个自由软件！您只能在不用于商业目的的前提下对程序代码进行修改和使用。
 * 任何企业和个人不允许对程序代码以任何形式任何目的再发布。
 * =========================================================
 */

namespace addon\wholesale\model;

use app\model\message\Message;
use app\model\order\OrderCreate;
use app\model\goods\GoodsStock;
use app\model\store\Store;
use think\facade\Cache;
use app\model\express\Express;
use app\model\system\Pay;
use addon\wholesale\model\Cart as CartModel;
use app\model\express\Config as ExpressConfig;
use think\facade\Db;

/**
 * 订单创建(批发)
 *
 * @author Administrator
 *
 */
class WholesaleOrderCreate extends OrderCreate
{
	
	public $goods_money = 0;//商品金额
	public $delivery_money = 0;//配送费用
	public $coupon_money = 0;//优惠券金额
	public $adjust_money = 0;//调整金额
	public $promotion_money = 0;//优惠金额
	public $order_money = 0;//订单金额
	public $pay_money = 0;//支付总价
	public $is_virtual = 0;  //是否是虚拟类订单
	public $order_name = '';  //订单详情
	public $goods_num = 0;  //商品种数
	public $pay_type = 'ONLINE_PAY';//支付方式

    public $is_exist_not_free = false;
    public $is_exist_free = false;
    public $balance_money = 0;//余额
    public $member_balance_money = 0;//会员账户余额(计算过程中会逐次减少)
	public $error = 0;  //是否有错误
	public $error_msg = '';  //错误描述

    public $invoice_money = 0;//发票费用
    public $invoice_delivery_money = 0;//发票邮寄费用


	
	/**
	 * 订单创建
	 * @param unknown $data
	 */
    /**
     * 订单创建
     * @param $data
     * @return array|mixed
     */
    public function create($data)
    {
        $calculate_data = $this->calculate($data);
        if (isset($calculate_data['code']) && $calculate_data['code'] < 0)
            return $calculate_data;

        if ($this->error > 0) {
            return $this->error(['error_code' => $this->error], $this->error_msg);
        }
        $pay = new Pay();
        if($this->is_exist_not_free){
            $out_trade_no = $pay->createOutTradeNo();
            $order_name = [];
        }
        if($this->is_exist_free){
            $free_out_trade_no = $pay->createOutTradeNo();
            $free_order_name = [];
        }

        model("order")->startTrans();
        //循环生成多个订单
        try{
            $pay_money = 0;
            $goods_stock_model = new GoodsStock();
            foreach ($calculate_data['shop_goods_list'] as $k => $v)
            {
                $item_delivery = $v['delivery'] ?? [];
                $delivery_type = $item_delivery['delivery_type'] ?? '';
                $delivery_type_name = Express::express_type[$delivery_type]["title"] ?? '';

                //判断交易流水号
                if($v["pay_money"] == 0){
                    $temp_out_trade_no = $free_out_trade_no;
                    $free_order_name[] = $v["order_name"];
                }else{
                    $temp_out_trade_no = $out_trade_no;
                    $order_name[] = $v["order_name"];
                }
                //订单主表
                $order_type = $this->orderType($v, $calculate_data);
                $order_no = $this->createOrderNo($v['site_id']);
                $data_order = [
                    'order_no'    => $order_no,
                    'site_id'     => $v['site_id'],
                    'site_name'   => $v['site_name'],
                    'order_from'  => $data['order_from'],
                    'order_from_name' => $data['order_from_name'],
                    'order_type' => $order_type['order_type_id'],
                    'order_type_name' => $order_type['order_type_name'],
                    'order_status_name' => $order_type['order_status']['name'],
                    'order_status_action' => json_encode($order_type['order_status'], JSON_UNESCAPED_UNICODE),
                    'out_trade_no' => $temp_out_trade_no,
                    'member_id' => $data['member_id'],
                    'name' => $calculate_data['member_address']['name'] ?? '',
                    'mobile' => $calculate_data['member_address']['mobile'] ?? '',
                    'telephone' => $calculate_data['member_address']['telephone'] ?? '',
                    'province_id' => $calculate_data['member_address']['province_id'] ?? '',
                    'city_id'  => $calculate_data['member_address']['city_id'] ?? '',
                    'district_id' => $calculate_data['member_address']['district_id'] ?? '',
                    'community_id' => $calculate_data['member_address']['community_id'] ?? '',
                    'address' => $calculate_data['member_address']['address'] ?? '',
                    'full_address' => $calculate_data['member_address']['full_address'] ?? '',
                    'longitude' => $calculate_data['member_address']['longitude'] ?? '',
                    'latitude' => $calculate_data['member_address']['latitude'] ?? '',
                    'buyer_ip' => request()->ip(),
                    'goods_money' => $v['goods_money'],
                    'delivery_money' => $v['delivery_money'],
                    'coupon_id' => isset($v['coupon_id']) ? $v['coupon_id'] : 0,
                    'coupon_money' => $v['coupon_money'],
                    'adjust_money' => $v['adjust_money'],
                    'invoice_money' => $v['invoice_money'],
                    'promotion_money' => $v['promotion_money'],
                    'order_money' => $v['order_money'],
                    'balance_money' => $v['balance_money'],
                    'pay_money' => $v['pay_money'],
                    'create_time' => time(),
                    'is_enable_refund' => 0,
                    'order_name' => $v["order_name"],
                    'goods_num' => $v['goods_num'],
                    'delivery_type' => $delivery_type,
                    'delivery_type_name' => $delivery_type_name,
                    'delivery_store_id' => $v["delivery_store_id"] ?? 0,
                    "delivery_store_name" => $v["delivery_store_name"] ?? '',
                    "delivery_store_info" => $v["delivery_store_info"] ?? '',
                    "buyer_message" => $v["buyer_message"],
                    "promotion_type" => "wholesale",
                    "promotion_type_name" => "批发",
                    "promotion_status_name" => "",
                    "website_id" => $v["website_id"],

                    'buyer_ask_delivery_time' => $v['buyer_ask_delivery_time'] ?? '',//定时达

                    "platform_coupon_id" => $v["platform_coupon_id"] ?? 0,
                    "platform_coupon_money" => $v["platform_coupon_money"] ?? 0,
                    "platform_coupon_total_money" => $v["platform_coupon_total_money"] ?? 0,
                    "platform_coupon_shop_money" => $v["platform_coupon_shop_money"] ?? 0,


                    //发票相关
                    "invoice_delivery_money" => $shop_goods_list["invoice_delivery_money"] ?? 0,
                    "taxpayer_number" => $shop_goods_list["taxpayer_number"] ?? '',
                    "invoice_rate" => $shop_goods_list["invoice_rate"] ?? 0,
                    "invoice_content" => $shop_goods_list["invoice_content"] ?? '',
                    "invoice_full_address" => $shop_goods_list["invoice_full_address"] ?? '',
                    "is_invoice" => $shop_goods_list["is_invoice"] ?? 0,
                    "invoice_type" => $shop_goods_list["invoice_type"] ?? 0,
                    "invoice_title" => $shop_goods_list["invoice_title"] ?? '',
                    'is_tax_invoice' => $shop_goods_list["is_tax_invoice"] ?? '',
                    'invoice_email' => $shop_goods_list["invoice_email"] ?? '',
                    'invoice_title_type' => $shop_goods_list["invoice_title_type"] ?? 0,
                ];

                $order_id = model("order")->add($data_order);
                $order_id_arr[] = $order_id;
                $pay_money += $v['pay_money'];
                //订单项目表
                foreach ($v['goods_list'] as $k_order_goods => $order_goods)
                {
                    $data_order_goods = array(
                        'order_id'        => $order_id,
                        'site_id'         => $v['site_id'],
                        'site_name'       => $v['site_name'],
                        'order_no'        => $order_no,
                        'member_id'       => $data['member_id'],
                        'sku_id'          => $order_goods['sku_id'],
                        'sku_name'        => $order_goods['sku_name'],
                        'sku_image'       => $order_goods['sku_image'],
                        'sku_no'        => $order_goods['sku_no'],
                        'is_virtual'      => $order_goods['is_virtual'],
                        'goods_class'      => $order_goods['goods_class'],
                        'goods_class_name' => $order_goods['goods_class_name'],
                        'price'           => $order_goods['price'],
                        'cost_price'      => $order_goods['cost_price'],
                        'num'             => $order_goods['num'],
                        'goods_money'     => $order_goods['goods_money'],
                        'cost_money'      => $order_goods['cost_price']*$order_goods['num'],
                        'commission_rate' => $order_goods['commission_rate'],
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
                    if($stock_result["code"] != 0){
                        model("order")->rollback();
                        return $stock_result;
                    }

                }
            }

            //扣除余额(统一扣除)
            if($calculate_data["balance_money"] > 0){
                $balance_result = $this->useBalance($calculate_data);
                if($balance_result["code"] < 0){
                    model("order")->rollback();
                    return $balance_result;
                }
            }

            //循环执行订单完成事件
            $message_model = new Message();
            foreach($order_id_arr as $k => $v){
                $result_list = event("OrderCreate", ['order_id' => $v]);
                if(!empty($result_list)){
                    foreach($result_list as $k => $v){
                        if(!empty($v) && $v["code"] < 0){
                            model("order")->rollback();
                            return $v;
                        }
                    }
                }
                //订单生成的消息
                $message_model->sendMessage(['keywords' => "ORDER_CREATE", 'order_id'=>$v]);
            }

            //生成整体付费支付单据
            if($this->is_exist_not_free) {
                $order_name_title = implode(",", $order_name);
                $pay->addPay(0, $out_trade_no, $this->pay_type, $order_name_title, $order_name_title, $this->pay_money, '', 'OrderPayNotify', '');
            }
            //免费订单支付单据
            if($this->is_exist_free){
                $free_order_name_title = implode(",", $free_order_name);
                $pay->addPay(0, $free_out_trade_no, $this->pay_type, $free_order_name_title, $free_order_name_title, 0, '', 'OrderPayNotify', '');
            }


            $this->addOrderCronClose($order_id);//增加关闭订单自动事件

            $cart_ids = isset($data['cart_ids']) ? $data['cart_ids'] : '';
            if(!empty($cart_ids))
            {
                $cart = new CartModel();
                $data_cart = [
                    'cart_id' => $cart_ids,
                    'member_id' => $data['member_id']
                ];
                $cart->deleteCart($data_cart);
            }
            Cache::tag("order_create_member_".$data['member_id'])->clear();
//            $this->checkFree($data_order);//如果订单金额为0, 直接调用支付成功
            model("order")->commit();
            return $this->success($out_trade_no ?? $free_out_trade_no);
        }catch(\Exception $e)
        {
            model("order")->rollback();
            return $this->error('', $e->getMessage().$e->getFile().$e->getLine());
        }

    }

    /**
     * 订单计算
     * @param $data
     * @return unknown|mixed
     */
    public function calculate($data)
    {
        $data = $this->initMemberAddress($data);//初始化地址
        $data = $this->initMemberAccount($data);//初始化会员账户
        //余额付款
        if($data['is_balance'] > 0)
        {
            $this->member_balance_money = $data["member_account"]["balance_total"] ?? 0;
        }

        //商品列表信息
        $shop_goods_list = $this->getOrderGoodsCalculate($data);
        foreach ($shop_goods_list as $k => $v)
        {
            $data['shop_goods_list'][$k] = $this->shopOrderCalculate($v, $data);
        }

        //总优惠使用
        $data = $this->eachShopOrder($data,true, $this);

        //总结计算
        $data['delivery_money'] = $this->delivery_money;
        $data['coupon_money'] = $this->coupon_money;
        $data['adjust_money'] = $this->adjust_money;
        $data['invoice_money'] = $this->invoice_money;
        $data['invoice_delivery_money'] = $this->invoice_delivery_money;
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
     * @param $data
     * @return unknown|mixed
     */
    public function orderPayment($data)
    {
        $calculate_data = $this->calculate($data);
        foreach ($calculate_data['shop_goods_list'] as $k => $v)
        {
            $calculate_data['shop_goods_list'][$k] = $this->itemPayment($v, $calculate_data, false, $this);
        }
        return $this->success($calculate_data);

    }



    /**
     * 获取商品的计算信息
     * @param unknown $data
     */
    public function getOrderGoodsCalculate($data)
    {
        $shop_goods_list = [];
        //传输购物车id组合','隔开要进行拆单
        $condition = [];

        if(!empty($data['cart_ids']))
        {
            $cart_condition = array(
                ['cart_id', 'in', $data['cart_ids']],
                ['member_id', '=', $data['member_id']]
            );
            $cart_list = model('wholesale_goods_cart')->getList($cart_condition, 'sku_id, num');
            $condition[] = ['wgs.sku_id', 'in', array_column($cart_list, 'sku_id')];
            $num_array = array_column($cart_list, 'num', 'sku_id');
        }else{
            $condition[] = ['wgs.sku_id', '=', $data['sku_id']];
            $num_array = [$data['sku_id'] => $data['num']];
        }

        $shop_goods = $this->getWholesaleGoodsSkuList($condition, $num_array);
        $shop_goods_list = $shop_goods;
        return $shop_goods_list;
    }

    /**
     * 获取购物车商品列表信息
     * @param unknown $cart_ids
     */
    public function getWholesaleGoodsSkuList($condition, $num_array)
    {
        //组装商品列表
        $field = 'wgs.price_json, ngs.sku_id,ngs.sku_name, ngs.sku_no, ngs.price, ngs.discount_price, ngs.cost_price, ngs.stock, ngs.weight, ngs.volume, ngs.sku_image, ngs.site_id, ngs.site_name, ngs.website_id, ngs.is_own, ngs.goods_state, ngs.is_virtual, ngs.is_free_shipping, ngs.shipping_template, ngs.goods_class, ngs.goods_class_name, ngs.commission_rate,ngs.goods_id';
        $alias = 'wgs';
        $join = [
            [
                'goods_sku ngs',
                'wgs.sku_id = ngs.sku_id',
                'inner'
            ],
        ];
        $goods_list = model("wholesale_goods_sku")->getList($condition, $field, '', $alias, $join);
        $shop_goods_list = [];
        if(!empty($goods_list))
        {
            foreach ($goods_list as $k => $v)
            {
                $site_id = $v['site_id'];
                $item_num = $num_array[$v['sku_id']];
                $price_array = empty($v['price_json']) ? [] : json_decode($v['price_json'], true);

                array_multisort(array_column($price_array,'num'),SORT_ASC,$price_array);
                $price = 0;
                //按照阶梯计算商品的价格
                $is_in = false;
                foreach($price_array as $price_k => $price_v){
                    if($item_num >= $price_v['num']){
                        $price = $price_v['price'];
                        $is_in = true;
                    }
                }
                if(!$is_in){
                    $this->error = 5;
                    $this->error_msg = $v['sku_name'].'没有参与批发配置';
                }
                $v['num'] = $item_num;
                $v['price'] = $price;
                $v['goods_money'] = $price * $item_num;
                $v['real_goods_money'] = $v['goods_money'];
                $v['coupon_money'] = 0;//优惠券金额
                $v['promotion_money'] = 0;//优惠金额

                if(isset($shop_goods_list[$site_id]))
                {
                    $shop_goods_list[$site_id]['goods_list'][] = $v;
                    $shop_goods_list[$site_id]['order_name'] = string_split($shop_goods_list[$site_id]['order_name'], ",", $v['sku_name']);
                    $shop_goods_list[$site_id]['goods_num'] += $v['num'];
                    $shop_goods_list[$site_id]['goods_money'] += $v['price'] * $v['num'];
                    $shop_goods_list[$site_id]['goods_list_str'] = $shop_goods_list[$site_id]['goods_list_str'].';'.$v['sku_id'].':'.$v['num'];
                    $shop_goods_list[$site_id]['promotion_money'] += $v['promotion_money'];
                    $shop_goods_list[$site_id]['coupon_money'] += $v['coupon_money'];
                }else{
                    $shop_goods_list[$site_id] = [];
                    $shop_goods_list[$site_id]['site_id'] = $site_id;
                    $shop_goods_list[$site_id]['site_name'] = $v['site_name'];
                    $shop_goods_list[$site_id]['website_id'] = $v['website_id'];
                    $shop_goods_list[$site_id]['goods_money'] = $v['price'] * $v['num'];
                    $shop_goods_list[$site_id]['goods_list_str'] = $v['sku_id'].':'.$v['num'];
                    $shop_goods_list[$site_id]['order_name'] = string_split("", ",", $v['sku_name']);
                    $shop_goods_list[$site_id]['goods_num'] = $v['num'];
                    $shop_goods_list[$site_id]['goods_list'][] = $v;
                    $shop_goods_list[$site_id]['promotion_money'] = $v['promotion_money'];
                    $shop_goods_list[$site_id]['coupon_money'] = $v['coupon_money'];
                }
            }
        }
        return $shop_goods_list;
    }






	
	/**
	 * 获取店铺订单计算
	 * @param unknown $site_id 店铺id
	 * @param unknown $site_name 店铺名称
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
        $shop_goods = $this->delivery($shop_goods, $data, $this);


        //是否符合免邮
        $is_free_delivery = $shop_goods['is_free_delivery'] ?? false;
        if ($is_free_delivery) {
            $shop_goods['delivery_money'] = 0;
        }

        $shop_goods['order_money'] = $shop_goods['goods_money'] + $shop_goods['delivery_money'] - $shop_goods['promotion_money'];
        //优惠券活动(采用站点id:coupon_id)
        $shop_goods = $this->couponPromotion($shop_goods, $data, $this);
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