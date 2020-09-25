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

namespace addon\store\model;

use app\model\BaseModel;
use app\model\system\Config;
use app\model\system\Cron;
use Carbon\Carbon;

class StoreAccount extends BaseModel
{
    public $period_types = [1, 2, 3];//转账周期类型1.天  2. 周  3. 月

    public $from_type = [
        'order' => [
            'type_name' => '门店结算',
            'type_url' => '',
        ],
        'withdraw' => [
            'type_name' => '提现',
            'type_url' => '',
        ],
    ];

    /**
     * 获取门店转账设置
     */
    public function getStoreWithdrawConfig($site_id)
    {
        $config = new Config();
        $res = $config->getConfig([['site_id', '=',  $site_id], ['app_module', '=', 'shop'], ['config_key', '=', 'STORE_WITHDRAW']]);
        if(empty($res['data']['value']))
        {
            //默认数据管理
            $res['data']['value'] = [
                'period_type' => 3,           //转账周期类型1.天  2. 周  3. 月
            ];
        }
        return $res;
    }

    /**
     * 修改门店转账设置
     */
    public function setStoreWithdrawConfig($site_id, $data)
    {
        $config = new Config();
        $res = $config->setConfig($data, '门店转账设置', 1, [['site_id', '=',  $site_id], ['app_module', '=', 'shop'], ['config_key', '=', 'STORE_WITHDRAW']]);

        $cron = new Cron();
        switch($data['period_type'])
        {
            case 1://天
                $date = strtotime(date('Y-m-d 00:00:00'));
                $execute_time = strtotime('+1day',$date);
                break;
            case 2://周
                $execute_time = Carbon::parse('next monday')->timestamp;
                break;
            case 3://月
                $execute_time = Carbon::now()->addMonth()->firstOfMonth()->timestamp;
                break;
        }
        $cron->deleteCron([['event', '=', 'StoreWithdrawPeriodCalc']]);
        $cron->addCron('2','1','门店周期结算','StoreWithdrawPeriodCalc', $execute_time, $site_id, $data['period_type']);
        return $res;
    }

    /**
     * 获取门店待结算订单金额
     */
    public function getWaitSettlementInfo($store_id)
    {
        $money_info = model("order")->getInfo([
            ['delivery_store_id', '=', $store_id],
            ['order_status', '=', 10],
            ['store_settlement_id', '=', 0]
        ], 'sum(order_money) as order_money, sum(refund_money) as refund_money, sum(shop_money) as shop_money, sum(platform_money) as platform_money, sum(refund_shop_money) as refund_shop_money, sum(refund_platform_money) as refund_platform_money, sum(commission) as commission');
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
}