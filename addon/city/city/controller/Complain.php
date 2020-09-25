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


namespace addon\city\city\controller;

use app\model\order\OrderCommon;
use app\model\order\OrderRefund as OrderRefundModel;
use app\model\order\Complain as ComplainModel;
/**
 * 维权 控制器
 */
class Complain extends BaseCity
{
    /**
     * 维权订单列表
     * @return mixed
     */
    public function lists()
    {

        $complain_status = input("complain_status", "");//退款状态
        $sku_name = input("sku_name", '');//商品名称
        $start_time = input("start_time", '');//开始时间
        $end_time = input("end_time", '');//结束时间
        $order_no = input("order_no", '');//订单编号
        $site_id = input("site_id", "");
        $complain_model = new ComplainModel();
        if(request()->isAjax()){
            $page_index = input('page', 1);
            $page_size = input('page_size', PAGE_LIST_ROWS);

            $condition[] = ['website_id','=',$this->site_id];
            //维权状态
            if ($complain_status != "") {
                $condition[] = ["complain_status", "=", $complain_status];
            }

            //商品名称
            if ($sku_name != "") {
                $condition[] = ["sku_name", "like", "%$sku_name%"];
            }

            //订单编号
            if($order_no != ""){
                $condition[] = ["order_no", "like", "%$order_no%"] ;
            }


            if (!empty($start_time) && empty($end_time)) {
                $condition[] = ["complain_apply_time", ">=", date_to_time($start_time)] ;
            } elseif (empty($start_time) && !empty($end_time)) {
                $condition[] = ["complain_apply_time", "<=", date_to_time($end_time)] ;
            } elseif (!empty($start_time) && !empty($end_time)) {
	            $condition[] = [ 'complain_apply_time', 'between', [ date_to_time($start_time), date_to_time($end_time) ] ];
            }
            if(!empty($site_id))
            {
                $condition[] = ["site_id", "=", $site_id] ;
            }
            $list = $complain_model->getOrderComplainPageList($condition, $page_index, $page_size, "complain_apply_time desc");
            return $list;
        }else{
            $complain_status_list = $complain_model->complain_status;
            $this->assign("complain_status_list", $complain_status_list);//退款状态
            return $this->fetch("complain/lists",[],$this->replace);
        }
    }

    /**
     * 维权订单详情
     * @return mixed
     */
    public function detail()
    {
        $order_goods_id = input("order_goods_id", 0);
        //维权订单项信息
        $complain_model = new ComplainModel();
        $detail_result = $complain_model->getComplainDetail($order_goods_id);
        $detail = $detail_result["data"];
        if(empty($detail))
            $this->error("操作失败!请重试");

        $order_common_model = new OrderCommon();
        $order_info_result = $order_common_model->getOrderInfo([["order_id", "=", $detail["order_id"]]]);
        $order_info = $order_info_result["data"];
        if(empty($order_info))
            $this->error("操作失败!请重试");

        $this->assign("detail", $detail);
        $this->assign("order_info", $order_info);
        return $this->fetch("complain/detail",[],$this->replace);
    }

    /**
     * 维权拒绝
     * @return mixed
     */
    public function refuse()
    {
        $order_goods_id = input("order_goods_id", 0);
        $refund_refuse_reason = input("refund_refuse_reason", '');
        $complain_model = new ComplainModel();

        $data = array(
            "order_goods_id" => $order_goods_id,
        );
        $result = $complain_model->complainRefuse($data, $this->user_info, $refund_refuse_reason);
        return $result;
    }
    /**
     * 维权同意
     * @return mixed
     */
    public function agree()
    {
        $order_goods_id = input("order_goods_id", 0);
        $complain_model = new ComplainModel();
        $data = array(
            "order_goods_id" => $order_goods_id
        );
        $res = $complain_model->complainAgree($data, $this->user_info);
        return $res;
    }

}