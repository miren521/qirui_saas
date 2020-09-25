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


namespace app\shop\controller;

use app\model\shop\Shop as ShopModel;
use app\model\shop\ShopAccount;
use app\model\shop\ShopOpenAccount;
use app\model\shop\ShopSettlement;
use app\model\shop\ShopReopen as ShopReopenModel;
use app\model\order\OrderCommon as OrderCommonModel;
use app\model\shop\ShopGroup as ShopGroupModel;
use app\model\web\WebSite as WebsiteModel;
use app\model\shop\Config as ShopConfigModel;

class Account extends BaseShop
{

	public function __construct()
	{
		//执行父类构造函数
		parent::__construct();
	}
	
	/**
	 * 店铺账户面板
	 */
    public function dashboard()
    {
        $shop_model = new ShopModel();
        $shop_account_model = new ShopAccount();

        //获取商家转账设置
        $shop_withdraw_config = $shop_account_model->getShopWithdrawConfig();
        $this->assign('shop_withdraw_config', $shop_withdraw_config['data']['value']);//商家转账设置

        //获取店铺的账户信息
        $condition = array(
            ["site_id", "=", $this->site_id]
        );
        $shop_info_result = $shop_model->getShopInfo($condition, 'site_name,logo,account, account_withdraw,account_withdraw_apply,shop_open_fee,shop_baozhrmb');
        $shop_info = $shop_info_result["data"];
        $this->assign('shop',$shop_info);
        //余额
        $account = $shop_info['account'] - $shop_info['account_withdraw_apply'];
        $this->assign('account',number_format($account,2, '.' , ''));
        //累计收入
        $total = $shop_info['account'] + $shop_info['account_withdraw'];
        $this->assign('total',number_format($total,2, '.' , ''));
        //已提现
        $this->assign('account_withdraw',number_format($shop_info['account_withdraw'],2,'.' , ''));
        //提现中
        $this->assign('account_withdraw_apply',number_format($shop_info['account_withdraw_apply'],2,'.' , ''));
        //获取店家结算账户信息
        $shop_cert_result = $shop_model->getShopCert($condition,'bank_type, settlement_bank_account_name, settlement_bank_account_number, settlement_bank_name, settlement_bank_address');
        $this->assign('shop_cert_info', $shop_cert_result['data']);//店家结算账户信息

        //店铺的待结算金额
        $settlement_model = new ShopSettlement();
        $settlement_info = $settlement_model->getWaitSettlementInfo($this->site_id);
        $order_apply = $settlement_info['shop_money'] - $settlement_info['refund_shop_money'] - $settlement_info['commission'];
        $this->assign('order_apply',number_format($order_apply,2,'.' , ''));

        //账户收支
        if(request()->isAjax()){

            $page = input('page', 1);
            $page_size = input('page_size', PAGE_LIST_ROWS);

            $condition[] = ['site_id','=',$this->site_id];
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

            $field = 'account_no,site_id,account_type,account_data,from_type,type_name,relate_tag,create_time,remark';
            return  $shop_account_model->getAccountPageList($condition,$page,$page_size,'id desc',$field);
        }

        return $this->fetch("account/dashboard");
    }
    
    /**
     * 账户交易记录
     */
    public function orderList()
    {
        //店铺的待结算金额
        $settlement_model = new ShopSettlement();
        $settlement_info = $settlement_model->getWaitSettlementInfo($this->site_id);
        $wait_settlement = $settlement_info['shop_money'] - $settlement_info['refund_shop_money'] - $settlement_info['commission'];
        $this->assign('wait_settlement',number_format($wait_settlement,2,'.' , ''));
        //店铺的已结算金额
        $settlement_model = new ShopSettlement();
        $finish_condition = [
            ['site_id','=',$this->site_id],
            ['order_status','=',10],
            ['settlement_id','>',0]
        ];
        $settlement_info = $settlement_model->getShopSettlementData($finish_condition);
        $finish_settlement = $settlement_info['shop_money'] - $settlement_info['refund_shop_money'] - $settlement_info['commission'];
        $this->assign('finish_settlement',number_format($finish_settlement,2,'.' , ''));
        //店铺的进行中金额
        $settlement_model = new ShopSettlement();
        $settlement_condition = [
            ['site_id','=',$this->site_id],
            ['order_status', "not in", [0,-1,10]]
        ];
        $settlement_info = $settlement_model->getShopSettlementData($settlement_condition);
        $settlement = $settlement_info['shop_money'] - $settlement_info['refund_shop_money'] - $settlement_info['commission'];
        $this->assign('settlement',number_format($settlement,2,'.' , ''));

        if(request()->isAjax()){

            $order_model = new OrderCommonModel();
            $condition[] = ['site_id','=',$this->site_id];

            //下单时间
            $start_time = input('start_time','');
            $end_time = input('end_time','');
            if (!empty($start_time) && empty($end_time)) {
                $condition[] = ["finish_time", ">=", $start_time];
            } elseif (empty($start_time) && !empty($end_time)) {
                $condition[] = ["finish_time", "<=", $end_time];
            } elseif (!empty($start_time) && !empty($end_time)) {
                $condition[] = [ 'finish_time', 'between', [ $start_time, $end_time ] ];
            }

            //订单状态
            $order_status = input("order_status", "");
            if ($order_status != "") {
                switch($order_status){
                    case 1://进行中

                        $condition[] = ["order_status", "not in", [0,-1,10]];
                        $order = 'pay_time desc';
                        break;
                    case 2://待结算

                        $condition[] = ["order_status", "=", 10];
                        $condition[] = ["is_settlement", "=", 0];
                        $order = 'finish_time desc';
                        break;
                    case 3://已结算

                        $condition[] = ["order_status", "=", 10];
                        $condition[] = ["settlement_id", ">", 0];
                        $order = 'finish_time desc';
                        break;
                    case 4://全部
                        $condition[] = ["order_status", "not in", [0,-1]];
                        $order = 'pay_time desc';
                        break;
                }
            }else{
                $condition[] = ["order_status", "=", 10];
                $condition[] = ["settlement_id", "=", 0];
                $order = 'finish_time desc';
            }
            $page = input('page', 1);
            $page_size = input('page_size', PAGE_LIST_ROWS);
            $field = 'order_id,order_no,order_money,order_status_name,shop_money,platform_money,refund_money,refund_shop_money,refund_platform_money,commission,finish_time,settlement_id';
            $list = $order_model->getOrderPageList($condition,$page,$page_size,$order,$field);

            return $list;
        }

        return $this->fetch('account/order_list');
    }

    
    /**
     * 店铺费用
     */
    public function fee()
    {
        $site_id = $this->site_id;//店铺ID

        //获取店铺信息
        $condition[] = ['site_id','=',$site_id];
        $apply_model = new ShopModel();
        $apply_info = $apply_model->getShopInfo($condition,'*');
        $apply_data = $apply_info['data'];

        //店铺的到期时间（0为永久授权）
        if($apply_data != null){

            if ($apply_data['expire_time'] == 0) {
                $apply_data['is_reopen'] = 1;//永久有效
            }elseif ($apply_data['expire_time'] > time()) {
                $cha = $apply_data['expire_time'] - time();
                $date = ceil(($cha/86400));
                if($date < 30){
                    $apply_data['is_reopen'] = 2;//离到期一月内才可以申请续签
                }

            }else{
                $apply_data['is_reopen'] = 2;
            }

            $apply_data['expire_time'] = $apply_data['expire_time'] == 0 ? '永久有效' :date("Y-m-d H:i:s",$apply_data['expire_time']);
        }
        $this->assign('apply_data',$apply_data);

        //判断是否有续签
        $reopen_model = new ShopReopenModel();
        $reopen_info = $reopen_model->getReopenInfo([['sr.site_id','=',$this->site_id],['sr.apply_state','in',[1,-1]]]);
        if(empty($reopen_info['data'])){
            $is_reopen = 1;
        }else{
            $is_reopen = 2;
        }
        $this->assign('is_reopen',$is_reopen);


        if(request()->isAjax()){

            $shop_open = new ShopOpenAccount();
            $page = input('page', 1);
            $page_size = input('page_size', PAGE_LIST_ROWS);
            $list = $shop_open->getShopOpenAccountPageList([['site_id', '=', $site_id]], $page, $page_size,'id desc');
            return $list;
        }
        //店铺等级
        $shop_group_model = new ShopGroupModel();
        $shop_group_list = $shop_group_model->getGroupList([['is_own','=',0]], 'group_id,is_own,group_name,fee,remark', 'is_own asc,fee asc');
        $this->assign('shop_group_list', $shop_group_list['data']);

        return $this->fetch('account/fee');
    }

    /**
     * 续签记录
     */
    public function reopenList()
    {

        $site_id = $this->site_id;//店铺ID

        //获取店铺信息
        $condition[] = ['site_id','=',$site_id];
        $apply_model = new ShopModel();
        $apply_info = $apply_model->getShopInfo($condition,'*');
        $apply_data = $apply_info['data'];

        //店铺的到期时间（0为永久授权）
        if($apply_data != null){

            if ($apply_data['expire_time'] == 0) {
                $apply_data['is_reopen'] = 1;//永久有效
            }elseif ($apply_data['expire_time'] > time()) {
                $cha = $apply_data['expire_time'] - time();
                $date = ceil(($cha/86400));
                if($date < 30){
                    $apply_data['is_reopen'] = 2;//离到期一月内才可以申请续签
                }

            }else{
                $apply_data['is_reopen'] = 2;
            }

            $apply_data['expire_time'] = $apply_data['expire_time'] == 0 ? '永久有效' :date("Y-m-d H:i:s",$apply_data['expire_time']);
        }
        $this->assign('apply_data',$apply_data);

        //判断是否有续签
        $reopen_model = new ShopReopenModel();
        $reopen_info = $reopen_model->getReopenInfo([['sr.site_id','=',$this->site_id],['sr.apply_state','in',[1,-1]]]);
        if(empty($reopen_info['data'])){
            $is_reopen = 1;
        }else{
            $is_reopen = 2;
        }
        $this->assign('is_reopen',$is_reopen);

        //获取续签信息
        if(request()->isAjax()){
            $shop_reopen = new ShopReopenModel();
            $page = input('page', 1);
            $page_size = input('page_size', PAGE_LIST_ROWS);
            $list = $shop_reopen->getReopenPageList([['site_id', '=', $site_id]], $page, $page_size);
            return $list;
        }else{
            $shop_reopen = new ShopReopenModel();
            $page_size = input('page_size', PAGE_LIST_ROWS);
            $shop_reopen = $shop_reopen->getReopenPageList([['site_id', '=', $site_id]], 1, $page_size);
            $this->assign('shop_reopen',$shop_reopen);
        }
        //店铺等级
        $shop_group_model = new ShopGroupModel();
        $shop_group_list = $shop_group_model->getGroupList([['is_own','=',0]], 'group_id,is_own,group_name,fee,remark', 'is_own asc,fee asc');
        $this->assign('shop_group_list', $shop_group_list['data']);

        //平台配置信息
        $website_model = new WebsiteModel();
        $website_info = $website_model->getWebSite([['site_id', '=', 0]], 'web_qrcode,web_phone');
        $this->assign('website_info',$website_info['data']);

        //收款信息
        $shop_config_model = new ShopConfigModel();
        $receivable_config = $shop_config_model->getSystemBankAccount();
        $this->assign('receivable_config',$receivable_config['data']);

        return $this->fetch("account/reopen_list");

    }
    
}