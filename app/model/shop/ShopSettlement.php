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


namespace app\model\shop;

use app\model\BaseModel;
use app\model\web\Account;
use app\model\web\WebSite;

/**
 * 订单计算与结算
 */
class ShopSettlement extends BaseModel
{
    /**
     * 平台整体对店铺进行结算
     * @param int $end_time 截至时间
     */
    public function shopSettlement($end_time)

    {
		//结算周期初始化
		//开始记录时间

		$last_period = model('shop_settlement_period')->getFirstData([],'period_start_time, period_end_time', 'period_id desc');
		if(!empty($last_period))
		{
		    $start_time = $last_period['period_end_time'];
		}else{
		    $start_time = 0;
		}
		if($end_time - $start_time < 3600 * 6)
		{
		    return $this->success();
		}

		$period_no = date('YmdHi').rand(1000,9999);
		$period_id = model("shop_settlement_period")->add(["period_no"=>$period_no,"create_time" => time(), "period_start_time" => $start_time, "period_end_time" => $end_time]);
		$shop_num = 0;
		$order_money = 0;
		$shop_money = 0;
		$platform_money = 0;
		$refund_money = 0;
		$refund_shop_money = 0;
		$refund_platform_money = 0;
		$commission = 0;
		$website_commission = 0;
		$platform_coupon_total_money = 0;
		//店铺列表统计
		$shop_list = model("shop")->getList([['shop_status', '=', 1]], 'site_id, site_name, website_id');
		//循环各个店铺数据
		foreach ($shop_list as $k => $shop)
		{

			$settlement = model("order")->getInfo([['order_status', '=', 10],['is_settlement', '=', 0], ['site_id', '=', $shop['site_id'], ['finish_time', '<=', $end_time]]], 'sum(order_money) as order_money, sum(refund_money) as refund_money, sum(shop_money) as shop_money, sum(platform_money) as platform_money, sum(refund_shop_money) as refund_shop_money, sum(refund_platform_money) as refund_platform_money, sum(commission) as commission, sum(platform_coupon_money) as platform_coupon_money');

			if(empty($settlement) || $settlement['order_money'] == null)
			{
				//注意总支出佣金要再订单完成后统计到订单
				$settlement = [
					'order_money' => 0,
					'refund_money'  => 0,
					'shop_money'    => 0,
					'platform_money'=> 0,
					'refund_shop_money' => 0,
					'refund_platform_money' => 0,
					'commission' => 0,
                    'platform_coupon_money' => 0
				];
				
			}

            //平台优惠券
            $refund_rate = $settlement['order_money'] > 0 ? $settlement['refund_money']/$settlement['order_money'] : 0;
            $platform_coupon_money = $settlement['platform_coupon_money']*(1-$refund_rate);//平台优惠券平台部分
            $settlement['platform_coupon_money'] = $platform_coupon_money;

			$settlement['settlement_no'] = date('YmdHi').$shop['site_id'].rand(1111,9999);
			$settlement['site_id'] = $shop['site_id'];
			$settlement['site_name'] = $shop['site_name'];
			$settlement['period_id'] = $period_id;
			$settlement['period_start_time'] = $start_time;
			$settlement['period_end_time'] = $end_time;
			if(addon_is_exit("city"))
			{
			    if($shop['website_id'] > 0)
			    {
			        //处理
                    $settlement['website_id'] = $shop['website_id'];
                    //查看站点信息
                    $website_model = new WebSite();
                    $website = $website_model->getWebSite([['site_id','=',$shop['website_id']]], 'site_area_name,order_rate');
                    $website_info = $website['data'];
                    //计算分站分成
                    if($settlement['platform_money'] > 0){
                        $settlement['website_commission'] = floor($settlement['platform_money']*$website_info['order_rate'])/100;
                    }else{
                        $settlement['website_commission'] = 0;
                    }

			    }
			}
            $settlement['website_commission'] = isset($settlement['website_commission']) ? $settlement['website_commission'] : 0;
			$settlement_id = model("shop_settlement")->add($settlement);
			model("order")->update(['is_settlement' => 1, "settlement_id" => $settlement_id], [['order_status', '=', 10],['is_settlement', '=', 0], ['site_id', '=', $shop['site_id']], ['finish_time', '<=', $end_time]]);
			$shop_account = new ShopAccount();
			//这里的备注还需要完善
			$shop_account->addShopAccount($shop['site_id'],'account', $settlement['shop_money'] + $settlement['platform_coupon_money'] - $settlement['refund_shop_money']-$settlement['commission'], "order", $settlement_id, '店铺结算，账单编号'.$settlement['settlement_no']);
			//平台也要进行统计
			$shop_num = $shop_num + 1;
			$order_money = $order_money + $settlement['order_money'];
			$shop_money = $shop_money + $settlement['shop_money'];
			$platform_money = $platform_money + $settlement['platform_money'];
			$refund_money = $refund_money + $settlement['refund_money'];
			$refund_shop_money = $refund_shop_money + $settlement['refund_shop_money'];
			$refund_platform_money = $refund_platform_money + $settlement['refund_platform_money'];
			$commission = $commission + $settlement['commission'];
            $website_commission = $website_commission + $settlement['website_commission'];
            $platform_coupon_total_money += $settlement['platform_coupon_money'];
		}
		$total_data = [
			'shop_num' => $shop_num,
			'order_money' => $order_money,
			'refund_money'  => $refund_money,
			'shop_money'    => $shop_money,
			'platform_money'=> $platform_money,
			'refund_shop_money' => $refund_shop_money,
			'refund_platform_money' => $refund_platform_money,
			'commission' => $commission,
            'website_commission' => $website_commission,
            'platform_coupon_money' => $platform_coupon_total_money
		];
		model("shop_settlement_period")->update($total_data, [['period_id', '=', $period_id]]);
		$account = new Account();
		$account->addAccount(0, 'account', $total_data['platform_money']-$total_data['refund_platform_money'], "order", $period_id, '订单结算，账单编号:'.$period_no);
		return $this->success();
    }
    /**
     * 获取店铺结算周期结算信息
     * @param unknown $condition
     * @param string $field
     */
    public function getShopSettlementInfo($condition, $field = '*')
    {
    	$res = model('shop_settlement')->getInfo( $condition, $field);
    	return $this->success($res);
    }
    /**
     * 获取店铺结算周期结算列表
     * @param array $condition
     * @param string $field
     * @param string $order
     * @param string $limit
     */
    public function getShopSettlementList($condition = [], $field = '*', $order = '', $limit = null)
    {
    
    	$list = model('shop_settlement')->getList($condition, $field, $order, '', '', '', $limit);
    	return $this->success($list);
    }
    
    /**
     * 获取店铺结算周期结算分页列表
     * @param array $condition
     * @param number $page
     * @param string $page_size
     * @param string $order
     * @param string $field
     */
    public function getShopSettlementPageList($condition = [], $page = 1, $page_size = PAGE_LIST_ROWS, $order = '', $field = '*')
    {
    
    	$list = model('shop_settlement')->pageList($condition, $field, $order, $page, $page_size);
    	return $this->success($list);
    }
    
    /**
     * 获取店铺结算周期信息
     * @param unknown $condition
     * @param string $field
     */
    public function getShopSettlementPeriodInfo($condition, $field = '*')
    {
    	$res = model('shop_settlement_period')->getInfo( $condition, $field);
    	return $this->success($res);
    }
    /**
     * 获取店铺结算周期结算列表
     * @param array $condition
     * @param string $field
     * @param string $order
     * @param string $limit
     */
    public function getShopSettlementPeriodList($condition = [], $field = '*', $order = '', $limit = null)
    {
    
    	$list = model('shop_settlement_period')->getList($condition, $field, $order, '', '', '', $limit);
    	return $this->success($list);
    }
    
    /**
     * 获取店铺结算周期结算分页列表
     * @param array $condition
     * @param number $page
     * @param string $page_size
     * @param string $order
     * @param string $field
     */
    public function getShopSettlementPeriodPageList($condition = [], $page = 1, $page_size = PAGE_LIST_ROWS, $order = '', $field = '*')
    {
    
    	$list = model('shop_settlement_period')->pageList($condition, $field, $order, $page, $page_size);
    	return $this->success($list);
    }
    
    /**
     * 获取店铺待结算订单金额
     */
    public function getWaitSettlementInfo($site_id)
    {
        $money_info = model("order")->getInfo([['site_id', '=', $site_id], ['order_status', '=', 10],['is_settlement', '=', 0]], 'sum(order_money) as order_money, sum(refund_money) as refund_money, sum(shop_money) as shop_money, sum(platform_money) as platform_money, sum(refund_shop_money) as refund_shop_money, sum(refund_platform_money) as refund_platform_money, sum(commission) as commission, sum(platform_coupon_money) as platform_coupon_money');
        if(empty($money_info) || $money_info == null)
        {
            
            $money_info = [
                'order_money' => 0,
                'refund_money'  => 0,
                'shop_money'    => 0,
                'platform_money'=> 0,
                'refund_shop_money' => 0,
                'refund_platform_money' => 0,
                'commission' => 0,
                'platform_coupon_money' => 0
            ];
            
        }
        
        return $money_info;
    }

    /**
     * 获取店铺待结算订单金额
     */
    public function getShopSettlementData($condition=[])
    {
        $money_info = model("order")->getInfo($condition, 'sum(order_money) as order_money, sum(refund_money) as refund_money, sum(shop_money) as shop_money, sum(platform_money) as platform_money, sum(refund_shop_money) as refund_shop_money, sum(refund_platform_money) as refund_platform_money, sum(commission) as commission, sum(platform_coupon_money) as platform_coupon_money');
        if(empty($money_info) || $money_info == null)
        {

            $money_info = [
                'order_money' => 0,
                'refund_money'  => 0,
                'shop_money'    => 0,
                'platform_money'=> 0,
                'refund_shop_money' => 0,
                'refund_platform_money' => 0,
                'commission' => 0,
                'platform_coupon_money' => 0
            ];

        }

        return $money_info;
    }

}