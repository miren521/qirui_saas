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


namespace addon\platformcoupon\api\controller;

use app\api\controller\BaseApi;
use addon\platformcoupon\model\Platformcoupon as PlatformcouponModel;
use addon\platformcoupon\model\PlatformcouponType as PlatformcouponTypeModel;
use addon\platformcoupon\model\MemberPlatformcoupon;

/**
 * 优惠券
 */
class Platformcoupon extends BaseApi
{

    /**
     * 优惠券类型信息
     */
    public function typeinfo()
    {
        $platformcoupon_type_id = isset($this->params['platformcoupon_type_id']) ? $this->params['platformcoupon_type_id'] : 0;
        if (empty($platformcoupon_type_id)) {
            return $this->response($this->error('', 'REQUEST_PLATFORMCOUPON_TYPE_ID'));
        }

        $app_type = isset($this->params['app_type']) ? $this->params['app_type'] : 'h5';

        $platformcoupon_model = new PlatformcouponModel();
        $condition = [
            [ 'platformcoupon_type_id', '=', $platformcoupon_type_id ],
            [ 'is_show', '=', 1 ]
        ];

        $platformcoupon_type_model = new PlatformcouponTypeModel();
        $qrcode = $platformcoupon_type_model->qrcode($platformcoupon_type_id, $app_type, 'create');
        $qrcode = $qrcode['data'];

        $info = $platformcoupon_model->getPlatformcouponTypeInfo($condition);
        if (!empty($info['data']) && !empty($qrcode)) {
            $info['data']['qrcode'] = $qrcode['path'];
        }
        return $this->response($info);
    }

    /**
     * 列表信息
     */
    public function memberpage()
    {
        $token = $this->checkToken();
        if ($token['code'] < 0) return $this->response($token);

        $page = isset($this->params['page']) ? $this->params['page'] : 1;
        $page_size = isset($this->params['page_size']) ? $this->params['page_size'] : PAGE_LIST_ROWS;
        $state = isset($this->params['state']) ? $this->params['state'] : 1;//优惠券状态 1已领用（未使用） 2已使用 3已过期
        $is_own = isset($this->params['is_own']) ? $this->params['is_own'] : '';//是否自营

        $platformcoupon_model = new PlatformcouponModel();
        $condition = [
            [ 'npc.member_id', '=', $token['data']['member_id'] ],
            [ 'npc.state', '=', $state ]
        ];
        $list = $platformcoupon_model->getMemberPlatformcouponPageList($condition, $page, $page_size);
        return $this->response($list);
    }

    /**
     * 优惠券类型列表
     */
    public function typelists()
    {

        $platformcoupon_model = new PlatformcouponModel();
        $condition = [
            [ 'status', '=', 1 ],
            [ 'is_show', '=', 1 ],
        ];

        $list = $platformcoupon_model->getPlatformcouponTypeList($condition, "platformcoupon_type_id,platformcoupon_name,money,max_fetch,at_least,end_time,validity_type,fixed_term,use_scenario", "money desc", "");
        return $this->response($list);
    }

    /**
     * 优惠券类型分页列表
     */
    public function typepagelists()
    {
        $page = isset($this->params['page']) ? $this->params['page'] : 1;
        $page_size = isset($this->params['page_size']) ? $this->params['page_size'] : PAGE_LIST_ROWS;

        $platformcoupon_model = new PlatformcouponModel();
        $condition = [
            [ 'status', '=', 1 ],
            [ 'is_show', '=', 1 ],
        ];

        $list = $platformcoupon_model->getPlatformcouponTypePageList($condition, $page, $page_size);
        return $this->response($list);
    }

    /**
     * 获取优惠券
     * @return false|string
     */
    public function receive()
    {
        $token = $this->checkToken();
        if ($token['code'] < 0) return $this->response($token);

        $platformcoupon_type_id = isset($this->params['platformcoupon_type_id']) ? $this->params['platformcoupon_type_id'] : 0;
        $get_type = isset($this->params['get_type']) ? $this->params['get_type'] : 2;//获取方式:1订单2.直接领取3.活动领取

        if (empty($platformcoupon_type_id)) {
            return $this->response($this->error('', 'REQUEST_COUPON_TYPE_ID'));
        }

        $platformcoupon_model = new PlatformcouponModel();
        $res = $platformcoupon_model->receivePlatformcoupon($platformcoupon_type_id, $token['data']['member_id'], $get_type);

        $res['data'] = [];
        //判断一下用户是否拥有当前优惠券
        $coupon_result = $platformcoupon_model->getPlatformcouponInfo([['platformcoupon_type_id', '=', $platformcoupon_type_id], ['member_id', '=', $token['data']['member_id']]], 'platformcoupon_type_id');
        $coupon = $coupon_result['data'];
        $res['data']['is_exist'] = empty($coupon) ? 0 : 1;

        return $this->response($res);
    }

    /**
     * 会员优惠券数量
     * @return string
     */
    public function num()
    {
        $token = $this->checkToken();
        if ($token['code'] < 0) return $this->response($token);

        $state = $this->params['state'] ?? 1;
        $platformcoupon_model = new MemberPlatformcoupon();

        $count = $platformcoupon_model->getMemberPlatformcouponNum($token['data']['member_id'], $state);
        return $this->response($count);
    }

    /**
     * 是否可以领取
     */
    public function receivedNum()
    {
        $token = $this->checkToken();
        if ($token['code'] < 0) return $this->response($token);

        $platformcoupon_type_id = isset($this->params['platformcoupon_type_id']) ? $this->params['platformcoupon_type_id'] : 0;

        $platformcoupon_model = new MemberPlatformcoupon();
        $res = $platformcoupon_model->receivedNum($platformcoupon_type_id, $this->member_id);
        return $this->response($res);
    }



}