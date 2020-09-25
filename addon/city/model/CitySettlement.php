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
use think\facade\Cache;


class CitySettlement extends BaseModel
{

    /**
     * 获取商家转账设置
     */
    public function getCitySettlementConfig()
    {
        $config = new Config();
        $res = $config->getConfig([['site_id', '=',  0], ['app_module', '=', 'admin'], ['config_key', '=', 'CITY_SETTLEMENT_CONFIG']]);
        if(empty($res['data']['value']))
        {
            //默认数据管理
            $res['data']['value'] = [
                'period_type' => 0,           //转账周期类型1.天  2. 周  3. 月
            ];
        }
        return $res;
    }

    /**
     * 设置商家转账设置
     */
    public function setCitySettlementConfig($data)
    {
        $config = new Config();
        $res = $config->setConfig($data, '分站周期结算配置', 1, [['site_id', '=',  0], ['app_module', '=', 'admin'], ['config_key', '=', 'CITY_SETTLEMENT_CONFIG']]);

        $cron = new Cron();
        switch($data['period_type'])
        {
            case 1://天

                $date = strtotime(date('Y-m-d 03:30:00'));
                $execute_time = strtotime('+1day',$date);
                break;
            case 2://周

                $execute_time = Carbon::parse('next monday')->timestamp + 30*60;
                break;
            case 3://月

                $execute_time = Carbon::now()->addMonth()->firstOfMonth()->timestamp + 30*60;
                break;
        }
        $cron->deleteCron([ [ 'event', '=', 'WebsiteSettlement' ] ]);
        $cron->addCron('2','1','分站周期结算','WebsiteSettlement',$execute_time,'0',$data['period_type']);
        return $res;
    }


    /**
     * 城市分站周期结算
     */
    public function citySettlement($end_time)
    {
        //查询最后一条结算周期信息
    	$last_period = model('website_settlement_period')->getFirstData([],'period_start_time, period_end_time', 'period_id desc');
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
		$website_model = new WebSite();
		$period_id = model("website_settlement_period")->add(["create_time" => time(), "period_start_time" => $start_time, "period_end_time" => $end_time]);
		$website_num = 0;
		$order_commission = 0;
		$shop_commission = 0;
		
		//店铺列表统计
		$website_list = model("website")->getList([['site_id', '<>', 0]], 'site_id, site_area_name');
		
		foreach ($website_list as $k => $v) {
		    
		        $order_settlement = model("shop_settlement")->getInfo([['settlement_id', '=', 0], ['website_id', '=', $v['site_id']]], 'sum(website_commission) as website_commission');
		        
		        if(empty($order_settlement) || $order_settlement['website_commission'] == null)
		        {
		            $order_settlement = [
                        'website_commission' => 0
		            ];
		        
		        }
		        
		        $shop_open_settlement = model("shop_open_account")->getInfo([['settlement_id', '=', 0], ['website_id', '=', $v['site_id']]], 'sum(website_commission) as website_commission');
		        
		        if(empty($shop_open_settlement) || $shop_open_settlement['website_commission'] == null)
		        {
		            $shop_open_settlement = [
		                'website_commission' => 0
		            ];
		        
		        }
		        
		        $tag = 1001 + $k;  
		        $website_data = [
		            'settlement_no' => date('Ymd') . $tag,
		            'period_id' => $period_id,
		            'website_id' => $v['site_id'],
		            'website_name' => $v['site_area_name'],
		            'order_commission' => $order_settlement['website_commission'],
		            'shop_commission' => $shop_open_settlement['website_commission'],
		            'create_time' => time(),
		            'period_start_time' => $start_time,
		            'period_end_time' => $end_time,
		        ];
		        $settlement_id = model('website_settlement')->add($website_data);
		        //分站订单结算金额自增
		        model('website')->setInc([['site_id', '=', $v['site_id']]], 'account_order', $order_settlement['website_commission']);
		        //分站结算店铺金额自增
		        model('website')->setInc([['site_id', '=', $v['site_id']]], 'account_shop', $shop_open_settlement['website_commission']);
		        //添加分站账户流水记录
		        $website_model->addWebsiteAccount($v['site_id'], 'account', $order_settlement['website_commission'], "order", $settlement_id, '分站结算，账单编号' . $website_data['settlement_no']);
		        //添加分站账户流水记录
		        $website_model->addWebsiteAccount($v['site_id'], 'account', $shop_open_settlement['website_commission'], "shop", $settlement_id, '分站结算，账单编号' . $website_data['settlement_no']);
		        model("shop_settlement")->update(['settlement_id' => $settlement_id],[['settlement_id', '=', 0], ['website_id', '=', $v['site_id']]]);
		        model("shop_open_account")->update(['settlement_id' => $settlement_id],[['settlement_id', '=', 0], ['website_id', '=', $v['site_id']]]);
		        
		        $website_num =$website_num + 1;
		        $order_commission = $order_commission + $order_settlement['website_commission'];
		        $shop_commission = $shop_commission + $shop_open_settlement['website_commission'];
		}
		
		$total_data = [
		    'settlement_no' => date('YmdHi'),
		    'website_num' => $website_num,
		    'order_commission' => $order_commission,
		    'shop_commission'  => $shop_commission,
		];
		model("website_settlement_period")->update($total_data, [['period_id', '=', $period_id]]);
        Cache::tag("website")->clear();
        return $this->success();
    }


    /**
     * 获取结算分页列表
     * @param array $condition
     * @param number $page
     * @param string $page_size
     * @param string $order
     * @param string $field
     */
    public function getCitySettlementPageList($condition = [], $page = 1, $page_size = PAGE_LIST_ROWS, $order = '', $field = '*')
    {

        $list = model('website_settlement')->pageList($condition, $field, $order, $page, $page_size);
        return $this->success($list);
    }

    /**
     * 分站结算数据统计
     * @param $condition
     * @return array
     */
    public function getCitySettlementSum()
    {
        $res = [];
        $res['shop_commission'] = model('website_settlement')->getSum([],'shop_commission');
        $res['order_commission'] = model('website_settlement')->getSum([],'order_commission');
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