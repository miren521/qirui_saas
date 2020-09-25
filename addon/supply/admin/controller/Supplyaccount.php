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

use addon\supply\model\SupplyOpenAccount;
use addon\supply\shop\controller\Supply;
use app\admin\controller\BaseAdmin;
use addon\supply\model\Supplier as SupplierModel;
use addon\supply\model\SupplyAccount as SupplyAccountModel;

/**
 * 供应商财务相关记录
 */
class Supplyaccount extends BaseAdmin
{

    /**
     * 供应商开店费用
     **/
    public function openaccount()
    {
        if (request()->isAjax()) {
            $page      = input('page', 1);
            $page_size = input('page_size', PAGE_LIST_ROWS);
            $site_name = input('site_name', '');

            $start_time = input("start_time", '');
            $end_time   = input("end_time", '');

            $condition = [];
            if (!empty($site_name)) {
                $condition[] = ['site_name', 'like', '%' . $site_name . '%'];
            }

            if (!empty($start_time) && empty($end_time)) {
                $condition[] = ['create_time', '>=', date_to_time($start_time)];
            } elseif (empty($start_time) && !empty($end_time)) {
                $condition[] = ["create_time", "<=", date_to_time($end_time)];
            } elseif (!empty($start_time) && !empty($end_time)) {
                $condition[] = ['create_time', 'between', [date_to_time($start_time), date_to_time($end_time)]];
            }

            $supply_open_model = new SupplyOpenAccount();
            $list              = $supply_open_model->getSupplyOpenAccountPageList($condition, $page, $page_size, 'id desc');
            return $list;

        } else {
            $supply_account_model = new SupplyAccountModel();
            //供应商费用、供应商保证金、供应商提现、供应商余额
            $supply_data_result = $supply_account_model->getSupplyStatSum();
            $supply_data        = $supply_data_result['data'] ?? [];
            $this->assign('account_data', $supply_data);

            //todo  city抽成
            return $this->fetch('supplyaccount/open_account');
        }
    }


    /**
     * 提现列表
     */
    public function withdraw()
    {
        $supply_account_model = new SupplyAccountModel();
        if (request()->isAjax()) {
            $page        = input('page', 1);
            $page_size   = input('page_size', PAGE_LIST_ROWS);
            $search_text = input('search_text', '');
            $status      = input('status', '');
            $start_date  = input('start_date', '');
            $end_date    = input('end_date', '');
            $period_id   = input('period_id', '');

            $condition = [];

            $site_id = input('site_id', '');
            if (!empty($site_id)) {
                $condition[] = ['site_id', '=', $site_id];
            }

            if ($search_text) {
                $condition[] = ['site_name', 'like', '%' . $search_text . '%'];
            }

            if ($start_date != '' && $end_date != '') {
                $condition[] = ['apply_time', 'between', [strtotime($start_date), strtotime($end_date)]];
            } elseif ($start_date != '' && $end_date == '') {
                $condition[] = ['apply_time', '>=', strtotime($start_date)];
            } elseif ($start_date == '' && $end_date != '') {
                $condition[] = ['apply_time', '<=', strtotime($end_date)];
            }

            if ($status !== '') {
                $condition[] = ['status', '=', $status];
            }
            if ($period_id !== '') {
                $condition[] = ['period_id', '=', $period_id];
            }
            return $supply_account_model->getSupplyWithdrawPageList($condition, $page, $page_size, 'apply_time desc');
        } else {
            $period_id = input('period_id', '');
            $this->assign('period_id', $period_id);
            $this->forthMenu();

            //供应商费用、供应商保证金、供应商提现、供应商余额
            $supply_data_result = $supply_account_model->getSupplyStatSum();
            $supply_data        = $supply_data_result['data'] ?? [];
            $this->assign('account_data', $supply_data);
            return $this->fetch("supplyaccount/withdraw");
        }
    }

    /**
     * 修改备注
     * @return mixed
     */
    public function withdrawMemo()
    {
        $account_model = new SupplyAccountModel();
        if (request()->isAjax()) {
            $memo            = input('memo', '');
            $apply_id        = input('apply_id', '');
            $data            = array(
                "memo" => $memo
            );
            $condition['id'] = $apply_id;
            $res             = $account_model->editSupplyWithdraw($data, $condition);
            return $res;
        }
    }


    /**
     * 转账申请同意
     * @return mixed
     */
    public function applyPass()
    {
        $account_model = new SupplyAccountModel();
        if (request()->isAjax()) {
            $apply_ids = input('apply_ids', '');
            $res       = $account_model->applyPass($apply_ids);
            return $res;
        }
    }

    /**
     * 转账申请拒绝
     * @return mixed
     */
    public function applyReject()
    {
        $account_model = new SupplyAccountModel();
        if (request()->isAjax()) {
            $apply_id = input('apply_id', '');
            $res      = $account_model->applyReject($apply_id);
            return $res;
        }
    }

    /**
     * 转账同意
     * @return mixed
     */
    public function applyPay()
    {
        $account_model = new SupplyAccountModel();
        if (request()->isAjax()) {
            $data = [
                'paying_money_certificate_explain' => input('paying_money_certificate_explain', ''),
                'paying_money_certificate'         => input('paying_money_certificate', '')
            ];
            $id   = input('id', '');
            $res  = $account_model->supplyWithdrawPass($id, $data);
            return $res;
        }
    }



    /**
     * 获取账户流水
     */
    public function getShopAccount()
    {
        if (request()->isAjax()) {
            $account_model = new SupplyAccount();
            $page          = input('page', 1);
            $page_size     = input('page_size', PAGE_LIST_ROWS);
            $order         = input("order", "create_time desc");
            $condition[]   = ['site_id', "=", $this->supply_id];

            $start_time = input('start_time', '');
            $end_time   = input('end_time', '');
            if (!empty($start_time) && empty($end_time)) {
                $condition[] = ["create_time", ">=", date_to_time($start_time)];
            } elseif (empty($start_time) && !empty($end_time)) {
                $condition[] = ["create_time", "<=", date_to_time($end_time)];
            } elseif (!empty($start_time) && !empty($end_time)) {
                $condition[] = ["create_time", "between", [date_to_time($start_time), date_to_time($end_time)]];
            }
            $type_name = input('type_name', '');
            if ($type_name) {
                $condition[] = ['type_name', '=', $type_name];
            }

            $list = $account_model->getAccountPageList($condition, $page, $page_size, $order);
            return $list;
        }
    }

    /**
     * 获取账户流水
     */
    public function getSupplyAccount()
    {
        if (request()->isAjax()) {
            $site_id = input('site_id', 0);
            $account_model = new SupplyAccountModel();

            $page = input('page', 1);
            $page_size = input('page_size', PAGE_LIST_ROWS);

            $condition[] = ['site_id','=',$site_id];
            $type = input('type','');//收支类型（1收入  2支出）
            if(!empty($type)){
                switch($type){
                    case 1:
                        $condition[] = ['account_data','>',0];
                        break;
                    case 2:
                        $condition[] = ['account_data','<',0];
                        break;
                }
            }
            $start_time = input('start_time','');
            $end_time = input('end_time','');
            if(!empty($start_time) && empty($end_time)){
                $condition[] = ['create_time','>=',$start_time];
            }elseif(empty($start_time) && !empty($end_time)){
                $condition[] = ['create_time','<=',$end_time];
            }elseif(!empty($start_time) && !empty($end_time)){
                $condition[] = ['create_time','between',[$start_time,$end_time]];
            }

            return  $account_model->getAccountPageList($condition,$page,$page_size,'id desc');
        }
    }
}
