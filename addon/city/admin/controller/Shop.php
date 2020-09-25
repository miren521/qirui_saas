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

use app\admin\controller\BaseAdmin;
use addon\city\model\Shop as ShopModel;
use app\model\shop\ShopApply as ShopApplyModel;

/**
 * 店铺结算
 * @author Administrator
 *
 */
class Shop extends BaseAdmin
{

    /**
     * 商家列表
     */
    public function lists()
    {
        if (request()->isAjax()) {
            $page = input('page', 1);
            $page_size = input('page_size', PAGE_LIST_ROWS);
            $search_text = input('search_text', '');
            $category_id = input('category_id', 0);
            $group_id = input('group_id', 0);
            $shop_status = input('shop_status', '');
            $cert_id = input('cert_id', '');
            $is_own = input('is_own', '');
            $start_time = input("start_time", '');
            $end_time = input("end_time", '');
            $website_id = input('website_id',0);//分站城市id

            $condition = [];
            if($search_text){
                $condition[] = ['s.site_name', 'like', '%' . $search_text . '%'];
            }

            if($website_id){
                if($website_id == -1){
                    $condition[] = ['s.website_id', '=', 0];
                }else{
                    $condition[] = ['s.website_id', '=', $website_id];
                }
            }

            //商家分类
            if ($category_id != 0) {
                $condition[] = ['s.category_id', '=', $category_id];
            }
            //店铺等级
            if ($group_id != 0) {
                $condition[] = ['s.group_id', '=', $group_id];
            }
            //商家状态
            if ($shop_status != '') {
                $condition[] = ['s.shop_status', '=', $shop_status];
            }
            if($cert_id){
                switch($cert_id){
                    case 1: //未认证
                        $condition[] = ['s.cert_id', '=', 0];
                        break;
                    case 2: //已认证
                        $condition[] = ['s.cert_id', '>', 0];
                        break;
                }
            }
            if($is_own != '')
            {
                $condition[] = ['s.is_own', '=', $is_own];
            }
            if(!empty($start_time) && empty($end_time)){
                $condition[] = ['s.expire_time', '>=', strtotime($start_time)];
            } elseif (empty($start_time) && !empty($end_time)) {
                $condition[] = ["s.expire_time", "<=", strtotime($end_time)];
            } elseif (!empty($start_time) && !empty($end_time)) {
                $condition[] = ["s.expire_time", ">=", strtotime($start_time)];
                $condition[] = ["s.expire_time", "<=", strtotime($end_time)];
            }

            $order = 's.site_id desc';

            $shop_model = new ShopModel();
            return $shop_model->getShopPageList($condition, $page, $page_size, $order);
        }

    }

    /**
     * 商家申请列表
     */
    public function apply()
    {
        if (request()->isAjax()) {
            $page = input('page', 1);
            $page_size = input('page_size', PAGE_LIST_ROWS);
            $search_text = input('search_text', '');
            $search_text_user = input('search_text_user', '');
            $category_id = input('category_id', 0);
            $group_id = input('group_id', 0);
            $apply_state = input('apply_state', 0);
            $start_time = input("start_time", '');
            $end_time = input("end_time", '');
            $website_id = input("website_id", '');//分站城市id

            $condition = [];

            $site_id = input('site_id', '');
            if ($site_id) {
                $condition[] = [ 'sa.site_id', '=', $site_id ];
            }
            if($website_id){
                if($website_id == -1){
                    $condition[] = ['sa.website_id', '=', 0];
                }else{
                    $condition[] = ['sa.website_id', '=', $website_id];
                }
            }
            if($search_text){
                $condition[] = [ 'sa.shop_name', 'like', '%' . $search_text . '%' ];
            }
            if($search_text_user){
                $condition[] = [ 'sa.username', 'like', '%' . $search_text_user . '%' ];
            }
            if ($category_id != 0) {
                $condition[] = [ 'sa.category_id', '=', $category_id ];
            }
            if ($group_id != 0) {
                $condition[] = [ 'sa.group_id', '=', $group_id ];
            }
            if ($apply_state != 0) {
                if ($apply_state == 4) {
                    $condition[] = ['sa.apply_state', '=', '2'];
                    $condition[] = ['sa.paying_money_certificate', '=', ''];
                } else {
                    $condition[] = [ 'sa.apply_state', '=', $apply_state ];
                }
            }
            if (!empty($start_time) && empty($end_time)) {
                $condition[] = [ 'sa.create_time', '>=', date_to_time($start_time) ];
            } elseif (empty($start_time) && !empty($end_time)) {
                $condition[] = [ "sa.create_time", "<=", date_to_time($end_time) ];
            } elseif (!empty($start_time) && !empty($end_time)) {
                $condition[] = [ 'sa.create_time', 'between', [ date_to_time($start_time), date_to_time($end_time) ] ];
            }

            $order = 'sa.create_time desc';
            $model = new ShopModel();
            $res = $model->getShopApplyPageList($condition, $page, $page_size, $order);

            //处理审核状态
            $shop_apply_model = new ShopApplyModel();
            $apply_state_arr = $shop_apply_model->getApplyState();
            foreach ($res['data']['list'] as $key => $val) {
                if ($apply_state == 2) {
                    if (empty(trim($val['paying_money_certificate']))) {
                        $res['data']['count'] = $res['data']['count'] - 1;
                        unset($res['data']['list'][$key]);
                        continue;
                    }
                }
                $res['data']['list'][ $key ]['apply_state_name'] = $apply_state_arr[ $val['apply_state'] ];
            }
            if ($apply_state == 2) {
                if (empty($res['data']['count'])) {
                    $res['data']['page_count'] = 0;
                } else {
                    $res['data']['page_count'] = ceil($res['data']['page_count']/$page_size);
                }
            }

            return $res;

        }
    }

    /**
     * 商家申请续签列表
     */
    public function reopen()
    {
        $model = new ShopModel();
        $condition = [];
        if (request()->isAjax()) {
            $page = input('page', 1);
            $page_size = input('page_size', PAGE_LIST_ROWS);
            $site_name = input('site_name', '');//店铺名称
            $category_id = input('category_id', '');//店铺类别id
            $group_id = input('group_id', '');//店铺等级id
            $website_id = input('website_id', '');//店铺等级id

            $site_id = input('site_id', '');//店铺等级id
            if ($site_id) {
                $condition[] = [ 'r.site_id', '=', $site_id ];
            }
            if($website_id){
                if($website_id == -1){
                    $condition[] = ['s.website_id', '=', 0];
                }else{
                    $condition[] = ['s.website_id', '=', $website_id];
                }
            }

            $apply_state = input('apply_state', '');
            if ($apply_state) {
                $condition[] = [ 'r.apply_state', '=', $apply_state ];
            }

            if ($site_name) {
                $condition[] = [ 's.site_name', 'like', '%' . $site_name . '%' ];
            }
            if ($category_id) {
                $condition[] = [ 's.category_id', '=', $category_id ];
            }
            if ($group_id) {
                $condition[] = [ 'r.shop_group_id', '=', $group_id ];
            }
            return $model->getShopReopenPageList($condition, $page, $page_size);

        }
    }
}