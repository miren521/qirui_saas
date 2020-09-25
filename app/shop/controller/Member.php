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


use app\model\shop\ShopMember;
use app\model\order\OrderCommon;
/**
 * 店铺会员
 * @package app\shop\controller
 */
class Member extends BaseShop
{
    public function __construct()
    {
        //执行父类构造函数
        parent::__construct();
    }
    
    /**
     * 会员概况
     */
    public function index(){
        $member = new ShopMember();
        
        // 累计会员数
        $total_count = $member->getMemberCount([ ['site_id', '=', $this->site_id] ]);
        // 今日新增数
        $newadd_count = $member->getMemberCount([ ['site_id', '=', $this->site_id], ['create_time', 'between', [ date_to_time(date('Y-m-d 00:00:00')), time() ] ] ]);
        // 累计关注数
        $subscribe_count = $member->getMemberCount([ ['site_id', '=', $this->site_id], ['is_subscribe', '=', 1] ]);
        // 已购会员数
        $buyed_count = $member->getPurchasedMemberCount($this->site_id);
        
        $this->assign('data', [
            'total_count' => $total_count['data'],
            'newadd_count' => $newadd_count['data'],
            'subscribe_count' => $subscribe_count['data'],
            'buyed_count' => $buyed_count['data']
        ]);
        
        return $this->fetch("member/index");
    }
    
    /**
     * 获取区域会员数量
     */
    public function areaCount(){
        if (request()->isAjax()){
            $member = new ShopMember();
            $handle = input('handle', false);
            $res = $member->getMemberCountByArea($this->site_id, $handle);
            return $res;
        }
    }
    
    /**
     * 店铺会员列表
     */
    public function lists(){
        $member = new ShopMember();
        if (request()->isAjax()) {
            $page_index = input('page', 1);
            $page_size = input('limit', PAGE_LIST_ROWS);
            $search_text = input('search_text', '');
            $search_text_type = input('search_text_type', 'nickname');
            $start_date = input('start_date', '');
            $end_date = input('end_date', '');
            
            $condition = [
                ['nsm.site_id', '=', $this->site_id]
            ];
            $condition[] = [ $search_text_type, 'like', "%" . $search_text . "%" ];
            // 关注时间
            if ($start_date != '' && $end_date != '') {
                $condition[] = [ 'nsm.subscribe_time', 'between', [ strtotime($start_date), strtotime($end_date) ] ];
            } else if ($start_date != '' && $end_date == '') {
                $condition[] = [ 'nsm.subscribe_time', '>=', strtotime($start_date) ];
            } else if ($start_date == '' && $end_date != '') {
                $condition[] = [ 'nsm.subscribe_time', '<=', strtotime($end_date) ];
            }
            $list = $member->getShopMemberPageList($condition, $page_index, $page_size, 'nsm.subscribe_time desc');
            return $list;
        } else {
            return $this->fetch("member/lists");
        }
    }
    
    /**
     * 会员详情
     */
    public function detail(){
        $member_id = input('member_id', 0);
        
        
        $member = new ShopMember();
        $condition = [
            ['nsm.member_id', '=', $member_id],
            ['nsm.site_id', '=', $this->site_id]
        ];
        $join = [
            [
                'member nm',
                'nsm.member_id = nm.member_id',
                'inner'
            ],
        ];
        $field = 'nm.member_id, nm.source_member, nm.username, nm.nickname, nm.mobile, nm.email, nm.headimg, nm.status, nsm.subscribe_time, nsm.site_id, nsm.is_subscribe';
        $info = $member->getMemberInfo($condition, $field, 'nsm', $join);
        if ($info['code'] < 0) $this->error($info['message']);
        $this->assign('info', $info['data']);
        return $this->fetch("member/detail");
    }
    
    /**
     * 获取会员订单列表
     */
    public function orderList(){
        if (request()->isAjax()) {
            $member_id = input('member_id', 0);
            $page_index = input('page', 1);
            $page_size = input('limit', PAGE_LIST_ROWS);
            
            $condition = [
                ['member_id', '=', $member_id],
                ['site_id', '=', $this->site_id]
            ];
            
            $field = 'order_id,order_no,order_name,order_money,pay_money,balance_money,order_type_name,order_status_name,create_time';
            $order = new OrderCommon();
            $list = $order->getMemberOrderPageList($condition, $page_index, $page_size, 'order_id desc', $field);
            return $list;
        }
    }
}