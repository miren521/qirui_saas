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


namespace addon\store\model;

use addon\coupon\model\Coupon;
use app\model\express\Config as ExpressConfig;
use app\model\express\Express;
use app\model\goods\GoodsStock;
use app\model\member\Member;
use app\model\member\MemberAccount;
use app\model\order\OrderCreate;
use app\model\store\Store;
use app\model\system\Pay;
use think\facade\Cache;

/**
 * 订单创建(秒杀)
 *
 * @author Administrator
 *
 */
class StoreOrderCreate extends OrderCreate
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

    public $error = 0;  //是否有错误
    public $error_msg = '';  //错误描述
    public $invoice_delivery_money = 0;//发票邮寄费用

    /**
     * 订单创建
     * @param unknown $data
     */
    public function create($data)
    {
        //查询出会员相关信息
        $calculate_data = $this->calculate($data);
        if (isset($calculate_data['code']) && $calculate_data['code'] < 0)
            return $calculate_data;

        if ($this->error > 0) {
            return $this->error(['error_code' => $this->error], $this->error_msg);
        }

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
                'coupon_money' => $shop_goods_list['coupon_money'],
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
                'delivery_store_id' => $calculate_data["delivery_store_id"] ?? 0,
                "delivery_store_name" => $calculate_data["delivery_store_name"] ?? '',
                "delivery_store_info" => $calculate_data["delivery_store_info"] ?? '',
                "buyer_message" => $shop_goods_list["buyer_message"] ?? '',
                "promotion_type" => "",
                "promotion_type_name" => "门店收银",
                "promotion_status_name" => "",
                "website_id" => $shop_goods_list["website_id"],

                'buyer_ask_delivery_time' => $shop_goods_list['buyer_ask_delivery_time'] ?? '',//定时达
                "platform_coupon_id" => $shop_goods_list["platform_coupon_id"] ?? 0,
                "platform_coupon_money" => $shop_goods_list["platform_coupon_money"] ?? 0,
                "platform_coupon_total_money" => $shop_goods_list["platform_coupon_total_money"] ?? 0,
                "platform_coupon_shop_money" => $shop_goods_list["platform_coupon_shop_money"] ?? 0,

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
            $pay_money += $shop_goods_list['pay_money'];
            //订单项目表
            foreach ($shop_goods_list['goods_list'] as $k_order_goods => $order_goods) {
                $data_order_goods = array(
                    'order_id' => $order_id,
                    'site_id' => $shop_goods_list['site_id'],
                    'site_name' => $shop_goods_list['site_name'],
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
            //扣除余额(统一扣除)
            if ($calculate_data["balance_money"] > 0) {
                $balance_result = $this->storeUseBalance($calculate_data, $this);
                if ($balance_result["code"] < 0) {
                    model("order")->rollback();
                    return $balance_result;
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
            $pay->addPay(0, $out_trade_no, $this->pay_type, $this->order_name, $this->order_name, $this->pay_money, '', 'OrderPayNotify', '');
            $this->addOrderCronClose($order_id);//增加关闭订单自动事件
            Cache::tag("order_create_cash_" . $data['member_id'])->clear();

            model("order")->commit();
            return $this->success($out_trade_no);

        } catch ( \Exception $e ) {
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
        $data = $this->storeInfo($data);
        $data = $this->initMemberAccount($data);//初始化会员账户
        //余额付款
        if ($data['is_balance'] > 0) {
            $this->member_balance_money = $data["member_account"]["balance_total"] ?? 0;
        }
        //商品列表信息
        $shop_goods_list = $this->getOrderGoodsCalculate($data);


        $data['shop_goods_list'] = $this->shopOrderCalculate($shop_goods_list, $data);

        //总优惠使用
        $data = $this->eachShopOrder($data, false, $this);

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
     * @param unknown $data
     */
    public function orderPayment($data)
    {
        $calculate_data = $this->calculate($data);
        if (isset($calculate_data['code']) && $calculate_data['code'] < 0)
            return $calculate_data;

        $shop_goods_list = $calculate_data['shop_goods_list'];

        //1、查询会员优惠券
        $coupon_list = $this->getOrderCouponList($shop_goods_list, $calculate_data);
        $calculate_data['shop_goods_list']["coupon_list"] = $coupon_list;

        $express_type = [];
        $calculate_data['shop_goods_list']["express_type"] = $express_type;
        return $this->success($calculate_data);
    }

    /**
     * 获取商品的计算信息
     * @param unknown $data
     */
    public function getOrderGoodsCalculate($data)
    {
        $shop_goods_list = [];
        //通过秒杀id查询 商品数据
//        $cache = Cache::get("order_create_store_".$data['id'].'_'.$data['num'].'_'.$data['member_id']);
//        if(!empty($cache))
//        {
//            return $cache;
//        }
        $goods_list = $this->getStoreGoodsList($data["sku_ids"], $data['nums'], $data);
        $goods_list['promotion_money'] = 0;
        $shop_goods_list = $goods_list;
//        Cache::tag("order_create_store_".$data['member_id'])->set("order_create_seckill_".$data['id'].'_'.$data['num'].'_'.$data['member_id'], $shop_goods_list, 600);
        return $shop_goods_list;
    }

    /**
     * 获取秒杀商品列表信息
     * @param unknown $bl_id
     */
    public function getStoreGoodsList($sku_ids, $nums, $data)
    {


        //组装商品列表
        $field = 'sgs.store_stock,sgs.store_sale_num,ngs.sku_name, ngs.sku_id,ngs.sku_no,
            ngs.price, ngs.discount_price, ngs.cost_price, ngs.stock, ngs.weight, ngs.volume, ngs.sku_image, 
            ngs.site_id, ngs.site_name, ngs.website_id, ngs.is_own, ngs.goods_state, ngs.verify_state, ngs.is_virtual, 
            ngs.is_free_shipping, ngs.shipping_template, ngs.goods_class, ngs.goods_class_name, ngs.commission_rate,ngs.goods_id';
        $alias = 'sgs';
        $join = [
            [
                'goods_sku ngs',
                'sgs.sku_id = ngs.sku_id',
                'inner'
            ],
        ];
        $goods_list = model("store_goods_sku")->getList([['sgs.sku_id', 'in', $sku_ids], ['sgs.store_id', '=', $data['store_id']]], $field, '', $alias, $join);

//            $shop_goods_list = [];
        if (!empty($goods_list)) {
            foreach ($goods_list as $k => $v) {

                //用于过滤商品
                if ($v['goods_state'] == 0 || $v['verify_state'] == 0) {
                    $this->error = 1;
                    $this->error_msg = '商品未上架或未通过审核';
                }
                $num = $nums[$v["sku_id"]] ?? 0;
                $v["num"] = $num;
                $site_id = $v['site_id'];

                $price = $v["discount_price"];

                $v['price'] = $price;
                $v['goods_money'] = $price * $v["num"];
                $v['real_goods_money'] = $v['goods_money'];
                $v['coupon_money'] = 0;//优惠券金额
                $v['promotion_money'] = 0;//优惠金额
                if (isset($shop_goods_list)) {
                    $shop_goods_list['goods_list'][] = $v;
                    $shop_goods_list['order_name'] = string_split($shop_goods_list['order_name'], ",", $v['sku_name']);
                    $shop_goods_list['goods_num'] += $num;
                    $shop_goods_list['goods_money'] += $v['goods_money'];
                    $shop_goods_list['goods_list_str'] = $shop_goods_list['goods_list_str'] . ';' . $v['sku_id'] . ':' . $num;
                    $shop_goods_list['coupon_money'] += $v['coupon_money'];
                    $shop_goods_list['promotion_money'] += $v['promotion_money'];

                } else {
                    $shop_goods_list['site_id'] = $site_id;
                    $shop_goods_list['site_name'] = $v['site_name'];
                    $shop_goods_list['website_id'] = $v['website_id'];
                    $shop_goods_list['goods_money'] = $v['goods_money'];
                    $shop_goods_list['goods_list_str'] = $v['sku_id'] . ':' . $num;
                    $shop_goods_list['order_name'] = string_split("", ",", $v['sku_name']);
                    $shop_goods_list['goods_num'] = $num;
                    $shop_goods_list['goods_list'][] = $v;

                    $shop_goods_list['coupon_money'] = $v['coupon_money'];
                    $shop_goods_list['promotion_money'] = $v['promotion_money'];
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


        //计算邮费
        if (empty($data['member_address'])) {
            $delivery_money = 0;
            $shop_goods['delivery']['delivery_type'] = 'store';

            $this->error = 1;
            $this->error_msg = "未配置门店地址!";
        } else {
            $express_config_model = new ExpressConfig();

            //查询店铺是否开启门店自提
            $store_config_result = $express_config_model->getStoreConfig($site_id);
            $store_config = $store_config_result["data"];
            $shop_goods["store_config"] = $store_config;
//              $shop_goods['delivery'] = [];
            $shop_goods['delivery']["delivery_type"] = "store";

            //门店自提
            $delivery_money = 0;
//                        $shop_goods['delivery']['delivery_type'] = 'store';
//                        if ($shop_goods["store_config"]["is_use"] == 0) {
//                            $this->error = 1;
//                            $this->error_msg = "门店自提方式未开启!";
//                        }
            if (empty($data["store_id"])) {
                $this->error = 1;
                $this->error_msg = "门店未选择!";
            }
            $shop_goods['delivery']['store_id'] = $data["store_id"];

        }

        $shop_goods['delivery_money'] = $delivery_money;

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

        //总结计算
        $shop_goods['adjust_money'] = $adjust_money;
        $shop_goods['invoice_money'] = $invoice_money;
        return $shop_goods;
    }


    /**
     * 补齐门店数据
     * @param $data
     */
    public function storeInfo($data)
    {
        $delivery_store_id = $data["store_id"] ?? 0;//门店id

        if ($delivery_store_id > 0) {
            $store_model = new Store();
            $condition = array(
                ["store_id", "=", $delivery_store_id],
            );
            $store_info_result = $store_model->getStoreInfo($condition);
            $store_info = $store_info_result["data"] ?? [];
            if (empty($store_info)) {
                $this->error = 1;
                $this->error_msg = "当前门店不存在或未开启!";
            } else {
                $data["delivery_store_id"] = $delivery_store_id;
                $delivery_store_name = $store_info_result["data"]["store_name"];
                $data["delivery_store_name"] = $delivery_store_name;
                $delivery_store_info = array(
                    "open_date" => $store_info["open_date"],
                    "full_address" => $store_info["full_address"],
                    "longitude" => $store_info["longitude"],
                    "latitude" => $store_info["latitude"],
                    "telphone" => $store_info["telphone"],
                );
                $data["delivery_store_info"] = json_encode($delivery_store_info, JSON_UNESCAPED_UNICODE);
                $data["member_address"] = array(
                    'name' => '门店收银',
                    'mobile' => '',
                    'telephone' => '',
                    'province_id' => $store_info['province_id'],
                    'city_id' => $store_info['city_id'],
                    'district_id' => $store_info['district_id'],
                    'community_id' => $store_info['community_id'],
                    'address' => $store_info['address'],
                    'full_address' => $store_info['full_address'],
                    'longitude' => $store_info['longitude'],
                    'latitude' => $store_info['latitude'],
                );


            }
        } else {
            $this->error = 1;
            $this->error_msg = "配送门店不可为空!";
        }
        return $data;

    }


    /**
     * 使用余额
     * @param $order_data
     * @return array
     */
    public function storeUseBalance($data)
    {
        $this->pay_type = "BALANCE";
        $member_model = new Member();
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
        if ($surplus_banance > 0) {
            $result = $member_account_model->addMemberAccount($data["member_id"], "balance_money", -$surplus_banance, "order", "余额抵扣", "订单余额抵扣,扣除可提现余额:" . $surplus_banance);
        }

        return $result;

    }
}