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

use addon\electronicsheet\model\ElectronicsheetDelivery;
use app\model\express\ExpressDelivery;
use app\model\express\ExpressPackage;
use app\model\goods\GoodsStock;
use app\model\member\Member;
use app\model\system\Cron;
use app\model\message\Message;

/**
 * 普通（快递）订单
 *
 * @author Administrator
 *
 */
class Order extends OrderCommon
{

    /*****************************************************************************************订单状态***********************************************/
    // 订单创建
    const ORDER_CREATE = 0;

    // 订单已支付
    const ORDER_PAY = 1;

    // 订单备货中
    const ORDER_PENDING_DELIVERY = 2;

    // 订单已发货（配货）
    const ORDER_DELIVERY = 3;

    // 订单已收货
    const ORDER_TAKE_DELIVERY = 4;

    // 订单已结算完成
    const ORDER_COMPLETE = 10;

    // 订单已关闭
    const ORDER_CLOSE = -1;


    /**
     * 订单类型
     *
     * @var int
     */
    public $order_type = 1;


    /***********************************************************************************订单项  配送状态**************************************************/
    // 待发货
    const DELIVERY_WAIT = 0;

    // 已发货
    const DELIVERY_DOING = 1;

    // 已收货
    const DELIVERY_FINISH = 2;

    /**
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
                [
                    'action' => 'orderDelivery',
                    'title' => '发货',
                    'color' => ''
                ],
                [
                    'action' => 'orderAddressUpdate',
                    'title' => '修改地址',
                    'color' => ''
                ],
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
                [
                    'action' => 'extendTakeDelivery',
                    'title' => '延长收货',
                    'color' => ''
                ],
            ],
            'member_action' => [
                [
                    'action' => 'memberTakeDelivery',
                    'title' => '确认收货',
                    'color' => ''
                ],
                [
                    'action' => 'trace',
                    'title' => '查看物流',
                    'color' => ''
                ],
                [
                    'action' => 'extendTakeDelivery',
                    'title' => '延长收货',
                    'color' => ''
                ],
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
        ]
    ];

    /**
     * 配送状态
     */
    public $delivery_status = [
        self::DELIVERY_WAIT => [
            'status' => self::DELIVERY_WAIT,
            'name' => '待发货',
            'color' => ''
        ],
        self::DELIVERY_DOING => [
            'status' => self::DELIVERY_DOING,
            'name' => '已发货',
            'color' => ''
        ],
        self::DELIVERY_FINISH => [
            'status' => self::DELIVERY_FINISH,
            'name' => '已收货',
            'color' => ''
        ]
    ];

    /**
     * 订单状态（发货列表）
     */
    public $delivery_order_status = [
        self::ORDER_PAY => [
            'status' => self::ORDER_PAY,
            'name' => '待发货',
            'is_allow_refund' => 0,
            'icon' => 'upload/uniapp/order/order-icon-send.png',
            'action' => [
                [
                    'action' => 'orderDelivery',
                    'title' => '发货',
                    'color' => ''
                ],
                [
                    'action' => 'orderAddressUpdate',
                    'title' => '修改地址',
                    'color' => ''
                ],
            ],
            'member_action' => [],
            'color' => ''
        ]
    ];

    /**
     * 订单支付
     * @param unknown $order_info
     */
    public function orderPay($order_info, $pay_type)
    {
        $pay_type_list = $this->getPayType();
        if ($order_info['order_status'] != 0) {
            return $this->error();
        }
        $condition = array(
            ["order_id", "=", $order_info["order_id"]],
            ["order_status", "=", self::ORDER_CREATE],
        );
        $data = array(
            "order_status" => self::ORDER_PAY,
            "order_status_name" => $this->order_status[self::ORDER_PAY]["name"],
            "pay_status" => 1,
            "order_status_action" => json_encode($this->order_status[self::ORDER_PAY], JSON_UNESCAPED_UNICODE),
            "pay_time" => time(),
            "is_enable_refund" => 1,
            "pay_type" => $pay_type,
            "pay_type_name" => $pay_type_list[$pay_type]
        );

        $result = model("order")->update($data, $condition);
        return $this->success($result);
    }

    /**
     * 订单项发货（物流）
     * @param $param
     * @param int $type //1 订单项发货  2整体发货
     * @return array
     */
    public function orderGoodsDelivery($param, $type = 1)
    {
        model('order_goods')->startTrans();
        try {
            $order_goods_ids = $param['order_goods_ids'];
            $delivery_no = $param["delivery_no"];//物流单号
            $delivery_type = $param["delivery_type"];
            if ($delivery_type == 0) {
                $express_company_id = 0;
            } else {
                $express_company_id = $param["express_company_id"] ?? 0;
            }

            $site_id = $param["site_id"];

            if ($type == 1) {
                if (empty($param['order_goods_ids'])) {
                    model('order_goods')->rollback();
                    return $this->error('', "发送货物不可为空!");
                }
                $order_goods_id_array = explode(",", $param['order_goods_ids']);
            } else {
                $order_goods_id_array = model("order_goods")->getColumn(
                    [
                        ['order_id', '=', $param['order_id']],
                        ['site_id', '=', $site_id],
                        ['delivery_status', "=", self::DELIVERY_WAIT]
                    ],
                    'order_goods_id'
                );
            }

            $order_id = 0;
            $member_id = 0;
            $goods_id_array = [];
            foreach ($order_goods_id_array as $k => $v) {
                $order_goods_info = model("order_goods")->getInfo([["order_goods_id", "=", $v], ["site_id", "=", $site_id]], "sku_id,num,order_id,sku_name,sku_image,member_id,refund_status,delivery_status");
                //已退款的订单项不可发货
                if ($order_goods_info["refund_status"] == 3) {
                    model('order_goods')->rollback();
                    return $this->error([], "ORDER_GOODS_IS_REFUND");
                }

                if ($order_goods_info["delivery_status"] == self::DELIVERY_DOING) {
                    model('order_goods')->rollback();
                    return $this->error([], 'ORDER_GOODS_IS_DELIVERYED');
                }

                $order_id = $order_goods_info["order_id"];
                $member_id = $order_goods_info["member_id"];
                $goods_id_array[] = $order_goods_info["sku_id"] . ":" . $order_goods_info["num"] . ":" . $order_goods_info["sku_name"] . ":" . $order_goods_info["sku_image"];
                $data = ["delivery_status" => self::DELIVERY_DOING, "delivery_status_name" => $this->delivery_status[self::DELIVERY_DOING]["name"]];
                if (!empty($delivery_no)) {
                    $data['delivery_no'] = $delivery_no;
                }
                $res = model('order_goods')->update($data, [
                    ['order_goods_id', "=", $v],
                    ['delivery_status', "=", self::DELIVERY_WAIT]
                ]);
            }
            //创建包裹
            $order_common_model = new OrderCommon();
            $lock_result = $order_common_model->verifyOrderLock($order_id);
            if ($lock_result["code"] < 0) {
                model('order_goods')->rollback();
                return $lock_result;
            }

            $member_model = new Member();
            $member_info_result = $member_model->getMemberInfo([["member_id", "=", $member_id]], "nickname");
            $member_info = $member_info_result["data"];
            $express_delivery_model = new ExpressDelivery();
            $delivery_data = array(
                "order_id" => $order_id,
                "order_goods_id_array" => $order_goods_id_array,
                "goods_id_array" => $goods_id_array,
                "goods_array" => $goods_id_array,
                "site_id" => $site_id,
                "delivery_no" => $delivery_no,
                "member_id" => $member_id,
                'member_name' => $member_info['nickname'],
                "express_company_id" => $express_company_id,
                "delivery_type" => $delivery_type,
                'type' => $param['type'],
                'template_id' => $param['template_id'],
            );
            $result = $express_delivery_model->delivery($delivery_data);

            //检测整体, 订单中订单项是否全部发放完毕
            $res = $this->orderCommonDelivery($order_id);
            model('order_goods')->commit();
            return $this->success($result);
        } catch ( \Exception $e ) {
            model('order_goods')->rollback();
            return $this->error('', $e->getMessage());
        }

    }


    /**
     * 批量订单发货（物流）
     * @param $param
     * @return array
     */
    public function orderBatchDelivery($param, $order_list)
    {
        model('express_delivery_package')->startTrans();
        try {

            if (empty($order_list)) {
                return $this->error('', '请先选择要发货的订单');
            }

            foreach ($order_list as $v) {
                $param['order_id'] = $v['order_id'];
                $param['order_goods_ids'] = '';


                if ($param['type'] == 'electronicsheet') {//电子面单发货

                    $addon_is_exit = addon_is_exit('electronicsheet');
                    if ($addon_is_exit != 1) {
                        return $this->error('', '电子面单插件不存在');
                    }

                    $electronicsheet_model = new ElectronicsheetDelivery();
                    $result = $electronicsheet_model->delivery($param);
                    if ($result['code'] < 0) {
                        return $result;
                    }
                    $param['delivery_no'] = $result['data']['Order']['LogisticCode'];
                } else {
                    $param['delivery_no'] = $v['delivery_no'];
                }
                $result = $this->orderGoodsDelivery($param, 2);
                if ($result['code'] < 0) {
                    model('express_delivery_package')->rollback();
                    return $result;
                }
            }
            model('express_delivery_package')->commit();
            return $this->success();
        } catch ( \Exception $e ) {

            model('express_delivery_package')->rollback();
            return $this->error('', $e->getMessage());
        }
    }


    /**
     * 订单发货
     *
     * @param array $condition
     */
    public function orderDelivery($order_id)
    {
        //统计订单项目
        $count = model('order_goods')->getCount([['order_id', "=", $order_id], ['delivery_status', "=", self::DELIVERY_WAIT], ["refund_status", "<>", 3]], "order_goods_id");
        $delivery_count = model('order_goods')->getCount([['order_id', "=", $order_id], ['delivery_status', "=", self::DELIVERY_DOING], ["refund_status", "<>", 3]], "order_goods_id");
        if ($count == 0 && $delivery_count > 0) {
            //修改订单项的配送状态
            $order_data = array(
                'order_status' => self::ORDER_DELIVERY,
                'order_status_name' => $this->order_status[self::ORDER_DELIVERY]["name"],
                'delivery_status' => self::DELIVERY_FINISH,
                'delivery_status_name' => $this->delivery_status[self::DELIVERY_FINISH]["name"],
                'order_status_action' => json_encode($this->order_status[self::ORDER_DELIVERY], JSON_UNESCAPED_UNICODE),
                'delivery_time' => time()
            );
            $res = model('order')->update($order_data, [['order_id', "=", $order_id]]);

//            //获取订单自动收货时间
//            $config_model = new Config();
//            $event_time_config_result = $config_model->getOrderEventTimeConfig();
//            $event_time_config = $event_time_config_result["data"];
//            $now_time = time();//当前时间
//
//            if (!empty($event_time_config)) {
//                $execute_time = $now_time + $event_time_config["value"]["auto_take_delivery"] * 86400;//自动收货时间
//            } else {
//                $execute_time = $now_time + 86400;//尚未配置  默认一天
//            }
//            //默认自动时间
//            $cron_model = new Cron();
//            $cron_model->addCron(1, 1, "订单自动收货", "CronOrderTakeDelivery", $execute_time, $order_id);
//
//            event('OrderDelivery', ['order_id' => $order_id]);


            return $res;
        } else {
            return $this->error();
        }

    }

    /**
     * 订单收货
     *
     * @param int $order_id
     */
    public function orderTakeDelivery($order_id)
    {
        return $this->success();
    }

    /**
     * 订单收货地址修改
     */
    public function orderAddressUpdate($param, $condition)
    {
        $province_id = $param["province_id"];
        $city_id = $param["city_id"];
        $district_id = $param["district_id"];
        $community_id = $param["community_id"];
        $address = $param["address"];
        $full_address = $param["full_address"];
        $longitude = $param["longitude"];
        $latitude = $param["latitude"];
        $mobile = $param["mobile"];
        $telephone = $param["telephone"];
        $name = $param["name"];
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
        $order_info = model("order")->getInfo($condition, "order_status");
        $order_status_array = [self::ORDER_PAY, self::ORDER_CREATE];
        if (!in_array($order_info["order_status"], $order_status_array))
            return $this->error("", "当前订单状态不可编辑收货地址!");

        $result = model('order')->update($data, $condition);
        return $this->success($result);
    }

    /**
     * 退款完成操作
     * @param $order_info
     */
    public function refund($order_goods_info)
    {
        //是否入库
        if ($order_goods_info["is_refund_stock"] == 1) {
            $goods_stock_model = new GoodsStock();
            $item_param = array(
                "sku_id" => $order_goods_info["sku_id"],
                "num" => $order_goods_info["num"],
            );
            //返还库存
            $goods_stock_model->incStock($item_param);
        }
        //检测订单项是否否全部发放完毕
        $this->orderDelivery($order_goods_info["order_id"]);
    }

    /**
     * 订单详情
     * @param $order_info
     */
    public function orderDetail($order_info)
    {
        $express_package_model = new ExpressPackage();
        $package_list = $express_package_model->package([["order_id", "=", $order_info['order_id']]], $order_info['mobile']);
        $order_info = [];
        $order_info["package_list"] = $package_list;
        return $order_info;
    }

    /**
     *  计算订单销售额
     * @param array $condition
     * @param string $field
     * @return array
     */
    public function getOrderMoneySum($condition = [], $field = 'order_money')
    {
        $res = model('order')->getSum($condition, $field);
        return $this->success($res);
    }



}