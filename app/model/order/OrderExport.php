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

use app\model\BaseModel;

/**
 * 订单导出
 * @author Administrator
 */

class OrderExport extends BaseModel
{
    public $order_field = [
        'order_no' => '订单编号',
        'site_name' => '店铺名称',
        'order_name' => '订单内容',
        'order_from_name' => '订单来源',
        'order_type_name' => '订单类型',
        'order_promotion_name' => '营销活动类型',
        'out_trade_no' => '支付流水号',
        'out_trade_no_2' => '支付流水号（多次支付）',
        'delivery_code' => '整体提货编码',
        'order_status_name' => '订单状态',
        'pay_status' => '支付状态',
        'delivery_status' => '配送状态',
        'refund_status' => '退款状态',
        'pay_type_name' => '支付方式',
        'delivery_type_name' => '配送方式',
        'name' => '客户姓名',
        'mobile' => '客户手机',
        'telephone' => '客户固定电话',
        'full_address' => '客户地址',
        'buyer_ip' => '客户ip',
        'buyer_ask_delivery_time' => '客户要求配送时间',
        'buyer_message' => '客户留言信息',
        'goods_money' => '商品总金额',
        'delivery_money' => '配送费用',
        'promotion_money' => '订单优惠金额',
        'coupon_money' => '优惠券金额',
        'order_money' => '订单合计金额',
        'adjust_money' => '订单调整金额',
        'balance_money' => '余额支付金额',
        'pay_money' => '抵扣之后应付金额',
        'refund_money' => '订单退款金额',
        'pay_time' => '支付时间',
        'delivery_time' => '配送时间',
        'sign_time' => '签收时间',
        'finish_time' => '完成时间',
        'remark' => '卖家留言',
        'goods_num' => '商品件数',
        'delivery_status_name' => '发货状态',
        'shop_money' => '店铺金额',
        'platform_money' => '平台金额',
        'is_settlement' => '是否进行结算',
        'delivery_store_name' => '门店名称',
        'promotion_type_name' => '营销类型'
    ];

    //订单商品信息
    public $order_goods_field = [
        'sku_name' => '商品名称',
        'sku_no' => '商品编码',
        'goods_class_name' => '商品类型',
        'price' => '商品卖价',
        'cost_price' => '成本价',
        'num' => '购买数量',
        'goods_money' => '商品总价',
        'cost_money' => '成本总价',
        'delivery_status_name' => '配送状态',
        'delivery_no' => '配送单号',
        'refund_no' => '退款编号',
        'refund_type' => '退货方式',
        'refund_apply_money' => '退款申请金额',
        'refund_reason' => '退款原因',
        'refund_real_money' => '实际退款金额',
        'refund_delivery_name' => '退款公司名称',
        'refund_delivery_no' => '退款单号',
        'refund_time' => '实际退款时间',
        'refund_refuse_reason' => '退款拒绝原因',
        'refund_action_time' => '申请退款时间',
        'commission_rate' => '待结算佣金比率',
        'real_goods_money' => '实际商品购买价',
        'shop_money' => '店铺实际金额',
        'platform_money' => '平台实际金额',
        'refund_remark' => '退款说明',
        'refund_delivery_remark' => '买家退货说明',
        'refund_address' => '退货地址',
        'is_refund_stock' => '是否返还库存'
    ];


    public $define_data = [
        'pay_status' => ['type' => 2, 'data' => ['未支付', '已支付']],//支付状态
        'delivery_status' => ['type' => 2, 'data' => ['待发货', '已发货','已收货']],//配送状态
        'refund_status' => ['type' => 2, 'data' => ['未退款', '已退款']],//退款状态
        'buyer_ask_delivery_time' => ['type' => 1],//购买人要求配送时间
        'pay_time' => ['type' => 1],//支付时间
        'delivery_time' => ['type' => 1],//订单配送时间
        'sign_time' => ['type' => 1],//订单签收时间
        'finish_time' => ['type' => 1],//订单完成时间
        'refund_time' => ['type' => 1],//退款到账时间
        'refund_action_time' => ['type' => 1],//实际退款时间
        'is_settlement' => ['type' => 2, 'data' => ['否', '是']],//是否进行结算
        'refund_type' => ['type' => 2, 'data' => [1 => '仅退款', 2 => '退款退货']],//退货方式
        'is_refund_stock' => ['type' => 2, 'data' => ['否', '是']],//是否返还库存
    ];


    /**
     *  数据处理
     * @param $data
     * @param $field
     * @return array
     */
    public function handleData($data, $field)
    {
        $define_data = $this->define_data;
        foreach ($data as $k => $v) {
            //获取键
            $keys = array_keys($v);

            foreach ($keys as $key) {

                if (in_array($key, $field)) {

                    if (array_key_exists($key, $define_data)) {

                        $type = $define_data[$key]['type'];

                        switch ($type) {

                            case 1:
                                $data[$k][$key] = time_to_date($v[$key]);
                                break;
                            case 2:
                                $define_data_data = $define_data[$key]['data'];
                                $data[$k][$key] = !empty($v[$key]) ? $define_data_data[$v[$key]] : '';
                        }

                    }
                }
            }

        }
        return $data;
    }

}