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


namespace addon\supply\model;

use app\model\BaseModel;

/**
 * 订单计算与结算
 */
class SupplyOrderCalc extends BaseModel
{
    /**
     * 供货商订单计算(支付完成后计算)
     * @param $order_id
     * @return array
     */
    public function calculate($order_id)
    {
        $order_info           = model("supply_order")->getInfo(
            [['order_id', '=', $order_id]],
            'goods_money, delivery_money, adjust_money'
        );
        $order_goods_list     = model("supply_order_goods")->getList(
            [['order_id', '=', $order_id]],
            'order_goods_id, goods_money, commission_rate,real_goods_money'
        );
        $money_total   = 0;
        $platform_money_total = 0;

        foreach ($order_goods_list as $k => $v) {
            //实际总商品金额
            $goods_money    = $v['real_goods_money'];
            $platform_money = $goods_money * $v['commission_rate'] / 100;
            $supply_money   = $goods_money - $platform_money;

            $data = [
                'supply_money'   => $supply_money,
                'platform_money' => $platform_money
            ];
            model("supply_order_goods")->update($data, [['order_goods_id', '=', $v['order_goods_id']]]);
            $money_total   += $supply_money;
            $platform_money_total += $platform_money;
        }
        model("supply_order")->update(
            ['supply_money' => $money_total + $order_info['delivery_money'], 'platform_money' => $platform_money_total],
            [['order_id', '=', $order_id]]
        );
        return $this->success();
    }


    /**
     * 订单退款金额累加计算
     * @param $order_goods_info
     * @return array
     */
    public function refundCalculate($order_goods_info)
    {
        $order_id       = $order_goods_info["order_id"];
        $order_goods_id = $order_goods_info["order_goods_id"];
        //订单项信息
        $order_goods_info = model("supply_order_goods")->getInfo(
            [['order_goods_id', '=', $order_goods_id]],
            'refund_real_money, platform_money,supply_money,commission_rate'
        );
        if (empty($order_goods_info)) {
            return $this->error([], "ORDER_GOODS_EMPTY");
        }

        //订单信息
        $order_info = model("supply_order")->getInfo(
            [['order_id', '=', $order_id]],
            'supply_money, platform_money, refund_money, refund_supply_money, refund_platform_money, commission'
        );
        if (empty($order_info)) {
            return $this->error([], "ORDER_EMPTY");
        }


        $refund_money          = $order_info["refund_money"];//订单总退款
        $refund_supply_money   = $order_info["refund_supply_money"];//订单退款 供货商金额
        $refund_platform_money = $order_info["refund_platform_money"];//订单退款  平台金额

        $item_refund_money          = $order_goods_info["refund_real_money"];
        $item_refund_platform_money = $order_goods_info["platform_money"];//订单项平台退款金额
        $item_refund_supply_money   = $item_refund_money - $item_refund_platform_money;//订单项平台退款金额

        $refund_supply_money   += $item_refund_supply_money;
        $refund_platform_money += $item_refund_platform_money;
        $refund_money          += $item_refund_money;
        $order_data            = array(
            "refund_money"          => $refund_money,
            "refund_supply_money"   => $refund_supply_money,
            "refund_platform_money" => $refund_platform_money,
        );
        $result                = model("supply_order")->update($order_data, [['order_id', '=', $order_id]]);

        return $this->success();
    }

    /**
     * 整体计算订单
     * @param $out_trade_no
     * @return array
     */
    public function orderCalculate($out_trade_no)
    {
        $order_list = model("supply_order")->getList([['out_trade_no', '=', $out_trade_no]], 'order_id');
        foreach ($order_list as $k => $v) {
            $this->calculate($v['order_id']);
        }
        return $this->success();
    }
}
