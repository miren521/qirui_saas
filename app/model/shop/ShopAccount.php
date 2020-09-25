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


namespace app\model\shop;


use app\model\system\Config;
use app\model\BaseModel;
use app\model\system\Cron;
use Carbon\Carbon;

/**
 * 店铺账户(无缓存)
 */
class ShopAccount extends BaseModel
{
    public $from_type = [
        'order' => [
            'type_name' => '店铺结算',
            'type_url' => '',
        ],
        'withdraw' => [
            'type_name' => '提现',
            'type_url' => '',
        ],
    ];
    /**************************************************************店铺账户****************************************************************/
    /**
     * 添加店铺账户数据
     * @param int $site_id
     * @param int $account_type 账户类型 默认account
     * @param float $account_data
     * @param string $relate_url
     * @param string $remark
     */
    public function addShopAccount($site_id, $account_type = 'account', $account_data, $from_type, $relate_tag, $remark)
    {
        $account_no = $this->getWithdrawNo();
        $data = array(
            'site_id' => $site_id,
            'account_no' => $account_no,
            'account_type' => $account_type,
            'account_data' => $account_data,
            'from_type' => $from_type,
            'type_name' => $this->from_type[$from_type]['type_name'],
            'relate_tag' => $relate_tag,
            'create_time' => time(),
            'remark' => $remark
        );

        $shop_account = model('shop')->getInfo([
            'site_id' => $site_id
        ], $account_type);
        $account_new_data = (float) $shop_account[ $account_type ] + (float) $account_data;
        if ((float) $account_new_data < 0) {
            return $this->error('', 'RESULT_ERROR');
        }

        $res = model('shop_account')->add($data);
        $res = model('shop')->update([
            $account_type => $account_new_data
        ], [
            'site_id' => $site_id
        ]);
        event("AddShopAccount", $data);
        return $this->success($res);
    }

    /**
     * 获取账户列表
     * @param array $condition
     * @param string $field
     * @param string $order
     * @param string $limit
     */
    public function getAccountList($condition = [], $field = 'site_id,type,money,order_id,text,create_time', $order = '', $limit = null)
    {

        $list = model('shop_account')->getList($condition, $field, $order, '', '', '', $limit);
        return $this->success($list);
    }

    /**
     * 获取账户分页列表
     * @param array $condition
     * @param number $page
     * @param string $page_size
     * @param string $order
     * @param string $field
     */
    public function getAccountPageList($condition = [], $page = 1, $page_size = PAGE_LIST_ROWS, $order = '', $field = '*')
    {

        $list = model('shop_account')->pageList($condition, $field, $order, $page, $page_size);
        return $this->success($list);
    }
    /**************************************************************店铺账户结束*************************************************************/

    /**************************************************************店铺提现转账*************************************************************/

    /**
     * 添加店铺转账
     * @param unknown $data
     */
    public function addShopWithdraw($data)
    {
        $res = model("shop_withdraw")->add($data);
        if($res)
        {
            $this->addShopAccount($data['site_id'], 'account', $data['money']*(-1), 'withdraw', $res, "店铺提现，账单编号:".$data['withdraw_no']);
        }

        return $this->success($res);
    }

    /**
     * 编辑店铺转账
     * @param unknown $data
     * @param unknown $condition
     */
    public function editShopWithdraw($data, $condition)
    {
        $res = model('shop_withdraw')->update($data, $condition);
        return $this->success($res);
    }

    /**
     * 获取提现申请流水号
     */
    public function getWithdrawNo()
    {
        return date('YmdHi').rand(1111,9999);
    }

    /**
     * 店铺申请转账
     * @param unknown $site_id
     * @param unknown $money
     */
    public function applyWithdraw($site_id, $money)
    {
        //查询店铺信息
        $shop = new Shop();
        $shop_info = $shop->getShopInfo([['site_id', '=', $site_id]], 'site_name,account,account_withdraw_apply');
        //查询店铺认证信息
        $shop_cert_info = $shop->getShopCert([['site_id', '=', $site_id]], 'contacts_name,contacts_mobile,bank_type, settlement_bank_account_name, settlement_bank_account_number, settlement_bank_name, settlement_bank_address');
        if($shop_cert_info['data']['settlement_bank_account_number'] == ''){
            return $this->error("", "请先添加结算账户");
        }

        //开始记录申请
        if(($shop_info['data']['account'] - $shop_info['data']['account_withdraw_apply']) < $money)
        {
            return $this->error("", "SHOP_APPLY_MONEY_NOT_ENOUGH");
        }
        $withdraw_no = $this->getWithdrawNo();

        model("shop_withdraw")->startTrans();
        try{
            $data = [
                'withdraw_no' => $withdraw_no,
                'site_id' => $site_id,
                'site_name' => $shop_info['data']['site_name'],
                'name' => $shop_cert_info['data']['contacts_name'],
                'mobile' => $shop_cert_info['data']['contacts_mobile'],
                'bank_type' => $shop_cert_info['data']['bank_type'],
                'settlement_bank_account_name' => $shop_cert_info['data']['settlement_bank_account_name'],
                'settlement_bank_account_number' => $shop_cert_info['data']['settlement_bank_account_number'],
                'settlement_bank_name' => $shop_cert_info['data']['settlement_bank_name'],
                'settlement_bank_address' => $shop_cert_info['data']['settlement_bank_address'],
                'money' => $money,
                'apply_time' => time(),
            ];
            model("shop_withdraw")->add($data);
            model("shop")->setInc([ [ 'site_id', '=', $site_id ] ], 'account_withdraw_apply',$money);
//            $res = $this->addShopWithdraw($data);
            model("shop_withdraw")->commit();
            return $this->success();
        }catch(\Exception $e)
        {
            model("shop_withdraw")->rollback();
            return $this->error('', $e->getMessage());
        }
    }

    /**
     * 审核通过
     * @param unknown $apply_ids
     */
    public function applyPass($apply_ids)
    {
        $res = model('shop_withdraw')->update(['status' => 1], [['id', 'in', $apply_ids], ['status', '=', 0]]);
        return $this->success($res);
    }

    /**
     * 审核拒绝
     * @param unknown $apply_id
     */
    public function applyReject($apply_id)
    {
        $res = model('shop_withdraw')->update(['status' => -1], [['id', '=', $apply_id], ['status', '=', 0]]);
        if($res)
        {
            $apply_info = model('shop_withdraw')->getInfo([['id', '=', $apply_id]], 'site_id,money,withdraw_no');
            model('shop')->setDec([ [ 'site_id', '=', $apply_info['site_id'] ] ], 'account_withdraw_apply',$apply_info['money']);
        }
        return $this->success($res);
    }
    /**
     * 转账数据
     * @param unknown $apply_ids
     */
    public function applyPay($apply_ids)
    {
        $res = model('shop_withdraw')->update(['status' => 2, 'payment_time' => time()], [['id', 'in', $apply_ids], ['status', '=', 1]]);
        return $this->success($res);
    }

    /**
     * 商家转账
     * @param array $condition
     * @param $data
     */
    public function shopWithdrawPass($id,$data)
    {
        $data['status'] = 2;
        $data['payment_time'] = time();
        model('shop_withdraw')->startTrans();

        try{

            model('shop_withdraw')->update($data,[ ['id','=',$id] ]);

            $apply_info = model('shop_withdraw')->getInfo([['id', '=', $id]], 'site_id,money,withdraw_no');

            //减少提现中金额
            model('shop')->setDec([ [ 'site_id', '=', $apply_info['site_id'] ] ], 'account_withdraw_apply',$apply_info['money']);

            //增加已提现金额
            model('shop')->setInc([ [ 'site_id', '=', $apply_info['site_id'] ] ], 'account_withdraw',$apply_info['money']);
            //增加流水
            $this->addShopAccount($apply_info['site_id'], 'account', $apply_info['money']*(-1), 'withdraw', $id, "店铺提现，提现账单编号:".$apply_info['withdraw_no']);
            model("shop_withdraw")->commit();
            return $this->success();
        }catch(\Exception $e)
        {
            model("shop_withdraw")->rollback();
            return $this->error('', $e->getMessage());
        }

    }

    /**
     * 获取店铺提现
     * @param array $condition
     * @param string $field
     */
    public function getShopWithdrawInfo($condition = [], $field = '*')
    {
        $info = model('shop_withdraw')->getInfo($condition, $field);
        return $this->success($info);
    }
    /**
     * 获取店铺转账列表
     * @param array $condition
     * @param string $field
     * @param string $order
     * @param string $limit
     */
    public function getShopWithdrawList($condition = [], $field = '*', $order = '', $limit = null)
    {

        $list = model('shop_withdraw')->getList($condition, $field, $order, '', '', '', $limit);
        return $this->success($list);
    }

    /**
     * 获取店铺转账分页列表
     * @param array $condition
     * @param number $page
     * @param string $page_size
     * @param string $order
     * @param string $field
     */
    public function getShopWithdrawPageList($condition = [], $page = 1, $page_size = PAGE_LIST_ROWS, $order = '', $field = '*')
    {

        $list = model('shop_withdraw')->pageList($condition, $field, $order, $page, $page_size);
        return $this->success($list);
    }

    /**
     * 获取商家转账设置
     */
    public function getShopWithdrawConfig()
    {
        $config = new Config();
        $res = $config->getConfig([['site_id', '=',  0], ['app_module', '=', 'admin'], ['config_key', '=', 'SHOP_WITHDRAW']]);
        if(empty($res['data']['value']))
        {
            //默认数据管理
            $res['data']['value'] = [
                'is_period_settlement' => 1,  //是否账期转账
                'period_type' => 3,           //转账周期类型1.天  2. 周  3. 月
                'min_withdraw' => 0,          //最低提现金额，对手动申请提现有效
                'max_withdraw' => 0,          //最高提现金额 , 对手动申请提现有效
                'withdraw_rate' => 0,          //提现或者转账手续费，对整体有效
                'id_experience' => 0,
                'expire_time' => 14,
                'group_id' => 0
            ];
        }
        return $res;
    }

    /**
     * 设置商家转账设置
     */
    public function setShopWithdrawConfig($data)
    {
        $config = new Config();
        $res = $config->setConfig($data, '商家转账设置', 1, [['site_id', '=',  0], ['app_module', '=', 'admin'], ['config_key', '=', 'SHOP_WITHDRAW']]);

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
        $cron->deleteCron([ [ 'event', '=', 'ShopWithdrawPeriodCalc' ] ]);
        $cron->addCron('2','1','店铺周期结算','ShopWithdrawPeriodCalc',$execute_time,'0',$data['period_type']);
        return $res;
    }

    /**************************************************************店铺提现转账结束**********************************************************/
    /**************************************************************店铺提现转账周期结算**********************************************************/
    /**
     * 店铺周期结算
     */
    public function shopWithdrawPeriodCalc()
    {

        //查询设置结算周期
        $config = $this->getShopWithdrawConfig();
        if($config['data']['value']['is_period_settlement'] != 1)
        {
            return $this->error();
        }
        switch ($config['data']['value']['period_type'])
        {
            case 3:
                $period_name = date('Y-m-d')."月结";
                break;
            case 2:
                $period_name = date('Y-m-d')."周结";
                break;
            case 1:
                $period_name = date('Y-m-d')."日结";
                break;
        }
        model("shop_withdraw_period")->startTrans();
        try{
            $period_data = [
                'remark' => $period_name,
                'end_time' => time(),
                'period_type' => $config['data']['value']['period_type']
            ];
            $period_id = model("shop_withdraw_period")->add($period_data);
            
            $field = 'ns.site_id, ns.site_name, ns.is_own, ns.account, ns.account_withdraw, nsc.bank_type, nsc.settlement_bank_account_name, nsc.settlement_bank_account_number, nsc.settlement_bank_name, nsc.settlement_bank_address, nsc.contacts_name, nsc.contacts_mobile';
            $alias = 'ns';
            $join = [
                [
                    'shop_cert nsc',
                    ' ns.cert_id = nsc.cert_id',
                    'left'
                ],
            ];
            $shop_list = model("shop")->getList([], $field, '', $alias, $join);
            $cache = 1111;
            $money = 0;
            $shop_count = 0;
            foreach ($shop_list as $k => $v)
            {

                $data = [
                    'withdraw_no' => date('YmdHi').$cache,
                    'site_id' => $v['site_id'],
                    'site_name' => $v['site_name'],
                    'name' => $v['contacts_name']  == null ? '' : $v['contacts_name'],
                    'mobile' => $v['contacts_mobile'] == null ? '' : $v['contacts_mobile'],
                    'bank_type' => $v['bank_type'] == null ? 0 : $v['bank_type'],
                    'settlement_bank_account_name' => $v['settlement_bank_account_name'] == null ? '' : $v['settlement_bank_account_name'],
                    'settlement_bank_account_number' => $v['settlement_bank_account_number'] == null ? '' : $v['settlement_bank_account_number'],
                    'settlement_bank_name' => $v['settlement_bank_name'] == null ? '' : $v['settlement_bank_name'],
                    'settlement_bank_address' => $v['settlement_bank_address'] == null ? '' : $v['settlement_bank_address'],
                    'money' => $v['account'] == null ? '' : $v['account'],
                    'apply_time' => time(),
                    'status' => 1,
                    'is_period' => 1,
                    'period_id' => $period_id,
                    'period_name' => date('Y-m-d')."结算"
                ];
                $cache += 1;
                $shop_count++;
                $money += $v['account'];
                $this->addShopWithdraw($data);
            }
            //添加周期
            model("shop_withdraw_period")->update(['money' => $money, 'shop_num' => $shop_count], [['period_id', '=', $period_id]]);
            model("shop_withdraw_period")->commit();
            return $this->success();
        }catch(\Exception $e)
        {
            model("shop_withdraw_period")->rollback();
            return $this->success('', $e->getMessage());
        }

    }
    /**
     * 获取店铺转账周期结算分页列表
     * @param array $condition
     * @param number $page
     * @param string $page_size
     * @param string $order
     * @param string $field
     */
    public function getShopWithdrawPeriodPageList($condition = [], $page = 1, $page_size = PAGE_LIST_ROWS, $order = '', $field = '*')
    {
    
        $list = model('shop_withdraw_period')->pageList($condition, $field, $order, $page, $page_size);
        return $this->success($list);
    }
}