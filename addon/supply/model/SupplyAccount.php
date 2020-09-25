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

/**
 * 供应商账户(无缓存)
 */
class SupplyAccount extends BaseModel
{
    public $from_type = [
        'order'    => [
            'type_name' => '供应商结算',
            'type_url'  => '',
        ],
        'withdraw' => [
            'type_name' => '提现',
            'type_url'  => '',
        ],
    ];
    /**************************************************************店铺账户****************************************************************/
    /**
     * 添加店铺账户数据
     * @param $site_id
     * @param string $type
     * @param $data
     * @param $from_type
     * @param $relate_tag
     * @param $remark
     * @return array
     */
    public function addSupplyAccount($site_id, $type = 'account', $account_data, $from_type, $relate_tag, $remark)
    {
        $account_no = $this->getWithdrawNo();
        $data       = array(
            'site_id'      => $site_id,
            'account_no'   => $account_no,
            'account_type' => $type,
            'account_data' => $account_data,
            'from_type'    => $from_type,
            'type_name'    => $this->from_type[$from_type]['type_name'],
            'relate_tag'   => $relate_tag,
            'create_time'  => time(),
            'remark'       => $remark
        );

        $supply_account   = model('supplier')->getInfo([
            'supplier_site_id' => $site_id
        ], $type);
        $account_new_data = (float)$supply_account[$type] + (float)$account_data;
        if ((float)$account_new_data < 0) {
            return $this->error('', 'RESULT_ERROR');
        }

        $res = model('supply_account')->add($data);


        $res = model('supplier')->update([
            $type => $account_new_data
        ], [
            'supplier_site_id' => $site_id
        ]);
        //todo  需要查询后续操作
//        event("AddShopAccount", $data);
        return $this->success($res);
    }

    /**
     * 获取账户列表
     * @param array $where
     * @param string $field
     * @param string $order
     * @param null $limit
     * @return array
     */
    public function getAccountList($where = [], $field = 'site_id,type,money,order_id,text,create_time', $order = '', $limit = null)
    {

        $list = model('supply_account')->getList($where, $field, $order, '', '', '', $limit);
        return $this->success($list);
    }

    /**
     * 获取账户分页列表
     * @param array $condition
     * @param int $page
     * @param int $page_size
     * @param string $order
     * @param string $field
     * @return array
     */
    public function getAccountPageList($condition = [], $page = 1, $page_size = PAGE_LIST_ROWS, $order = '', $field = '*')
    {

        $list = model('supply_account')->pageList($condition, $field, $order, $page, $page_size);
        return $this->success($list);
    }
    /**************************************************************店铺账户结束*************************************************************/

    /**************************************************************店铺提现转账*************************************************************/

    /**
     * 添加店铺转账
     * @param $data
     * @return array
     */
    public function addSupplyWithdraw($data)
    {
        $res = model("supply_withdraw")->add($data);
        if ($res) {
            $this->addSupplyAccount($data['site_id'], 'account', $data['money'] * (-1), 'withdraw', $res, "店铺提现，账单编号:" . $data['withdraw_no']);
        }

        return $this->success($res);
    }

    /**
     * 编辑店铺转账
     * @param $data
     * @param $condition
     * @return array
     */
    public function editSupplyWithdraw($data, $condition)
    {
        $res = model('supply_withdraw')->update($data, $condition);
        return $this->success($res);
    }

    /**
     * 获取提现申请流水号
     */
    public function getWithdrawNo()
    {
        return date('YmdHi') . rand(1111, 9999);
    }

    /**
     * v
     * @param $site_id
     * @param $money
     * @return array
     */
    public function applyWithdraw($site_id, $money)
    {
        //查询供应商信息
        $supplier_model = new Supplier();
        $supply_info    = $supplier_model->getSupplierInfo([['supplier_site_id', '=', $site_id]], 'title,account,account_withdraw_apply')['data'] ?? [];


        //todo  完善供应商结算账户  查询店铺认证信息
        $supply_cert_info = $supplier_model->getSupplierCert([['site_id', '=', $site_id]], 'contacts_name,contacts_mobile,bank_type, settlement_bank_account_name, settlement_bank_account_number, settlement_bank_name, settlement_bank_address')['data'] ?? [];
        if ($supply_cert_info['settlement_bank_account_number'] == '') {
            return $this->error("", "请先添加结算账户");
        }

        //开始记录申请
        if (($supply_info['account'] - $supply_info['account_withdraw_apply']) < $money) {
            return $this->error("", "SHOP_APPLY_MONEY_NOT_ENOUGH");
        }
        $withdraw_no = $this->getWithdrawNo();

        model("supply_withdraw")->startTrans();
        try {
            $data = [
                'withdraw_no'                    => $withdraw_no,
                'site_id'                        => $site_id,
                'site_name'                      => $supply_info['title'],
                'name'                           => $supply_cert_info['contacts_name'],
                'mobile'                         => $supply_cert_info['contacts_mobile'],
                'bank_type'                      => $supply_cert_info['bank_type'],
                'settlement_bank_account_name'   => $supply_cert_info['settlement_bank_account_name'],
                'settlement_bank_account_number' => $supply_cert_info['settlement_bank_account_number'],
                'settlement_bank_name'           => $supply_cert_info['settlement_bank_name'],
                'settlement_bank_address'        => $supply_cert_info['settlement_bank_address'],
                'money'                          => $money,
                'apply_time'                     => time(),
            ];
            model("supply_withdraw")->add($data);
            model("supplier")->setInc([['supplier_site_id', '=', $site_id]], 'account_withdraw_apply', $money);
//            $res = $this->addSupplyWithdraw($data);
            model("supply_withdraw")->commit();
            return $this->success();
        } catch (\Exception $e) {
            model("supply_withdraw")->rollback();
            return $this->error('', $e->getMessage());
        }
    }

    /**
     * 审核通过
     * @param $apply_ids
     * @return array
     */
    public function applyPass($apply_ids)
    {
        $res = model('supply_withdraw')->update(['status' => 1], [['id', 'in', $apply_ids], ['status', '=', 0]]);
        return $this->success($res);
    }

    /**
     * 审核拒绝
     * @param $apply_id
     * @return array
     */
    public function applyReject($apply_id)
    {
        $res = model('supply_withdraw')->update(['status' => -1], [['id', '=', $apply_id], ['status', '=', 0]]);
        if ($res) {
            $apply_info = model('supply_withdraw')->getInfo([['id', '=', $apply_id]], 'site_id,money,withdraw_no');
            model('supplier')->setDec([['supplier_site_id', '=', $apply_info['site_id']]], 'account_withdraw_apply', $apply_info['money']);
        }
        return $this->success($res);
    }

    /**
     * 转账数据
     * @param $apply_ids
     * @return array
     */
    public function applyPay($apply_ids)
    {
        $res = model('supply_withdraw')->update(['status' => 2, 'payment_time' => time()], [['id', 'in', $apply_ids], ['status', '=', 1]]);
        return $this->success($res);
    }

    /**
     * 商家转账
     * @param $id
     * @param $data
     * @return array
     */
    public function supplyWithdrawPass($id, $data)
    {
        $data['status']       = 2;
        $data['payment_time'] = time();
        model('supply_withdraw')->startTrans();

        try {
            model('supply_withdraw')->update($data, [['id', '=', $id]]);

            $apply_info = model('supply_withdraw')->getInfo([['id', '=', $id]], 'site_id,money,withdraw_no');

            //减少提现中金额
            model('supplier')->setDec([['supplier_site_id', '=', $apply_info['site_id']]], 'account_withdraw_apply', $apply_info['money']);

            //增加已提现金额
            model('supplier')->setInc([['supplier_site_id', '=', $apply_info['site_id']]], 'account_withdraw', $apply_info['money']);
            //增加流水
            $this->addSupplyAccount($apply_info['site_id'], 'account', $apply_info['money'] * (-1), 'withdraw', $id, "供应商提现，提现账单编号:" . $apply_info['withdraw_no']);
            model("supply_withdraw")->commit();
            return $this->success();
        } catch (\Exception $e) {
            model("supply_withdraw")->rollback();
            return $this->error('', $e->getMessage());
        }
    }

    /**
     * 获取店铺提现
     * @param array $condition
     * @param string $field
     * @return array
     */
    public function getSupplyWithdrawInfo($condition = [], $field = '*')
    {
        $info = model('supply_withdraw')->getInfo($condition, $field);
        return $this->success($info);
    }

    /**
     * 获取店铺转账列表
     * @param array $condition
     * @param string $field
     * @param string $order
     * @param null $limit
     * @return array
     */
    public function getSupplyWithdrawList($condition = [], $field = '*', $order = '', $limit = null)
    {

        $list = model('supply_withdraw')->getList($condition, $field, $order, '', '', '', $limit);
        return $this->success($list);
    }

    /**
     * 获取店铺转账分页列表
     * @param array $condition
     * @param int $page
     * @param int $page_size
     * @param string $order
     * @param string $field
     * @return array
     */
    public function getSupplyWithdrawPageList($condition = [], $page = 1, $page_size = PAGE_LIST_ROWS, $order = '', $field = '*')
    {

        $list = model('supply_withdraw')->pageList($condition, $field, $order, $page, $page_size);
        return $this->success($list);
    }

//    /**
//     * 获取商家转账设置
//     */
//    public function getSupplyWithdrawConfig()
//    {
//        $config = new Config();
//        $res = $config->getConfig([['site_id', '=',  0], ['app_module', '=', 'admin'], ['config_key', '=', 'SUPPLY_WITHDRAW']]);
//        if(empty($res['data']['value']))
//        {
//            //默认数据管理
//            $res['data']['value'] = [
//                'is_period_settlement' => 1,  //是否账期转账
//                'period_type' => 3,           //转账周期类型1.天  2. 周  3. 月
//                'min_withdraw' => 0,          //最低提现金额，对手动申请提现有效
//                'max_withdraw' => 0,          //最高提现金额 , 对手动申请提现有效
//                'withdraw_rate' => 0,          //提现或者转账手续费，对整体有效
//                'id_experience' => 0,
//                'expire_time' => 14,
//                'group_id' => 0
//            ];
//        }
//        return $res;
//    }
//
//    /**
//     * 设置商家转账设置
//     */
//    public function setSupplyWithdrawConfig($data)
//    {
//        $config = new Config();
//        $res = $config->setConfig($data, '商家转账设置', 1, [['site_id', '=',  0], ['app_module', '=', 'admin'], ['config_key', '=', 'SHOP_WITHDRAW']]);
//
//        $cron = new Cron();
//        switch($data['period_type'])
//        {
//            case 1://天
//
//                $date = strtotime(date('Y-m-d 00:00:00'));
//                $execute_time = strtotime('+1day',$date);
//                break;
//            case 2://周
//
//                $execute_time = Carbon::parse('next monday')->timestamp;
//                break;
//            case 3://月
//
//                $execute_time = Carbon::now()->addMonth()->firstOfMonth()->timestamp;
//                break;
//        }
//        $cron->deleteCron([ [ 'event', '=', 'SupplyWithdrawPeriodCalc' ] ]);
//        $cron->addCron('2','1','店铺周期结算','SupplyWithdrawPeriodCalc',$execute_time,'0',$data['period_type']);
//        return $res;
//    }

    /**************************************************************店铺提现转账结束**********************************************************/
    /**************************************************************店铺提现转账周期结算**********************************************************/
//    /**
//     * 店铺周期结算
//     */
//    public function supplyWithdrawPeriodCalc()
//    {
//
//        //查询设置结算周期
//        $config = $this->getShopWithdrawConfig();
//        if($config['data']['value']['is_period_settlement'] != 1)
//        {
//            return $this->error();
//        }
//        switch ($config['data']['value']['period_type'])
//        {
//            case 3:
//                $period_name = date('Y-m-d')."月结";
//                break;
//            case 2:
//                $period_name = date('Y-m-d')."周结";
//                break;
//            case 1:
//                $period_name = date('Y-m-d')."日结";
//                break;
//        }
//        model("shop_withdraw_period")->startTrans();
//        try{
//            $period_data = [
//                'remark' => $period_name,
//                'end_time' => time(),
//                'period_type' => $config['data']['value']['period_type']
//            ];
//            $period_id = model("shop_withdraw_period")->add($period_data);
//
//            $field = 'ns.site_id, ns.site_name, ns.is_own, ns.account, ns.account_withdraw, nsc.bank_type, nsc.settlement_bank_account_name, nsc.settlement_bank_account_number, nsc.settlement_bank_name, nsc.settlement_bank_address, nsc.contacts_name, nsc.contacts_mobile';
//            $alias = 'ns';
//            $join = [
//                [
//                    'shop_cert nsc',
//                    ' ns.cert_id = nsc.cert_id',
//                    'left'
//                ],
//            ];
//            $shop_list = model("shop")->getList([], $field, '', $alias, $join);
//            $cache = 1111;
//            $money = 0;
//            $shop_count = 0;
//            foreach ($shop_list as $k => $v)
//            {
//
//                $data = [
//                    'withdraw_no' => date('YmdHi').$cache,
//                    'site_id' => $v['site_id'],
//                    'site_name' => $v['site_name'],
//                    'name' => $v['contacts_name']  == null ? '' : $v['contacts_name'],
//                    'mobile' => $v['contacts_mobile'] == null ? '' : $v['contacts_mobile'],
//                    'bank_type' => $v['bank_type'] == null ? 0 : $v['bank_type'],
//                    'settlement_bank_account_name' => $v['settlement_bank_account_name'] == null ? '' : $v['settlement_bank_account_name'],
//                    'settlement_bank_account_number' => $v['settlement_bank_account_number'] == null ? '' : $v['settlement_bank_account_number'],
//                    'settlement_bank_name' => $v['settlement_bank_name'] == null ? '' : $v['settlement_bank_name'],
//                    'settlement_bank_address' => $v['settlement_bank_address'] == null ? '' : $v['settlement_bank_address'],
//                    'money' => $v['account'] == null ? '' : $v['account'],
//                    'apply_time' => time(),
//                    'status' => 1,
//                    'is_period' => 1,
//                    'period_id' => $period_id,
//                    'period_name' => date('Y-m-d')."结算"
//                ];
//                $cache += 1;
//                $shop_count++;
//                $money += $v['account'];
//                $this->addSupplyWithdraw($data);
//            }
//            //添加周期
//            model("shop_withdraw_period")->update(['money' => $money, 'shop_num' => $shop_count], [['period_id', '=', $period_id]]);
//            model("shop_withdraw_period")->commit();
//            return $this->success();
//        }catch(\Exception $e)
//        {
//            model("shop_withdraw_period")->rollback();
//            return $this->success('', $e->getMessage());
//        }
//
//    }
//    /**
//     * 获取店铺转账周期结算分页列表
//     * @param array $condition
//     * @param number $page
//     * @param string $page_size
//     * @param string $order
//     * @param string $field
//     */
//    public function getSupplyWithdrawPeriodPageList($condition = [], $page = 1, $page_size = PAGE_LIST_ROWS, $order = '', $field = '*')
//    {
//
//        $list = model('supply_withdraw_period')->pageList($condition, $field, $order, $page, $page_size);
//        return $this->success($list);
//    }


    /**
     * 整体查询供应商总额信息
     */
    public function getSupplyStatSum()
    {
        $info = model("supplier")->getInfo([['supplier_site_id', '>', 0]], "sum(account) as account, sum(account_withdraw) as account_withdraw, sum(account_withdraw_apply) as account_withdraw_apply, sum(bond) as bond, sum(open_fee) as open_fee");
        return $this->success($info);
    }


    /**
     * 获取供应商订单金额
     */
    public function getSupplyOrderSum()
    {
        $money = model("supply_order")->getSum([['order_status', '>', 0]], 'order_money');
        return $this->success($money);
    }

    /**
     * 获取整体供应商结算统计
     */
    public function getSupplySettlementSum()
    {
        $field = '
                sum(supply_money) as supply_money,
                sum(refund_supply_money) as refund_supply_money,
                sum(commission) as commission,
                sum(platform_money) as platform_money,
                sum(website_commission) as website_commission
                ';
        $res   = model("supply_settlement_period")->getInfo([['period_id', '>', 0]], $field);
        if ($res['supply_money'] == null) {
            $res['supply_money'] = '0.00';
        }
        if ($res['refund_supply_money'] == null) {
            $res['refund_supply_money'] = '0.00';
        }
        if ($res['commission'] == null) {
            $res['commission'] = '0.00';
        }
        if ($res['platform_money'] == null) {
            $res['platform_money'] = '0.00';
        }
        if ($res['website_commission'] == null) {
            $res['website_commission'] = '0.00';
        }

        return $this->success($res);
    }
}
