<?php
// +---------------------------------------------------------------------+
// | NiuCloud | [ WE CAN DO IT JUST NiuCloud ]                |
// +---------------------------------------------------------------------+
// | Copy right 2019-2029 www.niucloud.com                          |
// +---------------------------------------------------------------------+
// | Author | NiuCloud <niucloud@outlook.com>                       |
// +---------------------------------------------------------------------+
// | Repository | https://github.com/niucloud/framework.git          |
// +---------------------------------------------------------------------+

namespace addon\pointexchange\model;

use addon\coupon\model\Coupon;
use addon\gift\model\Gift;
use addon\gift\model\GiftOrder;
use addon\platformcoupon\model\Platformcoupon;
use app\model\member\MemberAccount;
use app\model\BaseModel;
use app\model\system\Pay;

/**
 * 积分兑换订单
 */
 
class Order extends BaseModel
{
	
    /**
     * 支付订单
     * @param unknown $out_trade_no
     */
    public function orderPay($data)
    {
        $out_trade_no = $data["out_trade_no"];
        $order_info = model("promotion_exchange_order")->getInfo([ [ 'out_trade_no', '=', $out_trade_no ] ], '*');
        if(empty($order_info)){
            return $this->error([], "找不到可支付的订单");
        }
        if($order_info["order_status"] == 1){
            return $this->error([], "当前订单已支付");
        }
        model("promotion_exchange_order")->startTrans();
        //循环生成多个订单
        try{
            $order_data = array(
                "order_status" => 1,
                "pay_time" => time()
            );
            $res = model("promotion_exchange_order")->update($order_data, [["order_id", "=", $order_info["order_id"]]]);
            switch ($order_info['type']) {
                case 1://礼品
                    $gift_model = new GiftOrder();
                    $gift_data = array(
                        "gift_id" => $order_info["type_id"],
                        "gift_name" => $order_info["exchange_name"],
                        "gift_image" => $order_info["exchange_image"],
                        "num" => $order_info["num"],
                        "remark" => $order_info["buyer_message"],
                        "member_id" => $order_info["member_id"],
                        "member_name" => $order_info["name"],
                        "province_id" => $order_info["province_id"],
                        "city_id" => $order_info["city_id"],
                        "mobile" => $order_info["mobile"],
                        "district_id" => $order_info["district_id"],
                        "full_address" => $order_info["full_address"],
                        "create_time" => time(),
                        'get_type_name' => '积分兑换'
                    );
                    $res = $gift_model->addOrder($gift_data, 1);
                    break;
                case 2://优惠券
                    $coupon_model = new Platformcoupon();
                    $res = $coupon_model->receivePlatformcoupon($order_info["type_id"], $order_info["member_id"], 1, 1, 0);
                    break;
                case 3://红包
                    $member_account_model = new MemberAccount();
                    $res = $member_account_model->addMemberAccount($order_info["member_id"], "balance", $order_info["balance"], "order", "积分兑换,","积分兑换,获得红包:".$order_info["balance"]);
                    break;
            }
            model("promotion_exchange_order")->commit();
            return $this->success();

        }catch(\Exception $e)
        {
            model("promotion_exchange_order")->rollback();
            return $this->error('', $e->getMessage());
        }
    }
    
    /**
     * 关闭订单
     * @param unknown $order_id
     */
    public function closeOrder($condition)
    {
        $order_info = model("promotion_exchange_order")->getInfo($condition, '*');
        if(empty($order_info))
            return $this->error();

        if($order_info["order_status"] != 0)
            return $this->error();

        model("promotion_exchange_order")->startTrans();
        //循环生成多个订单
        try{
            $data = array(
                "order_status" => -1,
            );
            $result = model("promotion_exchange_order")->update($data, [["order_id", '=',$order_info["order_id"]]]);
            //返还积分
            $member_account_model = new MemberAccount();
            $member_account_result = $member_account_model->addMemberAccount($order_info["member_id"], "point", $order_info["point"], "refund", "积分兑换", "积分兑换关闭,返还积分:" . $order_info["point"]);
            if ($member_account_result["code"] < 0) {
                model("promotion_exchange_order")->rollback();
                return $member_account_result;
            }


            //返回优惠券库存
            //判断库存
            switch($order_info["type"]){//兑换类型
                case "1"://礼品
                    $gift_model = new Gift();
                    $result = $gift_model->incStock(["gift_id" => $order_info["type_id"], "num" => $order_info["num"]]);
                    break;
                case "2"://优惠券
                    $coupon_model = new Platformcoupon();
                    $result = $coupon_model->incStock(["platformcoupon_type_id" => $order_info["type_id"], "num" => $order_info["num"]]);
                    break;

            }

            if($order_info['type'] == 1 && $order_info['price'] > 0){
                //关闭支付
                $pay_model = new Pay();
                $result = $pay_model->deletePay($order_info['out_trade_no']);//关闭旧支付单据
                if ($result["code"] < 0) {
                    model("promotion_exchange_order")->rollback();
                    return $result;
                }
            }
            model("promotion_exchange_order")->commit();
            return $this->success();

        }catch(\Exception $e)
        {
            model("promotion_exchange_order")->rollback();
            return $this->error('', $e->getMessage().$e->getFile().$e->getLine());
        }
    }
    
    /**
     * 获取积分兑换订单信息
     * @param unknown $id
     */
    public function getOrderInfo($condition, $field = '*')
    {

        $res = model('promotion_exchange_order')->getInfo($condition, $field);
        return $this->success($res);
    }
    
    /**
     * 获取订单列表
     * @param unknown $condition
     * @param string $field
     * @param string $order
     * @param string $limit
     * @return multitype:string
     */
    public function getOrderList($condition = [], $field = '*', $order = '', $limit = null)
    {
        $list = model('promotion_exchange_order')->getList($condition, $field, $order, '', '', '', $limit);
        return $this->success($list);
    }
    
    /**
     * 获取积分兑换订单分页列表
     * @param unknown $condition
     * @param number $page
     * @param string $page_size
     * @param string $order
     * @param string $field
     */
    public function getExchangePageList($condition = [], $page = 1, $page_size = PAGE_LIST_ROWS, $order = '', $field = '*')
    {
        $list = model('promotion_exchange_order')->pageList($condition, $field, $order, $page, $page_size);
        return $this->success($list);
    }
}