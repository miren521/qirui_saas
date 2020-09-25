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

use app\model\express\LocalPackage;
use app\model\goods\GoodsStock;
use app\model\message\Message;
use app\model\verify\Verify;
/**
 * 门店自提订单
 *
 * @author Administrator
 *
 */
class StoreOrder extends OrderCommon
{
    
    /*****************************************************************************************订单状态***********************************************/
    // 订单创建
    const ORDER_CREATE = 0;
    
    // 订单已支付
    const ORDER_PAY = 1;
    
    // 订单待提货
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
    public $order_type = 2;
    

    /**
     */
    public $order_status = [
        self::ORDER_CREATE => [
            'status' => self::ORDER_CREATE,
            'name' => '待支付',
            'is_allow_refund' => 0,
            'action' =>[
                [
                'action' => 'orderClose',
                'title'=> '关闭订单',
                'color'=> ''
                ],
                [
                'action' => 'orderAdjustMoney',
                'title'=> '调整价格',
                'color'=> ''
                ],
            ],
            'member_action' => [
                [
                'action' => 'orderClose',
                'title'=> '关闭订单',
                'color'=> ''
                    ],
                [
                'action' => 'orderPay',
                'title'=> '支付',
                'color'=> ''
                    ],
            ],
            'color' => ''
        ],
        self::ORDER_PENDING_DELIVERY => [
            'status' => self::ORDER_PENDING_DELIVERY,
            'name' => '待提货',
            'is_allow_refund' => 0,
            'action' =>[
//                [
//                    'action' => 'orderDelivery',
//                    'title'=> '提货',
//                    'color'=> ''
//                ],
            ],
            'member_action' => [
            
            ],
            'color' => ''
        ],
        self::ORDER_TAKE_DELIVERY => [
            'status' => self::ORDER_TAKE_DELIVERY,
            'name' => '已提货',
            'is_allow_refund' => 1,
            'action' =>[
            ],
            'member_action' => [
            ],
            'color' => ''
        ],
        self::ORDER_COMPLETE => [
            'status' => self::ORDER_COMPLETE,
            'name' => '已完成',
            'is_allow_refund' => 1,
            'action' =>[
            ],
            'member_action' => [
            
            ],
            'color' => ''
        ],
        self::ORDER_CLOSE => [
            'status' => self::ORDER_CLOSE,
            'name' => '已关闭',
            'is_allow_refund' => 0,
            'action' =>[
    
            ],
            'member_action' => [
            
            ],
            'color' => ''
        ],
    ];
    
    /**
     * 订单支付
     * @param unknown $order_info
     */
    public function orderPay($order_info, $pay_type)
    {
        if($order_info['order_status'] != 0)
        {
            return $this->error();
        }

        $condition = array(
            ["order_id", "=", $order_info["order_id"]],
            ["order_status", "=", self::ORDER_CREATE],
        );
        $verify = new Verify();
        $order_goods_list = model("order_goods")->getList([["order_id", "=", $order_info["order_id"]]], "sku_image,sku_name,price,num");
        $item_array = [];
        foreach($order_goods_list as $k => $v){
            $item_array[] = [
                "img" => $v["sku_image"],
                "name" => $v["sku_name"],
                "price" => $v["price"],
                "num" => $v["num"],
                "remark_array" => [

                ]
            ];
        }
        $pay_time = time();
        $remark_array = array(
            ["title" => '订单金额', "value" => $order_info["order_money"]],
            ["title" => '订单编号', "value" => $order_info["order_no"]],
            ["title" => '创建时间', "value" => time_to_date($order_info["create_time"])],
            ["title" => '付款时间', "value" => time_to_date($pay_time)],
            ["title" => '收货地址', "value" => $order_info["full_address"]],
            ["title" => '选择门店', "value" => $order_info["delivery_store_name"]],
        );
        $verify_content_json = $verify->getVerifyJson($item_array, $remark_array);

        $code = $verify->addVerify("pickup", $order_info['site_id'], $order_info['site_name'], $verify_content_json);
        $pay_type_list = $this->getPayType();
        $data = array(
            "order_status" => self::ORDER_PENDING_DELIVERY,
            "order_status_name" => $this->order_status[self::ORDER_PENDING_DELIVERY]["name"],
            "pay_status" => 1,
            "order_status_action" => json_encode($this->order_status[self::ORDER_PENDING_DELIVERY], JSON_UNESCAPED_UNICODE),
            "delivery_code" => $code['data']['verify_code'],
            "pay_time" => $pay_time,
            "is_enable_refund" => 1,
            "pay_type" => $pay_type,
            "pay_type_name" => $pay_type_list[$pay_type]
        );

        $res = model('order')->update($data, $condition);

        $result = $verify->qrcode($code['data']['verify_code'], "all","pickup", "create");
        return $this->success($res);
    }

    /**
     * 主动提货
     * @param $delivery_code
     * @return \multitype
     */
    public function verify($delivery_code){
        $order_info = model("order")->getInfo([['delivery_code', '=', $delivery_code]], 'order_id, order_type, sign_time, order_status, delivery_code');
        if(empty($order_info))
            return $this->error([], "ORDER_EMPTY");

        $take_condition = array(
            ['order_id', '=', $order_info["order_id"]],
        );
        $result = $this->orderCommonTakeDelivery($take_condition);
        if($result["code"] < 0){
            return $result;
        }
        //核销发送通知
        $message_model = new Message();
        $message_model->sendMessage([ 'keywords' => "VERIFY", 'order_id' => $order_info['order_id'] ]);
        return $result;


    }
    /**
     * 订单提货
     * @param unknown $order_id
     * @param unknown $delivery_code
     */
    public function orderTakeDelivery($order_id)
    {
        $res = model('order_goods')->update(['delivery_status' => 1, 'delivery_status_name' => "已发货"], ['order_id' => $order_id]);
        return $this->success($res);
    }

    /**
     * 退款完成操作
     * @param $order_info
     */
    public function refund($order_goods_info){
        //是否入库
        if($order_goods_info["is_refund_stock"] == 1){
            $goods_stock_model = new GoodsStock();
            $item_param = array(
                "sku_id" => $order_goods_info["sku_id"],
                "num" => $order_goods_info["num"],
            );
            //返还库存
            $goods_stock_model->incStock($item_param);
        }
    }
    /**
     * 订单详情
     * @param $order_info
     */
    public function orderDetail($order_info){
        return [];
    }
}