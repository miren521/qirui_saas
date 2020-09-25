<?php
// +---------------------------------------------------------------------+
// | NiuCloud | [ WE CAN DO IT JUST NiuCloud ]                |
// +---------------------------------------------------------------------+
// | Copy right 2019-2029 www.niucloud.com                          |
// +---------------------------------------------------------------------+
// | Author | NiuCloud <niucloud@outlook.com>                       |
// +---------------------------------------------------------------------+
// | Repository | https://github.com/niucloud/framework.git          |
// +---------------------------------------------------------------------+

namespace addon\platformcoupon\admin\controller;

use app\admin\controller\BaseAdmin;
use addon\platformcoupon\model\PlatformcouponType as PlatformcouponTypeModel;
use addon\platformcoupon\model\Platformcoupon as PlatformcouponModel;
use app\model\shop\ShopGroup as ShopGroupModel;

/**
 * 优惠券
 * @author Administrator
 *
 */
class Platformcoupon extends BaseAdmin
{
    /**
     * 活动列表
     */
    public function lists()
    {
        if (request()->isAjax()) {
            $page = input('page', 1);
            $page_size = input('page_size', PAGE_LIST_ROWS);
            $platformcoupon_name = input('platformcoupon_name', '');
            $status = input('status', '');

            $condition = [];
            if ($status !== "") {
                $condition[] = ['status', '=', $status];
            }
            $type = input('type');
            if ($type) {
                $condition[] = ['type', '=', $type];
            }
            //类型
            $validity_type = input('validity_type', '');
            if ($validity_type) {
                $start_time = input('start_time', '');
                $end_time = input('end_time', '');
                switch ($validity_type) {

                    case 1: //固定

                        $condition[] = ['end_time', 'between', [$start_time, $end_time]];
                        break;
                    case 2:

                        $condition[] = ['fixed_term', 'between', [$start_time, $end_time]];
                        break;
                }
            }

            $condition[] = ['platformcoupon_name', 'like', '%' . $platformcoupon_name . '%'];
            $order = 'create_time desc';
            $field = '*';

            $platformcoupon_type_model = new PlatformcouponTypeModel();
            $res = $platformcoupon_type_model->getPlatformcouponTypePageList($condition, $page, $page_size, $order, $field);

            //获取优惠券状态
            $platformcoupon_type_status_arr = $platformcoupon_type_model->getPlatformcouponTypeStatus();
            foreach ($res['data']['list'] as $key => $val) {
                $res['data']['list'][$key]['status_name'] = $platformcoupon_type_status_arr[$val['status']];
            }
            return $res;

        } else {
            //优惠券状态
            $platformcoupon_type_model = new PlatformcouponTypeModel();
            $platformcoupon_type_status_arr = $platformcoupon_type_model->getPlatformcouponTypeStatus();
            $this->assign('platformcoupon_type_status_arr', $platformcoupon_type_status_arr);

            //店铺等级
            $shop_group_model = new ShopGroupModel();
            $shop_group = $shop_group_model->getGroupList([],'*');
            $this->assign('group_list',$shop_group['data']);

            return $this->fetch("platformcoupon/lists");
        }
    }

    /**
     * 添加活动
     */
    public function add()
    {

        if (request()->isAjax()) {
            $group_ids = input('group_ids', []);
            $group_ids = implode(',', $group_ids);
            $shop_split_rare = input('shop_split_rare', 0);
            $data = [
                'platformcoupon_name' => input('platformcoupon_name', ''),//优惠券名称
                'money' => input('money', ''),//优惠券面额
                'count' => input('count', ''),//发放数量
                'max_fetch' => input('max_fetch', ''),//最大领取数量
                'at_least' => input('at_least', ''),//满多少元可以使用
                'end_time' => strtotime(input('end_time', '')),//活动结束时间
                'image' => input('image', ''),//优惠券图片
                'validity_type' => input('validity_type', ''),//有效期类型 0固定时间 1领取之日起
                'fixed_term' => input('fixed_term', ''),//领取之日起N天内有效
                'is_show' => input('is_show', 0),//是否允许直接领取 1:是 0：否 允许直接领取，用户才可以在手机端和PC端进行领取，否则只能以活动的形式发放。

                'use_scenario' => input('use_scenario', 1),//使用场景
                'group_ids' => $group_ids,
                'platform_split_rare' => 100 - $shop_split_rare,//平台分担比率
                'shop_split_rare' => input('shop_split_rare', 0),//店铺分担比率
                'create_time' => time()
            ];

            $platformcoupon_type_model = new PlatformcouponTypeModel();
            return $platformcoupon_type_model->addPlatformcouponType($data);
        } else {

            //店铺等级
            $shop_group_model = new ShopGroupModel();
            $shop_group = $shop_group_model->getGroupList([],'*');
            $this->assign('group_list',$shop_group['data']);
            return $this->fetch("platformcoupon/add");
        }
    }

    /**
     * 编辑活动
     */
    public function edit()
    {
        if (request()->isAjax()) {
            $group_ids = input('group_ids', []);
            $group_ids = implode(',', $group_ids);
            $shop_split_rare = input('shop_split_rare', 0);
            $data = [
                'platformcoupon_name' => input('platformcoupon_name', ''),//优惠券名称
                'money' => input('money', ''),//优惠券面额
                'count' => input('count', ''),//发放数量
                'max_fetch' => input('max_fetch', ''),//最大领取数量
                'at_least' => input('at_least', ''),//满多少元可以使用
                'end_time' => strtotime(input('end_time', '')),//活动结束时间
                'image' => input('image', ''),//优惠券图片
                'validity_type' => input('validity_type', ''),//有效期类型 0固定时间 1领取之日起
                'fixed_term' => input('fixed_term', ''),//领取之日起N天内有效
                'is_show' => input('is_show', 0),//是否允许直接领取 1:是 0：否 允许直接领取，用户才可以在手机端和PC端进行领取，否则只能以活动的形式发放。

                'use_scenario' => input('use_scenario', 1),//使用场景
                'group_ids' => $group_ids,
                'platform_split_rare' => 100 - $shop_split_rare,//平台分担比率
                'shop_split_rare' => input('shop_split_rare', 0),//店铺分担比率
            ];

            $platformcoupon_type_id = input('platformcoupon_type_id', 0);

            $platformcoupon_type_model = new PlatformcouponTypeModel();
            return $platformcoupon_type_model->editPlatformcouponType($data, $platformcoupon_type_id);
        } else {

            $platformcoupon_type_id = input('platformcoupon_type_id', 0);
            $this->assign('platformcoupon_type_id', $platformcoupon_type_id);

            $platformcoupon_type_model = new PlatformcouponTypeModel();
            $platformcoupon_type_info = $platformcoupon_type_model->getPlatformcouponTypeInfo($platformcoupon_type_id);
            $this->assign('platformcoupon_type_info', $platformcoupon_type_info['data']);

            //店铺等级
            $shop_group_model = new ShopGroupModel();
            $shop_group = $shop_group_model->getGroupList([],'*');
            $this->assign('group_list',$shop_group['data']);

            return $this->fetch("platformcoupon/edit");
        }
    }

    /**
     * 活动详情
     */
    public function detail()
    {
        $platformcoupon_type_id = input('platformcoupon_type_id', 0);
        $platformcoupon_type_model = new PlatformcouponTypeModel();
        $platformcoupon_type_info = $platformcoupon_type_model->getPlatformcouponTypeInfo($platformcoupon_type_id);

        $this->assign('platformcoupon_type_info', $platformcoupon_type_info['data']);

        //店铺等级
        $shop_group_model = new ShopGroupModel();
        $shop_group = $shop_group_model->getGroupList([],'*');
        $this->assign('group_list',$shop_group['data']);

        return $this->fetch("platformcoupon/detail");
    }


    /**
     * 关闭活动
     */
    public function close()
    {
        if (request()->isAjax()) {
            $platformcoupon_type_id = input('platformcoupon_type_id', 0);
            $platformcoupon_type_model = new PlatformcouponTypeModel();
            return $platformcoupon_type_model->closePlatformcouponType($platformcoupon_type_id);
        }
    }

    /**
     * 删除活动
     */
    public function delete()
    {
        if (request()->isAjax()) {
            $platformcoupon_type_id = input('platformcoupon_type_id', 0);
            $platformcoupon_type_model = new PlatformcouponTypeModel();
            return $platformcoupon_type_model->deletePlatformcouponType($platformcoupon_type_id);
        }
    }

    /**
     * 优惠券领取记录
     * */
    public function receive()
    {
        if (request()->isAjax()) {
            $page = input('page', 1);
            $page_size = input('page_size', PAGE_LIST_ROWS);
            $platformcoupon_type_id = input('platformcoupon_type_id', 0);
            $condition = [];
            $condition[] = ['npc.platformcoupon_type_id', '=', $platformcoupon_type_id];
            $platformcoupon_model = new PlatformcouponModel();
            $res = $platformcoupon_model->getMemberPlatformcouponPageList($condition, $page, $page_size);
            return $res;
        } else {
            $platformcoupon_type_id = input('platformcoupon_type_id', 0);
            $this->assign('platformcoupon_type_id', $platformcoupon_type_id);
            return $this->fetch("platformcoupon/receive");
        }
    }
}