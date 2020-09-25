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

namespace addon\fenxiao\model;

use app\model\BaseModel;


/**
 * 分销数据
 */
class FenxiaoData extends BaseModel
{

    /**
     * 分销商账户统计
     * @return array|mixed
     */
    public function getFenxiaoAccountData()
    {
        $field = 'sum(account) as account,sum(account_withdraw) as account_withdraw,sum(account_withdraw_apply) as account_withdraw_apply';
        $res = model('fenxiao')->getInfo([['status','in','1,-1']],$field);
        if($res['account'] == null){
            $res['account'] = '0.00';
        }
        if($res['account_withdraw'] == null){
            $res['account_withdraw'] = '0.00';
        }
        if($res['account_withdraw_apply'] == null){
            $res['account_withdraw_apply'] = '0.00';
        }

        return $res;
    }


    /**
     * 获取分销商申请人数
     * @return mixed
     */
    public function getFenxiaoApplyCount()
    {
        $count = model('fenxiao_apply')->getCount([['status','=',1]]);
        return $count;
    }

    /**
     * 获取分销商人数
     * @return mixed
     */
    public function getFenxiaoCount()
    {
        $count = model('fenxiao')->getCount();
        return $count;
    }

    /**
     * 统计分销订单总金额
     * @return mixed
     */
    public function getFenxiaoOrderSum()
    {
        $field = 'sum(real_goods_money) as real_goods_money';
        $res = model('fenxiao_order')->getInfo([ ['is_refund','=','0'],['is_settlement','=',1] ],$field);
        if($res['real_goods_money'] == null){
            $res['real_goods_money'] = '0.00';
        }
        return $res;
    }

}