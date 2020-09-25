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


namespace app\model\order;

use addon\coupon\model\Coupon;
use addon\coupon\model\CouponType;
use addon\freeshipping\model\Freeshipping;
use addon\manjian\model\Manjian;
use addon\platformcoupon\model\Platformcoupon;
use addon\present\model\Present;
use app\model\express\Local;
use app\model\goods\Goods;
use app\model\goods\GoodsStock;
use app\model\member\Member;
use app\model\member\MemberAccount;
use app\model\shop\Shop;
use app\model\store\Store;
use app\model\system\Cron;
use think\facade\Cache;
use app\model\express\Express;
use app\model\system\Pay;
use app\model\goods\Cart;
use app\model\member\MemberAddress;
use app\model\express\Config as ExpressConfig;
use app\model\BaseModel;
use app\model\message\Message;

/**
 * 订单创建(普通订单)
 *
 * @author Administrator
 *
 */
class OrderCreate extends BaseModel
{

    public $goods_money = 0;//商品金额
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
    public $error = 0;  //是否有错误
    public $error_msg = '';  //错误描述
    public $pay_type = 'ONLINE_PAY';

    public $balance_money = 0;
    public $is_exist_not_free = false;
    public $is_exist_free = false;
    public $member_balance_money = 0;//会员账户余额(计算过程中会逐次减少)

    public $invoice_delivery_money = 0;//发票邮寄费用

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
        if ($this->is_exist_not_free) {
            $out_trade_no = $pay->createOutTradeNo();
            $order_name = [];
        }
        if ($this->is_exist_free) {
            $free_out_trade_no = $pay->createOutTradeNo();
            $free_order_name = [];
        }

        model("order")->startTrans();
        //循环生成多个订单
        try {
            $pay_money = 0;
            $goods_stock_model = new GoodsStock();
            foreach ($calculate_data['shop_goods_list'] as $k => $v) {
                $item_delivery = $v['delivery'] ?? [];
                $delivery_type = $item_delivery['delivery_type'] ?? '';
                $delivery_type_name = Express::express_type[$delivery_type]["title"] ?? '';

                //判断交易流水号
                if ($v["pay_money"] == 0) {
                    $temp_out_trade_no = $free_out_trade_no;
                    $free_order_name[] = $v["order_name"];
                } else {
                    $temp_out_trade_no = $out_trade_no;
                    $order_name[] = $v["order_name"];
                }
                //订单主表
                $order_type = $this->orderType($v, $calculate_data);
                $order_no = $this->createOrderNo($v['site_id']);
                $data_order = [
                    'order_no' => $order_no,
                    'site_id' => $v['site_id'],
                    'site_name' => $v['site_name'],
                    'order_from' => $data['order_from'],
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
                    'city_id' => $calculate_data['member_address']['city_id'] ?? '',
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
                    "website_id" => $v["website_id"],

                    'buyer_ask_delivery_time' => $v['buyer_ask_delivery_time'] ?? '',//定时达

                    "platform_coupon_id" => $v["platform_coupon_id"] ?? 0,
                    "platform_coupon_money" => $v["platform_coupon_money"] ?? 0,
                    "platform_coupon_total_money" => $v["platform_coupon_total_money"] ?? 0,
                    "platform_coupon_shop_money" => $v["platform_coupon_shop_money"] ?? 0,

                    //发票相关
                    "invoice_delivery_money" => $v["invoice_delivery_money"] ?? 0,
                    "taxpayer_number" => $v["taxpayer_number"] ?? '',
                    "invoice_rate" => $v["invoice_rate"] ?? 0,
                    "invoice_content" => $v["invoice_content"] ?? '',
                    "invoice_full_address" => $v["invoice_full_address"] ?? '',
                    "is_invoice" => $v["is_invoice"] ?? 0,
                    "invoice_type" => $v["invoice_type"] ?? 0,
                    "invoice_title" => $v["invoice_title"] ?? '',
                    'is_tax_invoice' => $v["is_tax_invoice"] ?? '',
                    'invoice_email' => $v["invoice_email"] ?? '',
                    'invoice_title_type' => $v["invoice_title_type"] ?? 0,
                ];

                $order_id = model("order")->add($data_order);
                $order_id_arr[] = $order_id;
                $pay_money += $v['pay_money'];
                //订单项目表
                foreach ($v['goods_list'] as $k_order_goods => $order_goods) {
                    $data_order_goods = array(
                        'order_id' => $order_id,
                        'site_id' => $v['site_id'],
                        'site_name' => $v['site_name'],
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
                    if ($stock_result["code"] != 0) {
                        model("order")->rollback();
                        return $stock_result;
                    }

                }

                //满减优惠
                $manjian_rule_list = $v['manjian_rule_list'] ?? [];
                if (!empty($manjian_rule_list)) {
                    $mansong_data = [];
                    foreach ($manjian_rule_list as $item) {
                        $manjian_v = $item['manjian_info'] ?? [];
                        $item_type = $manjian_v['type'];
                        if ($item_type == 0) {
                            $item_unit = '元';
                        } else {
                            $item_unit = '件';
                        }
                        $item_discount_array = $item['discount_array'] ?? [];
                        $item_rule = $item['rule'] ?? [];
                        $promotion_text = '满' . $item_rule['money'] . $item_unit;
                        $discount_money = $item_rule['discount_money'] ?? 0;
                        if ($discount_money > 0) {
                            $promotion_text .= ',减' . $item_rule['discount_money'];
                        }
                        $present_coupon = $item_rule['coupon'] ?? 0;
                        if ($present_coupon > 0) {
                            $promotion_text .= ',送优惠券';
                        }
                        $present_goods = $item_rule['present'] ?? [];
                        if (!empty($present_goods)) {
                            $promotion_text .= ',送赠品(赠完即止)';
                        }
                        $item_order_promotion_data = [
                            'order_id' => $order_id,
                            'site_id' => $v['site_id'],
                            'promotion_text' => $promotion_text,
                            'money' => $item_discount_array['real_discount_money'],
                            'create_time' => time(),
                            'json' => json_encode($item['rule'], JSON_UNESCAPED_UNICODE),
                            'sku_list' => implode(',', $item['sku_ids'])
                        ];
                        $mansong_data[] = $item_order_promotion_data;
                    }
                    model('order_promotion_detail')->addList($mansong_data);
                }


                //判断赠品
                $present_list = $v['present_list'] ?? [];
                if (!empty($present_list)) {
                    //赠品商品
                    $present_goods_list = $present_list['goods_list'] ?? [];
                    $present_model = new Present();
                    if (!empty($present_goods_list)) {
                        //赠品订单项表
                        foreach ($present_goods_list as $present_goods_k => $present_goods_v) {
                            //库存变化
                            $item_stock_data = [
                                "sku_id" => $present_goods_v['sku_id'],
                                "num" => $present_goods_v['num'],
                                'present_id' => $present_goods_v['present_id'],
                                'member_id' => $data['member_id']
                            ];
                            $stock_result = $present_model->givingPresent($item_stock_data);
                            //赠品是赠完即止
                            if ($stock_result["code"] >= 0) {

                                $data_order_goods = array(
                                    'order_id' => $order_id,
                                    'site_id' => $v['site_id'],
                                    'site_name' => $v['site_name'],
                                    'order_no' => $order_no,
                                    'member_id' => $data['member_id'],
                                    'sku_id' => $present_goods_v['sku_id'],
                                    'sku_name' => $present_goods_v['sku_name'],
                                    'sku_image' => $present_goods_v['sku_image'],
                                    'sku_no' => $present_goods_v['sku_no'],
                                    'is_virtual' => $present_goods_v['is_virtual'],
                                    'goods_class' => $present_goods_v['goods_class'],
                                    'goods_class_name' => $present_goods_v['goods_class_name'],
                                    'price' => $present_goods_v['price'],
                                    'cost_price' => $present_goods_v['cost_price'],
                                    'num' => $present_goods_v['num'],
                                    'goods_money' => $present_goods_v['goods_money'],
                                    'cost_money' => $present_goods_v['cost_price'] * $present_goods_v['num'],
                                    'commission_rate' => $present_goods_v['commission_rate'],
                                    'goods_id' => $present_goods_v['goods_id'],
                                    'delivery_status' => 0,
                                    'delivery_status_name' => "未发货",

                                    'real_goods_money' => $present_goods_v['real_goods_money'] ?? 0,
                                    'coupon_money' => $present_goods_v['coupon_money'] ?? 0,
                                    'promotion_money' => $present_goods_v['promotion_money'] ?? 0,
                                    'platform_coupon_money' => $present_goods_v['platform_coupon_money'] ?? 0,

                                    'is_present' => 1
                                );
                                model("order_goods")->add($data_order_goods);

//                                model("order")->rollback();
//                                return $stock_result;
                            }

                        }
                    }
                }
                //优惠券
                if ($data_order['coupon_id'] > 0 && $data_order['coupon_money'] > 0) {
                    //优惠券处理方案
                    $member_coupon_model = new Coupon();
                    $coupon_use_result = $member_coupon_model->useCoupon($data_order['coupon_id'], $data['member_id'], $order_id);//使用优惠券
                    if ($coupon_use_result['code'] < 0) {
                        model("order")->rollback();
                        return $this->error('', "COUPON_ERROR");
                    }
                }
            }

            //扣除余额(统一扣除)
            if ($calculate_data["balance_money"] > 0) {
                $balance_result = $this->useBalance($calculate_data);
                if ($balance_result["code"] < 0) {
                    model("order")->rollback();
                    return $balance_result;
                }
            }

            //使用平台优惠券
            if ($calculate_data["platform_coupon_id"] > 0) {
                $platform_coupon_model = new Platformcoupon();
                $platform_coupon_use_result = $platform_coupon_model->usePlatformcoupon($calculate_data["platform_coupon_id"], $data['member_id'], $order_id);
                if ($platform_coupon_use_result['code'] < 0) {
                    model("order")->rollback();
                    return $this->error('', "COUPON_ERROR");
                }
            }


            //循环执行订单完成事件
            $message_model = new Message();
            foreach ($order_id_arr as $k => $v) {
                $result_list = event("OrderCreate", ['order_id' => $v]);
                if (!empty($result_list)) {
                    foreach ($result_list as $k => $v) {
                        if (!empty($v) && $v["code"] < 0) {
                            model("order")->rollback();
                            return $v;
                        }
                    }
                }
                //订单生成的消息
                $message_model->sendMessage(['keywords' => "ORDER_CREATE", 'order_id' => $v]);
            }

            //生成整体付费支付单据
            if ($this->is_exist_not_free) {
                $order_name_title = implode(",", $order_name);
                $pay->addPay(0, $out_trade_no, $this->pay_type, $order_name_title, $order_name_title, $this->pay_money, '', 'OrderPayNotify', '');
            }
            //免费订单支付单据
            if ($this->is_exist_free) {
                $free_order_name_title = implode(",", $free_order_name);
                $pay->addPay(0, $free_out_trade_no, $this->pay_type, $free_order_name_title, $free_order_name_title, 0, '', 'OrderPayNotify', '');
            }


            $this->addOrderCronClose($order_id);//增加关闭订单自动事件

            $cart_ids = isset($data['cart_ids']) ? $data['cart_ids'] : '';
            if (!empty($cart_ids)) {
                $cart = new Cart();
                $data_cart = [
                    'cart_id' => $cart_ids,
                    'member_id' => $data['member_id']
                ];
                $cart->deleteCart($data_cart);
            }
            Cache::tag("order_create_member_" . $data['member_id'])->clear();
//            $this->checkFree($data_order);//如果订单金额为0, 直接调用支付成功
            model("order")->commit();

            return $this->success($out_trade_no ?? $free_out_trade_no);

        } catch ( \Exception $e ) {
            model("order")->rollback();
            return $this->error('', $e->getMessage() . $e->getFile() . $e->getLine());
        }

    }

    /**
     * 生成订单编号
     *
     * @param array $site_id
     */
    public function createOrderNo($site_id)
    {
        $time_str = date('YmdHi');
        $num = 0;
        $max_no = Cache::get($site_id . "_" . $time_str);
        if (!isset($max_no) || empty($max_no)) {
            $max_no = 1;
        } else {
            $max_no = $max_no + 1;
        }
        $order_no = $time_str . sprintf("%04d", $max_no);
        Cache::set($site_id . "_" . $time_str, $max_no);
        return $order_no;
    }

    /**
     * 订单类型判断
     * @param unknown $shop_goods
     */
    public function orderType($shop_goods, $data)
    {
        if ($data["is_virtual"] == 1) {
            $order = new VirtualOrder();
            return [
                'order_type_id' => 4,
                'order_type_name' => '虚拟订单',
                'order_status' => $order->order_status[0]
            ];
        } else {
            if ($shop_goods['delivery']['delivery_type'] == 'express') {
                $order = new Order();
                return [
                    'order_type_id' => 1,
                    'order_type_name' => '普通订单',
                    'order_status' => $order->order_status[0]
                ];
            } elseif ($shop_goods['delivery']['delivery_type'] == 'store') {
                $order = new StoreOrder();
                return [
                    'order_type_id' => 2,
                    'order_type_name' => '自提订单',
                    'order_status' => $order->order_status[0]
                ];
            } elseif ($shop_goods['delivery']['delivery_type'] == 'local') {
                $order = new LocalOrder();
                return [
                    'order_type_id' => 3,
                    'order_type_name' => '外卖订单',
                    'order_status' => $order->order_status[0]
                ];
            }
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
        if ($data['is_balance'] > 0) {
            $this->member_balance_money = $data["member_account"]["balance_total"] ?? 0;
        }

        //商品列表信息
        $shop_goods_list = $this->getOrderGoodsCalculate($data);
        foreach ($shop_goods_list as $k => $v)
        {
            $data['shop_goods_list'][$k] = $this->shopOrderCalculate($v, $data);
        }

//        //传输购物车id组合','隔开要进行拆单
//        if (!empty($data['cart_ids'])) {
//            //商品列表信息
//            $shop_goods_list = $this->getOrderGoodsCalculate($data);
//            foreach ($shop_goods_list as $k => $v) {
//                $data['shop_goods_list'][$k] = $this->shopOrderCalculate($v, $data);
//            }
//        } else {
//            //商品列表信息
//            $shop_goods_list = $this->getOrderGoodsCalculate($data);
//
//            //判断是否是虚拟订单
//            if ($shop_goods_list[0]['goods_list'][0]['is_virtual']) {
//                $this->is_virtual = 1;
//            } else {
//                $this->is_virtual = 0;
//            }
//            $data['shop_goods_list'][$shop_goods_list[0]['goods_list'][0]['site_id']] = $this->shopOrderCalculate($shop_goods_list[0], $data);
//        }


        //平台优惠券计算
        $data = $this->platformCoupon($data);
        //总优惠使用
        $data = $this->eachShopOrder($data);

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
        foreach ($calculate_data['shop_goods_list'] as $k => $v) {
            $calculate_data['shop_goods_list'][$k] = $this->itemPayment($v, $calculate_data);
        }


        //查询可用的平台优惠券
        $platform_coupon_list = $this->getPlatformCouponList($calculate_data);
        $calculate_data['platform_coupon_list'] = $platform_coupon_list;
        return $calculate_data;

    }

    /**
     * 初始化组件 各订单
     * @param $shop_item
     * @param $data
     * @param null $self
     * @return mixed
     */
    public function itemPayment($shop_item, $data, $is_coupon = true, $self = null)
    {
        $self = $self ?? $this;
        //1、查询会员当前店铺可用优惠券
        if ($is_coupon) {
            $coupon_list = $this->getOrderCouponList($shop_item, $data);
        } else {
            $coupon_list = [];
        }

        $shop_item["coupon_list"] = $coupon_list;
        $express_type = [];
        if ($self->is_virtual == 0) {
            if (!empty($data['member_address'])) {
                //2. 查询店铺配送方式（1. 物流  2. 自提  3. 外卖）
                if ($shop_item["express_config"]["is_use"] == 1) {
                    $express_type[] = ["title" => Express::express_type["express"]["title"], "name" => "express"];
                }

                //查询店铺是否开启门店自提
                if ($shop_item["store_config"]["is_use"] == 1) {
                    //根据坐标查询门店
                    $store_model = new Store();
                    $store_condition = array(
                        ['site_id', '=', $shop_item['site_id']],
                        ['is_pickup', '=', 1],
                        ['status', '=', 1],
                        ['is_frozen', '=', 0],
                    );

                    $latlng = array(
                        'lat' => $data['latitude'],
                        'lng' => $data['longitude'],
                    );
                    $store_list_result = $store_model->getLocationStoreList($store_condition, '*', $latlng);
                    $store_list = $store_list_result["data"];

                    //如果用户默认选中了门店
                    $store_id = 0;
                    if (!empty($store_list)) {
                        $store_id = $store_list[0]['store_id'];
                    }
                    $express_type[] = ["title" => Express::express_type["store"]["title"], "name" => "store", "store_list" => $store_list, 'store_id' => $store_id];
                }

                //查询店铺是否开启外卖配送
                if ($shop_item["local_config"]["is_use"] == 1) {
                    //查询本店的通讯地址
                    $express_type[] = ["title" => "外卖配送", "name" => "local"];
                }
            }
        }
        $shop_item["express_type"] = $express_type;
        return $shop_item;
    }

    /**
     * 初始化收货地址
     * @param unknown $data
     */
    public function initMemberAddress($data)
    {
        //收货人地址管理
        if (empty($data['member_address'])) {
            $member_address = new MemberAddress();
            $address = $member_address->getMemberAddressInfo([['member_id', '=', $data['member_id']], ['is_default', '=', 1]]);
            $data['member_address'] = $address['data'];
        }
        return $data;
    }

    /**
     * 获取商品的计算信息
     * @param unknown $data
     */
    public function getOrderGoodsCalculate($data)
    {
        $shop_goods_list = [];

        if(!empty($data['cart_ids']))
        {
            $cart_condition = array(
                ['cart_id', 'in', $data['cart_ids']],
                ['member_id', '=', $data['member_id']]
            );
            $cart_list = model('goods_cart')->getList($cart_condition, 'sku_id, num');
            $condition[] = ['sku_id', 'in', array_column($cart_list, 'sku_id')];
            $num_array = array_column($cart_list, 'num', 'sku_id');
        }else{
            $condition[] = ['sku_id', '=', $data['sku_id']];
            $num_array = [$data['sku_id'] => $data['num']];
        }
        if(!empty($condition)){
            $shop_goods_list = $this->getGoodsSkuList($condition, $num_array, $data);
        }
        //传输购物车id组合','隔开要进行拆单
//        if (!empty($data['cart_ids'])) {
//            $cache = Cache::get("order_create_cart_".$data['cart_ids'].'_'.$data['member_id']);
//            if(!empty($cache))
//            {
//                return $cache;
//            }
//            $shop_goods_list = $this->getCartGoodsList($data['cart_ids'], $data['member_id']);
//            Cache::tag("order_create_member_".$data['member_id'])->set("order_create_cart_".$data['cart_ids'].'_'.$data['member_id'], $shop_goods_list, 600);
//        } else {
//            $cache = Cache::get("order_create_".$data['sku_id'].'_'.$data['num'].'_'.$data['member_id']);
//            if(!empty($cache))
//            {
//                return $cache;
//            }
//            $shop_goods = $this->getShopGoodsList($data);
//            $shop_goods_list[0] = $shop_goods;
//            Cache::tag("order_create_member_".$data['member_id'])->set("order_create_".$data['sku_id'].'_'.$data['num'].'_'.$data['member_id'], $shop_goods_list, 600);
//        }
        return $shop_goods_list;
    }

    /**
     * 获取购物车商品列表信息
     * @param unknown $cart_ids
     */
    public function getGoodsSkuList($condition, $num_array, $data)
    {
//        $cart_ids, $member_id
        //组装商品列表
        $field = ' sku_id, sku_name, sku_no, 
            price, discount_price, cost_price, stock, weight, volume, sku_image, 
            site_id, site_name, website_id, is_own, goods_state, verify_state, is_virtual, 
            is_free_shipping, shipping_template, goods_class, goods_class_name, commission_rate,goods_id,max_buy,min_buy';

        $goods_list = model("goods_sku")->getList($condition, $field, '');
        $shop_goods_list = [];
        if (!empty($goods_list)) {
            foreach ($goods_list as $k => $v) {
                $site_id = $v['site_id'];
                $item_num = $num_array[$v['sku_id']];
                $v['member_id'] = $data['member_id'];
                $v['num'] = $item_num;
                $price = $v['discount_price'];
                $v['price'] = $price;
                $v['goods_money'] = $price * $v['num'];
                $v['real_goods_money'] = $v['goods_money'];
                $v['coupon_money'] = 0;//优惠券金额
                $v['promotion_money'] = 0;//优惠金额

                //用于过滤商品
                if($v['goods_state'] == 0 || $v['verify_state'] == 0){
                    $this->error = 1;
                    $this->error_msg = '商品未上架或未通过审核';
                }

                if (isset($shop_goods_list[$site_id])) {
                    $shop_goods_list[$site_id]['goods_list'][] = $v;
                    $shop_goods_list[$site_id]['order_name'] = string_split($shop_goods_list[$site_id]['order_name'], ",", $v['sku_name']);
                    $shop_goods_list[$site_id]['goods_num'] += $v['num'];
                    $shop_goods_list[$site_id]['goods_money'] += $v['price'] * $v['num'];
                    $shop_goods_list[$site_id]['goods_list_str'] = $shop_goods_list[$site_id]['goods_list_str'] . ';' . $v['sku_id'] . ':' . $v['num'];
                    $shop_goods_list[$site_id]['promotion_money'] += $v['promotion_money'];
                    $shop_goods_list[$site_id]['coupon_money'] += $v['coupon_money'];
                    // 商品限购处理
                    if (isset($shop_goods_list[$site_id]['limit_purchase']['goods_' . $v['goods_id']])) {
                        $shop_goods_list[$site_id]['limit_purchase']['goods_' . $v['goods_id']]['num'] += $v['num'];
                    } else {
                        $shop_goods_list[$site_id]['limit_purchase']['goods_' . $v['goods_id']] = [
                            'goods_id' => $v['goods_id'],
                            'goods_name' => $v['sku_name'],
                            'num' => $v['num'],
                            'max_buy' => $v['max_buy'],
                            'min_buy' => $v['min_buy']
                        ];
                    }

                } else {
                    $shop_goods_list[$site_id]['site_id'] = $site_id;
                    $shop_goods_list[$site_id]['site_name'] = $v['site_name'];
                    $shop_goods_list[$site_id]['website_id'] = $v['website_id'];
                    $shop_goods_list[$site_id]['goods_money'] = $v['price'] * $v['num'];
                    $shop_goods_list[$site_id]['goods_list_str'] = $v['sku_id'] . ':' . $v['num'];
                    $shop_goods_list[$site_id]['order_name'] = string_split("", ",", $v['sku_name']);
                    $shop_goods_list[$site_id]['goods_num'] = $v['num'];
                    $shop_goods_list[$site_id]['goods_list'][] = $v;
                    $shop_goods_list[$site_id]['promotion_money'] = $v['promotion_money'];
                    $shop_goods_list[$site_id]['coupon_money'] = $v['coupon_money'];
                    // 商品限购处理
                    $shop_goods_list[$site_id]['limit_purchase']['goods_' . $v['goods_id']] = [
                        'goods_id' => $v['goods_id'],
                        'goods_name' => $v['sku_name'],
                        'num' => $v['num'],
                        'max_buy' => $v['max_buy'],
                        'min_buy' => $v['min_buy']
                    ];
                }
            }
        }
        return $shop_goods_list;
    }

    /**
     * 获取立即购买商品信息
     * @param unknown $data
     * @return multitype:string number unknown mixed
     */
//    public function getShopGoodsList($data)
//    {
//        $sku_info = model("goods_sku")->getInfo([['sku_id', '=', $data['sku_id']]], 'sku_id, sku_name, sku_no, price, discount_price,
//             cost_price, stock, volume, weight, sku_image, site_id, site_name,
//             website_id, is_own, goods_state, is_virtual, verify_state, is_free_shipping, shipping_template,goods_class, goods_class_name, commission_rate, goods_id,max_buy,min_buy');
//        $sku_info['num'] = $data['num'];
//
//        if($sku_info['goods_state'] == 0 || $sku_info['verify_state'] == 0){
//            $this->error = 1;
//            $this->error_msg = '商品未上架或未通过审核';
//        }
//        $is_present = $data['is_present'] ?? 0;
//        $price = $is_present == 1 ? 0 : $sku_info['discount_price'];//如果是赠品的话, 价格为0
//        $sku_info['num'] = $data['num'];
//        $goods_money = $price * $data['num'];
//        $sku_info['price'] = $price;
//        $sku_info['goods_money'] = $goods_money;
//        $sku_info['real_goods_money'] = $goods_money;
//        $sku_info['coupon_money'] = 0;//优惠券金额
//        $sku_info['promotion_money'] = 0;//优惠金额
//        $goods_list[] = $sku_info;
//
//        $shop_goods = [
//            'goods_money' => $goods_money,
//            'site_id' => $sku_info['site_id'],
//            'site_name' => $sku_info['site_name'],
//            'website_id' => $sku_info['website_id'],
//            'goods_list_str' => $sku_info['sku_id'] . ':' . $sku_info['num'],
//            'goods_list' => $goods_list,
//            'order_name' => $sku_info["sku_name"],
//            'goods_num' => $sku_info['num'],
//            'promotion_money' => $sku_info['promotion_money'],
//            'coupon_money' => $sku_info['coupon_money'],
//            'limit_purchase' => [
//                'goods_' . $sku_info['goods_id'] => [
//                    'goods_id' => $sku_info['goods_id'],
//                    'goods_name' => $sku_info["sku_name"],
//                    'num' => $sku_info['num'],
//                    'max_buy' => $sku_info['max_buy'],
//                    'min_buy' => $sku_info['min_buy']
//                ]
//            ]
//        ];
//        return $shop_goods;
//    }

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
        $site_id = $shop_goods['site_id'];

        //查询店铺信息(以及店铺对于商品的相关控制)
        $shop_goods = $this->getShopInfo($shop_goods);

        //定义计算金额
        $adjust_money = 0;     //调整金额
        $invoice_money = 0;    //发票金额
        //满减优惠
        $shop_goods = $this->manjianPromotion($shop_goods);

        //运费计算
        $shop_goods = $this->delivery($shop_goods, $data);
        //满额包邮插件
        $shop_goods = $this->freeShippingCalculate($shop_goods, $data);

        //是否符合免邮
        $is_free_delivery = $shop_goods['is_free_delivery'] ?? false;
        if ($is_free_delivery) {
            $shop_goods['delivery_money'] = 0;
        }


        $shop_goods['order_money'] = $shop_goods['goods_money'] + $shop_goods['delivery_money'] - $shop_goods['promotion_money'];
        //优惠券活动(采用站点id:coupon_id)
        $shop_goods = $this->couponPromotion($shop_goods, $data, $this);
        if ($shop_goods['order_money'] < 0) {
            $shop_goods['order_money'] = 0;
        }

        //发票相关
        $shop_goods = $this->invoice($shop_goods, $data);

        $shop_goods['order_money'] = $shop_goods['order_money'] + $shop_goods['invoice_money'] + $shop_goods['invoice_delivery_money'];

        //买家留言
        if (isset($data['buyer_message']) && isset($data['buyer_message'][$site_id])) {
            $item_buyer_message = $data['buyer_message'][$site_id];
            $shop_goods["buyer_message"] = $item_buyer_message;
        } else {
            $shop_goods["buyer_message"] = '';
        }


        // 商品限购判断
        foreach ($shop_goods['limit_purchase'] as $item) {
            if ($item['min_buy'] > 0 && $item['num'] < $item['min_buy']) {
                $this->error = 1;
                $this->error_msg = "商品“{$item['goods_name']}”{$item['min_buy']}件起售";
                break;
            }
            if ($item['max_buy'] > 0) {
                $goods_model = new Goods();
                $purchased_num = $goods_model->getGoodsPurchasedNum($item['goods_id'], $data['member_id']);
                if (($purchased_num + $item['num']) > $item['max_buy']) {
                    $this->error = 1;
                    $this->error_msg = "商品“{$item['goods_name']}”每人限购{$item['max_buy']}件，您已购买{$purchased_num}件";
                    break;
                }
            }
        }
        //总结计算
        $shop_goods['adjust_money'] = $adjust_money;
//        $shop_goods['invoice_money'] = $invoice_money;

        return $shop_goods;
    }

    /**
     * 运费计算
     * @param $shop_goods
     * @param $data
     * @param null $self
     * @return mixed
     */
    public function delivery($shop_goods, $data, $self = null)
    {
        $self = $self ?? $this;
        $site_id = $shop_goods['site_id'];
        //计算邮费
        if ($self->is_virtual == 1) {
            //虚拟订单  运费为0
            $delivery_money = 0;
            $shop_goods['delivery']['delivery_type'] = '';
        } else {

            //查询店铺是否开启快递配送
            $express_config_model = new ExpressConfig();
            $express_config_result = $express_config_model->getExpressConfig($site_id);
            $express_config = $express_config_result["data"];
            $shop_goods["express_config"] = $express_config;
            //查询店铺是否开启门店自提
            $store_config_result = $express_config_model->getStoreConfig($site_id);
            $store_config = $store_config_result["data"];
            $shop_goods["store_config"] = $store_config;
            //查询店铺是否开启外卖配送
            $local_config_result = $express_config_model->getLocalDeliveryConfig($site_id);
            $local_config = $local_config_result["data"];
            $shop_goods["local_config"] = $local_config;
            //如果本地配送开启, 则查询出本地配送的配置
            if ($shop_goods["local_config"]['is_use'] == 1) {
                $local_model = new Local();
                $local_info_result = $local_model->getLocalInfo([['site_id', '=', $site_id]]);
                $local_info = $local_info_result['data'];
                $shop_goods["local_config"]['info'] = $local_info;
            }

            //如果本地配送开启, 则查询出本地配送的配置
            if ($shop_goods["local_config"]['is_use'] == 1) {
                $local_model = new Local();
                $local_info_result = $local_model->getLocalInfo([['site_id', '=', $site_id]]);
                $local_info = $local_info_result['data'];
                $shop_goods["local_config"]['info'] = $local_info;
            }
            $delivery_array = $data['delivery'][$site_id] ?? [];
            $delivery_type = $delivery_array["delivery_type"] ?? 'express';

            if (empty($data['member_address'])) {
                $delivery_money = 0;
                $shop_goods['delivery']['delivery_type'] = 'express';
                $self->error = 1;
                $self->error_msg = "未配置默认收货地址!";
            } else {
                if ($delivery_type == "express") {
                    if ($shop_goods["express_config"]["is_use"] == 1) {
                        //物流配送
                        $express = new Express();
                        $express_fee_result = $express->calculate($shop_goods, $data);
                        if ($express_fee_result["code"] < 0) {
                            $self->error = 1;
                            $self->error_msg = $express_fee_result["message"];
                            $delivery_fee = 0;
                        } else {
                            $delivery_fee = $express_fee_result['data']['delivery_fee'];
                        }
                    } else {
                        $self->error = 1;
                        $self->error_msg = "物流配送方式未开启!";
                        $delivery_fee = 0;
                    }
                    $delivery_money = $delivery_fee;
                    $shop_goods['delivery']['delivery_type'] = 'express';
                } else if ($delivery_type == "local") {
                    //外卖配送
                    $delivery_money = 0;
                    $shop_goods['delivery']['delivery_type'] = 'local';
                    if ($shop_goods["local_config"]["is_use"] == 0) {
                        $self->error = 1;
                        $self->error_msg = "外卖配送方式未开启!";
                    } else {
                        $local_delivery_time = 0;
                        if (!empty($delivery_array['buyer_ask_delivery_time'])) {
                            $buyer_ask_delivery_time_temp = explode(':', $delivery_array['buyer_ask_delivery_time']);
                            $local_delivery_time = $buyer_ask_delivery_time_temp[0] * 3600 + $buyer_ask_delivery_time_temp[1] * 60;
                        }
                        $shop_goods['buyer_ask_delivery_time'] = $local_delivery_time;
                        $local_model = new Local();
                        $local_result = $local_model->calculate($shop_goods, $data);
                        if ($local_result['code'] < 0) {
                            $self->error = $local_result['data']['code'];
                            $self->error_msg = $local_result['message'];
                        } else {
                            $delivery_money = $local_result['data']['delivery_money'];
                            if (!empty($local_result['data']['error_code'])) {
                                $self->error = $local_result['data']['code'];
                                $self->error_msg = $local_result['data']['error'];
                            }
                        }
                    }
                } else if ($delivery_type == "store") {
                    //门店自提
                    $delivery_money = 0;
                    $shop_goods['delivery']['delivery_type'] = 'store';
                    if ($shop_goods["store_config"]["is_use"] == 0) {
                        $self->error = 1;
                        $self->error_msg = "门店自提方式未开启!";
                    }
                    if (empty($delivery_array["store_id"])) {
                        $self->error = 1;
                        $self->error_msg = "门店未选择!";
                    }
                    $shop_goods['delivery']['store_id'] = $delivery_array["store_id"];
                    $shop_goods = $this->storeOrderData($shop_goods, $data, $self);
                }
            }

        }
        $shop_goods['delivery_money'] = $delivery_money;
        return $shop_goods;
    }

    /**
     * 增加订单自动关闭事件
     * @param $order_id
     */
    public function addOrderCronClose($order_id)
    {
        //计算订单自动关闭时间
        $config_model = new Config();
        $order_config_result = $config_model->getOrderEventTimeConfig();
        $order_config = $order_config_result["data"];
        $now_time = time();
        if (!empty($order_config)) {
            $execute_time = $now_time + $order_config["value"]["auto_close"] * 60;//自动关闭时间
        } else {
            $execute_time = $now_time + 3600;//尚未配置  默认一天
        }
        $cron_model = new Cron();
        $cron_model->addCron(1, 0, "订单自动关闭", "CronOrderClose", $execute_time, $order_id);
    }

    /**
     * 验证订单支付金额知否为0  如果为0  立即支付完成
     * @param $order_data
     */
    public function checkFree($order_data)
    {
        if ($order_data["pay_money"] == 0) {
//            $pay_model = new Pay();
//            $pay_model->onlinePay($order_data["out_trade_no"], "ONLINE_PAY", '', '');
        }

    }

    /**
     * 补齐门店数据
     * @param $data
     */
    public function storeOrderData($shop_goods, $data, $self = null)
    {
        $self = $self ?? $this;
        $temp_data = [];
        $delivery_store_id = $shop_goods['delivery']['store_id'] ?? 0;//门店id

        if ($delivery_store_id > 0) {
            $store_model = new Store();
            $condition = array(
                ["store_id", "=", $delivery_store_id],
                ["site_id", "=", $shop_goods['site_id']],
                ["status", "=", 1],
                ["is_pickup", "=", 1],
            );
            $store_info_result = $store_model->getStoreInfo($condition);
            $store_info = $store_info_result["data"] ?? [];
            if (empty($store_info)) {
                $self->error = 1;
                $self->error_msg = "当前门店不存在或未开启!";
            } else {
                $temp_data["delivery_store_id"] = $delivery_store_id;
                $delivery_store_name = $store_info_result["data"]["store_name"];
                $temp_data["delivery_store_name"] = $delivery_store_name;
                $delivery_store_info = array(
                    "open_date" => $store_info["open_date"],
                    "full_address" => $store_info["full_address"],
                    "longitude" => $store_info["longitude"],
                    "latitude" => $store_info["latitude"],
                    "telphone" => $store_info["telphone"],
                );
                $temp_data["delivery_store_info"] = json_encode($delivery_store_info, JSON_UNESCAPED_UNICODE);
            }
        } else {
            $self->error = 1;
            $self->error_msg = "配送门店不可为空!";
        }
        return array_merge($shop_goods, $temp_data);

    }


    /**
     * 使用余额
     * @param $order_data
     * @return array
     */
    public function useBalance($data, $self = null)
    {
        $self = $self ?? $this;
        $self->pay_type = "BALANCE";
        $member_model = new Member();
        $result = $member_model->checkPayPassword($data["member_id"], $data["pay_password"]);
        if ($result["code"] >= 0) {

            $balance_money = $data["member_account"]["balance_money"];//不可提现余额
            $balance = $data["member_account"]["balance"];//可提现余额
            $member_account_model = new MemberAccount();
            $surplus_banance = $data["balance_money"];
            //优先扣除不可提现余额
            if ($balance > 0) {
                if ($balance >= $surplus_banance) {
                    $real_balance = $surplus_banance;
                } else {
                    $real_balance = $balance;
                }
                $result = $member_account_model->addMemberAccount($data["member_id"], "balance", -$real_balance, "order", "余额抵扣", "订单余额抵扣,扣除不可提现余额:" . $real_balance);
                $surplus_banance -= $real_balance;
            }

//            if($balance_money > 0){
//                if($balance_money > $surplus_banance){
//                    $real_balance_money = $surplus_banance;
//                }else{
//                    $real_balance_money = $balance_money;
//                }
//                $result = $member_account_model->addMemberAccount($data["member_id"], "balance", -$real_balance, "order", "余额抵扣","订单余额抵扣,扣除不可提现余额:".$real_balance);
//            }
            if ($surplus_banance > 0) {
                $result = $member_account_model->addMemberAccount($data["member_id"], "balance_money", -$surplus_banance, "order", "余额抵扣", "订单余额抵扣,扣除可提现余额:" . $surplus_banance);
            }

            return $result;
        } else {
            return $result;
        }
    }

    /**
     * 初始化会员账户
     * @param $data
     * @return mixed
     */
    public function initMemberAccount($data)
    {
        $member_model = new Member();
        $member_info_result = $member_model->getMemberDetail($data["member_id"]);
        $member_info = $member_info_result["data"];

        if (!empty($member_info)) {
            if (!empty($member_info["pay_password"])) {
                $is_pay_password = 1;
            } else {
                $is_pay_password = 0;
            }
            unset($member_info["pay_password"]);
            $member_info["is_pay_password"] = $is_pay_password;
            $data['member_account'] = $member_info;
        }

        return $data;
    }



    /****************************************************************************** 满减 start *****************************************************************************/
    /**
     * 满减优惠
     * @param $data
     */
    public function manjianPromotion($calculate_data)
    {

        $calculate_data['manjian_rule_list'] = [];
        //先查询全部商品的满减套餐  进行中
        $manjian_model = new Manjian();
        $all_info_result = $manjian_model->getManjianInfo([['manjian_type', '=', 1], ['site_id', '=', $calculate_data['site_id']], ['status', '=', 1]], 'manjian_name,type,goods_ids,rule_json');
        $all_info = $all_info_result['data'];
        $goods_list = $calculate_data['goods_list'];
        //存在全场满减(不考虑部分满减情况)
        if (!empty($all_info)) {
            $discount_array = $this->getManjianDiscountMoney($all_info, $calculate_data);
            $all_info['discount_array'] = $discount_array;
            //判断有没有优惠
            $temp_goods_list = $this->distributionGoodsDiscount($goods_list, $calculate_data['goods_money'], $discount_array['real_discount_money'], isset($discount_array['rule']['free_shipping']));
            $goods_list = $temp_goods_list;

            $manjian_list[] = $all_info;

            $discount_money = $discount_array['real_discount_money'];
            $calculate_data['goods_list'] = $goods_list;
            $calculate_data["promotion"]['manjian'] = $manjian_list;
            $calculate_data['promotion_money'] += $discount_money;

            if (!empty($discount_array['rule'])) {
                $calculate_data['manjian_rule_list'][] = [
                    'manjian_info' => $all_info,
                    'rule' => $discount_array['rule'],
                    'sku_ids' => array_column($goods_list, 'sku_id'),
                    'discount_array' => $discount_array
                ];
                $present_list = $calculate_data['present_list'] ?? [];

                //只有实物商品送赠品
                if ($this->is_virtual == 0) {
                    //是否有赠品
                    $present_value = $discount_array['rule']['present'] ?? [];
                    //赠品商品
                    if (!empty($present_value)) {
                        $present_id = $present_value['present_id'] ?? 0;
                        $present_num = $present_value['present_num'] ?? 0;

                        $present_goods_list = $present_list['goods_list'] ?? [];
                        $present_goods = [['present_id' => $present_id, 'present_num' => $present_num]];
                        $present_goods_list = $this->presentGoods($present_goods, $present_goods_list);


                        $present_list['goods_list'] = $present_goods_list;
                    }
                }

                //赠品优惠券
                $present_coupon = $discount_array['rule']['coupon'] ?? 0;
                if ($present_coupon > 0) {
                    $present_coupon_list = $present_list['coupon_list'] ?? [];
                    $present_coupon_list = $this->presentCoupon([$present_coupon], $present_coupon_list, $calculate_data['site_id']);
                    $present_list['coupon_list'] = $present_coupon_list;
                }
                $calculate_data['present_list'] = $present_list;
            }
        } else {
            $goods_ids = array_unique(array_column($calculate_data['goods_list'], 'goods_id'));

            $manjian_condition = array(
                ['goods_id', 'in', $goods_ids],
                ['status', '=', 1]
            );
            $manjian_goods_list_result = $manjian_model->getManjianGoodsList($manjian_condition, 'manjian_id');
            $manjian_goods_list = $manjian_goods_list_result['data'];
            if (!empty($manjian_goods_list)) {

                $present_goods_ids = [];
                $present_coupon_ids = [];
                $present_list = $calculate_data['present_list'] ?? [];

                $discount_money = 0;
                $manjian_goods_list = array_column($manjian_goods_list, 'manjian_id');
                $manjian_goods_list = array_unique($manjian_goods_list); //去重
                sort($manjian_goods_list);
                $manjian_list_result = $manjian_model->getManjianList([['manjian_id', 'in', $manjian_goods_list], ['status', '=', 1]]);
                $manjian_list = $manjian_list_result['data'];
                foreach ($manjian_list as $k => $v) {
                    $manjian_goods_ids = explode(',', $v['goods_ids']);
                    $item_goods_data = [
                        'goods_money' => 0,
                        'goods_num' => 0
                    ];
                    $item_goods_list = [];
                    $sku_ids = [];
                    foreach ($goods_list as $goods_k => $goods_item) {
                        if (in_array($goods_item['goods_id'], $manjian_goods_ids)) {
                            $item_goods_data['goods_money'] += $goods_item['goods_money'];
                            $item_goods_data['goods_num'] += $goods_item['num'];
                            $item_goods_list[] = $goods_item;
                            array_push($sku_ids, $goods_item['sku_id']);
                            unset($goods_list[$goods_k]);
                        }
                    }
                    $discount_array = $this->getManjianDiscountMoney($v, $item_goods_data);

                    $temp_goods_list = $this->distributionGoodsDiscount($item_goods_list, $item_goods_data['goods_money'], $discount_array['real_discount_money'], isset($discount_array['rule']['free_shipping']), $sku_ids);
                    $goods_list = array_merge($goods_list, $temp_goods_list);
                    $manjian_list[$k]['discount_array'] = $discount_array;
                    $discount_money += $discount_array['real_discount_money'];

                    if (!empty($discount_array['rule'])) {
                        array_push($calculate_data['manjian_rule_list'], [
                            'manjian_info' => $v,
                            'rule' => $discount_array['rule'],
                            'sku_ids' => $sku_ids,
                            'discount_array' => $discount_array
                        ]);

                        //是否有赠品
                        $present_value = $discount_array['rule']['present'] ?? [];
                        if (!empty($present_value)) {
                            $present_id = $present_value['present_id'] ?? 0;
                            $present_num = $present_value['present_num'] ?? 0;
                            $present_goods_ids[] = ['present_id' => $present_id, 'present_num' => $present_num];
                        }
                        $present_coupon = $discount_array['rule']['coupon'] ?? 0;
                        if ($present_coupon > 0) {
                            $present_coupon_ids[] = $present_coupon;
                        }

                        if(!empty($calculate_data['manjian_rule_list'])){
                            //只有实物商品送赠品
                            if ($this->is_virtual == 0) {
                                //赠品 商品
                                if (!empty($present_goods_ids)) {
                                    $present_goods_list = $present_list['goods_list'] ?? [];
                                    $present_goods_list = $this->presentGoods($present_goods_ids, $present_goods_list);
                                    $present_list['goods_list'] = $present_goods_list;
                                }
                            }
                            //赠品 优惠券
                            if (!empty($present_coupon_ids)) {
                                //查询赠送的优惠券
                                $present_coupon_list = $present_list['coupon_list'] ?? [];
                                $present_coupon_list = $this->presentCoupon($present_coupon_ids, $present_coupon_list, $calculate_data['site_id']);
                                $present_list['coupon_list'] = $present_coupon_list;
                            }
                            $calculate_data['present_list'] = $present_list;
                            $calculate_data['goods_list'] = $goods_list;
                            $calculate_data["promotion"]['manjian'] = $manjian_list;
                            $calculate_data['promotion_money'] += $discount_money;
                        }
                    }
                }

            }
        }
        return $calculate_data;
    }

    /**
     * 满减优惠金额
     * @param $rule_list
     * @param $goods_money
     */
    public function getManjianDiscountMoney($manjian_info, $data)
    {
        $goods_money = $data['goods_money'];
        $value = $manjian_info['type'] == 0 ? $data['goods_money'] : $data['goods_num'];

        //阶梯计算优惠
        $rule_item = json_decode($manjian_info['rule_json'], true);
        $discount_money = 0;
        $money = 0;
        $rule = []; // 符合条件的优惠规则
        $desc = '';
        array_multisort(array_column($rule_item, 'money'), SORT_ASC, $rule_item); //排序，根据num 排序
        foreach ($rule_item as $k => $v) {
            if ($value >= $v['money']) {
                $rule = $v;
//                if (isset($v['discount_money'])) {
                    $discount_money = $v['discount_money'] ?? 0;
                    $money = $v['money'];

                    $desc = '符合满减送活动' . $manjian_info['manjian_name'] . '的满减规则';
                    $item_type = $manjian_info['type'];
                    if ($item_type == 0) {
                        $item_unit = '元';
                    } else {
                        $item_unit = '件';
                    }
                    $item_rule = $v ?? [];
                    $desc .= ',满' . $item_rule['money'] . $item_unit;
                    if ($discount_money > 0) {
                        $desc .= ',减' . $item_rule['discount_money'] . '元';
                    }
                    $present_coupon = $item_rule['coupon'] ?? 0;
                    if ($present_coupon > 0) {
                        $desc .= ',送优惠券';
                    }
                    $present_goods = $item_rule['present'] ?? [];
                    if (!empty($present_goods)) {
                        $desc .= ',送赠品(赠完即止)';
                    }
//                }
            }
        }

        $real_discount_money = $discount_money > $goods_money ? $goods_money : $discount_money;
        return ['discount_money' => $discount_money, 'money' => $money, 'real_discount_money' => $real_discount_money, 'rule' => $rule, 'desc' => $desc];
    }

    /**
     * 按比例摊派满减优惠
     */
    public function distributionGoodsDiscount($goods_list, $goods_money, $discount_money, $is_free_shipping = false, $sku_ids = [])
    {
        $temp_discount_money = $discount_money;
        $last_key = count($goods_list) - 1;
        foreach ($goods_list as $k => $v) {
            if ($last_key != $k) {
                $item_discount_money = round(floor($v['goods_money'] / $goods_money * $discount_money * 100) / 100, 2);
            } else {
                $item_discount_money = $temp_discount_money;
            }
            $temp_discount_money -= $item_discount_money;
            $goods_list[$k]['promotion_money'] = $item_discount_money;
            $real_goods_money = $v['real_goods_money'] - $item_discount_money;
            $real_goods_money = $real_goods_money < 0 ? 0 : $real_goods_money;
            $goods_list[$k]['real_goods_money'] = $real_goods_money; //真实订单项金额
            // 满减送包邮
            if ($is_free_shipping) {
                if (empty($sku_ids) || in_array($v['sku_id'], $sku_ids)) {
                    $goods_list[$k]['is_free_shipping'] = 1;
                }
            }
        }
        return $goods_list;
    }
    /****************************************************************************** 满减 end *****************************************************************************/
    /****************************************************************************** 订单优惠券 start *****************************************************************************/
    /**
     * 优惠券活动
     * @param $shop_goods
     * @param $coupon_info
     * @return mixed
     */
    public function couponPromotion($shop_goods, $data, $self = null)
    {
        $self = $self ?? $this;//真实操作类
        $coupon_money = 0;
        $site_id = $shop_goods['site_id'];
        if (!empty($data['coupon'][$site_id]) && $data['coupon'][$site_id]["coupon_id"] > 0) {
            $coupon_id = $data['coupon'][$site_id]["coupon_id"];
            //查询优惠券信息,计算优惠券费用
            $coupon_model = new Coupon();
            $coupon_info_result = $coupon_model->getCouponInfo([['coupon_id', '=', $coupon_id], ["site_id", "=", $site_id]], 'member_id,at_least,money,state,goods_type,type,goods_ids,discount,discount_limit');
            $coupon_info = $coupon_info_result["data"];
            $is_coupon = false;
            $coupon_goods_money = 0;
            $goods_list = $shop_goods['goods_list'];

            //防止用户优惠券越权,并且防止优惠券状态已使用
            if ($coupon_info['member_id'] == $data['member_id'] && $coupon_info['state'] == 1) {
                $coupon_goods_list = [];
                if ($coupon_info['goods_type'] == 1) {//全场通用优惠券
                    if ($coupon_info['at_least'] <= $shop_goods['goods_money']) {
                        $is_coupon = true;
                    } else {
                        $self->error = 1;
                        $self->error_msg = "优惠券不可用!";
                    }
                    $coupon_goods_money = $shop_goods['goods_money'];
                    $coupon_goods_list = $goods_list;
                    $goods_list = [];
                } else {
                    $item_goods_ids = explode(',', $coupon_info['goods_ids']);
                    $temp_money = 0;
                    foreach ($goods_list as $goods_k => $goods_v) {
                        if (in_array($goods_v['goods_id'], $item_goods_ids)) {
                            $temp_money += $goods_v['goods_money'];
                            $coupon_goods_list[] = $goods_v;
                            unset($goods_list[$goods_k]);
                        }
                    }
                    if ($temp_money >= $coupon_info['at_least']) {
                        $is_coupon = true;
                    }
                    $coupon_goods_money = $temp_money;
                }
            }
            if ($is_coupon) {
                $coupon_money = 0;

                if ($coupon_info['type'] == 'reward') {//满减优惠券
                    $coupon_money = $coupon_info['money'] > $coupon_goods_money ? $coupon_goods_money : $coupon_info['money'];
                } else if ($coupon_info['type'] == 'discount') {//折扣优惠券
                    //计算折扣优惠金额
                    $coupon_money = $coupon_goods_money * (10 - $coupon_info['discount']) / 10;
                    $coupon_money = $coupon_money > $coupon_info['discount_limit'] && $coupon_info['discount_limit'] != 0 ? $coupon_info['discount_limit'] : $coupon_money;
                    $coupon_money = $coupon_money > $coupon_goods_money ? $coupon_goods_money : $coupon_money;
                    $coupon_money = round(floor($coupon_money * 100) / 100, 2);

                }
                $temp_goods_list = $this->distributionGoodsCouponMoney($coupon_goods_list, $coupon_goods_money, $coupon_money);
                $goods_list = array_merge($goods_list, $temp_goods_list);
                $shop_goods['goods_list'] = $goods_list;
            } else {
                $self->error = 1;
                $self->error_msg = "优惠券不可用!";
            }
        }

        if ($coupon_money > 0) {
            $shop_goods['coupon_id'] = $coupon_id;
            if ($coupon_money > $shop_goods['order_money']) {
                $coupon_money = $shop_goods['order_money'];
            }
            $shop_goods['order_money'] -= $coupon_money;

            $shop_goods['coupon_money'] = $coupon_money;

        }
        return $shop_goods;
    }

    /**
     * 查询可用优惠券
     * @param $data
     */
    public function getOrderCouponList($shop_goods, $data)
    {
        $site_id = $shop_goods['site_id'];
        $coupon_list = [];
        //先查询全场通用的优惠券
        $member_coupon_model = new Coupon();
        $all_condition = array(
            ['member_id', '=', $data["member_id"]],
            ['state', '=', 1],
            ['site_id', '=', $site_id],
            ['goods_type', '=', 1],
            ['at_least', "<=", $shop_goods["goods_money"]]
        );
        $all_coupon_list_result = $member_coupon_model->getCouponList($all_condition);

        $all_coupon_list = $all_coupon_list_result["data"];
        $coupon_list = array_merge($coupon_list, $all_coupon_list);
        $shop_goods_list = $shop_goods;
        $goods_ids = array_column($shop_goods_list['goods_list'], 'goods_id');
        $goods_list = $shop_goods_list['goods_list'];
        $item_condition = array(
            ['member_id', '=', $data["member_id"]],
            ['state', '=', 1],
            ['site_id', '=', $site_id],
            ['goods_type', '=', 2],
        );
        $item_like_array = [];
        foreach ($goods_ids as $k => $v) {
            $item_like_array[] = "%," . $v . ",%";
        }
        $item_condition[] = ['goods_ids', 'like', $item_like_array, 'OR'];
        $item_coupon_list_result = $member_coupon_model->getCouponList($item_condition);
        $item_coupon_list = $item_coupon_list_result["data"];
        if (!empty($item_coupon_list)) {
            foreach ($item_coupon_list as $item_k => $item_v) {
                $item_goods_ids = explode(',', $item_v['goods_ids']);
                $item_goods_money = 0;
                foreach ($goods_list as $goods_k => $goods_v) {
                    if (in_array($goods_v['goods_id'], $item_goods_ids)) {
                        $item_goods_money += $goods_v['goods_money'];
                    }
                }
                if ($item_goods_money >= $item_v['at_least']) {
                    $coupon_list[] = $item_v;
                }
            }
        }
        return $coupon_list;
    }

    /**
     * 按比例摊派优惠券优惠
     */
    public function distributionGoodsCouponMoney($goods_list, $goods_money, $coupon_money)
    {
        $temp_coupon_money = $coupon_money;
        $last_key = count($goods_list) - 1;
        foreach ($goods_list as $k => $v) {
            if ($last_key != $k) {
                $item_coupon_money = round(floor($v['goods_money'] / $goods_money * $coupon_money * 100) / 100, 2);
            } else {
                $item_coupon_money = $temp_coupon_money;
            }
            $temp_coupon_money -= $item_coupon_money;
            $goods_list[$k]['coupon_money'] = $item_coupon_money;
            $real_goods_money = $v['real_goods_money'] - $item_coupon_money;
            $real_goods_money = $real_goods_money < 0 ? 0 : $real_goods_money;
            $goods_list[$k]['real_goods_money'] = $real_goods_money;//真实订单项金额
        }
        return $goods_list;
    }
    /****************************************************************************** 订单优惠券 end *****************************************************************************/

    /**************************************************************************** 遍历各店铺订单start ************************************************************/
    /**
     * 遍历各店铺订单  计算余额
     * @param $data
     * @return mixed
     */
    public function eachShopOrder($data, $is_self = true, $self = null)
    {
//        $is_self = false;
        $shop_goods_list = $data['shop_goods_list'];
        if ($is_self) {
            $self = $this;
            foreach ($shop_goods_list as $k => $v) {
                $shop_goods_list[$k] = $this->eachShopOrderItem($v, $self, $is_self);
            }
        } else {
            $shop_goods_list = $this->eachShopOrderItem($shop_goods_list, $self, $is_self);
        }
        $data['shop_goods_list'] = $shop_goods_list;
        return $data;
    }

    /**
     * 循环计算各个订单
     * @param $item
     * @param $self
     * @param $is_self
     * @return mixed
     */
    public function eachShopOrderItem($item, $self, $is_self)
    {
        //余额抵扣(判断是否使用余额)
        if ($self->member_balance_money > 0) {
            if ($item['order_money'] <= $self->member_balance_money) {
                $balance_money = $item['order_money'];
            } else {
                $balance_money = $self->member_balance_money;
            }
        } else {
            $balance_money = 0;
        }
        $pay_money = $item['order_money'] - $balance_money;//计算出实际支付金额
        //判断是否存在支付金额为0的订单
        if ($is_self) {
            if ($pay_money > 0) {
                $self->is_exist_not_free = true;
            } else {
                $self->is_exist_free = true;
            }
        }

        $self->member_balance_money -= $balance_money;//预减少账户余额

        $item['pay_money'] = $pay_money;
        $item['balance_money'] = $balance_money;
        $self->pay_money += $pay_money;
        $self->balance_money += $balance_money;//累计余额
        $self->goods_money += $item['goods_money'];
        $self->delivery_money += $item['delivery_money'];
        $self->coupon_money += $item['coupon_money'];
        $self->adjust_money += $item['adjust_money'];

        $this->invoice_money += $item['invoice_money'];
        $this->invoice_delivery_money += $item['invoice_delivery_money'];
        $self->promotion_money += $item['promotion_money'];
        $self->order_money += $item['order_money'];

        $self->goods_num += $item["goods_num"];
        $self->order_name = string_split($this->order_name, ",", $item["order_name"]);

        return $item;
    }

    /**************************************************************************** 遍历各店铺订单end ************************************************************/


    /**************************************************************************** 平台优惠券 start ************************************************************/
    /**
     * 计算平台优惠券优惠
     * @param $data
     */
    public function platformCoupon($data, $self = null)
    {
        if (empty($self)) {
            $self = $this;//真实操作类
            $is_self = true;
        } else {
            $is_self = false;
        }
        $platform_coupon_id = $data['platform_coupon_id'] ?? 0;
        $real_platform_coupon_money = 0;
        $real_platform_coupon_id = 0;
        //如果使用了优惠券
        if ($platform_coupon_id > 0) {
            $platform_coupon_model = new Platformcoupon();
            $platform_coupon_condition = array(
                ['member_id', '=', $data['member_id']],
                ['platformcoupon_id', '=', $platform_coupon_id],
            );
            $platform_coupon_info_result = $platform_coupon_model->getPlatformcouponInfo($platform_coupon_condition);
            $platform_coupon_info = $platform_coupon_info_result['data'];

            if (!empty($platform_coupon_info)) {
                //全场店铺

                $platform_coupon_money = $platform_coupon_info['money'];

                if ($is_self) {
                    $shop_goods_list = $data['shop_goods_list'];
                } else {
                    $shop_goods_list = [];
                    $shop_goods_list[$data['shop_goods_list']['site_id']] = $data['shop_goods_list'];
                }
                $temp_shop_goods_list = [];
                if ($platform_coupon_info['use_scenario'] == 1) {
                    $platform_order_money = array_sum(array_column($shop_goods_list, 'order_money'));
                    $temp_shop_goods_list = $shop_goods_list;
                    $shop_goods_list = [];
                } else {
                    $shop_model = new Shop();
                    $group_array = array_filter(explode(',', $platform_coupon_info['group_ids']));
                    $platform_order_money = 0;
                    foreach ($shop_goods_list as $shop_k => $shop_item) {
//                        $item_rate = $shop_item['order_money']/$this->order_money;
//                        round(floor($shop_item['order_money']/$this->order_money * $coupon_money * 100) / 100, 2);
//                        $item_condition = array(
//                            ['site_id', '=', $shop_item['site_id']],
//                        );
//                        $shop_info_result = $shop_model->getShopInfo($item_condition);
                        $shop_info = $shop_item['shop']['shop_info'];
                        if (!empty($shop_info)) {
                            $shop_group_id = $shop_info['group_id'];
                            if (in_array($shop_group_id, $group_array)) {
                                $temp_shop_goods_list[] = $shop_item;
                                $platform_order_money += $shop_item['order_money'];
                                unset($shop_goods_list[$shop_k]);
                            }
                        }
                    }

                }

                //使用门槛
                if ($platform_order_money >= $platform_coupon_info['at_least']) {
                    $platform_coupon_money = $platform_coupon_money > $platform_order_money ? $platform_order_money : $platform_coupon_money;
                    $real_platform_coupon_money = $platform_coupon_money;
//                    $self->order_money -= $real_platform_coupon_money;

                    $temp_shop_goods_list = $this->distributionPlatformCouponMoney($temp_shop_goods_list, $platform_order_money,
                        $platform_coupon_money, $platform_coupon_info['platform_split_rare'], $platform_coupon_info['shop_split_rare'], $platform_coupon_info['platformcoupon_id']);
                    $shop_goods_list = array_merge($shop_goods_list, $temp_shop_goods_list);

                    $real_platform_coupon_id = $platform_coupon_id;
                } else {
                    $shop_goods_list = array_merge($shop_goods_list, $temp_shop_goods_list);

                }
                if ($is_self) {
                    $shop_goods_list = array_column($shop_goods_list, null, 'site_id');
                    $data["shop_goods_list"] = $shop_goods_list;
                } else {
                    $data["shop_goods_list"] = $shop_goods_list[0];
                }
            }

        }

        $data['platform_coupon_id'] = $real_platform_coupon_id;
        $data['platform_coupon_money'] = $real_platform_coupon_money;
        return $data;

    }

    /**
     * 按比例摊派平台优惠券优惠
     */
    public function distributionPlatformCouponMoney($shop_goods_list, $order_money, $platform_coupon_money, $platform_rate, $shop_rate, $platform_coupon_id)
    {

        $temp_coupon_money = $platform_coupon_money;
        end($shop_goods_list);
        $last_key = key($shop_goods_list);
        foreach ($shop_goods_list as $k => $v) {
            if ($last_key != $k) {
                $item_coupon_money = round(floor($v['order_money'] / $order_money * $platform_coupon_money * 100) / 100, 2);
            } else {
                $item_coupon_money = round(floor($temp_coupon_money * 100) / 100, 2);
            }

            $temp_coupon_money -= $item_coupon_money;

            $shop_goods_list[$k]['platform_coupon_total_money'] = $item_coupon_money;
            $item_platform_coupon_money = round(floor($item_coupon_money * $platform_rate) / 100, 2);//平台优惠券 平台分摊金额
            $item_platform_coupon_shop_money = $item_coupon_money - $item_platform_coupon_money;//平台优惠券 店铺分摊金额

            $shop_goods_list[$k]['platform_coupon_money'] = $item_platform_coupon_money;//平台优惠券 平台分摊金额
            $shop_goods_list[$k]['platform_coupon_shop_money'] = $item_platform_coupon_shop_money;//平台优惠券 店铺分摊金额
            $real_order_money = $v['order_money'] - $item_coupon_money;
            $real_order_money = $real_order_money < 0 ? 0 : $real_order_money;
            $shop_goods_list[$k]['order_money'] = $real_order_money;//真实订单项金额
            $shop_goods_list[$k]['platform_coupon_id'] = $platform_coupon_id;//平台优惠券id

            $temp_goods_list = $this->distributionSitePlatformCouponMoney($v['goods_list'], $v['goods_money'], $item_coupon_money);
            $shop_goods_list[$k]['goods_list'] = $temp_goods_list;
        }
        return $shop_goods_list;
    }

    /**
     * 遍历平台优惠券 站点内部分配平台优惠券优惠
     * @param $goods_list
     * @param $goods_money
     * @param $platform_coupon_money
     * @return mixed
     */
    public function distributionSitePlatformCouponMoney($goods_list, $order_money, $platform_coupon_money)
    {
        $temp_coupon_money = $platform_coupon_money;
        $last_key = count($goods_list) - 1;
        foreach ($goods_list as $k => $v) {
            if ($last_key != $k) {
                $item_coupon_money = round(floor($v['real_goods_money'] / $order_money * $platform_coupon_money * 100) / 100, 2);
            } else {
                $item_coupon_money = $temp_coupon_money;
            }
            $temp_coupon_money -= $item_coupon_money;
            $goods_list[$k]['platform_coupon_money'] = $item_coupon_money;
            $real_goods_money = $v['real_goods_money'] - $item_coupon_money;
            $real_goods_money = $real_goods_money < 0 ? 0 : $real_goods_money;
            $goods_list[$k]['real_goods_money'] = $real_goods_money;//真实订单项金额
        }
        return $goods_list;
    }

    /**
     * 查询可用平台优惠券
     * @param $data
     * @return array
     */
    public function getPlatformCouponList($data, $is_self = true)
    {
        $platform_coupon_model = new Platformcoupon();
        //先查询全场的平台优惠券
        $all_platform_coupon_condition = array(
            ['member_id', '=', $data['member_id']],
            ['state', '=', 1],
            ['use_scenario', '=', 1],
            ['at_least', '<=', $data['order_money']]
        );
        $all_platform_coupon_list_result = $platform_coupon_model->getPlatformcouponList($all_platform_coupon_condition, '*');
        $all_platform_coupon_list = $all_platform_coupon_list_result['data'];
        //指定店铺的平台优惠券
        if (!$is_self) {
            $shop_goods_list[$data['shop_goods_list']['site_id']] = $data['shop_goods_list'];
        } else {
            $shop_goods_list = $data['shop_goods_list'];
        }
        $platform_coupon_condition = array(
            ['member_id', '=', $data['member_id']],
            ['state', '=', 1],
            ['use_scenario', '=', 2],
        );
        $shop_temp_platform_coupon_list_result = $platform_coupon_model->getPlatformcouponList($platform_coupon_condition, '*');
        $shop_temp_platform_coupon_list = $shop_temp_platform_coupon_list_result['data'];
        $shop_model = new Shop();
        $shop_platform_coupon_list = [];
        if (!empty($shop_temp_platform_coupon_list)) {
            foreach ($shop_temp_platform_coupon_list as $shop_coupon_k => $shop_coupon_item) {
                $group_id_array = explode(',', $shop_coupon_item['group_ids']);
                $item_order_money = 0;
                foreach ($shop_goods_list as $shop_k => $shop_item) {
//                    $item_condition = array(
//                        ['site_id', '=', $shop_item['site_id']],
//                    );
//                    $shop_info_result = $shop_model->getShopInfo($item_condition);
//                    $shop_info = $shop_info_result['data'];
                    $shop_info = $shop_item['shop']['shop_info'] ?? [];
                    if (!empty($shop_info) && in_array($shop_info['group_id'], $group_id_array)) {
                        $item_order_money += $shop_item['order_money'];
                    }
                }
                if ($item_order_money >= $shop_coupon_item['at_least']) {
                    $shop_platform_coupon_list[] = $shop_coupon_item;
                }
            }
        }
        $coupon_list = array_merge($all_platform_coupon_list, $shop_platform_coupon_list);
        return $coupon_list;
    }
    /**************************************************************************** 平台优惠券 end ************************************************************/
    /**************************************************************************** 满额包邮 start ************************************************************/
    /**
     * 满额包邮
     * @param $shop_goods
     */
    public function freeShippingCalculate($shop_goods, $data)
    {

        if (addon_is_exit("freeshipping")) {
            $free_shipping_model = new Freeshipping();
            $city_id = $data['member_address']['city_id'] ?? 0;
            $free_result = $free_shipping_model->calculate($shop_goods['goods_money'], $city_id, $shop_goods['site_id']);
            if ($free_result['code'] >= 0) {
                $shop_goods["promotion"]['freeshipping'] = $free_result['data'];//优惠活动  满额包邮
                $shop_goods['is_free_delivery'] = true;
            }
        }
        return $shop_goods;
    }

    /**************************************************************************** 满额包邮 end ************************************************************/

    /**************************************************************************** 发票 start ************************************************************/
    /**
     * 发票信息
     * @param $shop_goods
     * @param $data
     */
    public function invoice($shop_goods, $data, $self = null)
    {
        if (empty($self)) {
            $self = $this;//真实操作类
        }
        $condfig_model = new Config();
        $invoice_config_result = $condfig_model->getOrderInvoiceConfig($shop_goods['site_id'], 'shop');
        $invoice_config = $invoice_config_result['data']['value'] ?? [];

        $invoice_status = $shop_goods['invoice_config']['invoice_status'] ?? 0;

        $invoice_money = 0;
        $invoice_delivery_money = 0;
        $type = $invoice_config['type'] ?? [];//支持发票类型
        //如果没有可用的类型,则视为未开启发票
        if (empty($type)) {
            $invoice_status = 0;
            $invoice_config['invoice_status'] = $invoice_status;
        }
        $shop_goods['invoice_status'] = $invoice_status;
        $shop_goods['invoice_config'] = $invoice_config;
        if ($invoice_status == 1 || !empty($type)) {
            $invoice_content_array = $shop_goods['invoice_config']['invoice_content'] ?? [];
//            $invoice_content_array = explode(',', $invoice_content);
            $shop_goods['invoice']['invoice_content_array'] = $invoice_content_array;
            $shop_goods['invoice']['invoice_delivery_money'] = $shop_goods['invoice_config']['invoice_money'] ?? 0;
            $shop_goods['invoice']['invoice_rate'] = $shop_goods['invoice_config']['invoice_rate'] ?? 0;

            $shop_invoice_item = $data['invoice'][$shop_goods['site_id']] ?? [];
            $is_invoice = $shop_invoice_item['is_invoice'] ?? 0;
            $shop_goods['is_invoice'] = $is_invoice;
            //是否需要发票
            if ($is_invoice) {
                $promotion_money = $shop_goods['promotion_money'];//优惠金额
                $coupon_money = $shop_goods['coupon_money'] ?? 0;//优惠券金额
                $real_goods_money = $shop_goods['goods_money'] - $promotion_money - $coupon_money;
                $invoice_money = round(floor($real_goods_money * $shop_goods['invoice']['invoice_rate']) / 100, 2);
                $invoice_type = $shop_invoice_item['invoice_type'] ?? 1;
                //验证发票类型
                if (!in_array($invoice_type, $type)) {
                    $self->error = 1;
                    $self->error_msg = "当前商家不支持您选择的发票类型!";
                }
                if ($invoice_type == 1) {
                    $invoice_delivery_money = $shop_goods['invoice']['invoice_delivery_money'];

                    if (empty($shop_invoice_item['invoice_full_address'])) {
                        $self->error = 1;
                        $self->error_msg = "发票邮寄地址不能为空!";
                    } else {
                        $shop_goods['invoice_full_address'] = $shop_invoice_item['invoice_full_address'];
                    }
                } else {
                    if (empty($shop_invoice_item['invoice_email'])) {
                        $self->error = 1;
                        $self->error_msg = "发票邮箱不能为空!";
                    } else {
                        $shop_goods['invoice_email'] = $shop_invoice_item['invoice_email'];
                    }
                }
                if (empty($shop_invoice_item['invoice_title']) || empty($shop_invoice_item['invoice_type']) || empty($shop_invoice_item['invoice_content'] || $shop_invoice_item['invoice_title_type'] == 0)) {
                    $self->error = 1;
                    $self->error_msg = "发票相关项不能为空!";
                }
                //企业抬头  必须填写税号
                if ($shop_invoice_item['invoice_title_type'] == 2 && empty($shop_invoice_item['taxpayer_number'])) {
                    $self->error = 1;
                    $self->error_msg = "发票相关项不能为空!";
                }

                $shop_goods['invoice_title_type'] = $shop_invoice_item['invoice_title_type'];
                $shop_goods['is_tax_invoice'] = $shop_invoice_item['is_tax_invoice'];
                $shop_goods['taxpayer_number'] = $shop_invoice_item['taxpayer_number'];
                $shop_goods['invoice_title'] = $shop_invoice_item['invoice_title'];
                $shop_goods['invoice_type'] = $shop_invoice_item['invoice_type'];
                $shop_goods['invoice_content'] = $shop_invoice_item['invoice_content'];
                $shop_goods['invoice_rate'] = $shop_goods['invoice']['invoice_rate'];
            }
        }
        $shop_goods['invoice_money'] = $invoice_money;
        $shop_goods['invoice_delivery_money'] = $invoice_delivery_money;
        return $shop_goods;
    }

    /**************************************************************************** 发票 end ************************************************************/

    /**************************************************************************** 赠品 start ************************************************************/

    /**
     * 赠品转化为店铺商品数据
     * @param $data
     */
    public function presentGoods($data_goods_list, $present_list)
    {
        //商品数据
        $present_model = new Present();
        if (!empty($data_goods_list)) {
            $present_column_list = array_column($present_list, '*', 'sku_id');
            $temp_goods_list = [];
            foreach ($data_goods_list as $data_list_k => $goods_list_v) {
                $num = $temp_goods_list[$goods_list_v['present_id']] ?? 0;
                $temp_goods_list[$goods_list_v['present_id']] = $num + $goods_list_v['present_num'];
            }
            foreach ($temp_goods_list as $goods_k => $goods_item) {
                if ($goods_item <= 0) {
                    continue;
                }
                $item_condition = array(
                    ['present_id', '=', $goods_k],
                    ['status', '=', 2],
                );
                $num = $goods_item;
                $present_info = $present_model->getPresentInfo($item_condition, 'stock,sku_id')['data'];
                //赠品失效的情况不需要考虑
                if (!empty($present_info)) {

                    if ($present_info['stock'] <= 0) {
                        continue;
                    }

                    //数量足够的话
                    if ($present_info['stock'] < $num) {
                        $num = $present_info['stock'];
                    }
                    if ($goods_item <= 0) {
                        continue;
                    }
                    $column_list_item = $present_column_list[$present_info['sku_id']] ?? [];
                    if (!empty($column_list_item)) {
                        $column_list_item['num'] += $num;
                        if ($present_info['stock'] < $column_list_item['num']) {
                            $column_list_item['num'] = $present_info['stock'];
                        }
                        if ($column_list_item['num'] <= 0) {
                            continue;
                        }
                        $present_column_list[$present_info['sku_id']] = $column_list_item;
                    } else {
                        $shop_goods_data = array(
                            'sku_id' => $present_info['sku_id'],
                            'num' => $num,
                            'is_present' => 1,
                            'present_id' => $goods_k
                        );
                        $shop_goods_item = $this->getShopPresentGoodsInfo($shop_goods_data);
                        $present_column_list[] = $shop_goods_item;
                    }
                }
            }
            sort($present_column_list);
            $present_list = $present_column_list;
        }
        return $present_list;
    }

    /**
     * 赠品优惠券
     * @param $data_coupon_list
     * @param $present_coupon_list
     * @param $site_id
     * @return array
     */
    public function presentCoupon($data_coupon_list, $present_coupon_list, $site_id)
    {

        $coupon_model = new CouponType();
        $present_coupon_condition = [
            ['coupon_type_id', 'in', $data_coupon_list],
            ['site_id', '=', $site_id],
            ['status', '=', 1],
        ];
        $temp_present_coupon_list = $coupon_model->getCouponTypeList($present_coupon_condition, '*')['data'] ?? [];
        if (!empty($temp_present_coupon_list)) {

            foreach ($temp_present_coupon_list as $temp_k => $temp_v) {
//                ['count-lead_count', '>', 0]
                if (($temp_v['count'] - $temp_v['lead_count']) <= 0) {
                    unset($temp_present_coupon_list[$temp_k]);
                }
            }
            sort($temp_present_coupon_list);
            //优惠券必须存在库存
            $present_coupon_list = array_merge($temp_present_coupon_list, $present_coupon_list);
        }
        return $present_coupon_list;
    }

    /**
     * 获取立即购买商品信息
     * @param unknown $data
     * @return multitype:string number unknown mixed
     */
    public function getShopPresentGoodsInfo($data)
    {
        $sku_info = model("goods_sku")->getInfo([['sku_id', '=', $data['sku_id']]], 'sku_id, sku_name, sku_no, price, discount_price,
             cost_price, stock, volume, weight, sku_image, site_id, site_name,
             website_id, is_own, goods_state, is_virtual, is_free_shipping, shipping_template,goods_class, goods_class_name, commission_rate, goods_id');
        $sku_info['num'] = $data['num'];

        $price = 0;//如果是赠品的话, 价格为0
        $sku_info['num'] = $data['num'];
        $goods_money = $price * $data['num'];
        $sku_info['price'] = $price;
        $sku_info['goods_money'] = $goods_money;
        $sku_info['real_goods_money'] = $goods_money;
        $sku_info['coupon_money'] = 0;//优惠券金额
        $sku_info['promotion_money'] = 0;//优惠金额
        $sku_info['is_present'] = 1;
        $sku_info['present_id'] = $data['present_id'];
        return $sku_info;
    }


    /**************************************************************************** 赠品 end ************************************************************/


    /**************************************************************************** 店铺信息 start ************************************************************/

    /**
     * 获取站点信息
     * @param $site_id
     */
    public function getShopInfo($shop_goods){
        $site_id = $shop_goods['site_id'] ?? 0;
        $shop_model = new Shop();
        $shop_condition = array(
            ['site_id', '=', $site_id]
        );
        $shop_info = $shop_model->getShopInfo($shop_condition, '*')['data'] ?? [];
        $shop_goods['shop']['shop_info'] = $shop_info;
        if(empty($shop_info)){
            $this->error = 1;
            $this->error_msg = '店铺不存在';
            return $shop_goods;
        }
        if($shop_info['shop_status'] != 1){
            $this->error = 1;
            $this->error_msg = '店铺'.$shop_info['site_name'].'已关闭';
            return $shop_goods;
        }
        //查询店铺的
        return $shop_goods;

    }
    /**************************************************************************** 店铺信息 end ************************************************************/
}