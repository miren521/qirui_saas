<?php
/**
 * Niushop商城系统 - 团队十年电商经验汇集巨献!
 * =========================================================
 * Copy right 2019-2029 山西牛酷信息科技有限公司, 保留所有权利。
 * ----------------------------------------------
 * 官方网址: https://www.niushop.com
 * 这不是一个自由软件！您只能在不用于商业目的的前提下对程序代码进行修改和使用。
 * 任何企业和个人不允许对程序代码以任何形式任何目的再发布。
 * =========================================================
 */

namespace addon\coupon\api\controller;

use app\api\controller\BaseApi;
use addon\coupon\model\Coupon as CouponModel;
use addon\coupon\model\CouponType as CouponTypeModel;
use addon\coupon\model\MemberCoupon;
use think\facade\Db;

/**
 * 优惠券
 */
class Coupon extends BaseApi
{

    /**
     * 优惠券类型信息
     */
    public function typeinfo()
    {
        $coupon_type_id = isset($this->params['coupon_type_id']) ? $this->params['coupon_type_id'] : 0;
        if (empty($coupon_type_id)) {
            return $this->response($this->error('', 'REQUEST_COUPON_TYPE_ID'));
        }

        $app_type = isset($this->params['app_type']) ? $this->params['app_type'] : 'h5';

        $coupon_model = new CouponModel();
        $condition = [
            ['coupon_type_id', '=', $coupon_type_id],
            ['is_show', '=', 1],
        ];

        $coupon_type_model = new CouponTypeModel();
        $qrcode = $coupon_type_model->qrcode($coupon_type_id, $app_type);
        $qrcode = $qrcode['data'];

        $info = $coupon_model->getCouponTypeInfo($condition);
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

        $coupon_model = new CouponModel();
        $condition = [
            ['npc.member_id', '=', $token['data']['member_id']],
            ['npc.state', '=', $state]
        ];
        $list = $coupon_model->getMemberCouponPageList($condition, $page, $page_size);
        return $this->response($list);
    }

    /**
     * 优惠券类型列表
     */
    public function typelists()
    {
        $coupon_model = new CouponModel();
        $condition = [
            ['status', '=', 1],
            ['is_show', '=', 1]
        ];
        $goods_id = $this->params['goods_id'] ??  0;
        if($goods_id > 0){
//            $condition[] = ["goods_ids", "like", ' %,'.$goods_id.',%'];
            $condition[] = ["goods_ids", "exp", Db::raw(" like  "."'%,".$goods_id.",%'"." or goods_type = 1")];
        }
        $site_id = $this->params['site_id'] ??  0;
        if($site_id > 0){
            $condition[] = ['site_id', '=', $site_id];
        }
        $list = $coupon_model->getCouponTypeList($condition, "coupon_type_id,type,site_id,coupon_name,money,discount,max_fetch,at_least,end_time,validity_type,fixed_term,goods_type,site_name", "money desc", "");
        return $this->response($list);
    }

    /**
     * 优惠券类型分页列表
     */
    public function typepagelists()
    {
        $page = isset($this->params['page']) ? $this->params['page'] : 1;
        $page_size = isset($this->params['page_size']) ? $this->params['page_size'] : PAGE_LIST_ROWS;

        $coupon_model = new CouponModel();
        $condition = [
            ['status', '=', 1],
            ['is_show', '=', 1],
        ];

        $list = $coupon_model->getCouponTypePageList($condition, $page, $page_size, 'coupon_type_id desc', 'coupon_type_id,type,site_id,coupon_name,money,discount,max_fetch,at_least,end_time,image,validity_type,fixed_term,status,is_show,goods_type,site_name');
        return $this->response($list);
    }

    /**
     * 获取优惠券
     */
    public function receive()
    {
        $token = $this->checkToken();
        if ($token['code'] < 0) return $this->response($token);

        $coupon_type_id = isset($this->params['coupon_type_id']) ? $this->params['coupon_type_id'] : 0;
        $get_type = isset($this->params['get_type']) ? $this->params['get_type'] : 2;//获取方式:1订单2.直接领取3.活动领取

        if (empty($coupon_type_id)) {
            return $this->response($this->error('', 'REQUEST_COUPON_TYPE_ID'));
        }

        $coupon_model = new CouponModel();
        $res = $coupon_model->receiveCoupon($coupon_type_id, $token['data']['member_id'], $get_type);
        $res['data'] = [];
        //判断一下用户是否拥有当前优惠券
        $coupon_result = $coupon_model->getCouponInfo([['coupon_type_id', '=', $coupon_type_id], ['member_id', '=', $token['data']['member_id']]], 'coupon_id');
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
        $coupon_model = new MemberCoupon();

        $count = $coupon_model->getMemberCouponNum($token['data']['member_id'], $state);
        return $this->response($count);
    }

    /**
     * 是否可以领取
     */
    public function receivedNum()
    {
        $token = $this->checkToken();
        if ($token['code'] < 0) return $this->response($token);

        $coupon_type_id = isset($this->params['coupon_type_id']) ? $this->params['coupon_type_id'] : 0;

        $coupon_model = new MemberCoupon();
        $res = $coupon_model->receivedNum($coupon_type_id, $this->member_id);
        return $this->response($res);
    }
}