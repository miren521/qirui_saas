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


namespace app\model\shop;

use app\model\BaseModel;
/**
 * 店铺保证金（预存款）
 */
class ShopDeposit extends BaseModel
{
    /**
     * 添加保证金(不考虑审核情况)
     * @param unknown $data
     */
    public function addDeposit($data)
    {
        $res = model("shop_deposit")->add($data);
        return $this->success($res);
    }
    
    /**
     * 修改保证金(不考虑审核)
     * @param array $data
     * @param array $condition
     */
    public function editDeposit($data, $condition)
    {
        $res = model('shop_deposit')->update($data, $condition);
        return $this->success($res);
    }
    
    /**
     * 审核通过保证金
     * @param unknown $id
     * @param unknown $site_id
     */
    public function auditDeposit($id, $site_id)
    {
        $shop_deposit = model('shop_deposit')->getInfo([['id', '=', $id], ['site_id', '=', $site_id]], 'money');
        $res = model('shop_deposit')->update(['status' => 1, 'audit_time' => time()], [['id', '=', $id], ['site_id', '=', $site_id]]);
        if($res)
        {
            //修改店铺保证金字段
            model("shop")->setInc([['site_id', '=', $site_id]], 'shop_baozhrmb', $shop_deposit['money']);
        }
        return $this->success($res);
    }
    
    /**
     * 获取店铺保证金信息
     * @param unknown $condition
     * @param string $field
     */
    public function getShopDepositInfo($condition, $field = '*')
    {
        $res = model('shop_deposit')->getInfo( $condition, $field);
        return $this->success($res);
    }
    
    /**
     * 获取店铺保证金列表
     * @param array $condition
     * @param string $field
     * @param string $order
     * @param string $limit
     */
    public function getShopDepositList($condition = [], $field = '*', $order = '', $limit = null)
    {
    
        $list = model('shop_deposit')->getList($condition, $field, $order, '', '', '', $limit);
        return $this->success($list);
    }
    
    /**
     * 获取店铺保证金分页列表
     * @param array $condition
     * @param number $page
     * @param string $page_size
     * @param string $order
     * @param string $field
     */
    public function getShopDepositPageList($condition = [], $page = 1, $page_size = PAGE_LIST_ROWS, $order = '', $field = '*')
    {
    
        $list = model('shop_deposit')->pageList($condition, $field, $order, $page, $page_size);
        return $this->success($list);
    }
    
}