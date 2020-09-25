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


namespace app\admin\controller;

use addon\supply\model\SupplyAccount;
use app\model\shop\ShopDeposit;
use app\model\web\Account as AccountModel;
use app\model\shop\Shop;
use app\model\web\WebSite;
use addon\fenxiao\model\FenxiaoData;

/**
 * 账户数据控制器
 */
class Account extends BaseAdmin
{
    /**
     * 数据概况
     * @return mixed
     */
    public function index()
    {
        $account_model = new AccountModel();
        $website_model = new WebSite();
        //订单金额
        $order_sum = $account_model->getOrderSum();
        //平台抽成
        $account_info = $website_model->getWebSite(['site_id' => 0], 'account');
        //店铺结算
        $shop_settlement_sum = $account_model->getShopSettlementSum();
        //店铺费用、店铺保证金、店铺提现、店铺余额
        $shop_sum = $account_model->getShopDataSum();
        //会员余额
        $member_balance_sum = $account_model->getMemberBalanceSum();
        $is_memberwithdraw = addon_is_exit('memberwithdraw');
        $this->assign('is_memberwithdraw', $is_memberwithdraw);
        if ($is_memberwithdraw == 1) {
            $this->assign('member_balance_sum', $member_balance_sum[ 'data' ]);
        } else {
            $member_balance = number_format($member_balance_sum[ 'data' ][ 'balance' ] + $member_balance_sum[ 'data' ][ 'balance_money' ], 2, '.', '');
            $this->assign('member_balance', $member_balance);
        }

        $settlement_sum = $shop_settlement_sum[ 'data' ][ 'shop_money' ] - $shop_settlement_sum[ 'data' ][ 'refund_shop_money' ] - $shop_settlement_sum[ 'data' ][ 'commission' ];
        $account = [
            'order_sum' => $order_sum[ 'data' ],
            'account_info' => $account_info[ 'data' ][ 'account' ],
            'shop_settlement_sum' => number_format($settlement_sum, 2, '.', ''),
            'shop_fee' => $shop_sum[ 'data' ][ 'shop_open_fee' ],
            'shop_baozhrmb' => $shop_sum[ 'data' ][ 'shop_baozhrmb' ],
            'account_withdraw' => $shop_sum[ 'data' ][ 'account_withdraw' ],
            'account_withdraw_apply' => $shop_sum[ 'data' ][ 'account_withdraw_apply' ],
            'account' => $shop_sum[ 'data' ][ 'account' ]
        ];
        $this->assign('account', $account);

        //分站抽成统计
        $is_addon_city = addon_is_exit('city');
        $this->assign('is_addon_city', $is_addon_city);
        if ($is_addon_city == 1) {
            $website_info = $website_model->getWebSiteSum([['site_id', '>', 0]]);
            $total_account = $website_info[ 'data' ][ 'account' ] + $website_info[ 'data' ][ 'account_withdraw' ];
            $this->assign('total_account', number_format($total_account, 2, '.', ''));
            $this->assign('website_info', $website_info[ 'data' ]);
        }

        //获取分销商账户统计
        $is_addon_fenxiao = addon_is_exit('fenxiao');
        $this->assign('is_addon_fenxiao', $is_addon_fenxiao);
        if ($is_addon_fenxiao == 1) {
            $fenxiao_data_model = new FenxiaoData();
            $account_data = $fenxiao_data_model->getFenxiaoAccountData();
            $this->assign('account_data', $account_data);
            //累计佣金
            $fenxiao_account = number_format($account_data[ 'account' ] + $account_data[ 'account_withdraw' ], 2, '.', '');
            $this->assign('fenxiao_account', $fenxiao_account);
            //分销订单总金额
            $fenxiao_order_money = $fenxiao_data_model->getFenxiaoOrderSum();
            $this->assign('fenxiao_order_money', $fenxiao_order_money);
        }

        
        //获取供应商财务统计

        $is_addon_supply = addon_is_exit('supply');
        $this->assign('is_addon_supply', $is_addon_supply);
        if ($is_addon_supply == 1) {
            //供应商结算
            $supply_account_model = new SupplyAccount();
            $supply_settlement_data = $supply_account_model->getSupplySettlementSum();
            //供应商费用、供应商保证金、供应商提现、供应商余额
            $supply_data = $supply_account_model->getSupplyStatSum();
            $order_money_data = $supply_account_model->getSupplyOrderSum();
            $supply_settlement = $supply_settlement_data[ 'data' ][ 'supply_money' ] - $supply_settlement_data[ 'data' ][ 'refund_supply_money' ] - $supply_settlement_data[ 'data' ][ 'commission' ];
            $account = [
                'order' => $order_money_data[ 'data' ],
                'settlement' => number_format($supply_settlement, 2, '.', ''),
                'fee' => $supply_data[ 'data' ][ 'open_fee' ],
                'bond' => $supply_data[ 'data' ][ 'bond' ],
                'withdraw' => $supply_data[ 'data' ][ 'account_withdraw' ],
                'withdraw_apply' => $supply_data[ 'data' ][ 'account_withdraw_apply' ],
                'account' => $supply_data[ 'data' ][ 'account' ]
            ];
            $this->assign('supply_account', $account);
        }
        return $this->fetch("account/index");
    }

    /**
     * 佣金列表
     */
    public function lists()
    {
        if (request()->isAjax()) {
            $page_index = input('page', 1);
            $page_size = input('page_size', PAGE_LIST_ROWS);
            $site_name = input("site_name", '');
            $start_time = input("start_time", '');
            $end_time = input("end_time", '');

            $condition = [
                ["na.account_type", "=", 'account']
            ];
            if (!empty($site_name)) {
                $condition[] = ['no.site_name', 'like', '%' . $site_name . '%'];
            }
            if (!empty($start_time) && empty($end_time)) {
                $condition[] = ['na.create_time', '>=', date_to_time($start_time)];
            } elseif (empty($start_time) && !empty($end_time)) {
                $condition[] = ["na.create_time", "<=", date_to_time($end_time)];
            } elseif (!empty($start_time) && !empty($end_time)) {
                $condition[] = ['na.create_time', 'between', [date_to_time($start_time), date_to_time($end_time)]];
            }
            $account_model = new AccountModel();
            $list = $account_model->getOrderAccountPageList($condition, $page_index, $page_size);
            return $list;
        } else {
            return $this->fetch("account/lists");
        }
    }

    /**
     * 订单列表
     */
    public function order()
    {
        return $this->fetch("account/order");
    }

    /**
     * 店铺余额
     */
    public function shopBalance()
    {
        if (request()->isAjax()) {
            $page_index = input('page', 1);
            $page_size = input('page_size', PAGE_LIST_ROWS);
            $site_name = input("site_name", '');
            $condition = [];
            $condition[] = ['site_name', 'like', '%' . $site_name . '%'];
            $shop_model = new Shop();
            $list = $shop_model->getShopPageList($condition, $page_index, $page_size, 'site_id desc', 'site_id, site_name, category_name, group_name, account, account_withdraw, shop_open_fee, shop_baozhrmb, logo, is_own, account_withdraw_apply, shop_status');
            return $list;
        } else {
            return $this->fetch("account/shop_balance");
        }

    }

    /**
     * 店铺余额
     */
    public function shopDeposit()
    {
        if (request()->isAjax()) {
            $page_index = input('page', 1);
            $page_size = input('page_size', PAGE_LIST_ROWS);
            $site_name = input("site_name", '');
            $condition = [];
            $condition[] = ['site_name', 'like', '%' . $site_name . '%'];
            $shop_model = new ShopDeposit();
            $list = $shop_model->getShopDepositPageList($condition, $page_index, $page_size, 'id desc');
            return $list;
        } else {
            return $this->fetch("account/shop_deposit");
        }
    }

}