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

declare (strict_types = 1);

namespace addon\platformcoupon\event;



use addon\platformcoupon\model\Platformcoupon;
use app\model\order\OrderCommon;

/**
 * 判断使用优惠券的订单是否全部被关闭,如果全部被关闭,则返还优惠券
 */
class OrderClose
{

	public function handle($params=[])
	{
	    $order_common_model = new OrderCommon();
	    $order_condition = array(
	        ['order_id', '=', $params['order_id']]
          );
          $order_info_result = $order_common_model->getOrderInfo($order_condition, 'platform_coupon_id,member_id');
            $order_info = $order_info_result['data'];
            $platform_coupon_id = $order_info['platform_coupon_id'];
            $coupon_order_condition = array(
                ['platform_coupon_id', '=', $platform_coupon_id],
                ['order_status', '<>', -1]
            );
            $count_result = $order_common_model->getOrderCount($coupon_order_condition);
            //如果订单全部被关闭,则返还平台优惠券
            if($count_result['data'] == 0){
                $platform_coupon_model = new Platformcoupon();
                $result = $platform_coupon_model->refundPlatformcoupon($platform_coupon_id, $order_info['member_id']);
                if($result['code'] < 0){
                    return $result;
                }
            }
            return '';
	}
}