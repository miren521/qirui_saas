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


namespace addon\supply\admin\controller;

use addon\supply\model\order\OrderCommon;
use addon\supply\model\SupplySettlement as SupplySettlementModel;
use app\admin\controller\BaseAdmin;
use addon\supply\model\SupplyAccount as SupplyAccountModel;
use addon\supply\model\Supplier as SupplierModel;

/**
 * 供应商结算
 * @author Administrator
 *
 */
class Supplysettlement extends BaseAdmin
{

    /**
     * 供应商结算列表
     */
    public function lists()
    {
        if (request()->isAjax()) {
            $page       = input('page', 1);
            $page_size  = input('page_size', PAGE_LIST_ROWS);
            $start_date = input('start_date', '');
            $end_date   = input('end_date', '');
            $condition  = [];
            if ($start_date != '' && $end_date != '') {
                $condition[] = ['period_start_time', '>=', strtotime($start_date)];
                $condition[] = ['period_end_time', '<=', strtotime($end_date)];
            } elseif ($start_date != '' && $end_date == '') {
                $condition[] = ['period_start_time', '>=', strtotime($start_date)];
            } elseif ($start_date == '' && $end_date != '') {
                $condition[] = ['period_end_time', '<=', strtotime($end_date)];
            }
            $order                   = 'create_time desc';
            $supply_settlement_model = new SupplySettlementModel();
            $res = $supply_settlement_model->getSupplySettlementPeriodPageList($condition, $page, $page_size, $order);
            if (!empty($res['data']['list'])) {
                foreach ($res['data']['list'] as $k => $v) {
                    $supply_money_actual = $v['supply_money'] - $v['refund_supply_money'] - $v['commission'];
                    $res['data']['list'][$k]['real_supply_money'] = number_format($supply_money_actual, 2, '.', '');

                    $money_actual                          = $v['platform_money'] - $v['refund_platform_money'];
                    $res['data']['list'][$k]['real_money'] = number_format($money_actual, 2, '.', '');
                }
            }
            return $res;
        } else {
            $supply_account_model = new SupplyAccountModel();
            $data  = $supply_account_model->getSupplySettlementSum()['data'] ?? [];
            $settlement_sum = $data['supply_money'] - $data['refund_supply_money'] - $data['commission'];
            $this->assign('supply_settlement', number_format($settlement_sum, 2, '.', ''));
            $this->assign('platform_money', number_format($data['platform_money'], 2, '.', ''));
            //todo  city抽成

            return $this->fetch("supplysettlement/lists");
        }
    }

    /**
     * 供应商结算详情(显示供应商结算供应商列表)
     */
    public function detail()
    {
        $supply_settlement_model = new SupplySettlementModel();
        if (request()->isAjax()) {
            $period_id   = input('period_id', '');
            $page        = input('page', 1);
            $page_size   = input('page_size', PAGE_LIST_ROWS);
            $search_text = input('search_text', '');
            $start_date  = input('start_date', '');
            $end_date    = input('end_date', '');
            $condition   = [];

            if ($search_text) {
                $condition[] = ['site_name', 'like', '%' . $search_text . '%'];
            }
            if ($start_date != '' && $end_date != '') {
                $condition[] = ['create_time', 'between', [strtotime($start_date), strtotime($end_date)]];
            } elseif ($start_date != '' && $end_date == '') {
                $condition[] = ['create_time', '>=', strtotime($start_date)];
            } elseif ($start_date == '' && $end_date != '') {
                $condition[] = ['create_time', '<=', strtotime($end_date)];
            }
            $condition[] = ['period_id', '=', $period_id];
            $order       = 'id desc';

            $res = $supply_settlement_model->getSupplySettlementPageList($condition, $page, $page_size, $order);
            if (!empty($res['data']['list'])) {
                foreach ($res['data']['list'] as $k => $v) {
                    $shop_money_actual = $v['supply_money'] - $v['refund_supply_money'] - $v['commission'];
                    $res['data']['list'][$k]['real_supply_money'] = number_format($shop_money_actual, 2, '.', '');

                    $money_actual                          = $v['platform_money'] - $v['refund_platform_money'];
                    $res['data']['list'][$k]['real_money'] = number_format($money_actual, 2, '.', '');
                }
            }
            return $res;
        } else {
            $period_id = input('period_id', 0);
            $this->assign("period_id", $period_id);

            $supply_settlement_period_info = $supply_settlement_model
                ->getSupplySettlementPeriodInfo(['period_id' => $period_id], '*');

            $period = $supply_settlement_period_info['data'];

            //供应商收入
            $supply_money = $period['supply_money'] - $period['refund_supply_money'] - $period['commission'];
            $this->assign('supply_money', number_format($supply_money, 2, '.', ''));
            //平台收入
            $money = $period['platform_money'] - $period['refund_platform_money'];
            $this->assign('money', number_format($money, 2, '.', ''));
            //todo  分站抽成

            $this->assign("info", $period);
            return $this->fetch("supplysettlement/detail");
        }
    }

    /**
     * 供应商订单列表
     */
    public function supply()
    {
        if (request()->isAjax()) {
            $settlement_id = input('settlement_id', '');
            $page          = input('page', 1);
            $page_size     = input('page_size', PAGE_LIST_ROWS);
            $start_date    = input('start_date', '');
            $end_date      = input('end_date', '');
            $condition     = [];

            if ($start_date != '' && $end_date != '') {
                $condition[] = ['finish_time', 'between', [strtotime($start_date), strtotime($end_date)]];
            } elseif ($start_date != '' && $end_date == '') {
                $condition[] = ['finish_time', '>=', strtotime($start_date)];
            } elseif ($start_date == '' && $end_date != '') {
                $condition[] = ['finish_time', '<=', strtotime($end_date)];
            }
            $condition[]        = ['settlement_id', '=', $settlement_id];
            $order              = 'finish_time desc';
            $order_common_model = new OrderCommon();

            return $order_common_model->getOrderPageList($condition, $page, $page_size, $order);
        } else {
            $id = input('settlement_id', 0);
            $this->assign("settlement_id", $id);

            $supply_settlement_model       = new SupplySettlementModel();
            $supply_settlement_result      = $supply_settlement_model->getSupplySettlementInfo(['id' => $id], '*');
            $period = $supply_settlement_result['data'] ?? [];

            //供应商名称
            $supplier_model = new SupplierModel();
            $supply_info    = $supplier_model->getSupplierInfo(
                [['supplier_site_id', '=', $period['site_id']]],
                'title'
            );
            $this->assign('supply_info', $supply_info['data']);
            //供应商收入
            $supply_money = $period['supply_money'] - $period['refund_supply_money'] - $period['commission'];
            $this->assign('supply_money', number_format($supply_money, 2, '.', ''));
            //平台收入
            $money = $period['platform_money'] - $period['refund_platform_money'];
            $this->assign('money', number_format($money, 2, '.', ''));
            $this->assign("info", $period);
            return $this->fetch("supplysettlement/supply");
        }
    }
}
