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


namespace addon\coupon\model;

use app\model\BaseModel;
/**
 * 优惠券
 */
class MemberCoupon extends BaseModel
{

    /**
     * 获取会员已领取优惠券优惠券
     * @param array $member_id
     */
    public function getMemberCouponList($member_id, $state, $site_id = 0, $order = "fetch_time desc"){
        $condition = array(
            ["member_id", "=", $member_id],
            ["state", "=", $state],
        );
        if($site_id > 0){
            $condition[] = ["site_id", "=", $site_id];
        }
        $list = model("promotion_coupon")->getList($condition, "*", $order, '', '', '', 0);
        return $this->success($list);
    }

    /**
     * 使用优惠券
     * @param $coupon_id
     */
    public function useMemberCoupon($coupon_id, $member_id, $order_id = 0){
        //优惠券处理方案
        $result = model('promotion_coupon')->update(['use_order_id' => $order_id, 'state' => 2, 'use_time' => time()], [['coupon_id', '=', $coupon_id], ["member_id", "=", $member_id], ['state', '=', 1]]);
        if($result === false){
            return $this->error();
        }
        return $this->success();
    }

    
    /**
     * 获取会员已领取优惠券优惠券数量
     * @param unknown $member_id
     * @param unknown $state
     * @param number $site_id
     * @return multitype:number unknown
     */
    public function getMemberCouponNum($member_id, $state, $site_id = 0)
    {
        $condition = array(
            [ "member_id", "=", $member_id ],
            [ "state", "=", $state ],
        );
        if ($site_id > 0) {
            $condition[] = [ "site_id", "=", $site_id ];
        }
        $num = model("promotion_coupon")->getCount($condition);
        return $this->success($num);
    }
    
    /**
     * 会员是否可领取该优惠券
     */
    public function receivedNum($coupon_type_id, $member_id){
        $received_num = model('promotion_coupon')->getCount([ ['coupon_type_id', '=', $coupon_type_id], ['member_id', '=', $member_id] ]);
        return $this->success($received_num);
    }
}