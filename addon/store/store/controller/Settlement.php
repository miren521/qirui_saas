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


namespace addon\store\store\controller;

use addon\store\model\Settlement as SettlementModel;
use app\model\order\OrderCommon as OrderCommonModel;
use app\model\store\Store;


class Settlement extends BaseStore
{

	public function __construct()
	{
		//执行父类构造函数
		parent::__construct();
	}
	
	/**
	 * 店铺账户面板
	 */
    public function index()
    {

        if(request()->isAjax()){
            $model = new SettlementModel();
            $page = input('page', 1);
            $page_size = input('page_size', PAGE_LIST_ROWS);

            $condition = [
                ['site_id', '=', $this->site_id],
                ['store_id', '=', $this->store_id]
            ];
            $start_time = input('start_time','');
            $end_time = input('end_time','');
            if(!empty($start_time) && empty($end_time)){
                $condition[] = ['start_time','>=',$start_time];
            }elseif(empty($start_time) && !empty($end_time)){
                $condition[] = ['end_time','<=',$end_time];
            }elseif(!empty($start_time) && !empty($end_time)){
                $condition[] = ['start_time','>=',$start_time];
                $condition[] = ['end_time','<=',$end_time];
            }

            $order = 'id desc';
            $field = 'id,settlement_no,site_id,site_name,store_name,order_money,shop_money,refund_platform_money,platform_money,refund_shop_money,
        refund_money,create_time,commission,is_settlement,offline_refund_money,offline_order_money,start_time,end_time';
            $list = $model->getStoreSettlementPageList($condition, $page, $page_size, $order, $field);
            return $list;
        }

        return $this->fetch('settlement/index', [], $this->replace);
    }


    /**
     * detail 结算详情
     */
    public function detail() {
        $settlement_id = input('settlement_id', 0);
        $order_model = new OrderCommonModel();
        if(request()->isAjax()){
            $condition[] = ['store_settlement_id', '=' , $settlement_id];

            $page = input('page', 1);
            $page_size = input('page_size', PAGE_LIST_ROWS);
            $field = 'order_id,order_no,order_money,order_status,pay_type_name,shop_money,platform_money,refund_money,refund_shop_money,refund_platform_money,commission,finish_time';
            $list = $order_model->getOrderPageList($condition, $page, $page_size, 'finish_time desc', $field);

            return $list;
        }
        $settlement_model = new SettlementModel();
        $settlement_info = $settlement_model->getSettlementInfo([['id', '=', $settlement_id]]);
        $this->assign('info', $settlement_info['data']);
        return $this->fetch('settlement/detail', [], $this->replace);
    }
}