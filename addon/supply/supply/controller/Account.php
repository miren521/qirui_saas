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

use addon\supply\model\Config;
use addon\supply\model\OpenAccount;
use addon\supply\model\Supplier;
use addon\supply\model\SupplyOpenAccount;
use addon\supply\model\Supplier as SupplierModel;
use addon\supply\model\SupplyAccount as SupplyAccountModel;
use addon\supply\model\SupplyReopen as SupplyReopenModel;
use addon\supply\model\order\OrderCommon as OrderCommonModel;
use addon\supply\model\SupplySettlement;

class Account extends BaseSupply
{

    public function __construct()
    {
        //执行父类构造函数
        parent::__construct();
    }

    /**
     * 供货商账户面板
     */
    public function dashboard()
    {

        $supply_account_model = new SupplyAccountModel();
        //账户收支
        if (request()->isAjax()) {
            $page      = input('page', 1);
            $page_size = input('page_size', PAGE_LIST_ROWS);

            $condition[] = ['site_id', '=', $this->supply_id];
            $type        = input('type', '');//收支类型（1收入  2支出）
            if (!empty($type)) {
                switch ($type) {
                    case 1:
                        $condition[] = ['account_data', '>', 0];
                        break;
                    case 2:
                        $condition[] = ['account_data', '<', 0];
                        break;
                }
            }
            $start_time = input('start_time', '');
            $end_time   = input('end_time', '');
            if (!empty($start_time) && empty($end_time)) {
                $condition[] = ['create_time', '>=', $start_time];
            } elseif (empty($start_time) && !empty($end_time)) {
                $condition[] = ['create_time', '<=', $end_time];
            } elseif (!empty($start_time) && !empty($end_time)) {
                $condition[] = ['create_time', 'between', [$start_time, $end_time]];
            }

            $field = 'account_no,site_id,account_type,account_data,from_type,type_name,relate_tag,create_time,remark';
            return $supply_account_model->getAccountPageList($condition, $page, $page_size, 'id desc', $field);
        } else {
            //获取商家转账设置
            $supply_config_model = new Config();
            $withdraw_config     = $supply_config_model->getSupplyWithdrawConfig();

            $this->assign('withdraw_config', $withdraw_config['data']['value'] ?? []);//商家转账设置

            //获取供货商的账户信息
            $condition          = array(
                ["supplier_site_id", "=", $this->supply_id]
            );
            $supplier_model     = new Supplier();
            $supply_info_result = $supplier_model->getSupplierInfo($condition);

            $supply_info = $supply_info_result["data"] ?? [];
            $this->assign('supply_info', $supply_info);
            //余额
            $account = $supply_info['account'] - $supply_info['account_withdraw_apply'];
            $this->assign('account', number_format($account, 2, '.', ''));
            //累计收入
            $total = $supply_info['account'] + $supply_info['account_withdraw'];
            $this->assign('total', number_format($total, 2, '.', ''));
            //已提现
            $this->assign('account_withdraw', number_format($supply_info['account_withdraw'], 2, '.', ''));
            //提现中
            $this->assign('account_withdraw_apply', number_format($supply_info['account_withdraw_apply'], 2, '.', ''));
            //获取店家结算账户信息
            //获取供货商的账户信息
            $cert_condition     = array(
                ["site_id", "=", $this->supply_id]
            );
            $supply_cert_result = $supplier_model->getSupplierCert($cert_condition, 'bank_type, settlement_bank_account_name, settlement_bank_account_number, settlement_bank_name, settlement_bank_address');

            $this->assign('supply_cert_info', $supply_cert_result['data'] ?? []);//店家结算账户信息
            //供货商的待结算金额
            $settlement_model = new SupplySettlement();
            $settlement_info  = $settlement_model->getWaitSettlementInfo($this->supply_id);
            $order_apply      = $settlement_info['supply_money'] - $settlement_info['refund_supply_money'] - $settlement_info['commission'];
            $this->assign('order_apply', number_format($order_apply, 2, '.', ''));
        }

        return $this->fetch("account/dashboard");
    }

    /**
     * 账户交易记录
     */
    public function orderList()
    {
        if (request()->isAjax()) {
            $order_model = new OrderCommonModel();
            $condition[] = ['site_id', '=', $this->supply_id];
            //下单时间
            $start_time = input('start_time', '');
            $end_time   = input('end_time', '');
            if (!empty($start_time) && empty($end_time)) {
                $condition[] = ["finish_time", ">=", $start_time];
            } elseif (empty($start_time) && !empty($end_time)) {
                $condition[] = ["finish_time", "<=", $end_time];
            } elseif (!empty($start_time) && !empty($end_time)) {
                $condition[] = ['finish_time', 'between', [$start_time, $end_time]];
            }

            //订单状态
            $order_status = input("order_status", "");
            if ($order_status != "") {
                switch ($order_status) {
                    case 1://进行中
                        $condition[] = ["order_status", "not in", [0, -1, 10]];
                        $order       = 'pay_time desc';
                        break;
                    case 2://待结算
                        $condition[] = ["order_status", "=", 10];
                        $condition[] = ["is_settlement", "=", 0];
                        $order       = 'finish_time desc';
                        break;
                    case 3://已结算
                        $condition[] = ["order_status", "=", 10];
                        $condition[] = ["settlement_id", ">", 0];
                        $order       = 'finish_time desc';
                        break;
                    case 4://全部
                        $condition[] = ["order_status", "not in", [0, -1]];
                        $order       = 'pay_time desc';
                        break;
                }
            } else {
                $condition[] = ["order_status", "=", 10];
                $condition[] = ["settlement_id", "=", 0];
                $order       = 'finish_time desc';
            }
            $page      = input('page', 1);
            $page_size = input('page_size', PAGE_LIST_ROWS);
            $field     = 'order_id,order_no,order_money,order_status_name,supply_money,platform_money,refund_money,refund_supply_money,refund_platform_money,commission,finish_time,settlement_id';
            $list      = $order_model->getOrderPageList($condition, $page, $page_size, $order, $field);

            return $list;
        } else {
            //供货商的待结算金额
            $settlement_model = new SupplySettlement();
            $settlement_info  = $settlement_model->getWaitSettlementInfo($this->supply_id);
            $wait_settlement  = $settlement_info['supply_money'] - $settlement_info['refund_supply_money'] - $settlement_info['commission'];
            $this->assign('wait_settlement', number_format($wait_settlement, 2, '.', ''));
            //供货商的已结算金额

            $finish_condition  = [
                ['site_id', '=', $this->supply_id],
                ['order_status', '=', 10],
                ['settlement_id', '>', 0]
            ];
            $settlement_info   = $settlement_model->getSupplySettlementData($finish_condition);
            $finish_settlement = $settlement_info['supply_money'] - $settlement_info['refund_supply_money'] - $settlement_info['commission'];
            $this->assign('finish_settlement', number_format($finish_settlement, 2, '.', ''));
            //供货商的进行中金额
            $settlement_condition = [
                ['site_id', '=', $this->supply_id],
                ['order_status', "not in", [0, -1, 10]]
            ];
            $settlement_info      = $settlement_model->getSupplySettlementData($settlement_condition);
            $settlement           = $settlement_info['supply_money'] - $settlement_info['refund_supply_money'] - $settlement_info['commission'];
            $this->assign('settlement', number_format($settlement, 2, '.', ''));
            return $this->fetch('account/order');
        }
    }


    /**
     * 供货商费用
     */
    public function openaccount()
    {
        $site_id = $this->supply_id;//供货商ID
        if (request()->isAjax()) {
            $supply_open = new SupplyOpenAccount();
            $page        = input('page', 1);
            $page_size   = input('page_size', PAGE_LIST_ROWS);
            $list        = $supply_open
                ->getSupplyOpenAccountPageList([['site_id', '=', $site_id]], $page, $page_size, 'id desc');
            return $list;
        } else {
            //获取供货商信息
            $condition[] = ['supplier_site_id', '=', $site_id];
            $apply_model = new SupplierModel();
            $apply_info  = $apply_model->getSupplierInfo($condition, '*');
            $apply_data  = $apply_info['data'];

            //供货商的到期时间（0为永久授权）
            if ($apply_data != null) {
                if ($apply_data['expire_time'] == 0) {
                    $apply_data['is_reopen'] = 1;//永久有效
                } elseif ($apply_data['expire_time'] > time()) {
                    $cha  = $apply_data['expire_time'] - time();
                    $date = ceil(($cha / 86400));
                    if ($date < 30) {
                        $apply_data['is_reopen'] = 2;//离到期一月内才可以申请续签
                    }
                } else {
                    $apply_data['is_reopen'] = 2;
                }
                $apply_data['expire_time'] = $apply_data['expire_time'] == 0 ? '永久有效' : date("Y-m-d H:i:s", $apply_data['expire_time']);
            }
            $this->assign('apply_data', $apply_data);

            //判断是否有续签  todo  供应商续签
            $reopen_model = new SupplyReopenModel();
            $reopen_info  = $reopen_model->getReopenInfo([
                ['sr.site_id', '=', $this->supply_id], ['sr.apply_state', 'in', [1, -1]]
            ]);
            if (empty($reopen_info['data'])) {
                $is_reopen = 1;
            } else {
                $is_reopen = 2;
            }
            $this->assign('is_reopen', $is_reopen);
            return $this->fetch('account/open_account');
        }
    }

    /**
     * 续签记录
     */
    public function reopenList()
    {
        $site_id = $this->supply_id;//供货商ID


        //获取续签信息
        if (request()->isAjax()) {
            $reopen_model = new SupplyReopenModel();
            $page         = input('page', 1);
            $page_size    = input('page_size', PAGE_LIST_ROWS);
            $list         = $reopen_model->getReopenPageList([['site_id', '=', $site_id]], $page, $page_size);
            return $list;
        }

        //获取供货商信息
        $condition[] = ['supplier_site_id', '=', $site_id];
        $apply_model = new SupplierModel();
        $apply_info  = $apply_model->getSupplierInfo($condition, '*');
        $apply_data  = $apply_info['data'];

        //供货商的到期时间（0为永久授权）
        if ($apply_data != null) {
            if ($apply_data['expire_time'] == 0) {
                $apply_data['is_reopen'] = 1;//永久有效
            } elseif ($apply_data['expire_time'] > time()) {
                $cha  = $apply_data['expire_time'] - time();
                $date = ceil(($cha / 86400));
                if ($date < 30) {
                    $apply_data['is_reopen'] = 2;//离到期一月内才可以申请续签
                }
            } else {
                $apply_data['is_reopen'] = 2;
            }

            $apply_data['expire_time'] = $apply_data['expire_time'] == 0 ? '永久有效' : date("Y-m-d H:i:s", $apply_data['expire_time']);
        }
        $this->assign('apply_data', $apply_data);

        //判断是否有续签
        $reopen_model = new SupplyReopenModel();
        $reopen_info  = $reopen_model->getReopenInfo([
            ['sr.site_id', '=', $this->supply_id], ['sr.apply_state', 'in', [1, -1]]
        ]);
        if (empty($reopen_info['data'])) {
            $is_reopen = 1;
        } else {
            $is_reopen = 2;
        }
        $this->assign('is_reopen', $is_reopen);

        return $this->fetch("account/reopen");
    }

    /**
     * 供应商费用
     */
    public function fee()
    {
        $site_id = $this->supply_id;//供应商ID

        if (request()->isAjax()) {
            $open = new OpenAccount();
            $page      = input('page', 1);
            $page_size = input('page_size', PAGE_LIST_ROWS);
            $list      = $open->getOpenAccountPageList([['site_id', '=', $site_id]], $page, $page_size, 'id desc');
            return $list;
        }
        //获取供应商信息
        $condition[] = ['supplier_site_id', '=', $site_id];
        $apply_model = new Supplier();
        $apply_info  = $apply_model->getSupplierInfo($condition, '*');
        $data        = $apply_info['data'];

        //供应商的到期时间（0为永久授权）
        if ($data != null) {
            if ($data['expire_time'] == 0) {
                $data['is_reopen'] = 1;//永久有效
            } elseif ($data['expire_time'] > time()) {
                $cha  = $data['expire_time'] - time();
                $date = ceil(($cha / 86400));
                if ($date < 30) {
                    $data['is_reopen'] = 2;//离到期一月内才可以申请续签
                }
            } else {
                $data['is_reopen'] = 2;
            }

            $data['expire_time'] = $data['expire_time'] == 0 ? '永久有效' : date("Y-m-d H:i:s", $data['expire_time']);
        }
        $this->assign('apply_data', $data);

        //判断是否有续签
        $reopen_model = new SupplyReopenModel();
        $reopen_info  = $reopen_model->getReopenInfo([
            ['sr.site_id', '=', $this->site_id], ['sr.apply_state', 'in', [1, -1]]
        ]);
        if (empty($reopen_info['data'])) {
            $is_reopen = 1;
        } else {
            $is_reopen = 2;
        }
        $this->assign('is_reopen', $is_reopen);

        return $this->fetch('account/fee');
    }
}
