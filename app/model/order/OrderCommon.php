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
use app\model\goods\Goods;
use app\model\goods\GoodsStock;
use app\model\member\MemberAccount;
use app\model\system\Cron;
use app\model\system\Pay;
use app\model\verify\Verify;
use Exception;
use think\facade\Cache;
use app\model\BaseModel;
use app\model\message\Message;

/**
 * 常规订单操作
 *
 * @author Administrator
 *
 */
class OrderCommon extends BaseModel
{
    /*****************************************************************************************订单基础状态（其他使用）********************************/
    // 订单待付款
    const ORDER_CREATE = 0;


    // 订单已支付
    const ORDER_PAY = 1;

    // 订单已发货（配货）
    const ORDER_DELIVERY = 3;

    // 订单已收货
    const ORDER_TAKE_DELIVERY = 4;

    // 订单已结算完成
    const ORDER_COMPLETE = 10;
    // 订单已关闭
    const ORDER_CLOSE = -1;

    /*********************************************************************************订单支付状态****************************************************/
    // 待支付
    const PAY_WAIT = 0;

    // 支付中
    const PAY_DOING = 1;

    // 已支付
    const PAY_FINISH = 2;

    /**************************************************************************支付方式************************************************************/
    const OFFLINEPAY = 10;


    /**
     * 基础订单状态(不同类型的订单可以不使用这些状态，但是不能冲突)
     * @var unknown
     */
    public $order_status = [
        self::ORDER_CREATE => [
            'status' => self::ORDER_CREATE,
            'name' => '待支付',
            'is_allow_refund' => 0,
            'action' => [
                [
                    'action' => 'orderClose',
                    'title' => '关闭订单',
                    'color' => ''
                ],
                [
                    'action' => 'orderAddressUpdate',
                    'title' => '修改地址',
                    'color' => ''
                ],
                [
                    'action' => 'orderAdjustMoney',
                    'title' => '调整价格',
                    'color' => ''
                ],
            ],
            'member_action' => [
                [
                    'action' => 'orderClose',
                    'title' => '关闭订单',
                    'color' => ''
                ],
                [
                    'action' => 'orderPay',
                    'title' => '支付',
                    'color' => ''
                ],
            ],
            'color' => ''
        ],
        self::ORDER_PAY => [
            'status' => self::ORDER_PAY,
            'name' => '待发货',
            'is_allow_refund' => 0,
            'action' => [

            ],
            'member_action' => [

            ],
            'color' => ''
        ],
        self::ORDER_DELIVERY => [
            'status' => self::ORDER_DELIVERY,
            'name' => '已发货',
            'is_allow_refund' => 1,
            'action' => [
            ],
            'member_action' => [
            ],
            'color' => ''
        ],
        self::ORDER_TAKE_DELIVERY => [
            'status' => self::ORDER_TAKE_DELIVERY,
            'name' => '已收货',
            'is_allow_refund' => 1,
            'action' => [
            ],
            'member_action' => [
            ],
            'color' => ''
        ],
        self::ORDER_COMPLETE => [
            'status' => self::ORDER_COMPLETE,
            'name' => '已完成',
            'is_allow_refund' => 1,
            'action' => [
            ],
            'member_action' => [

            ],
            'color' => ''
        ],
        self::ORDER_CLOSE => [
            'status' => self::ORDER_CLOSE,
            'name' => '已关闭',
            'is_allow_refund' => 0,
            'action' => [

            ],
            'member_action' => [

            ],
            'color' => ''
        ],

    ];

    /**
     * 基础支付方式(不考虑实际在线支付方式或者货到付款方式)
     * @var array
     */
    public $pay_type = [
        'ONLINE_PAY' => '在线支付',
        'BALANCE' => '余额支付',
        'OFFLINE_PAY' => '线下支付'
    ];

    /**
     * 订单类型
     *
     * @var int
     */
    public $order_type = [
        1 => "普通订单",
        2 => "自提订单",
        3 => "外卖订单",
        4 => "虚拟订单",
    ];

    /**
     * 获取支付方式
     */
    public function getPayType()
    {
        //获取订单基础的其他支付方式
        $pay_type = $this->pay_type;
        //获取当前所有在线支付方式
        $onlinepay = event('PayType', []);
        if (!empty($onlinepay)) {
            foreach ($onlinepay as $k => $v) {
                $pay_type[$v['pay_type']] = $v['pay_type_name'];
            }
        }
        return $pay_type;
    }

    /**
     * 订单类型(根据物流配送来区分)
     */
    public function getOrderTypeStatusList()
    {
        $list = [];
        $list['all'] = array(
            "name" => "全部",
            "type" => 'all',
            "status" => array_column($this->order_status, "name", "status")
        );
        foreach ($this->order_type as $k => $v) {
            switch ($k) {
                case 1:
                    $order_model = new Order();
                    break;
                case 2:
                    $order_model = new StoreOrder();
                    break;
                case 3:
                    $order_model = new LocalOrder();
                    break;
                case 4:
                    $order_model = new VirtualOrder();
                    break;
            }
            $item = array(
                "name" => $v,
                "type" => $k,
                "status" => array_column($order_model->order_status, "name", "status")
            );
            $list[$k] = $item;
        }
        return $list;
    }

    /**
     * 生成订单编号
     * @param $site_id
     * @return string
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
    /**********************************************************************************订单操作基础方法（订单关闭，订单完成，订单调价）开始********/

    /**
     * 订单删除
     * @param $condition
     * @return array
     */
    public function deleteOrder($condition)
    {
        $res = model('order')->update(['is_delete' => 1], $condition);
        if ($res === false) {
            return $this->error();
        } else {
            return $this->success($res);
        }
    }

    /**
     * 订单完成
     * @param $order_id
     * @return array
     */
    public function orderComplete($order_id)
    {
        $cache = Cache::get("order_complete_execute_" . $order_id);
        if (empty($cache)) {
            Cache::set("order_complete_execute_" . $order_id, 1);
        } else {
            return $this->success();
        }

        $lock_result = $this->verifyOrderLock($order_id);
        if ($lock_result["code"] < 0)
            return $lock_result;

        $order_info = model('order')->getInfo([['order_id', '=', $order_id]], 'member_id, order_money, refund_money,order_status');

        if ($order_info['order_status'] == self::ORDER_COMPLETE) {
            return $this->success();
        }
        $order_data = array(
            'order_status' => self::ORDER_COMPLETE,
            'order_status_name' => $this->order_status[self::ORDER_COMPLETE]["name"],
            'order_status_action' => json_encode($this->order_status[self::ORDER_COMPLETE], JSON_UNESCAPED_UNICODE),
            'finish_time' => time(),
            'is_enable_refund' => 0
        );
        $res = model('order')->update($order_data, [['order_id', "=", $order_id]]);
        Cache::set("order_complete_execute_" . $order_id, '');
        //修改用户表order_complete_money和order_complete_num

        model('member')->setInc([['member_id', '=', $order_info['member_id']]], 'order_complete_money', $order_info['order_money'] - $order_info['refund_money']);
        model('member')->setInc([['member_id', '=', $order_info['member_id']]], 'order_complete_num');
        event('OrderComplete', ['order_id' => $order_id]);
        $order_refund_model = new OrderRefund();
        //订单项移除可退款操作
        $order_refund_model->removeOrderGoodsRefundAction([["order_id", "=", $order_id]]);
        //订单完成
        $message_model = new Message();
        $message_model->sendMessage(['keywords' => "ORDER_COMPLETE", 'order_id' => $order_id]);

        return $this->success($res);
    }

    /**
     * 订单关闭
     * @param $order_id
     * @return array
     */
    public function orderClose($condition)
    {
        model('order')->startTrans();
        try {
            $order_info = model("order")->getInfo($condition, "order_id,coupon_id,pay_status,member_id,is_lock,balance_money,order_no,mobile,order_status");
            if ($order_info["order_status"] == -1) {
                model('order')->commit();
                return $this->success();
            }
            $order_id = $order_info['order_id'];
            $lock_result = $this->verifyOrderLock($order_info);
            if ($lock_result["code"] < 0) {
                model('order')->rollback();
                return $lock_result;
            }

            $order_data = [
                'order_status' => self::ORDER_CLOSE,
                'order_status_name' => $this->order_status[self::ORDER_CLOSE]["name"],
                'order_status_action' => json_encode($this->order_status[self::ORDER_CLOSE], JSON_UNESCAPED_UNICODE),
                'close_time' => time(),
                'is_enable_refund' => 0
            ];
            model('order')->update($order_data, [['order_id', "=", $order_id]]);

            //库存处理
            $condition = [["order_id", "=", $order_id]];

            //循环订单项 依次返还库存
            $order_goods_list = model('order_goods')->getList($condition, "sku_id,num,refund_status");
            $goods_stock_model = new GoodsStock();
            $order_refund_model = new OrderRefund();
            $goods_model = new Goods();

            $is_exist_refund = false;//是否存在退款
            foreach ($order_goods_list as $k => $v) {
                //如果是已维权完毕的订单项, 库存不必再次返还
                if ($v["refund_status"] != $order_refund_model::REFUND_COMPLETE) {
                    $item_param = [
                        "sku_id" => $v["sku_id"],
                        "num" => $v["num"],
                    ];
                    //返还库存
                    $goods_stock_model->incStock($item_param);
                }

                if ($v["refund_status"] == $order_refund_model::REFUND_COMPLETE) {
                    $is_exist_refund = true;
                }

                //减少商品销量(必须支付过)
                if ($order_info["pay_status"] > 0) {
                    $goods_model->decGoodsSaleNum($v["sku_id"], $v["num"]);
                }
            }

            //订单项移除可退款操作
            $order_refund_model->removeOrderGoodsRefundAction([["order_id", "=", $order_id]]);

            //返还店铺优惠券
            $coupon_id = $order_info["coupon_id"];
            if ($coupon_id > 0) {
                $coupon_model = new Coupon();
                $coupon_model->refundCoupon($coupon_id, $order_info["member_id"]);
            }
            //平台优惠券

            //平台余额  退还余额
            if (!$is_exist_refund) {//因为订单完成后  只有全部退款完毕订单才会关闭
                if ($order_info["balance_money"] > 0) {
                    $member_account_model = new MemberAccount();
                    $member_account_model->addMemberAccount($order_info["member_id"], "balance", $order_info["balance_money"], "order", "余额返还", "订单关闭,返还余额:" . $order_info["balance_money"]);
                }
            }
            //订单关闭后操作
            event('OrderClose', ['order_id' => $order_id]);
            model('order')->commit();

            //订单关闭消息
            $message_model = new Message();
            $message_model->sendMessage(['keywords' => "ORDER_CLOSE", 'order_id' => $order_id]);

            return $this->success();
        } catch (Exception $e) {
            model('order')->rollback();
            return $this->error('', $e->getMessage());
        }
    }

    /**
     * 订单线上支付
     * @param $data
     * @return array
     */
    public function orderOnlinePay($data)
    {
        model('order')->startTrans();
        try {
            $out_trade_no = $data["out_trade_no"];
            $pay_type = $data['pay_type'] ?? 'ONLINE_PAY';
            $order_list = model("order")->getList([['out_trade_no', '=', $out_trade_no]], '*');
            $message_list = [];
            //订单支付消息
            $order_message = new OrderMessage();
            foreach ($order_list as $k => $order) {
                if ($order['order_status'] == -1) {
                    continue;
                }
                switch ($order['order_type']) {
                    case 1:
                        $order_model = new Order();
                        break;
                    case 2:
                        $order_model = new StoreOrder();
                        break;
                    case 3:
                        $order_model = new LocalOrder();
                        break;
                    case 4:
                        $order_model = new VirtualOrder();
                        break;
                    default:
                        model('order')->rollback();
                        return $this->error('', '订单类型错误');
                }
                $order_model->orderPay($order, $pay_type);
                //同时将用户表的order_money和order_num更新
                model('member')->setInc([['member_id', '=', $order['member_id']]], 'order_money', $order['order_money']);
                model('member')->setInc([['member_id', '=', $order['member_id']]], 'order_num');
                $order_message->sendMessagePay($order);
                //支付后商品增加销量
                $order_goods_list = model("order_goods")->getList([["order_id", "=", $order["order_id"]]], "sku_id,num");
                $goods_model = new Goods();
                foreach ($order_goods_list as $ck => $v) {
                    $goods_model->incGoodsSaleNum($v["sku_id"], $v["num"]);
                }

                //订单项增加可退款操作
                $order_refund_model = new OrderRefund();
                $order_refund_model->initOrderGoodsRefundAction([["order_id", "=", $order["order_id"]]]);

                // 发送消息

                $message_list[] = $order;
                event("OrderPay", $order);
            }


            model('order')->commit();
            return $this->success();
        } catch (Exception $e) {
            model('order')->rollback();
            return $this->error('', $e->getMessage());
        }
    }

    /**
     * 订单线下支付
     * @param $order_id
     * @return array
     */
    public function orderOfflinePay($order_id)
    {
        model('order')->startTrans();
        try {
            $split_result = $this->splitOrderPay($order_id);
            if ($split_result["code"] < 0)
                return $split_result;

            $out_trade_no = $split_result["data"];
            $pay_model = new Pay();
            $result = $pay_model->onlinePay($out_trade_no, "OFFLINE_PAY", '', '');
            if ($result["code"] < 0) {
                model('order')->rollback();
                return $result;
            }
            model('order')->commit();
            return $result;
        } catch (Exception $e) {
            model('order')->rollback();
            return $this->error('', $e->getMessage());
        }
    }

    /**
     * 拆分订单
     * @param $order_ids
     * @return array
     */
    public function splitOrderPay($order_ids)
    {
        $order_ids = empty($order_ids) ? [] : explode(",", $order_ids);
        $order_list = model("order")->getList([["order_id", "in", $order_ids], ["pay_status", "=", 0]], "pay_money,order_name,out_trade_no,order_id,pay_status");
        $order_count = count($order_list);
        //判断订单数是否匹配
        if (count($order_ids) > $order_count)
            return $this->error([], "选中订单中包含已支付数据!");

//        $rewrite_order_ids = [];//受影响的id组
        $close_out_trade_no_array = [];
        $pay_money = 0;
        $pay_model = new Pay();
        $order_name_array = [];

        foreach ($order_list as $order_k => $item) {
            $pay_money += $item["pay_money"];//累加金额
            $order_name_array[] = $item["order_name"];
            if (!in_array($item["out_trade_no"], $close_out_trade_no_array)) {
                $close_out_trade_no_array[] = $item["out_trade_no"];
            }
//            $field_list = model("order")->getColumn([["out_trade_no", "=", $item["out_trade_no"]]], "order_id");
        }
        //现有的支付单据完全匹配
        if (count($close_out_trade_no_array) == 1) {
            $out_trade_no = $close_out_trade_no_array[0];
            //必须是有效的支付单据
            $pay_info_result = $pay_model->getPayInfo($out_trade_no);
            if (!empty($pay_info_result["data"])) {
                $temp_order_count = model("order")->getCount([["out_trade_no", "=", $out_trade_no], ["order_id", "not in", $order_ids]], "order_id");
                if ($temp_order_count == 0) {
                    return $this->success($out_trade_no);
                }
            }
        }
        //循环管理订单支付单据
        foreach ($close_out_trade_no_array as $close_k => $close_v) {
            $result = $pay_model->deletePay($close_v);//关闭旧支付单据
            if ($result["code"] < 0) {
                return $this->error([], "选中订单中包含已支付数据!");
            }
        }
        $order_name = implode(",", $order_name_array);
        //生成新的支付单据
        $out_trade_no = $pay_model->createOutTradeNo();
        //修改交易流水号为新生成的
        model("order")->update(["out_trade_no" => $out_trade_no], [["order_id", "in", $order_ids], ["pay_status", "=", 0]]);
        $pay_model->addPay(0, $out_trade_no, "", $order_name, $order_name, $pay_money, '', 'OrderPayNotify', '');
        return $this->success($out_trade_no);
    }

    /**
     * 订单金额调整
     * @param $order_id
     * @param $adjust_money
     * @param $delivery_money
     * @return array|mixed|void
     */
    public function orderAdjustMoney($condition, $adjust_money, $delivery_money)
    {
        model('order')->startTrans();
        try {
            //查询订单
            $order_info = model('order')->getInfo($condition, 'order_id, site_id, out_trade_no,delivery_money, adjust_money, pay_money, order_money, promotion_money, coupon_money, goods_money, balance_money, invoice_money, invoice_delivery_money,invoice_rate');
            if (empty($order_info))
                return $this->error("", "找不到订单");

            $order_id = $order_info['order_id'];
            if ($delivery_money < 0)
                return $this->error("", "配送费用不能小于0!");

            $real_goods_money = $order_info['goods_money'] - $order_info['promotion_money'] - $order_info['coupon_money'];//计算出订单真实商品金额

            $new_goods_money = $real_goods_money + $adjust_money;

            if ($new_goods_money < 0)
                return $this->error("", "真实商品金额不能小于0!");

            $invoice_money = $new_goods_money * $order_info['invoice_rate'];

            $new_order_money = $invoice_money + $new_goods_money + $delivery_money + $order_info['invoice_delivery_money'];

            if ($new_order_money < 0)
                return $this->error("", "订单金额不能小于0!");

            $pay_money = $new_order_money - $order_info['balance_money'];
            if ($pay_money < 0)
                return $this->error("", "实际支付不能小于0!");


            $data_order = array(
                'delivery_money' => $delivery_money,
                'pay_money' => $pay_money,
                'adjust_money' => $adjust_money,
                'order_money' => $new_order_money,
                'invoice_money' => $invoice_money,
            );
            model('order')->update($data_order, [['order_id', "=", $order_id]]);


            $order_goods_list = model('order_goods')->getList([['order_id', "=", $order_id]], 'order_goods_id,goods_money,adjust_money,coupon_money,promotion_money,platform_coupon_money');
            //将调价摊派到所有订单项()
            $real_goods_money = $order_info['goods_money'] - $order_info['promotion_money'] - $order_info['coupon_money'];
            $this->distributionGoodsAdjustMoney($order_goods_list, $real_goods_money, $adjust_money);

            //关闭原支付  生成新支付
            $pay_model = new Pay();
            $pay_result = $pay_model->deletePay($order_info["out_trade_no"]);//关闭旧支付单据
            if ($pay_result["code"] < 0) {
                model('order')->rollback();
                return $pay_result;
            }
            $pay_result["data"];

            if($pay_money == 0){
                $this->orderOfflinePay($order_info['order_id']);
            }
            model('order')->commit();
            return $this->success();
        } catch (Exception $e) {
            model('order')->rollback();
            return $this->error('', $e->getMessage());
        }
    }


    /**
     * 按比例摊派订单调价
     */
    public function distributionGoodsAdjustMoney($goods_list, $goods_money, $adjust_money)
    {
        $temp_adjust_money = $adjust_money;
        $last_key = count($goods_list) - 1;
        foreach ($goods_list as $k => $v) {
            $item_goods_money = $v['goods_money'] - $v['promotion_money'] - $v['coupon_money'] - $v['platform_coupon_money'];
            if ($last_key != $k) {
                $item_adjust_money = round(floor($item_goods_money / $goods_money * $adjust_money * 100) / 100, 2);
            } else {
                $item_adjust_money = $temp_adjust_money;
            }
            $temp_adjust_money -= $item_adjust_money;
            $real_goods_money = $item_goods_money + $item_adjust_money;
            $real_goods_money = $real_goods_money < 0 ? 0 : $real_goods_money;
            $order_goods_data = array(
                'adjust_money' => $item_adjust_money,
                'real_goods_money' => $real_goods_money,
            );
            model('order_goods')->update($order_goods_data, [['order_goods_id', '=', $v['order_goods_id']]]);
        }
        return $this->success();
    }

    /**
     * 订单编辑
     * @param $data
     * @param $condition
     */
    public function orderUpdate($data, $condition)
    {
        $order_model = model("order");
        $res = $order_model->update($data, $condition);
        if ($res === false) {
            return $this->error();
        } else {
            return $this->success($res);
        }
    }

    /**
     * 订单发货
     * @param $order_id
     * @return array
     */
    public function orderCommonDelivery($order_id)
    {
        $order_common_model = new OrderCommon();
        $lock_result = $order_common_model->verifyOrderLock($order_id);
        if ($lock_result["code"] < 0)
            return $lock_result;


        $order_info = model("order")->getInfo([["order_id", "=", $order_id]], "order_type");
        switch ($order_info['order_type']) {
            case 1:
                $order_model = new Order();
                break;
            case 2:
                $order_model = new StoreOrder();
                break;
            case 3:
                $order_model = new LocalOrder();
                break;
            case 4:
                $order_model = new VirtualOrder();
                break;
        }
        $result = $order_model->orderDelivery($order_id);
        if ($result["code"] < 0) {
            return $result;
        }
        //获取订单自动收货时间
        $config_model = new Config();
        $event_time_config_result = $config_model->getOrderEventTimeConfig();
        $event_time_config = $event_time_config_result["data"];
        $now_time = time();//当前时间

        if (!empty($event_time_config)) {
            $execute_time = $now_time + $event_time_config["value"]["auto_take_delivery"] * 86400;//自动收货时间
        } else {
            $execute_time = $now_time + 86400;//尚未配置  默认一天
        }
        //默认自动时间
        $cron_model = new Cron();
        $cron_model->addCron(1, 1, "订单自动收货", "CronOrderTakeDelivery", $execute_time, $order_id);
        event('OrderDelivery', ['order_id' => $order_id]);

        //订单发货消息
        $message_model = new Message();
        $message_model->sendMessage(['keywords' => "ORDER_DELIVERY", 'order_id' => $order_id]);
        return $result;


    }

    /**
     * 订单收货
     * @param $order_id
     * @return array
     */
    public function orderCommonTakeDelivery($condition)
    {
        $order_info = model('order')->getInfo($condition, 'order_type, order_id');
        if (empty($order_info))
            return $this->error([], "ORDER_EMPTY");

        $order_id = $order_info['order_id'];
        $lock_result = $this->verifyOrderLock($order_id);
        if ($lock_result["code"] < 0)
            return $lock_result;

        switch ($order_info['order_type']) {
            case 1:
                $order_model = new Order();
                break;
            case 2:
                $order_model = new StoreOrder();
                break;
            case 3:
                $order_model = new LocalOrder();
                break;
            case 4:
                $order_model = new VirtualOrder();
                break;
        }
        model('order')->startTrans();
        try {
            $order_model->orderTakeDelivery($order_id);
            //改变订单状态
            $order_data = array(
                'order_status' => $order_model::ORDER_TAKE_DELIVERY,
                'order_status_name' => $order_model->order_status[$order_model::ORDER_TAKE_DELIVERY]["name"],
                'order_status_action' => json_encode($order_model->order_status[$order_model::ORDER_TAKE_DELIVERY], JSON_UNESCAPED_UNICODE),
                "is_evaluate" => 1,
                "evaluate_status" => 0,
                "evaluate_status_name" => "待评价",
                "sign_time" => time()
            );
            model('order')->update($order_data, [['order_id', '=', $order_id]]);
            $this->addCronOrderComplete($order_id);
            event('OrderTakeDelivery', ['order_id' => $order_id]);
            model('order')->commit();

            //订单收货消息
            $message_model = new Message();
            $message_model->sendMessage(['keywords' => "ORDER_TAKE_DELIVERY", 'order_id' => $order_id]);

            return $this->success();
        } catch (Exception $e) {
            model('order')->rollback();
            return $this->error('', $e->getMessage());
        }
    }

    /**
     * 延长收货
     * 会员或商家延长收货时间
     * @param $order_id
     * @param $condition
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function extendTakeDelivery($condition, $action_data = []){
        $check_condition = array_column($condition, 2, 0);
        $order_info = model('order')->getInfo($condition, 'order_id, order_status,order_status_name, name, order_status_action');
        if (empty($order_info))
            return $this->error([], "ORDER_EMPTY");

        //如果是会员的操作,需要判断会员延长收货的操作记录, 因为会员只能延长一次收货时间
        $member_id = $check_condition['member_id'] ?? 0;
        if($member_id > 0){
            $order_log_condition = array(
                ['uid', '=', $member_id],
                ['action', '=','order'],
                ['order_id', '=',$order_info['order_id']],
                ['module', '=','member'],
                ['action_way', '=','extendTakeDelivery']
            );
            $order_log_info = $this->getOrderLog($order_log_condition);
            if(!empty($order_log_info['data'])){
                return $this->error([], "一个订单只能延长一次收货时间!");
            }
            $action_data = array(
                'uid' => $member_id,
                'username' => $order_info['name'] ?? '',
                'module' => 'member',
            );

            //改变订单的操作,将会员的操作删除
            $order_status_action = json_decode($order_info['order_status_action'], true);
            $order_member_action = $order_status_action['member_action'] ?? [];
            foreach($order_member_action as $k => $v){
                if($v['action'] == 'extendTakeDelivery'){
                    unset($order_member_action[$k]);
                }
            }
            $order_status_action['member_action'] = $order_member_action;

            $order_data = array(
                'order_status_action' => json_encode($order_status_action, JSON_UNESCAPED_UNICODE),
            );
            model('order')->update($order_data, [['order_id', '=', $order_info['order_id']]]);


        }
        //生成延长收货订单日志
        $order_log_data = array(
            'uid' => $action_data['uid'] ?? 0,
            'nick_name' => $action_data['username'] ?? '',
            'action' => 'order',
            'order_status' => $order_info['order_status'],
            'order_status_name' => $order_info['order_status_name'],
            'module' => $action_data['module'] ?? 'shop',
            'action_way' => 'extendTakeDelivery',
            'order_id' => $order_info['order_id']
        );
        $order_log_result = $this->addOrderLog($order_log_data);

        //判断订单是否处于待收货状态
        if($order_info['order_status'] != self::ORDER_DELIVERY)
            return $this->error([], "ORDER_STATUS_NOT_DELIVERY");

        $order_id = $order_info['order_id'];
        $cron_model = new Cron();
        $cron_condition = array(
            ['event', '=', 'CronOrderTakeDelivery'],
            ['relate_id', '=', $order_id]
        );
        //查询之前的自动收货时间
        $cron_info = $cron_model->getCron($cron_condition, 'execute_time');
        //删除当前订单的自动收货事件
        $cron_result = $cron_model->deleteCron($cron_condition);
        if($cron_result['code'] < 0)
            return $cron_result;
        //并创建新的订单自动收货事件
        $execute_time = $cron_info['data']['execute_time'] ?? time();
        $new_execute_time = $execute_time + 86400 * 3;
        $result = $cron_model->addCron(1, 1, "订单自动收货", "CronOrderTakeDelivery", $new_execute_time, $order_id);
        return $result;
    }
    /**
     * 添加订单自动完成事件
     * @param $order_id
     * @return array
     */
    public function addCronOrderComplete($order_id)
    {
        //获取订单自动完成时间
        $config_model = new Config();
        $event_time_config_result = $config_model->getOrderEventTimeConfig();
        $event_time_config = $event_time_config_result["data"];
        $now_time = time();
        if (!empty($event_time_config)) {
            $execute_time = $now_time + $event_time_config["value"]["auto_complete"] * 86400;//自动完成时间
        } else {
            $execute_time = $now_time + 86400;//尚未配置  默认一天
        }
        //设置订单自动完成事件
        $cron_model = new Cron();
        $result = $cron_model->addCron(1, 0, "订单自动完成", "CronOrderComplete", $execute_time, $order_id);
        return $this->success($result);
    }

    /**
     * 订单解除锁定
     * @param $order_id
     * @return int
     */
    public function orderUnlock($order_id)
    {
        $data = array(
            "is_lock" => 0
        );
        $res = model("order")->update($data, [["order_id", "=", $order_id]]);
        return $res;
    }

    /**
     * 订单锁定
     * @param $order_id
     * @return mixed
     */
    public function orderLock($order_id)
    {
        $data = array(
            "is_lock" => 1
        );
        $res = model("order")->update($data, [["order_id", "=", $order_id]]);
        return $res;
    }

    /**
     * 验证订单锁定状态
     * @param $param
     * @return array
     */
    public function verifyOrderLock($param)
    {
        if (!is_array($param)) {
            $order_info = model("order")->getInfo([["order_id", "=", $param]], "is_lock");
        } else {
            $order_info = $param;
        }
        if ($order_info["is_lock"] == 1) {//判断订单锁定状态
            model('order')->rollback();
            return $this->error('', "ORDER_LOCK");
        } else {
            return $this->success();
        }
    }



    /**********************************************************************************订单操作基础方法（订单关闭，订单完成，订单调价）结束********/

    /****************************************************************************订单数据查询（开始）*************************************/

    /**
     * 获取订单详情
     * @param $order_id
     * @return array
     */
    public function getOrderDetail($condition)
    {
        $order_info = model('order')->getInfo($condition);
        if (empty($order_info))
            return $this->error('', 'ORDER_EMPTY');

        $order_id = $order_info['order_id'] ?? 0;
        $member_info = model('member')->getInfo([['member_id', "=", $order_info['member_id']]], 'nickname');

        $order_info['nickname'] = $member_info['nickname'];

        $order_goods_list = model('order_goods')->getList([['order_id', "=", $order_id]]);
        $order_info['order_goods'] = $order_goods_list;


        switch ($order_info['order_type']) {
            case 1:
                $order_model = new Order();
                break;
            case 2:
                $order_model = new StoreOrder();
                break;
            case 3:
                $order_model = new LocalOrder();
                break;
            case 4:
                $order_model = new VirtualOrder();
                break;
        }

        $temp_info = $order_model->orderDetail($order_info);
        $order_info = array_merge($order_info, $temp_info);

        //如果是待收货状态,需要查询订单自动收货的时间
        if($order_info['order_status'] == self::ORDER_DELIVERY){
            $order_id = $order_info['order_id'];
            $cron_model = new Cron();
            $cron_condition = array(
                ['event', '=', 'CronOrderTakeDelivery'],
                ['relate_id', '=', $order_id]
            );
            //查询之前的自动收货时间
            $cron_info = $cron_model->getCron($cron_condition, 'execute_time')['data'] ?? [];
            $execute_time = $cron_info['execute_time'] ?? 0;
            $order_info['take_delivery_execute_time'] = $execute_time;//自动收货时间
        }
        return $this->success($order_info);
    }


    /**
     * 得到订单基础信息
     * @param $condition
     * @param string $field
     * @return array
     */
    public function getOrderInfo($condition, $field = "*")
    {
        $res = model("order")->getInfo($condition, $field);
        return $this->success($res);
    }

    /**
     * 得到订单数量
     * @param $condition
     * @return array
     */
    public function getOrderCount($condition)
    {
        $res = model("order")->getCount($condition);
        return $this->success($res);
    }

    /**
     * 获取订单列表
     * @param array $condition
     * @param string $field
     * @param string $order
     * @param null $limit
     * @return array
     */
    public function getOrderList($condition = [], $field = '*', $order = '', $limit = null)
    {
        $list = model('order')->getList($condition, $field, $order, '', '', '', $limit);
        return $this->success($list);
    }

    /**
     * 获取订单分页列表
     * @param array $condition
     * @param int $page
     * @param int $page_size
     * @param string $order
     * @param string $field
     * @return array
     */
    public function getOrderPageList($condition = [], $page = 1, $page_size = PAGE_LIST_ROWS, $order = '', $field = '*')
    {
        $order_list = model('order')->pageList($condition, $field, $order, $page, $page_size);
        if (!empty($order_list['list'])) {
            foreach ($order_list['list'] as $k => $v) {
                $order_goods_list = model("order_goods")->getList([
                    'order_id' => $v['order_id']
                ]);
                $order_list['list'][$k]['order_goods'] = $order_goods_list;
            }
        }

        return $this->success($order_list);
    }

    /**
     * 订单列表（已商品为主）
     * @param array $condition
     * @return array
     */
    public function getOrderGoodsDetailList($condition = [])
    {
        $alias = 'og';
        $join = [
            [
                'order o',
                'o.order_id = og.order_id',
                'left'
            ]
        ];
        $order_field = 'o.order_no,o.site_name,o.order_name,o.order_from_name,o.order_type_name,o.order_promotion_name,o.out_trade_no,o.out_trade_no_2,o.delivery_code,o.order_status_name,o.pay_status,o.delivery_status,o.refund_status,o.pay_type_name,o.delivery_type_name,o.name,o.mobile,o.telephone,o.full_address,o.buyer_ip,o.buyer_ask_delivery_time,o.buyer_message,o.goods_money,o.delivery_money,o.promotion_money,o.coupon_money,o.order_money,o.adjust_money,o.balance_money,o.pay_money,o.refund_money,o.pay_time,o.delivery_time,o.sign_time,o.finish_time,o.remark,o.goods_num,o.delivery_status_name,o.shop_money,o.platform_money,o.is_settlement,o.delivery_store_name,o.promotion_type_name,';

        $order_goods_field = 'og.sku_name,og.sku_no,og.is_virtual,og.goods_class_name,og.price,og.cost_price,og.num,og.goods_money,og.cost_money,og.delivery_no,og.refund_no,og.refund_type,og.refund_apply_money,og.refund_reason,og.refund_real_money,og.refund_delivery_name,og.refund_delivery_no,og.refund_time,og.refund_refuse_reason,og.refund_action_time,og.commission_rate,og.real_goods_money,og.shop_money,og.platform_money,og.refund_remark,og.refund_delivery_remark,og.refund_address,og.is_refund_stock';

        $list = model('order_goods')->getList($condition, $order_field . $order_goods_field, 'og.order_goods_id desc', $alias, $join);
        return $this->success($list);
    }

    /**
     * 获取订单项详情
     * @param array $condition
     * @param string $field
     * @return array
     */
    public function getOrderGoodsInfo($condition = [], $field = '*')
    {
        $info = model("order_goods")->getInfo($condition, $field);
        return $this->success($info);
    }

    /**
     * 获取订单列表
     * @param array $condition
     * @param string $field
     * @param string $order
     * @param null $limit
     * @param string $group
     * @return array
     */
    public function getOrderGoodsList($condition = [], $field = '*', $order = '', $limit = null, $group = '')
    {
        $list = model('order_goods')->getList($condition, $field, $order, '', '', $group, $limit);
        return $this->success($list);
    }
    /****************************************************************************订单数据查询结束*************************************/

    /****************************************************************************会员订单订单数据查询开始*************************************/

    /**
     * 会员订单详情
     * @param $order_id
     * @param $member_id
     * @return array
     */
    public function getMemberOrderDetail($order_id, $member_id)
    {
        $order_info = model('order')->getInfo([['order_id', "=", $order_id], ["member_id", "=", $member_id]]);
        if (empty($order_info))
            return $this->error([], "当前订单不是本账号的订单!");

        $action = empty($order_info["order_status_action"]) ? [] : json_decode($order_info["order_status_action"], true);
        $member_action = $action["member_action"] ?? [];
        $order_info['action'] = $member_action;
        $order_goods_list = model('order_goods')->getList([['order_id', "=", $order_id], ["member_id", "=", $member_id]]);

//        $complain_model = new Complain();
        foreach ($order_goods_list as $k => $v) {
            $refund_action = empty($v["refund_status_action"]) ? [] : json_decode($v["refund_status_action"], true);
            $refund_action = $refund_action["member_action"] ?? [];
            $order_goods_list[$k]["refund_action"] = $refund_action;

            //判断维权操作
            $complain_action = 0;
            //订单项未退款完毕  订单未完成  为关闭
            if ($v["refund_status"] != 3 && $v["refund_status"] != 0 && !in_array($order_info["order_status"], [-1, 10])) {
//                    $complain_info_result = $complain_model->getComplainInfo([["order_goods_id", "=", $v["order_goods_id"]]]);
//                    $complain_info = $complain_info_result["data"];
//                    if(!empty($complain_info)){
//                        $complain_action = empty($v["complain_status_action"]) ? [] : json_decode($v["complain_status_action"], true);
//                        $complain_action = $complain_action["member_action"] ?? [];
//                    }else{
//                        $complain_action = [
//                            'event' => 'complain',
//                            'title' => '平台维权',
//                            'color' => ''
//                        ];
//                    }
                $complain_action = 1;
            }

            $order_goods_list[$k]["complain_action"] = $complain_action;
        }
        $order_info['order_goods'] = $order_goods_list;
        $code_result = $this->orderQrcode($order_info);
        $order_info = array_merge($order_info, $code_result);
        $order_info["code_info"] = $code_result;

        switch ($order_info['order_type']) {
            case 1:
                $order_model = new Order();
                break;
            case 2:
                $order_model = new StoreOrder();
                break;
            case 3:
                $order_model = new LocalOrder();
                break;
            case 4:
                $order_model = new VirtualOrder();
                break;
        }

        $temp_info = $order_model->orderDetail($order_info);
        $order_info = array_merge($order_info, $temp_info);

        //如果是待收货状态,需要查询订单自动收货的时间
        if($order_info['order_status'] == self::ORDER_DELIVERY){
            $order_id = $order_info['order_id'];
            $cron_model = new Cron();
            $cron_condition = array(
                ['event', '=', 'CronOrderTakeDelivery'],
                ['relate_id', '=', $order_id]
            );
            //查询之前的自动收货时间
            $cron_info = $cron_model->getCron($cron_condition, 'execute_time')['data'] ?? [];
            $execute_time = $cron_info['execute_time'] ?? 0;
            $order_info['take_delivery_execute_time'] = $execute_time;//自动收货时间
        }
        return $this->success($order_info);
    }

    /**
     * 会员订单分页列表
     * @param array $condition
     * @param int $page
     * @param int $page_size
     * @param string $order
     * @param string $field
     * @return array
     */
    public function getMemberOrderPageList($condition = [], $page = 1, $page_size = PAGE_LIST_ROWS, $order = '', $field = '*')
    {
        $order_list = model('order')->pageList($condition, $field, $order, $page, $page_size);
        if (!empty($order_list['list'])) {
            foreach ($order_list['list'] as $k => $v) {
                $order_goods_list = model("order_goods")->getList([
                    'order_id' => $v['order_id']
                ]);
                $order_list['list'][$k]['order_goods'] = $order_goods_list;
                $action = empty($v["order_status_action"]) ? [] : json_decode($v["order_status_action"], true);
                $member_action = $action["member_action"] ?? [];
                $order_list['list'][$k]['action'] = $member_action;
            }
        }
        return $this->success($order_list);
    }

    /**
     * 订单生成码
     * @param $order_info
     * @return array
     */
    public function orderQrcode($order_info)
    {

        $app_type = input("app_type");
        switch ($order_info['order_type']) {
            case 2:
                $code = $order_info["delivery_code"];
                $verify_type = "pickup";
                break;
            case 4:
                $code = $order_info["virtual_code"];
                $verify_type = "virtualgoods";
                break;
            default:
                return [];
        }
        $verify_model = new Verify();
        $result = $verify_model->qrcode($code, $app_type, $verify_type, "get");
        $data = [];
        if (!empty($result) && $result["code"] >= 0) {
            $data[$verify_type] = $result["data"]["path"];
        }
        return $data;
    }

    /****************************************************************************会员订单订单数据查询结束*************************************/


    /***************************************************************** 交易记录 *****************************************************************/

    /**
     * 获取交易记录分页列表
     * @param array $condition
     * @param int $page
     * @param int $page_size
     * @param string $order
     * @param string $field
     * @return array
     */
    public function getTradePageList($condition = [], $page = 1, $page_size = PAGE_LIST_ROWS, $order = '', $field = '*')
    {
        $list = model('order')->pageList($condition, $field, $order, $page, $page_size);
        return $this->success($list);
    }

    /***************************************************************** 交易记录 *****************************************************************/


    /************************************************************************* 订单日志 start ********************************************************************/
    /**
     * 添加订单日志
     * @param array $data
     * @return array
     */
    public function addOrderLog($data)
    {
        $data['action_time'] = time();//操作时间
        $res = model("order_log")->add($data);
        return $this->success($res);
    }

    /**
     * 获取订单日志
     * @param $condition
     * @param string $field
     */
    public function getOrderLog($condition, $field = "*"){
        $res = model("order_log")->getInfo($condition, $field);
        return $this->success($res);
    }
    /**
     * 删除订单日志
     * @param $condition
     * @return array
     */
    public function deleteOrderLog($condition)
    {
        $res = model("order_log")->delete($condition);
        return $this->success($res);
    }



}