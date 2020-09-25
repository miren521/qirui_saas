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


namespace addon\supply\model;

use app\model\BaseModel;
use app\model\web\Account;
use app\model\web\WebSite;
use think\facade\Cache;

/**
 * 订单计算与结算
 */
class SupplySettlement extends BaseModel
{
    /**
     * 平台整体对供货商进行结算
     * @param int $end_time 截至时间
     */
    public function supplySettlement($end_time)

    {
        //结算周期初始化
        //开始记录时间
        $last_period = model('supply_settlement_period')->getFirstData([], 'period_start_time, period_end_time', 'period_id desc');
        if (!empty($last_period)) {
            $start_time = $last_period[ 'period_end_time' ];
        } else {
            $start_time = 0;
        }

        if ($end_time - $start_time < 3600 * 6) {
            return $this->success();
        }

        $period_no = date('YmdHi') . rand(1000, 9999);
        $period_id = model("supply_settlement_period")->add(["period_no" => $period_no, "create_time" => time(), "period_start_time" => $start_time, "period_end_time" => $end_time]);
        $supply_num = 0;
        $order_money = 0;
        $supply_money = 0;
        $platform_money = 0;
        $refund_money = 0;
        $refund_supply_money = 0;
        $refund_platform_money = 0;
        $commission = 0;
        $website_commission = 0;
        //供货商列表统计
        $supply_list = model("supplier")->getList([['status', '=', 1]], 'supplier_site_id, title, website_id');
        //循环各个供货商数据
        foreach ($supply_list as $k => $site_item) {
            $site_id = $site_item[ 'supplier_site_id' ] ?? 0;
            $website_id = $site_item[ 'website_id' ] ?? 0;

            $settlement = model("supply_order")->getInfo([['order_status', '=', 10], ['is_settlement', '=', 0], ['site_id', '=', $site_id], ['finish_time', '<=', $end_time]], 'sum(order_money) as order_money, sum(refund_money) as refund_money, sum(supply_money) as supply_money, sum(platform_money) as platform_money, sum(refund_supply_money) as refund_supply_money, sum(refund_platform_money) as refund_platform_money, sum(commission) as commission');

            if (empty($settlement) || $settlement[ 'order_money' ] == null) {
                //注意总支出佣金要再订单完成后统计到订单
                $settlement = [
                    'order_money' => 0,
                    'refund_money' => 0,
                    'supply_money' => 0,
                    'platform_money' => 0,
                    'refund_supply_money' => 0,
                    'refund_platform_money' => 0,
                    'commission' => 0,
                ];
            }

            $settlement[ 'settlement_no' ] = date('YmdHi') . $site_id . rand(1111, 9999);
            $settlement[ 'site_id' ] = $site_id;
            $settlement[ 'site_name' ] = $site_item[ 'title' ];
            $settlement[ 'period_id' ] = $period_id;
            $settlement[ 'period_start_time' ] = $start_time;
            $settlement[ 'period_end_time' ] = $end_time;
            if (addon_is_exit("city")) {
                if ($site_item[ 'website_id' ] > 0) {
                    //处理
                    $settlement[ 'website_id' ] = $website_id;
                    //查看站点信息
                    $website_model = new WebSite();
                    $website = $website_model->getWebSite([['site_id', '=', $website_id]], 'site_area_name,order_rate');
                    $website_info = $website[ 'data' ];
                    //计算分站分成
                    if ($settlement[ 'platform_money' ] > 0) {
                        $settlement[ 'website_commission' ] = floor($settlement[ 'platform_money' ] * $website_info[ 'order_rate' ]) / 100;
                    } else {
                        $settlement[ 'website_commission' ] = 0;
                    }
                }
            }
            $settlement[ 'website_commission' ] = isset($settlement[ 'website_commission' ]) ? $settlement[ 'website_commission' ] : 0;
            $settlement_id = model("supply_settlement")->add($settlement);
            model("supply_order")->update(['is_settlement' => 1, "settlement_id" => $settlement_id], [['order_status', '=', 10], ['is_settlement', '=', 0], ['site_id', '=', $site_id], ['finish_time', '<=', $end_time]]);
            $supply_account = new SupplyAccount();
            //这里的备注还需要完善
            $supply_account->addSupplyAccount($site_id, 'account', $settlement[ 'supply_money' ] - $settlement[ 'refund_supply_money' ] - $settlement[ 'commission' ], "order", $settlement_id, '供应商结算，账单编号' . $settlement[ 'settlement_no' ]);
            //平台也要进行统计
            $supply_num = $supply_num + 1;
            $order_money = $order_money + $settlement[ 'order_money' ];
            $supply_money = $supply_money + $settlement[ 'supply_money' ];
            $platform_money = $platform_money + $settlement[ 'platform_money' ];
            $refund_money = $refund_money + $settlement[ 'refund_money' ];
            $refund_supply_money = $refund_supply_money + $settlement[ 'refund_supply_money' ];
            $refund_platform_money = $refund_platform_money + $settlement[ 'refund_platform_money' ];
            $commission = $commission + $settlement[ 'commission' ];
            $website_commission = $website_commission + $settlement[ 'website_commission' ];
        }
        $total_data = [
            'supply_num' => $supply_num,
            'order_money' => $order_money,
            'refund_money' => $refund_money,
            'supply_money' => $supply_money,
            'platform_money' => $platform_money,
            'refund_supply_money' => $refund_supply_money,
            'refund_platform_money' => $refund_platform_money,
            'commission' => $commission,
            'website_commission' => $website_commission,
        ];
        //清空供应商账户缓存记录
        Cache::tag("supply")->clear();
        model("supply_settlement_period")->update($total_data, [['period_id', '=', $period_id]]);
        $account = new Account();
        $account->addAccount(0, 'account', $total_data[ 'platform_money' ] - $total_data[ 'refund_platform_money' ], "order", $period_id, '供应商订单结算，账单编号:' . $period_no);
        return $this->success();

    }

    /**
     * 获取供货商结算周期结算信息
     * @param $condition
     * @param string $field
     * @return array
     */
    public function getSupplySettlementInfo($condition, $field = '*')
    {
        $res = model('supply_settlement')->getInfo($condition, $field);
        return $this->success($res);
    }

    /**
     * 获取供货商结算周期结算列表
     * @param array $condition
     * @param string $field
     * @param string $order
     * @param string $limit
     */
    public function getSupplySettlementList($condition = [], $field = '*', $order = '', $limit = null)
    {

        $list = model('supply_settlement')->getList($condition, $field, $order, '', '', '', $limit);
        return $this->success($list);
    }

    /**
     * 获取供货商结算周期结算分页列表
     * @param array $condition
     * @param number $page
     * @param string $page_size
     * @param string $order
     * @param string $field
     */
    public function getSupplySettlementPageList($condition = [], $page = 1, $page_size = PAGE_LIST_ROWS, $order = '', $field = '*')
    {

        $list = model('supply_settlement')->pageList($condition, $field, $order, $page, $page_size);
        return $this->success($list);
    }

    /**
     * 获取供货商结算周期信息
     * @param $condition
     * @param string $field
     * @return array
     */
    public function getSupplySettlementPeriodInfo($condition, $field = '*')
    {
        $res = model('supply_settlement_period')->getInfo($condition, $field);
        return $this->success($res);
    }

    /**
     * 获取供货商结算周期结算列表
     * @param array $condition
     * @param string $field
     * @param string $order
     * @param string $limit
     */
    public function getSupplySettlementPeriodList($condition = [], $field = '*', $order = '', $limit = null)
    {

        $list = model('supply_settlement_period')->getList($condition, $field, $order, '', '', '', $limit);
        return $this->success($list);
    }

    /**
     * 获取供货商结算周期结算分页列表
     * @param array $condition
     * @param number $page
     * @param string $page_size
     * @param string $order
     * @param string $field
     */
    public function getSupplySettlementPeriodPageList($condition = [], $page = 1, $page_size = PAGE_LIST_ROWS, $order = '', $field = '*')
    {

        $list = model('supply_settlement_period')->pageList($condition, $field, $order, $page, $page_size);
        return $this->success($list);
    }

    /**
     * 获取供货商待结算订单金额
     */
    public function getWaitSettlementInfo($site_id)
    {
        $money_info = model("supply_order")->getInfo([['site_id', '=', $site_id], ['order_status', '=', 10], ['is_settlement', '=', 0]], 'sum(order_money) as order_money, sum(refund_money) as refund_money, sum(supply_money) as supply_money, sum(platform_money) as platform_money, sum(refund_supply_money) as refund_supply_money, sum(refund_platform_money) as refund_platform_money, sum(commission) as commission');
        if (empty($money_info) || $money_info == null) {

            $money_info = [
                'order_money' => 0,
                'refund_money' => 0,
                'supply_money' => 0,
                'platform_money' => 0,
                'refund_supply_money' => 0,
                'refund_platform_money' => 0,
                'commission' => 0,
            ];

        }

        return $money_info;
    }

    /**
     * 获取供货商待结算订单金额
     */
    public function getSupplySettlementData($condition = [])
    {
        $money_info = model("supply_order")->getInfo($condition, 'sum(order_money) as order_money, sum(refund_money) as refund_money, sum(supply_money) as supply_money, sum(platform_money) as platform_money, sum(refund_supply_money) as refund_supply_money, sum(refund_platform_money) as refund_platform_money, sum(commission) as commission');
        if (empty($money_info) || $money_info == null) {

            $money_info = [
                'order_money' => 0,
                'refund_money' => 0,
                'supply_money' => 0,
                'platform_money' => 0,
                'refund_supply_money' => 0,
                'refund_platform_money' => 0,
                'commission' => 0,
            ];

        }

        return $money_info;
    }

}