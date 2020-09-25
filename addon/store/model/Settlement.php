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


namespace addon\store\model;

use app\model\BaseModel;
use app\model\web\Account;
/**
 * 门店订单与结算
 */
class Settlement extends BaseModel
{

    /**
     * 店铺门店结算
     * @param unknown $site_id
     * @param unknown $end_time
     * @return multitype:
     */
    public function settlement($site_id, $end_time)
    {
        //获取最近的店铺结算时间
		$last_time = model("shop")->getInfo([['site_id', '=', $site_id]], 'store_settlement_time');
        $start_time = $last_time['store_settlement_time'];
		if($end_time - $start_time < 3600 * 20)
		{
		    return $this->success();
		}

		//店铺列表
		$store_list = model('store')->getList([['site_id', '=', $site_id]], 'site_id, site_name, store_id, store_name');
		model('store_settlement')->startTrans();
		try {
            //循环各个店铺数据
            foreach ($store_list as $k => $store)
            {
                $online_settlement = model('order')->getInfo([
                    ['order_status', '=', 10],
                    ['delivery_store_id', '=', $store['store_id']],
                    ['site_id', '=', $store['site_id']],
                    ['finish_time', '<=', $end_time],
                    ['store_settlement_id', '=', 0],
                    ['pay_type', '<>', 'OFFLINE_PAY']
                ], 'sum(order_money) as order_money, sum(refund_money) as refund_money, sum(shop_money) as shop_money, sum(platform_money) as platform_money, sum(refund_shop_money) as refund_shop_money, sum(refund_platform_money) as refund_platform_money, sum(commission) as commission');

                $offline_settlement = model('order')->getInfo([
                    ['order_status', '=', 10],
                    ['is_settlement', '=', 1],
                    ['delivery_store_id', '=', $store['store_id']],
                    ['site_id', '=', $store['site_id']],
                    ['finish_time', '<=', $end_time],
                    ['store_settlement_id', '=', 0],
                    ['pay_type', '=', 'OFFLINE_PAY']
                ], 'sum(order_money) as offline_order_money, sum(refund_money) as offline_refund_money');

                $settlement = [
                    'settlement_no' => date('YmdHi') . $store['store_id'] . rand(1111,9999),
                    'site_id' => $store['site_id'],
                    'site_name' => $store['site_name'],
                    'store_id' => $store['store_id'],
                    'store_name' => $store['store_name'],
                    'order_money' => !empty($online_settlement['order_money']) ? $online_settlement['order_money'] : 0,
                    'refund_money'  => !empty($online_settlement['refund_money']) ? $online_settlement['refund_money'] : 0,
                    'shop_money'    => !empty($online_settlement['shop_money']) ? $online_settlement['shop_money'] : 0,
                    'platform_money'=> !empty($online_settlement['platform_money']) ? $online_settlement['platform_money'] : 0,
                    'refund_shop_money' => !empty($online_settlement['refund_shop_money']) ? $online_settlement['refund_shop_money'] : 0,
                    'refund_platform_money' => !empty($online_settlement['refund_platform_money']) ? $online_settlement['refund_platform_money'] : 0,
                    'commission' => !empty($online_settlement['commission']) ? $online_settlement['commission'] : 0,
                    'offline_order_money' => !empty($offline_settlement['offline_order_money']) ? $offline_settlement['offline_order_money'] : 0,
                    'offline_refund_money' => !empty($offline_settlement['offline_refund_money']) ? $offline_settlement['offline_refund_money'] : 0,
                    'create_time' => $end_time,
                    'start_time' => $start_time,
                    'end_time' => $end_time
                ];
                $store_settlement_id = model("store_settlement")->add($settlement);
                model('order')->update(['store_settlement_id' => $store_settlement_id], [
                    ['order_status', '=', 10],
                    ['delivery_store_id', '=', $store['store_id']],
                    ['site_id', '=', $store['site_id']],
                    ['store_settlement_id', '=', 0],
                    ['finish_time', '<=', $end_time],
                ]);
            }
            model('shop')->update(['store_settlement_time' => $end_time], [['site_id', '=', $site_id]]);
            model('store_settlement')->commit();
            return $this->success();
        } catch (\Exception $e) {
            model('store_settlement')->rollback();
            return $this->error($e->getMessage());
        }
    }


    /**
     * getSettlementInfo 获取详情
     * @param $condition
     * @param string $fields
     * @return array
     */
    public function getSettlementInfo($condition, $fields = '*') {
        $res = model('store_settlement')->getInfo($condition, $fields);
        return $this->success($res);
    }

    /**
     * 修改结算记录
     * @param
     */
    public function editSettlement($data, $condition) {
        $res = model('store_settlement')->update($data, $condition);
        return $this->success($res);
    }

    /**
     * 获取店铺待结算订单金额
     */
    public function getStoreSettlementData($condition=[])
    {
        $money_info = model("order")->getInfo($condition, 'sum(order_money) as order_money, sum(refund_money) as refund_money, sum(shop_money) as shop_money, sum(platform_money) as platform_money, sum(refund_shop_money) as refund_shop_money, sum(refund_platform_money) as refund_platform_money, sum(commission) as commission');
        if(empty($money_info) || $money_info == null)
        {

            $money_info = [
                'order_money' => 0,
                'refund_money'  => 0,
                'shop_money'    => 0,
                'platform_money'=> 0,
                'refund_shop_money' => 0,
                'refund_platform_money' => 0,
                'commission' => 0
            ];

        }

        return $money_info;
    }

    /**
     * 获取店铺结算周期结算分页列表
     * @param array $condition
     * @param number $page
     * @param string $page_size
     * @param string $order
     * @param string $field
     */
    public function getStoreSettlementPageList($condition = [], $page = 1, $page_size = PAGE_LIST_ROWS, $order = '', $field = '*')
    {

        $list = model('store_settlement')->pageList($condition, $field, $order, $page, $page_size);
        return $this->success($list);
    }

}