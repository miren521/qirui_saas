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


namespace addon\city\model;

use app\model\BaseModel;
use app\model\web\WebSite;


class CityWithdraw extends BaseModel
{

    public function withdraw($data)
    {
        if($data['money'] <= 0){
            return $this->error('','转账金额不能小于等于0');
        }

        //获取站点信息
        $website_model = new WebSite();
        $website_info = $website_model->getWebSite([['site_id','=',$data['website_id']]],'site_area_name,account');
        if($website_info['data']['account'] < $data['money']){
            return $this->error('','转账金额不能大于分站城市可用余额');
        }

        $data['withdraw_no'] = date('YmdHi').rand(1000,9999);
        $data['website_name'] = $website_info['data']['site_area_name'];
        $data['apply_time'] = time();
        $data['payment_time'] = time();
        $data['status'] = 2;

        model('website_withdraw')->startTrans();
        try{
            $res = model('website_withdraw')->add($data);
            //增加转账金额
            model('website')->setInc([['site_id','=',$data['website_id']]],'account_withdraw',$data['money']);
            //添加账户流水记录
            $website_model->addWebsiteAccount($data['website_id'],'account', '-'.$data['money'], "withdraw", $res, '平台转账，账单编号'.$data['withdraw_no']);

            model('website_withdraw')->commit();
            return $this->success();
        }catch (\Exception $e){
            model('website_withdraw')->rollback();
            return $this->error('',$e->getMessage());
        }

    }

    /**
     * 获取转账分页列表
     * @param array $condition
     * @param number $page
     * @param string $page_size
     * @param string $order
     * @param string $field
     */
    public function getCityWithdrawPageList($condition = [], $page = 1, $page_size = PAGE_LIST_ROWS, $order = '', $field = '*')
    {

        $list = model('website_withdraw')->pageList($condition, $field, $order, $page, $page_size);
        return $this->success($list);
    }
}