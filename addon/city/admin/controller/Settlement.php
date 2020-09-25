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


namespace addon\city\admin\controller;

use addon\city\model\CitySettlement;
use addon\city\model\CitySettlementPeriod;
use app\admin\controller\BaseAdmin;
use app\model\shop\ShopOpenAccount;
use app\model\shop\ShopSettlement;

/**
 * 店铺结算
 * @author Administrator
 *
 */
class Settlement extends BaseAdmin
{

    /**
     *结算列表
     */
    public function lists()
    {
        if(request()->isAjax()){
            $model = new CitySettlementPeriod();
            $page = input('page', 1);
            $page_size = input('page_size', PAGE_LIST_ROWS);

            $condition = [];

            $start_time = input('start_time','');
            $end_time = input('end_time','');
            if(!empty($start_time) && empty($end_time)){
                $condition[] = ['period_start_time','>=',$start_time];
            }elseif(empty($start_time) && !empty($end_time)){
                $condition[] = ['period_end_time','<=',$end_time];
            }elseif(!empty($start_time) && !empty($end_time)){
                $condition[] = ['period_start_time','>=',$start_time];
                $condition[] = ['period_end_time','<=',$end_time];
            }

            $order = 'period_id desc';
            $list = $model->getCitySettlementPeriodPageList($condition, $page, $page_size, $order);
            return $list;

        }else{

            $model = new CitySettlementPeriod();
            $account_info = $model->getCitySettlementPeriodSum();
            $this->assign('account_info',$account_info['data']);
            return $this->fetch('settlement/lists');
        }

    }

    /**
     * 结算详情
     */
    public function detail()
    {
        $period_id = input('period_id',0);
        $condition[] = ['period_id','=',$period_id];

        if(request()->isAjax()){

            $model = new CitySettlement();
            $page = input('page', 1);
            $page_size = input('page_size', PAGE_LIST_ROWS);

            $start_time = input('start_time','');
            $end_time = input('end_time','');
            if(!empty($start_time) && empty($end_time)){
                $condition[] = ['period_start_time','>=',$start_time];
            }elseif(empty($start_time) && !empty($end_time)){
                $condition[] = ['period_end_time','<=',$end_time];
            }elseif(!empty($start_time) && !empty($end_time)){
                $condition[] = ['period_start_time','>=',$start_time];
                $condition[] = ['period_end_time','<=',$end_time];
            }

            $list = $model->getCitySettlementPageList($condition, $page, $page_size);
            return $list;

        }else{

            $period_model = new CitySettlementPeriod();
            $period_info = $period_model->getCitySettlementPeriodInfo($condition,'order_commission,shop_commission');
            $this->assign('period_info',$period_info['data']);

            $this->assign('period_id',$period_id);
            return $this->fetch('settlement/detail');
        }

    }


    /**
     * 结算详情
     */
    public function orderDetail()
    {
        $settlement_id = input('settlement_id',0);
        $website_id = input('website_id',0);

        if(request()->isAjax()){

            $order_model = new ShopSettlement();
            $condition[] = ['settlement_id','=',$settlement_id];
            $condition[] = ['website_id','=',$website_id];

            $page = input('page', 1);
            $page_size = input('page_size', PAGE_LIST_ROWS);

            $list = $order_model->getShopSettlementPageList($condition,$page,$page_size);
            return $list;

        }else{

            $this->forthMenu(['settlement_id'=>$settlement_id,'website_id'=>$website_id]);

            $this->assign('settlement_id',$settlement_id);
            $this->assign('website_id',$website_id);
            return $this->fetch('settlement/order_detail');
        }

    }

    /**
     * 店铺入驻
     */
    public function openShopAccount()
    {
        $settlement_id = input('settlement_id',0);
        $website_id = input('website_id',0);

        if(request()->isAjax()){

            $page = input('page', 1);
            $page_size = input('page_size', PAGE_LIST_ROWS);
            $site_name = input('site_name', '');

            $start_time = input("start_time", '');
            $end_time = input("end_time", '');

            $condition[] = ['settlement_id','=',$settlement_id];
            $condition[] = ['website_id','=',$website_id];

            if(!empty($site_name)){
                $condition[] = [ 'site_name', 'like', '%' . $site_name . '%' ];
            }

            if(!empty($start_time) && empty($end_time)){
                $condition[] = ['create_time', '>=', date_to_time($start_time)];
            } elseif (empty($start_time) && !empty($end_time)) {
                $condition[] = ["create_time", "<=", date_to_time($end_time)];
            } elseif (!empty($start_time) && !empty($end_time)) {
                $condition[] = [ 'create_time', 'between', [ date_to_time($start_time), date_to_time($end_time) ] ];
            }

            $shop_open = new ShopOpenAccount();
            $list = $shop_open->getShopOpenAccountPageList($condition, $page, $page_size,'id desc');
            return $list;

        }else{

            $this->forthMenu(['settlement_id'=>$settlement_id,'website_id'=>$website_id]);

            $this->assign('settlement_id',$settlement_id);
            $this->assign('website_id',$website_id);
            return $this->fetch('settlement/shop_detail');
        }
    }
}