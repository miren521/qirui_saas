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


namespace addon\memberrecharge\model;


use app\model\BaseModel;
use addon\coupon\model\CouponType;
use think\facade\Cache;

/**
 * 订单
 */
class MemberrechargeOrder extends BaseModel
{

    /**
     * 基础支付方式(不考虑实际在线支付方式或者货到付款方式)
     * @var unknown
     */
    public $pay_type = [

    ];

    /**
     * 获取支付方式
     * @return unknown
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
     * 订单详情
     * @param array $condition
     * @param string $field
     * @return array
     */
    public function getMemberRechargeOrderInfo($condition = [], $field = '*')
    {
        $order = model('member_recharge_order')->getInfo($condition, $field);
        if ($order) {
            //获取优惠券信息
            if ($order['coupon_id']) {
                //优惠券字段
                $coupon_field = 'coupon_type_id,coupon_name,money,count,lead_count,max_fetch,at_least,end_time,image,validity_type,fixed_term';

                $model = new CouponType();
                $coupon = $model->getCouponTypeList([['coupon_type_id', 'in', $order['coupon_id']]], $coupon_field);
                $order['coupon_list'] = $coupon;
            }

        }

        Cache::tag("member_recharge_order")->clear();
        return $this->success($order);
    }
    
    /**
     * 订单列表
     * @param array $condition
     * @param int $page
     * @param int $page_size
     * @param string $order
     * @param string $field
     * @return array
     */
    public function getMemberRechargeOrderPageList($condition = [], $page = 1, $page_size = PAGE_LIST_ROWS, $order = '', $field = '*')
    {
        $list = model('member_recharge_order')->pageList($condition, $field, $order, $page, $page_size);
        Cache::tag("member_recharge_order")->clear();
        return $this->success($list);
    }

    /**
     * 支付回调
     * @param $data
     * @return array|\multitype
     */
    public function orderPay($data)
    {
        $order_field = 'order_id,recharge_id,recharge_name,order_no,cover_img,face_value,buy_price,point,growth,coupon_id,price,pay_type,status,create_time,pay_time,member_id,member_img,nickname';
        $order = $this->getMemberRechargeOrderInfo([['out_trade_no', '=', $data['out_trade_no']]], $order_field);
        $order_info = $order['data'];
        if ($order_info['status'] == 1) {
            model('member_recharge_order')->startTrans();
            try {

                $pay_list = $this->getPayType();

                $pay_type_name = '';
                if (!empty($data['pay_type'])) {
                    $pay_type_name = $pay_list[$data['pay_type']];
                }

                //修改订单状态
                $order_data = [
                    'pay_type' => $data['pay_type'],
                    'pay_type_name' => $pay_type_name,
                    'pay_time' => time(),
                    'price' => $order_info['buy_price'],
                    'status' => 2
                ];
                $res = model('member_recharge_order')->update($order_data, [['out_trade_no', '=', $data['out_trade_no']]]);

                //添加开卡记录
                $card_model = new MemberrechargeCard();
                $order_info['use_status'] = 2;
                $order_info['use_time'] = time();
                $card_model->addMemberRechargeCard($order_info);

                //发放礼包
                $card_model->addMemberAccount($order_info);

                //获取套餐信息
                $recharge_model = new Memberrecharge();
                $recharge_info = $recharge_model->getMemberRechargeInfo([['recharge_id','=',$order_info['recharge_id']]],'sale_num,coupon_id');
                //增加发放数
                $sale_num = $recharge_info['data']['sale_num'] + 1;
                $recharge_model->editMemberRecharge([['recharge_id','=',$order_info['recharge_id']]],['sale_num'=>$sale_num]);

                model('member_recharge_order')->commit();
                return $this->success($res);
            } catch (\Exception $e) {

                model('member_recharge_order')->rollback();
                return $this->error('', $e->getMessage());
            }
        } else {
            return $this->success(true);
        }

    }

    /**
     * 定时关闭订单
     * @param $order_id
     * @return array
     */
    public function cronMemberRechargeOrderClose($order_id)
    {
        //获取订单信息
        $order = $this->getMemberRechargeOrderInfo([['order_id', '=', $order_id]], 'status');
        $order_info = $order['data'];
        if (empty($order_info)) {
            $res = true;
        } else {
            if ($order_info['status'] == 1) {
                //删除订单
                $res = model('member_recharge_order')->delete([['order_id', '=', $order_id]]);
                Cache::tag("member_recharge_order")->clear();
            } else {
                $res = true;
            }
        }
        return $this->success($res);
    }
}