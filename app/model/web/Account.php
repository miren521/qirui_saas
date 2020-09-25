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


namespace app\model\web;


use app\model\BaseModel;
/**
 * 系统站账户
 */
class Account extends BaseModel
{
    public $from_type = [
        'order' => [
            'type_name' => '订单结算',
            'type_url' => '',
        ],
        'withdraw' => [
            'type_name' => '提现',
            'type_url' => '',
        ],
    ];
    /**************************************************************店铺账户****************************************************************/
    /**
     * 添加分站账户数据
     * @param int $site_id
     * @param int $account_type 账户类型 默认account
     * @param float $account_data
     * @param string $relate_url
     * @param string $remark
    */
    public function addAccount($site_id, $account_type = 'account', $account_data, $from_type, $relate_tag, $remark)
    {
        $data = array(
            'account_no' => date('YmdHi').rand(1000,9999),
            'site_id' => $site_id,
            'account_type' => $account_type,
            'account_data' => $account_data,
            'from_type' => $from_type,
            'relate_tag' => $relate_tag,
            'create_time' => time(),
            'remark' => $remark
        );
    
        $account = model('website')->getInfo([
            'site_id' => 0
        ], $account_type);
        $account_new_data = (float) $account[ $account_type ] + (float) $account_data;
        if ((float) $account_new_data < 0) {
            return $this->error('', 'RESULT_ERROR');
        }
    
        $res = model('account')->add($data);
        $res = model('website')->update([
            $account_type => $account_new_data
        ], [
            'site_id' => 0
        ]);
        event("AddAccount", $data);
        return $this->success($res);
    }
    /**************************************************************账户查询****************************************************************/
    /**
     * 获取账户流水
     * @param unknown $condition
     * @param string $field
     * @param string $order
     * @param string $limit
     * @return multitype:number unknown
     */
    public function getAccountList($condition = [], $field = '*', $order = '', $limit = null)
    {
        $list = model('account')->getList($condition, $field, $order, '', '', '', $limit);
        return $this->success($list);
    }
    
    
    /**
     * 获取店铺账户流水分页
     * @param unknown $condition
     * @param number $page
     * @param string $page_size
     * @param string $order
     * @param string $field
     * @return multitype:number unknown
     */
    public function getAccountPageList($condition = [], $page = 1, $page_size = PAGE_LIST_ROWS, $order = '', $field = '*')
    {
    
        $list = model('account')->pageList($condition, $field, $order, $page, $page_size);
        return $this->success($list);
    }
    
    /**
     * 按照订单查询账户流水
     * @param unknown $condition
     * @param number $page
     * @param string $page_size
     * @param string $order
     * @param string $field
     */
    public function getOrderAccountPageList($condition = [], $page = 1, $page_size = PAGE_LIST_ROWS, $order = 'na.create_time desc')
    {
        $field = 'no.site_id, no.site_name, no.order_no, no.order_money, na.account_data, no.shop_money, na.create_time, na.remark';
        $alias = 'na';
        $join = [
            [
                'order no',
                'na.relate_tag = no.order_id',
                'left'
            ],
        ];
        $list = model("account")->pageList($condition, $field, $order, $page, $page_size, $alias, $join);
        return $this->success($list);
    }
    
    /**
     * 获取平台或者分站账户
     * @return multitype:number unknown
     */
    public function getAccountInfo($site_id = 0)
    {
        $info = model("website")->getInfo([['site_id', '=', $site_id]], "account");
        return $this->success($info);
    }
    
    
    /**
     * 获取店铺申请金额统计
     * @param unknown $start_time
     * @param unknown $end_time
     */
    public function getShopApplySum($start_time, $end_time)
    {
        $money = model("shop_apply")->getSum([['finish_time', '>', $start_time], ['finish_time', '<', $end_time]], 'paying_apply');
        if($money == null)
        {
            $money = 0;
        }
        return $this->success($money);
    }
    
    /**
     * 获取店铺续签金额统计
     * @param unknown $start_time
     * @param unknown $end_time
     * @return multitype:number unknown
     */
    public function getShopReopenSum($start_time, $end_time)
    {
        $money = model("shop_apply")->getSum([['finish_time', '>', $start_time], ['finish_time', '<', $end_time]], 'paying_apply');
        if($money == null)
        {
            $money = 0;
        }
        return $this->success($money);
    }
    
    /**
     * 获取店铺入驻费用统计
     */
    public function getShopOpenFeeSum()
    {
        $money = model("shop")->getSum([], 'shop_open_fee');
        return $this->success($money);
    }
    
    /**
     * 获取店铺保证金统计
     */
    public function getShopDepositSum()
    {
        $money = model("shop")->getSum([], 'shop_baozhrmb');
        return $this->success($money);
    }
    
    /**
     * 获取店铺余额统计
     */
    public function getShopOrderAccountSum()
    {
        $money = model("shop")->getSum([], 'account');
        return $this->success($money);
    }
    
    /**
     * 查询店铺提现总额
     * @return multitype:number unknown
     */
    public function getShopWithDrawSum()
    {
        $money = model("shop")->getSum([], 'account_withdraw');
        return $this->success($money);
    }
    /**
     * 整体查询店铺总额信息
     */
    public function getShopDataSum()
    {
        $info = model("shop")->getInfo([['site_id', '>', 0]], "sum(account) as account, sum(account_withdraw) as account_withdraw, sum(account_withdraw_apply) as account_withdraw_apply, sum(shop_baozhrmb) as shop_baozhrmb, sum(shop_open_fee) as shop_open_fee");
        return $this->success($info);
    }
    
    /**
     * 获取整体店铺结算
     */
    public function getShopSettlementSum()
    {
        $field = '
                sum(shop_money) as shop_money,
                sum(refund_shop_money) as refund_shop_money,
                sum(commission) as commission,
                sum(platform_money) as platform_money,
                sum(website_commission) as website_commission
                ';
        $res = model("shop_settlement_period")->getInfo([['period_id', '>', 0]], $field);
        if($res['shop_money'] == null){
            $res['shop_money'] = '0.00';
        }
        if($res['refund_shop_money'] == null){
            $res['refund_shop_money'] = '0.00';
        }
        if($res['commission'] == null){
            $res['commission'] = '0.00';
        }
        if($res['platform_money'] == null){
            $res['platform_money'] = '0.00';
        }
        if($res['website_commission'] == null){
            $res['website_commission'] = '0.00';
        }

        return $this->success($res);
    }
    
    /**
     * 获取订单金额
     */
    public function getOrderSum()
    {
        $money = model("order")->getSum([['order_status', '>', 0]], 'order_money');
        return $this->success($money);
    }
    
    /**
     * 会员金额账户
     * @return multitype:
     */
    public function getMemberBalanceSum()
    {
        $field = '
                sum(balance) as balance, 
                sum(balance_money) as balance_money,
                sum(balance_withdraw_apply) as balance_withdraw_apply, 
                sum(balance_withdraw) as balance_withdraw
                ';
        $info = model("member")->getInfo([['member_id', '>', 0]], $field);
        if($info['balance'] == null){
            $info['balance'] = '0.00';
        }
        if($info['balance_money'] == null){
            $info['balance_money'] = '0.00';
        }
        if($info['balance_withdraw_apply'] == null){
            $info['balance_withdraw_apply'] = '0.00';
        }
        if($info['balance_withdraw'] == null){
            $info['balance_withdraw'] = '0.00';
        }
        return $this->success($info);
    }

    /**
     * 获取分站店铺入驻总抽成
     * @return array
     */
    public function getWebsiteAccountShop()
    {
        $info = model("shop_open_account")->getSum([ ['website_id','>',0] ], 'website_commission');
        return $this->success($info);
    }


}