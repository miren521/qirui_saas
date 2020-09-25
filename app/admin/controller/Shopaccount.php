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

use app\model\shop\ShopAccount as ShopaccountModel;
use app\model\shop\ShopDeposit;
use app\model\shop\ShopCategory as ShopCategoryModel;
use app\model\shop\ShopGroup as ShopGroupModel;
use app\model\shop\ShopApply as ShopApplyModel;
use app\model\shop\ShopOpenAccount;
use app\model\shop\ShopReopen as ShopReopenModel;
use app\model\shop\Shop as ShopModel;
use app\model\web\Account as AccountModel;
use addon\shopwithdraw\model\Config as ShopWithdrawConfig;

/**
 * 商家账户控制器
 */
class Shopaccount extends BaseAdmin
{

    /**
     * 账户列表
     * @return mixed
     */
    public function lists()
    {
        if (request()->isAjax()) {
            $page        = input('page', 1);
            $page_size   = input('page_size', PAGE_LIST_ROWS);
            $search_text = input('search_text', '');
            $start_date  = input('start_date', '');
            $end_date    = input('end_date', '');
            $condition   = [];
            if ($search_text) {
                $condition[] = ['ns.site_name', 'like', '%' . $search_text . '%'];
            }
            if ($start_date != '' && $end_date != '') {
                $condition[] = ['create_time', 'between', [strtotime($start_date), strtotime($end_date)]];
            } elseif ($start_date != '' && $end_date == '') {
                $condition[] = ['create_time', '>=', strtotime($start_date)];
            } elseif ($start_date == '' && $end_date != '') {
                $condition[] = ['create_time', '<=', strtotime($end_date)];
            }
            $order         = '';
            $account_model = new ShopaccountModel();
            return $account_model->getShopPageList($condition, $page, $page_size, $order);
        } else {
            return $this->fetch("shop_account/lists");
        }
    }

    /**
     * 店铺保证金
     * @return mixed
     */
    public function deposit()
    {
        if (request()->isAjax()) {
            $page        = input('page', 1);
            $page_size   = input('page_size', PAGE_LIST_ROWS);
            $search_text = input('search_text', '');
            $status      = input('status', '');
            $start_date  = input('start_date', '');
            $end_date    = input('end_date', '');

            $condition = [];
            if ($search_text) {
                // $condition[] = ['site_name|pay_no', 'like', '%'.$search_text.'%'];
                $condition[] = ['site_name', 'like', '%' . $search_text . '%'];
            }

            if ($status !== '') {
                $condition[] = ['status', '=', $status];
            }

            if ($start_date != '' && $end_date != '') {
                $condition[] = ['create_time', 'between', [strtotime($start_date), strtotime($end_date)]];
            } elseif ($start_date != '' && $end_date == '') {
                $condition[] = ['create_time', '>=', strtotime($start_date)];
            } elseif ($start_date == '' && $end_date != '') {
                $condition[] = ['create_time', '<=', strtotime($end_date)];
            }

            $deposit = new ShopDeposit();
            return $deposit->getShopDepositPageList($condition, $page, $page_size, 'create_time desc');
        } else {
            return $this->fetch("shop_account/deposit");
        }
    }

    /**
     * 转账列表
     */
    public function withdrawList()
    {
        $model    = new AccountModel();
        $shop_sum = $model->getShopDataSum();
        $this->assign('shop_sum', $shop_sum);

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
                // $condition[] = ['site_name|settlement_bank_account_name|mobile|withdraw_no', 'like', '%'.$search_text.'%'];
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
            $account_model = new ShopaccountModel();
            return $account_model->getShopWithdrawPageList($condition, $page, $page_size, 'apply_time desc');
        } else {
            $period_id = input('period_id', '');
            $this->assign('period_id', $period_id);
            $this->forthMenu();
            $this->getTransferAction();

            return $this->fetch("shop_account/withdraw_list");
        }
    }

    /**
     * 查询在线转账的可能性
     */
    public function getTransferAction()
    {
        $is_action    = false;
        $support_type = '';
        if (addon_is_exit("shopwithdraw")) {
            $config_model  = new ShopWithdrawConfig();
            $config_result = $config_model->getConfig();
            $config        = $config_result["data"];
            if ($config["is_use"] == 1) {
                $is_action         = true;
                $support_type      = $config["value"]["transfer_type"];
                $support_type_list = explode(",", $support_type);
                $temp_type_list    = [];
                foreach ($support_type_list as $k => $v) {
                    if ($v == "wechatpay") {
                        $temp_type_list[] = 3;
                    } else if ($v == "alipay") {
                        $temp_type_list[] = 2;
                    } else {
                        $temp_type_list[] = 1;
                    }
                }
                if (!empty($temp_type_list)) {
                    $support_type = implode(",", $temp_type_list);
                }

            }
        }
        $this->assign("support_type", $support_type);
        $this->assign("is_transfer_action", $is_action);
    }


    /**
     * 结算周期
     */
    public function period()
    {
        $account_model = new ShopaccountModel();
        if (request()->isAjax()) {
            $page        = input('page', 1);
            $page_size   = input('page_size', PAGE_LIST_ROWS);
            $period_type = input('period_type', '');
            $start_date  = input('start_date', '');
            $end_date    = input('end_date', '');

            $condition = [];

            if ($start_date != '' && $end_date != '') {
                $condition[] = ['end_time', 'between', [strtotime($start_date), strtotime($end_date)]];
            } else if ($start_date != '' && $end_date == '') {
                $condition[] = ['end_time', '>=', strtotime($start_date)];
            } else if ($start_date == '' && $end_date != '') {
                $condition[] = ['end_time', '<=', strtotime($end_date)];
            }

            if ($period_type !== '') {
                $condition[] = ['period_type', '=', $period_type];
            }
            return $account_model->getShopWithdrawPeriodPageList($condition, $page, $page_size, 'end_time desc');
        } else {
            $this->forthMenu();
            return $this->fetch("shop_account/period");
        }
    }

    /**
     * 转账设置
     * @return mixed
     */
    public function withdrawConfig()
    {
        $account_model = new ShopaccountModel();
        if (request()->isAjax()) {
            $config_json = input('config_json', '');
            $data        = $config_json ? json_decode($config_json, true) : [];
            if (!array_key_exists('id_experience', $data)) {
                $data['id_experience'] = 0;
            }
            return $account_model->setShopWithdrawConfig($data);
        } else {
            $config_info = $account_model->getShopWithdrawConfig();
            $this->assign('config_info', $config_info['data']);

            //店铺等级
            $shop_group_model = new ShopGroupModel();
            $shop_group       = $shop_group_model->getGroupList([['is_own', '=', 0]], '*');
            $this->assign('group_list', $shop_group['data']);

            return $this->fetch("shop_account/withdraw_config");
        }
    }

    /**
     * 转账申请同意
     * @return mixed
     */
    public function applyPass()
    {
        $account_model = new ShopaccountModel();
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
        $account_model = new ShopaccountModel();
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
        $account_model = new ShopaccountModel();
        if (request()->isAjax()) {

            $data = [
                'paying_money_certificate_explain' => input('paying_money_certificate_explain', ''),
                'paying_money_certificate'         => input('paying_money_certificate', '')
            ];
            $id   = input('id', '');

            $res = $account_model->shopWithdrawPass($id, $data);
            return $res;
        }
    }

    /**
     * 修改备注
     * @return mixed
     */
    public function editShopWithdrawMemo()
    {
        $account_model = new ShopaccountModel();
        if (request()->isAjax()) {
            $memo            = input('memo', '');
            $apply_id        = input('apply_id', '');
            $data            = array(
                "memo" => $memo
            );
            $condition['id'] = $apply_id;
            $res             = $account_model->editShopWithdraw($data, $condition);
            return $res;
        }
    }

    /**
     * 店铺费用
     * */
    public function fee()
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

            $shop_open = new ShopOpenAccount();
            $list      = $shop_open->getShopOpenAccountPageList($condition, $page, $page_size, 'id desc');
            return $list;

        } else {
            $account_model = new AccountModel();

            //店铺费用、店铺保证金、店铺提现、店铺余额
            $shop_sum = $account_model->getShopDataSum();
            $account  = [
                'shop_fee' => $shop_sum['data']['shop_open_fee'],
            ];
            $this->assign('account', $account);

            $is_addon_city = addon_is_exit('city');
            $this->assign('is_addon_city', $is_addon_city);

            //分站总抽成
            if ($is_addon_city == 1) {
                $website_account_shop = $account_model->getWebsiteAccountShop();
                $this->assign('website_account_shop', number_format($website_account_shop['data'], 2, '.', ''));
            }
            return $this->fetch('shop_account/fee');
        }
    }

    /**
     *  店铺续签列表
     * */
    public function reopen()
    {
        $model     = new ShopReopenModel();
        $condition = [];
        if (request()->isAjax()) {
            $page        = input('page', 1);
            $page_size   = input('page_size', PAGE_LIST_ROWS);
            $site_name   = input('site_name', '');//店铺名称
            $category_id = input('category_id', '');//店铺类别id
            $group_id    = input('group_id', '');//店铺等级id

            $site_id = input('site_id', '');//店铺等级id
            if ($site_id) {
                $condition[] = ['r.site_id', '=', $site_id];
            }

            $apply_state = input('apply_state', '');
            if ($apply_state) {
                $condition[] = ['r.apply_state', '=', $apply_state];
            }

            if ($site_name) {
                $condition[] = ['s.site_name', 'like', '%' . $site_name . '%'];
            }
            if ($category_id) {
                $condition[] = ['s.category_id', '=', $category_id];
            }
            if ($group_id) {
                $condition[] = ['s.group_id', '=', $group_id];
            }
            return $model->getApplyReopenPageList($condition, $page, $page_size);
        }
    }
}