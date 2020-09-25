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

use addon\supply\model\SupplyDeposit;
use app\admin\controller\BaseAdmin;
use addon\supply\model\Supplier as SupplierModel;

/**
 * 供应商财务相关
 */
class Account extends BaseAdmin
{

    /**
     * 余额  todo  待考虑
     */
    public function balance()
    {
        if (request()->isAjax()) {
            $page_index = input('page', 1);
            $page_size  = input('page_size', PAGE_LIST_ROWS);
            $site_name  = input("site_name", '');
            $where      = [];
            if (!empty($site_name)) {
                $where[] = ['title', 'like', '%' . $site_name . '%'];
            }
            $supply_model = new SupplierModel();
            $list         = $supply_model->getSupplierPageList($where, $page_index, $page_size, 'supplier_site_id desc', 'supplier_site_id, title, category_name, account, account_withdraw, open_fee, bond, logo, account_withdraw_apply, status');
            return $list;
        } else {
            return $this->fetch("account/balance");
        }
    }

    /**
     * 保证金列表
     */
    public function deposit()
    {
        if (request()->isAjax()) {
            $page_index  = input('page', 1);
            $page_size   = input('page_size', PAGE_LIST_ROWS);
            $site_name   = input("site_name", '');
            $condition   = [];
            $condition[] = ['site_name', 'like', '%' . $site_name . '%'];
            $shop_model  = new SupplyDeposit();
            $list        = $shop_model->getDepositPageList($condition, $page_index, $page_size, 'id desc');
            return $list;
        } else {
            return $this->fetch("account/deposit");
        }
    }
}