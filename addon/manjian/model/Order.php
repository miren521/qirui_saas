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


namespace addon\manjian\model;

use addon\coupon\model\Coupon;
use app\model\BaseModel;
use app\model\order\Order as BaseOrder;
use think\Exception;

class Order extends BaseModel
{
    /**
     * 订单完成发放满减送所送积分
     * @param $order_id
     */
    public function orderComplete($order_id){
        //判断订单是否完成
        $order_info = model('order')->getInfo([ ['order_id', '=', $order_id], ['order_status', '=', BaseOrder::ORDER_COMPLETE] ], 'site_id,order_id,member_id');
        if (!empty($order_info)) {
            $mansong_record = model('order_promotion_detail')->getList([ ['order_id', '=', $order_id] ], '*');
            if (!empty($mansong_record)) {
                model('order_promotion_detail')->startTrans();
                foreach ($mansong_record as $item) {
                    try {
                        $item_data = [];
                        $array = empty($item['json']) ? [] : json_decode($item['json'], true);
                        $status_data = [];
                        $status_array = empty($item['status_json']) ? [] : json_decode($item['status_json'], true);
                        $coupon_status = $status_array['coupon'] ?? 0;

                        $coupon = $array['coupon'] ?? 0;
                        // 必须没法放过优惠券  并且当前有优惠券奖励
                        if ($coupon_status == 0 && $coupon > 0) {
                            $coupon_model = new Coupon();
                            //赠完即止,所以不考虑没有发放的情况
                            $receive_res = $coupon_model->receiveCoupon($coupon, $order_info['member_id'], 3, 0, 0);

                            $status_data['coupon'] = 1;
                        }
                        // 变更发放状态
                        if(!empty($status_data)){
                            $item_data['status_json'] = json_encode($status_data);
                            model('order_promotion_detail')->update($item_data, [ ['id', '=', $item['id'] ] ]);
                        }

                        model('order_promotion_detail')->commit();
                        return $this->success();
                    } catch (\Exception $e) {
                        model('order_promotion_detail')->rollback();
                        return $this->error([], $e->getMessage().$e->getFile().$e->getLine());
                    }
                }
            }
        }
        return $this->success();
    }
}