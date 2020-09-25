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


namespace addon\supply\supply\controller;

use addon\supply\model\Supplier;
use addon\supply\model\SupplyAccount;

class Supplywithdraw extends BaseSupply
{


    public function __construct()
    {
        //执行父类构造函数
        parent::__construct();
    }

    /**
     * 申请提现
     */
    public function apply()
    {
        if (request()->isAjax()) {
            $money = input("apply_money", "");
            $supply_account_model = new SupplyAccount();
            $result = $supply_account_model->applyWithdraw($this->supply_id, $money);
            return $result;
        }
    }

    /**
     * 获取提现记录
     */
    public function lists()
    {
        if (request()->isAjax()) {
            $account_model = new SupplyAccount();
            $page = input('page', 1);
            $page_size = input('page_size', PAGE_LIST_ROWS);
            $status = input('status', '');
            $condition = [
                ['site_id', '=', $this->supply_id]
            ];
            if (!empty($status)) {
                if ($status == 3) {//待审核
                    $condition[] = ['status', '=', 0];
                } else {
                    $condition[] = ['status', '=', $status];
                }
            }
            $start_time = input('start_time', '');
            $end_time = input('end_time', '');
            if (!empty($start_time) && empty($end_time)) {
                $condition[] = ['apply_time', '>=', $start_time];
            } elseif (empty($start_time) && !empty($end_time)) {
                $condition[] = ['apply_time', '<=', $end_time];
            } elseif (!empty($start_time) && !empty($end_time)) {
                $condition[] = ['apply_time', 'between', [$start_time, $end_time]];
            }
            $order = "id desc";
            $list = $account_model->getSupplyWithdrawPageList($condition, $page, $page_size, $order);
            return $list;
        }else{
            $supplier_model = new Supplier();
            //获取供应商的账户信息
            $condition = array(
                ["supplier_site_id", "=", $this->supply_id]
            );
            $supply_info_result = $supplier_model->getSupplierInfo($condition, 'account, account_withdraw,account_withdraw_apply,open_fee,bond');
            $supply_info = $supply_info_result[ "data" ];
            //已提现
            $this->assign('account_withdraw', number_format($supply_info[ 'account_withdraw' ], 2, '.', ''));
            //提现中
            $this->assign('account_withdraw_apply', number_format($supply_info[ 'account_withdraw_apply' ], 2, '.', ''));
            return $this->fetch('supplywithdraw/lists');
        }
    }

}