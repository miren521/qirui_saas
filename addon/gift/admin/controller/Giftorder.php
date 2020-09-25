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


namespace addon\gift\admin\controller;

use app\admin\controller\BaseAdmin;
use addon\gift\model\GiftOrder as GiftOrderModel;

/**
 * 礼品发放订单
 */
class Giftorder extends BaseAdmin
{

    /**
     * 礼品列表
     * @return mixed
     */
    public function lists()
    {
        $gift_id = input("gift_id", 0);
        if(request()->isAjax())
        {
            $page = input('page', 1);
            $page_size = input('page_size', PAGE_LIST_ROWS);
            $search_text = input('search_text', '');
            $condition = [];
            if($search_text) {
                $condition[] = ['gift_name', 'like', '%'. $search_text .'%'];
            }
            if($gift_id > 0){
                $condition[] = ["gift_id", "=", $gift_id];
            }

            $express_status = input('express_status','');
            if($express_status){
                if($express_status == 1){
                    $condition[] = ['express_status', '=', 1];
                }else{
                    $condition[] = ['express_status', '=', 0];
                }

            }

            $order = 'create_time desc';
            $field = '*';


            $gift_order_model = new GiftOrderModel();
            //礼品名称 礼品图片 礼品库存  礼品价格
            return $gift_order_model->getOrderPageList($condition, $page, $page_size, $order, $field);
        }else{
            $this->assign('gift_id',$gift_id);
            $this->forthMenu();
            return $this->fetch("giftorder/lists");
        }

    }

    /**订单详情
     * @return mixed
     */
    public function detail()
    {
        if(request()->isAjax()){
            $order_id = input('order_id', 0);
            $order_model = new GiftOrderModel();
            $order_info = $order_model->getOrderInfo([['order_id', '=', $order_id]]);
            return $order_info;
        }

    }

    /**
     *礼品发货
     */
    public function express()
    {
        if(request()->isAjax())
        {
            $order_id = input('order_id', 0);
            $data = [
                'express_no' => input('express_no',''),//配送编码
                'express_company_name' => input('express_company_name',''),//物流公司名称
                'express_time' => time(),
                'express_status' => 1
            ];

            $gift_order_model = new GiftOrderModel();
            $this->addLog("礼品订单发货id:".$order_id);
            return $gift_order_model->editOrder($data, $order_id);
        }
    }

}