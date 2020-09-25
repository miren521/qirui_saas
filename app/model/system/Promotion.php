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

namespace app\model\system;

use app\model\BaseModel;
/**
 * 活动整体管理
 */
class Promotion extends BaseModel
{
    /**
     * 获取营销活动展示
     */
    public function getPromotions()
    {
        $show = event("ShowPromotion", []);
        $admin_promotion = [];
        $shop_promotion = [];
        foreach ($show as $k => $v)
        {
            if(!empty($v['admin']))
            {
                $admin_promotion = array_merge($admin_promotion, $v['admin']);
            }
            if(!empty($v['shop']))
            {
                $shop_promotion = array_merge($shop_promotion, $v['shop']);
            }
        }
        return [
            'admin' => $admin_promotion,
            'shop'  => $shop_promotion
        ];
    }

    /**
     * 获取营销类型
     */
    public function getPromotionType(){
        $promotion_type = event("PromotionType");
        $promotion_type[] = ["type" => "empty", "name" => "无营销活动"];
        return $promotion_type;
    }
}