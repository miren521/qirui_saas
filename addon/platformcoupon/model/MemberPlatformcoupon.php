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


namespace addon\platformcoupon\model;

use app\model\BaseModel;
/**
 * 优惠券
 */
class MemberPlatformcoupon extends BaseModel
{

    /**
     * 获取会员已领取优惠券优惠券
     * @param array $member_id
     */
    public function getMemberPlatformcouponList($member_id, $state, $order = "fetch_time desc"){
        $condition = array(
            ["member_id", "=", $member_id],
            ["state", "=", $state],
        );

        $list = model("promotion_platformcoupon")->getList($condition, "*", $order, '', '', '', 0);
        return $this->success($list);
    }

    /**
     * 使用优惠券
     * @param $platformcoupon_id
     */
    public function useMemberPlatformcoupon($platformcoupon_id, $member_id, $order_id = 0){
        //优惠券处理方案
        $result = model('promotion_platformcoupon')->update(['use_order_id' => $order_id, 'state' => 2, 'use_time' => time()], [['platformcoupon_id', '=', $platformcoupon_id], ["member_id", "=", $member_id], ['state', '=', 1]]);
        if($result === false){
            return $this->error();
        }
        return $this->success();
    }

    
    /**
     * 获取会员已领取优惠券优惠券数量
     * @param unknown $member_id
     * @param unknown $state
     * @return multitype:number unknown
     */
    public function getMemberPlatformcouponNum($member_id, $state)
    {
        $condition = array(
            [ "member_id", "=", $member_id ],
            [ "state", "=", $state ],
        );

        $num = model("promotion_platformcoupon")->getCount($condition);
        return $this->success($num);
    }
    
    /**
     * 会员是否可领取该优惠券
     */
    public function receivedNum($platformcoupon_type_id, $member_id){
        $received_num = model('promotion_platformcoupon')->getCount([ ['platformcoupon_type_id', '=', $platformcoupon_type_id], ['member_id', '=', $member_id] ]);
        return $this->success($received_num);
    }
}