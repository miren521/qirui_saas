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


namespace addon\supply\model\order;

use app\model\BaseModel;
use addon\supply\model\goods\GoodsStock;
use app\model\system\Cron;
use app\model\system\SiteAddress;
use think\facade\Cache;
use app\model\express\Express;
use app\model\system\Pay;
use addon\supply\model\goods\Cart as CartModel;
use app\model\express\Config as ExpressConfig;
use addon\supply\model\order\Config as OrderConfig;

/**
 * 订单创建(批发)
 *
 * @author Administrator
 *
 */
class OrderCreate extends BaseModel
{

    public $goods_money = 0;//商品金额
    public $delivery_money = 0;//配送费用
    public $adjust_money = 0;//调整金额
    public $invoice_money = 0;//发票费用
    public $order_money = 0;//订单金额
    public $pay_money = 0;//支付总价
    public $is_virtual = 0;  //是否是虚拟类订单
    public $order_name = '';  //订单详情
    public $goods_num = 0;  //商品种数
    public $pay_type = 'ONLINE_PAY';//支付方式

    public $is_exist_not_free = false;
    public $is_exist_free = false;
    public $balance_money = 0;//余额
    public $account_balance_money = 0;//会员账户余额(计算过程中会逐次减少)
    public $error = 0;  //是否有错误
    public $error_msg = '';  //错误描述



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

        model("supply_order")->startTrans();
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
                    'order_from'  => $data['order_from'],
                    'order_from_name' => $data['order_from_name'],
                    'order_type' => $order_type['order_type_id'],
                    'order_type_name' => $order_type['order_type_name'],
                    'order_status_name' => $order_type['order_status']['name'],
                    'order_status_action' => json_encode($order_type['order_status'], JSON_UNESCAPED_UNICODE),
                    'out_trade_no' => $temp_out_trade_no,
                    'goods_money' => $v['goods_money'],
                    'delivery_money' => $v['delivery_money'],
                    'adjust_money' => $v['adjust_money'],
                    'invoice_money' => $v['invoice_money'],
                    'order_money' => $v['order_money'],
                    'balance_money' => $v['balance_money'],
                    'pay_money' => $v['pay_money'],
                    'create_time' => time(),
                    'is_enable_refund' => 0,
                    'order_name' => $v["order_name"],
                    'goods_num' => $v['goods_num'],
                    'delivery_type' => $delivery_type,
                    'delivery_type_name' => $delivery_type_name,
                    "website_id" => $v["website_id"],


                    'buyer_shop_id'     => $calculate_data['buyer_shop_id'],//购买人所在店铺id
                    'buyer_shop_name'   => $calculate_data['buyer_shop_name'],//购买人所在店铺
                    'buyer_uid' => $data['buyer_uid'],
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
                    'buyer_ask_delivery_time' => $v['buyer_ask_delivery_time'] ?? '',//定时达
                    "buyer_message" => $v["buyer_message"],

                    'site_id'     => $v['site_id'],
                    'site_name'   => $v['site_name'],

                ];

                $order_id = model("supply_order")->add($data_order);
                $order_id_arr[] = $order_id;
                $pay_money += $v['pay_money'];
                //订单项目表
                foreach ($v['goods_list'] as $k_order_goods => $order_goods)
                {
                    $data_order_goods = array(
                        'order_id'        => $order_id,
                        'order_no'        => $order_no,
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

                        'buyer_shop_id'         => $calculate_data['buyer_shop_id'],
                        'buyer_shop_name'       => $calculate_data['buyer_shop_name'],
                        'buyer_uid'       => $calculate_data['buyer_uid'],

                        'site_id'     => $v['site_id'],
                        'site_name'   => $v['site_name'],

                    );
                    model("supply_order_goods")->add($data_order_goods);
                    //库存变化
                    $stock_result = $goods_stock_model->decStock(["sku_id" => $order_goods['sku_id'], "num" => $order_goods['num']]);
                    if($stock_result["code"] != 0){
                        model("supply_order")->rollback();
                        return $stock_result;
                    }

                }
            }

//            //扣除余额(统一扣除)
//            if($calculate_data["balance_money"] > 0){
//                $balance_result = $this->useBalance($calculate_data);
//                if($balance_result["code"] < 0){
//                    model("supply_order")->rollback();
//                    return $balance_result;
//                }
//            }

            //循环执行订单完成事件
            foreach($order_id_arr as $k => $v){
                $result_list = event("SupplyOrderCreate", ['order_id' => $v]);
                if(!empty($result_list)){
                    foreach($result_list as $k => $v){
                        if(!empty($v) && $v["code"] < 0){
                            model("supply_order")->rollback();
                            return $v;
                        }
                    }
                }

            }

            //生成整体付费支付单据
            if($this->is_exist_not_free) {
                $order_name_title = implode(",", $order_name);
                $pay->addPay(0, $out_trade_no, $this->pay_type, $order_name_title, $order_name_title, $this->pay_money, '', 'SupplyOrderPayNotify', '');
            }
            //免费订单支付单据
            if($this->is_exist_free){
                $free_order_name_title = implode(",", $free_order_name);
                $pay->addPay(0, $free_out_trade_no, $this->pay_type, $free_order_name_title, $free_order_name_title, 0, '', 'SupplyOrderPayNotify', '');
            }


            $this->addOrderCronClose($order_id);//增加关闭订单自动事件

            $cart_ids = isset($data['cart_ids']) ? $data['cart_ids'] : '';
            if(!empty($cart_ids))
            {
                $cart = new CartModel();
                $data_cart = [
                    'cart_id' => $cart_ids,
                    'uid' => $data['buyer_uid']
                ];
                $cart->deleteCart($data_cart);
            }
            Cache::tag("supply_order_create_uid_".$data['buyer_uid'])->clear();
//            $this->checkFree($data_order);//如果订单金额为0, 直接调用支付成功
            model("supply_order")->commit();
            return $this->success($out_trade_no ?? $free_out_trade_no);
        }catch(\Exception $e)
        {
            model("supply_order")->rollback();
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
        $data = $this->initAddress($data);//初始化地址
        $data = $this->initAccount($data);//初始化会员账户


        //商品列表信息
        $shop_goods_list = $this->getOrderGoodsCalculate($data);
        if(empty($shop_goods_list)){
            $data['shop_goods_list'] = [];
            $this->error = 1;
            $this->error_msg = '找不到商品!';
        }else{
            foreach ($shop_goods_list as $k => $v)
            {
                $data['shop_goods_list'][$k] = $this->shopOrderCalculate($v, $data);
            }
        }


        //总优惠使用
        $data = $this->eachShopOrder($data,true, $this);

        //总结计算
        $data['delivery_money'] = $this->delivery_money;
        $data['adjust_money'] = $this->adjust_money;
        $data['invoice_money'] = $this->invoice_money;
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
            $calculate_data['shop_goods_list'][$k] = $this->itemPayment($v, $calculate_data);
        }
        return $this->success($calculate_data);

    }


    /**
     * 初始化组件 各订单
     * @param $shop_item
     * @param $data
     * @param null $self
     * @return mixed
     */
    public function itemPayment($shop_item, $data){

        $express_type = [];
        if($this->is_virtual == 0){
            if(!empty($data['member_address'])){
                //2. 查询店铺配送方式（1. 物流  2. 自提  3. 外卖）
                if($shop_item["express_config"]["is_use"] == 1){
                    $express_type[] = ["title" => Express::express_type["express"]["title"], "name" => "express"];
                }

//                //查询店铺是否开启门店自提
//                if ($shop_item["store_config"]["is_use"] == 1) {
//                    //根据坐标查询门店
//                    $store_model = new Store();
//                    $store_condition = array(
//                        ['site_id', '=', $shop_item['site_id']],
//                        ['is_pickup', '=', 1],
//                        ['status', '=', 1],
//                        ['is_frozen', '=', 0],
//                    );
//
//                    $latlng = array(
//                        'lat' => $data['latitude'],
//                        'lng' => $data['longitude'],
//                    );
//                    $store_list_result = $store_model->getLocationStoreList($store_condition, '*', $latlng);
//                    $store_list = $store_list_result["data"];
//
//                    //如果用户默认选中了门店
//                    $store_id = 0;
//                    if(!empty($store_list)){
//                        $store_id = $store_list[0]['store_id'];
//                    }
//                    $express_type[] = ["title" => Express::express_type["store"]["title"], "name" => "store", "store_list" => $store_list, 'store_id' => $store_id];
//                }
//
//                //查询店铺是否开启外卖配送
//                if($shop_item["local_config"]["is_use"] == 1){
//                    //查询本店的通讯地址
//                    $express_type[] = ["title" => "外卖配送", "name" => "local"];
//                }
            }
        }
        $shop_item["express_type"] = $express_type;
        return $shop_item;
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
                ['shop_id', '=', $data['buyer_shop_id']]
            );
            $cart_list = model('supply_goods_cart')->getList($cart_condition, 'sku_id, num');
            $condition[] = ['sku_id', 'in', array_column($cart_list, 'sku_id')];
            $num_array = array_column($cart_list, 'num', 'sku_id');
        }else{
            $condition[] = ['sku_id', '=', $data['sku_id']];
            $num_array = [$data['sku_id'] => $data['num']];
        }

        $shop_goods = $this->getGoodsSkuList($condition, $num_array);
        $shop_goods_list = $shop_goods;
        return $shop_goods_list;
    }

    /**
     * 获取购物车商品列表信息
     * @param unknown $cart_ids
     */
    public function getGoodsSkuList($condition, $num_array)
    {
        //组装商品列表
        $field = 'price_json, sku_id,sku_name, sku_no, price, cost_price, stock, weight, volume, sku_image, site_id, site_name, website_id, is_own, goods_state, is_virtual, is_free_shipping, shipping_template, goods_class, goods_class_name, commission_rate,goods_id';

        $goods_list = model("supply_goods_sku")->getList($condition, $field);
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
                    $this->error_msg = $v['sku_name'].'没有配置阶梯价格';
                }
                $v['num'] = $item_num;
                $v['price'] = $price;
                $v['goods_money'] = $price * $item_num;
                $v['real_goods_money'] = $v['goods_money'];

                if(isset($shop_goods_list[$site_id]))
                {
                    $shop_goods_list[$site_id]['goods_list'][] = $v;
                    $shop_goods_list[$site_id]['order_name'] = string_split($shop_goods_list[$site_id]['order_name'], ",", $v['sku_name']);
                    $shop_goods_list[$site_id]['goods_num'] += $v['num'];
                    $shop_goods_list[$site_id]['goods_money'] += $v['price'] * $v['num'];
                    $shop_goods_list[$site_id]['goods_list_str'] = $shop_goods_list[$site_id]['goods_list_str'].';'.$v['sku_id'].':'.$v['num'];

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
        $shop_goods['order_money'] = $shop_goods['goods_money'] + $shop_goods['delivery_money'] + $invoice_money;


        //买家留言
        if(isset($data['buyer_message']) && isset($data['buyer_message'][$site_id])){
            $item_buyer_message = $data['buyer_message'][$site_id];
            $shop_goods["buyer_message"] = $item_buyer_message;
        }else{
            $shop_goods["buyer_message"] = '';
        }

        //总结计算
        $shop_goods['adjust_money'] = $adjust_money;
        $shop_goods['invoice_money'] = $invoice_money;

        return $shop_goods;
    }










    /******************************************************************* 订单(不具体区分订单形式)公有操作 ******************************************************************/

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
        if (! isset($max_no) || empty($max_no)) {
            $max_no = 1;
        } else {
            $max_no = $max_no + 1;
        }
        $order_no = $time_str . sprintf("%04d", $max_no);
        Cache::set($site_id . "_" . $time_str, $max_no);
        return $order_no;
    }



    /**
     * 遍历各店铺订单  计算余额
     * @param $data
     * @return mixed
     */
    public function eachShopOrder($data,  $is_self = true, $self = null){
//        $is_self = false;
        $shop_goods_list = $data['shop_goods_list'];
        if($is_self){
            $self = $this;
            foreach($shop_goods_list as $k => $v){
                $shop_goods_list[$k] = $this->eachShopOrderItem($v, $self, $is_self);
            }
        }else{
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
    public function eachShopOrderItem($item, $self, $is_self){
        //余额抵扣(判断是否使用余额)
        if ($self->account_balance_money > 0) {
            if ($item['order_money'] <= $self->account_balance_money) {
                $balance_money = $item['order_money'];
            } else {
                $balance_money = $self->account_balance_money;
            }
        } else {
            $balance_money = 0;
        }
        $pay_money = $item['order_money'] - $balance_money;//计算出实际支付金额
        //判断是否存在支付金额为0的订单
        if($is_self){
            if ($pay_money > 0) {
                $self->is_exist_not_free = true;
            } else {
                $self->is_exist_free = true;
            }
        }

        $self->account_balance_money -= $balance_money;//预减少账户余额

        $item['pay_money'] = $pay_money;
        $item['balance_money'] = $balance_money;
        $self->pay_money += $pay_money;
        $self->balance_money += $balance_money;//累计余额
        $self->goods_money += $item['goods_money'];
        $self->delivery_money += $item['delivery_money'];
        $self->adjust_money += $item['adjust_money'];
        $self->invoice_money += $item['invoice_money'];
        $self->order_money += $item['order_money'];

        $self->goods_num += $item["goods_num"];
        $self->order_name = string_split($this->order_name, ",", $item["order_name"]);

        return $item;
    }
    

    /**
     * 初始化收货地址(用户)
     * @param unknown $data
     */
    public function initAddress($data)
    {
        //收货人地址管理
        if(empty($data['member_address']))
        {
            //todo 店铺或个人用户的收货地址管理
            $site_address_model = new SiteAddress();
            $condition = array(
                ['site_id', '=', $data['buyer_shop_id']],
                ['is_default', '=', 1],
            );
            $address_info_result = $site_address_model->getSiteAddressInfo($condition);
            $data['member_address'] = $address_info_result['data'] ?? [];
        }
        return $data;
    }


    /**
     * 初始化账户
     * @param $data
     * @return mixed
     */
    public function initAccount($data){
        //todo 店铺或个人用户的账户信息(余额或.....)

        //余额付款
        if($data['is_balance'] > 0)
        {
//            $this->account_balance_money = $data["account"]["balance_total"] ?? 0;
        }
        return $data;
    }



    /**
     * 运费计算
     * @param $shop_goods
     * @param $data
     * @param null $self
     * @return mixed
     */
    public function delivery($shop_goods, $data, $self = null){
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
//            //查询店铺是否开启门店自提
//            $store_config_result = $express_config_model->getStoreConfig($site_id);
//            $store_config = $store_config_result["data"];
//            $shop_goods["store_config"] = $store_config;
//            //查询店铺是否开启外卖配送
//            $local_config_result = $express_config_model->getLocalDeliveryConfig($site_id);
//            $local_config = $local_config_result["data"];
//            $shop_goods["local_config"] = $local_config;
//            //如果本地配送开启, 则查询出本地配送的配置
//            if($shop_goods["local_config"]['is_use'] == 1){
//                $local_model = new Local();
//                $local_info_result = $local_model->getLocalInfo([['site_id', '=', $site_id]]);
//                $local_info = $local_info_result['data'];
//                $shop_goods["local_config"]['info'] = $local_info;
//            }
//

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

                        //todo 做出兼容
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
                }
//                else if ($delivery_type == "local") {
//                    //外卖配送
//                    $delivery_money = 0;
//                    $shop_goods['delivery']['delivery_type'] = 'local';
//                    if ($shop_goods["local_config"]["is_use"] == 0) {
//                        $self->error = 1;
//                        $self->error_msg = "外卖配送方式未开启!";
//                    }else{
//                        $local_delivery_time = 0;
//                        if(!empty($delivery_array['buyer_ask_delivery_time'])){
//                            $buyer_ask_delivery_time_temp = explode(':', $delivery_array['buyer_ask_delivery_time']);
//                            $local_delivery_time = $buyer_ask_delivery_time_temp[0] * 3600 + $buyer_ask_delivery_time_temp[1] * 60;
//                        }
//                        $shop_goods['buyer_ask_delivery_time'] = $local_delivery_time;
//                        $local_model = new Local();
//                        $local_result = $local_model->calculate($shop_goods, $data);
//                        if($local_result['code'] < 0){
//                            $self->error = $local_result['data']['code'];
//                            $self->error_msg = $local_result['message'];
//                        }else{
//                            $delivery_money = $local_result['data']['delivery_money'];
//                            if(!empty($local_result['data']['error_code'])){
//                                $self->error = $local_result['data']['code'];
//                                $self->error_msg = $local_result['data']['error'];
//                            }
//                        }
//                    }
//                }else if ($delivery_type == "store") {
//                    //门店自提
//                    $delivery_money = 0;
//                    $shop_goods['delivery']['delivery_type'] = 'store';
//                    if ($shop_goods["store_config"]["is_use"] == 0) {
//                        $self->error = 1;
//                        $self->error_msg = "门店自提方式未开启!";
//                    }
//                    if (empty($delivery_array["store_id"])) {
//                        $self->error = 1;
//                        $self->error_msg = "门店未选择!";
//                    }
//                    $shop_goods['delivery']['store_id'] = $delivery_array["store_id"];
//                    $shop_goods = $this->storeOrderData($shop_goods, $data, $self);
//                }
            }

        }
        $shop_goods['delivery_money'] = $delivery_money;
        return $shop_goods;
    }



    /**
     * 增加订单自动关闭事件
     * @param $order_id
     */
    public function addOrderCronClose($order_id){
        //计算订单自动关闭时间
        $config_model = new OrderConfig();
        $order_config_result = $config_model->getOrderTradeConfig();
        $order_config = $order_config_result["data"];
        $now_time = time();
        if(!empty($order_config)){
            $execute_time = $now_time + $order_config["value"]["auto_close"]*60;//自动关闭时间
        }else{
            $execute_time = $now_time + 3600;//尚未配置  默认一天
        }
        $cron_model = new Cron();
        $cron_model->addCron(1, 0, "供应商订单自动关闭", "CronSupplyOrderClose", $execute_time, $order_id);
    }

    /**
     * 订单类型判断
     * @param unknown $shop_goods
     */
    public function orderType($shop_goods, $data)
    {
        if($data["is_virtual"] == 1)
        {
//            $order = new VirtualOrder();
//            return [
//                'order_type_id' => 4,
//                'order_type_name' => '虚拟订单',
//                'order_status' => $order->order_status[0]
//            ];
        }else{
            if($shop_goods['delivery']['delivery_type'] == 'express')
            {
                $order = new Order();
                return [
                    'order_type_id' => 1,
                    'order_type_name' => '普通订单',
                    'order_status' => $order->order_status[0]
                ];
            }elseif($shop_goods['delivery']['delivery_type'] == 'store'){
//                $order = new StoreOrder();
//                return [
//                    'order_type_id' => 2,
//                    'order_type_name' => '自提订单',
//                    'order_status' => $order->order_status[0]
//                ];
            }elseif($shop_goods['delivery']['delivery_type'] == 'local'){
//                $order = new LocalOrder();
//                return [
//                    'order_type_id' => 3,
//                    'order_type_name' => '外卖订单',
//                    'order_status' => $order->order_status[0]
//                ];
            }
        }
    }
}