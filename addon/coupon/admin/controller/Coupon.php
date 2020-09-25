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


namespace addon\coupon\admin\controller;

use app\admin\controller\BaseAdmin;
use addon\coupon\model\CouponType as CouponTypeModel;
use addon\coupon\model\Coupon as CouponModel;

/**
 * 优惠券
 */
class Coupon extends BaseAdmin
{
    /**
     * 活动列表
     */
    public function lists()
    {
        if(request()->isAjax()){
            $page = input('page', 1);
            $page_size = input('page_size', PAGE_LIST_ROWS);
            $coupon_name = input('coupon_name', '');
            $site_id = input('site_id', 0);
			$status = input('status', '');

            $condition = [];
            if($coupon_name !== ""){
                $condition[] = ['coupon_name', 'like', '%'. $coupon_name .'%'];
            }
            if($site_id != 0){
                $condition[] = ['site_id', '=', $site_id];
            }
			if($status !== ""){
			    $condition[] = [ 'status', '=', $status];
			}
            $order = 'create_time desc';
            $field = '*';

            $coupon_model = new CouponModel();
            $res = $coupon_model->getCouponTypePageList($condition, $page, $page_size, $order, $field);

            $coupon_type_model = new CouponTypeModel();
            //获取优惠券状态
            $coupon_type_status_arr = $coupon_type_model->getCouponTypeStatus();
            foreach($res['data']['list'] as $key=>$val){
                $res['data']['list'][$key]['status_name'] = $coupon_type_status_arr[$val['status']];
            }
            return $res;

        }else{
            //优惠券状态
            $coupon_type_model = new CouponTypeModel();
            $coupon_type_status_arr = $coupon_type_model->getCouponTypeStatus();
            $this->assign('coupon_type_status_arr', $coupon_type_status_arr);

            return $this->fetch("coupon/lists");
        }
    }
    /**
     * 优惠券领取记录
     */
    public function receive()
    {   
        if(request()->isAjax()){
            $page = input('page', 1);
            $page_size = input('page_size', PAGE_LIST_ROWS);
            $coupon_name = input('coupon_name', '');
            $site_id = input('site_id', 0);
            $state = input('state', '');;
            $coupon_type_id = input('coupon_type_id', '');

            $condition = [];

            if($coupon_name !== ""){
                $condition[] = ['npc.coupon_name', 'like', '%'. $coupon_name .'%'];
            }
            if($site_id != 0){
                $condition[] = ['npc.site_id', '=', $site_id];
            }
            if($state !== ""){
                $condition[] = [ 'npc.state', '=', $state];
            }
            if($coupon_type_id !== ""){
                $condition[] = [ 'npc.coupon_type_id', '=', $coupon_type_id];
            }

            $coupon_model = new CouponModel();
            $res = $coupon_model->getMemberCouponPageList($condition, $page, $page_size);

            return $res;

        }else{
            //优惠券状态
            $coupon_type_model = new CouponTypeModel();
            $coupon_type_status_arr = $coupon_type_model->getCouponTypeStatus();
            $this->assign('coupon_type_status_arr', $coupon_type_status_arr);
            
            $coupon_type_id = input('coupon_type_id', '');
            $this->assign('coupon_type_id', $coupon_type_id);
            return $this->fetch("coupon/receive");
        }
    }

    /**
     * 活动详情
     */
    public function detail()
    {
        $coupon_type_id = input('coupon_type_id', 0);
        $site_id = input('site_id', 0);
        $coupon_type_model = new CouponTypeModel();
        $coupon_type_info = $coupon_type_model->getCouponTypeInfo($coupon_type_id, $site_id);

        $this->assign('coupon_type_info', $coupon_type_info['data']);

        return $this->fetch("coupon/detail");
    }
    
    /**
     * 强制关闭优惠券
     */
    public function close()
    {
        if(request()->isAjax()){
            $coupon_type_id = input('coupon_type_id', 0);
            $site_id = input('site_id', 0);
            $this->addLog("强制关闭优惠券活动id:".$coupon_type_id);
            $coupon_type_model = new CouponTypeModel();
            return $coupon_type_model->closeCouponType($coupon_type_id, $site_id);
        }
    }

    /**
     * 删除活动
     */
    public function delete()
    {
        if(request()->isAjax()){
            $coupon_type_id = input('coupon_type_id', 0);
            $site_id = input('site_id', 0);
            $this->addLog("删除优惠券活动id:".$coupon_type_id);
            $coupon_type_model = new CouponTypeModel();
            return $coupon_type_model->deleteCouponType($coupon_type_id, $site_id);
        }
    }
}