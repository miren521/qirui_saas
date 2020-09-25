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


namespace addon\city\model;

use app\model\BaseModel;
use app\model\web\WebSite;
use app\model\system\Config;
use app\model\system\Cron;
use Carbon\Carbon;


class CitySettlementPeriod extends BaseModel
{

    /**
     * 获取账单结算周期信息
     * @param array $condition
     * @param string $field
     * @return array
     */
    public function getCitySettlementPeriodInfo($condition = [], $field='*')
    {
        $res = model('website_settlement_period')->getInfo($condition,$field);
        return $this->success($res);
    }

    /**
     * 获取结算分页列表
     * @param array $condition
     * @param number $page
     * @param string $page_size
     * @param string $order
     * @param string $field
     */
    public function getCitySettlementPeriodPageList($condition = [], $page = 1, $page_size = PAGE_LIST_ROWS, $order = '', $field = '*')
    {

        $list = model('website_settlement_period')->pageList($condition, $field, $order, $page, $page_size);
        return $this->success($list);
    }

    /**
     * 分站结算数据统计
     * @param $condition
     * @return array
     */
    public function getCitySettlementPeriodSum()
    {
        $res = [];
        $res['shop_commission'] = model('website_settlement_period')->getSum([],'shop_commission');
        $res['order_commission'] = model('website_settlement_period')->getSum([],'order_commission');
        if(empty($res['shop_commission']) || $res['shop_commission'] == null){
            $res['shop_commission'] = '0.00';
        }else{
            $res['shop_commission'] = number_format($res['shop_commission'],2,'.' , '');
        }
        if(empty( $res['order_commission']) ||  $res['order_commission'] == null){
            $res['order_commission'] = '0.00';
        }else{
            $res['order_commission'] = number_format($res['order_commission'],2,'.' , '');
        }
        return $this->success($res);
    }

}