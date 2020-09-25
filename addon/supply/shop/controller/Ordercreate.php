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


namespace addon\supply\shop\controller;

use addon\supply\model\order\OrderCreate as OrderCreateModel;
use app\model\system\Address;

/**
 * 订单创建
 * @author Administrator
 *
 */
class Ordercreate extends BaseSupplyshop
{
    public function __construct()
    {
        parent::__construct();
        $check_login_result = $this->checkLogin();
        if($check_login_result['code'] < 0){
            echo json_encode($check_login_result);
            exit();
        }
    }

    /**
     * 创建订单
     */
    public function create()
    {
        $order_create  = new OrderCreateModel();
        $buyer_message = input("buyer_message", '');
        $buyer_message = !empty($buyer_message) ? json_decode($buyer_message, true) : [];
        $delivery      = input("delivery", '');
        $delivery      = !empty($delivery) ? json_decode($delivery, true) : [];
//        $member_address = input("member_address", '');
//        $member_address = !empty($delivery) ? json_decode($member_address, true) : [];
        $data = [
            'cart_ids'        => input('cart_ids', ''),
            'sku_id'          => input('sku_id', ''),
            'num'             => input('num', ''),
            'buyer_shop_id'   => $this->site_id,
            'buyer_shop_name' => $this->shop_info['site_name'],
            'is_balance'      => input('is_balance', 0),//是否使用余额
            'order_from'      => 'pc',
            'order_from_name' => "PC",
            'pay_password'    => input('pay_password', ''),//支付密码
            'buyer_message'   => $buyer_message,
            'delivery'        => $delivery,
            'buyer_uid'       => $this->uid
//            'member_address' => $member_address,
        ];
        if (empty($data['cart_ids']) && empty($data['sku_id'])) {
            return $order_create->error('', '缺少必填参数商品数据');
        }
        $res = $order_create->create($data);
        return $res;
    }

    /**
     * 计算信息
     * @return array|void
     */
    public function calculate()
    {
        $order_create  = new OrderCreateModel();
        $buyer_message = input("buyer_message", '');
        $buyer_message = !empty($buyer_message) ? json_decode($buyer_message, true) : [];
        $delivery      = input("delivery", '');
        $delivery      = !empty($delivery) ? json_decode($delivery, true) : [];
//        $member_address = input("member_address", '');
//        $member_address = !empty($delivery) ? json_decode($member_address, true) : [];
        $data = array(
            'cart_ids'        => input('cart_ids', ''),
            'sku_id'          => input('sku_id', ''),
            'num'             => input('num', ''),
            'buyer_shop_id'   => $this->site_id,
            'is_balance'      => input('is_balance', 0),//是否使用余额
            'order_from'      => 'pc',
            'order_from_name' => "PC",
            'pay_password'    => input('pay_password', ''),//支付密码
            'buyer_message'   => $buyer_message,
            'delivery'        => $delivery,
//            'member_address' => $member_address,
        );
        if (empty($data['cart_ids']) && empty($data['sku_id'])) {
            return $order_create->error('', '缺少必填参数商品数据');
        }
        $res = $order_create->calculate($data);
        return $this->success($res);
    }

    /**
     * 待支付订单 数据初始化
     * @return array|mixed
     */
    public function payment()
    {
        $cart_ids = input('cart_ids', '');
        $sku_id   = input('sku_id', '');
        $num      = input('num', 0);
        if (request()->isAjax()) {
            $order_create  = new OrderCreateModel();
            $buyer_message = input("buyer_message", '');
            $buyer_message = !empty($buyer_message) ? json_decode($buyer_message, true) : [];
            $delivery      = input("delivery", '');
            $delivery      = !empty($delivery) ? json_decode($delivery, true) : [];
//            $member_address = input("member_address", '');
//            $member_address = !empty($delivery) ? json_decode($member_address, true) : [];
            $data = [
                'cart_ids'        => $cart_ids,
                'sku_id'          => $sku_id,
                'num'             => $num,
                'buyer_shop_id'   => $this->site_id,
                'is_balance'      => input('is_balance', 0),//是否使用余额
                'order_from'      => 'pc',
                'order_from_name' => "PC",
                'pay_password'    => input('pay_password', ''),//支付密码
                'buyer_message'   => $buyer_message,
                'delivery'        => $delivery,
//                'member_address' => $member_address,
            ];
            if (empty($data['cart_ids']) && empty($data['sku_id'])) {
                return $order_create->error('', '缺少必填参数商品数据');
            }
            $res = $order_create->orderPayment($data);
            return $order_create->success($res);
        } else {
            $this->assign('cart_ids', $cart_ids);
            $this->assign('sku_id', $sku_id);
            $this->assign('num', $num);

            //查询省级数据列表
            $address_model = new Address();
            $list          = $address_model->getAreaList([["pid", "=", 0], ["level", "=", 1]]);
            $this->assign("province_list", $list["data"]);
            return $this->fetch("ordercreate/payment", [], $this->replace);
        }
    }
}
