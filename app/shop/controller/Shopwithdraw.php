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

use app\model\shop\ShopAccount;
use app\model\shop\Shop as ShopModel;

class Shopwithdraw extends BaseShop
{
	
	
	public function __construct()
	{
		//执行父类构造函数
		parent::__construct();
	}
	
    /**
     * 申请提现
     * */
    public function apply()
    {
        if(request()->isAjax()){
            $money = input("apply_money", "");
            $shop_account_model = new ShopAccount();
            $result = $shop_account_model->applyWithdraw($this->site_id, $money);

            return $result;
        }
    }

    /**
     * 获取提现记录
     */
    public function lists()
    {
        $shop_model = new ShopModel();
        //获取店铺的账户信息
        $condition = array(
            ["site_id", "=", $this->site_id]
        );
        $shop_info_result = $shop_model->getShopInfo($condition, 'account, account_withdraw,account_withdraw_apply,shop_open_fee,shop_baozhrmb');
        $shop_info = $shop_info_result["data"];
        //已提现
        $this->assign('account_withdraw',number_format($shop_info['account_withdraw'],2,'.' , ''));
        //提现中
        $this->assign('account_withdraw_apply',number_format($shop_info['account_withdraw_apply'],2,'.' , ''));

        if(request()->isAjax()){
            $account_model = new ShopAccount();
            $page = input('page', 1);
            $page_size = input('page_size', PAGE_LIST_ROWS);

            $status = input('status','');

            $condition[] = ['site_id','=',$this->site_id];
            if(!empty($status)){
                if($status  == 3){//待审核
                    $condition[] = ['status','=',0];
                }else{
                    $condition[] = ['status','=',$status];
                }
            }

            $start_time = input('start_time','');
            $end_time = input('end_time','');
            if(!empty($start_time) && empty($end_time)){
                $condition[] = ['apply_time','>=',$start_time];
            }elseif(empty($start_time) && !empty($end_time)){
                $condition[] = ['apply_time','<=',$end_time];
            }elseif(!empty($start_time) && !empty($end_time)){
                $condition[] = ['apply_time','between',[$start_time,$end_time]];
            }

            $order = "id desc";

            $list = $account_model->getShopWithdrawPageList($condition, $page, $page_size, $order);

            return $list;
        }

        return $this->fetch('shopwithdraw/lists');
    }
    
}