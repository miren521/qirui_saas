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


namespace addon\supply\model;

use app\model\BaseModel;

/**
 * 供应商保证金（预存款）
 */
class SupplyDeposit extends BaseModel
{
    /**
     * 添加保证金(不考虑审核情况)
     * @param $data
     * @return array
     */
    public function addDeposit($data)
    {
        $res = model("supply_deposit")->add($data);
        return $this->success($res);
    }

    /**
     * 修改保证金(不考虑审核)
     * @param $data
     * @param $where
     * @return array
     */
    public function editDeposit($data, $where)
    {
        $res = model('supply_deposit')->update($data, $where);
        return $this->success($res);
    }

    /**
     * 审核通过保证金
     * @param $id
     * @param $site_id
     * @return array
     */
    public function auditDeposit($id, $site_id)
    {
        $supply_deposit = model('supply_deposit')->getInfo([['id', '=', $id], ['site_id', '=', $site_id]], 'money');
        $res = model('supply_deposit')->update(
            ['status' => 1, 'audit_time' => time()],
            [['id', '=', $id], ['site_id', '=', $site_id]]
        );
        if ($res) {
            //修改供应商保证金字段
            model("supplier")->setInc(
                [['supplier_site_id', '=', $site_id]],
                'bond',
                $supply_deposit['money']
            );
        }
        return $this->success($res);
    }

    /**
     * 获取供应商保证金信息
     * @param $where
     * @param string $field
     * @return array
     */
    public function getDepositInfo($where, $field = '*')
    {
        $res = model('supply_deposit')->getInfo($where, $field);
        return $this->success($res);
    }

    /**
     * 获取供应商保证金列表
     * @param array $where
     * @param string $field
     * @param string $order
     * @param null $limit
     * @return array
     */
    public function getDepositList($where = [], $field = '*', $order = '', $limit = null)
    {
    
        $list = model('supply_deposit')->getList($where, $field, $order, '', '', '', $limit);
        return $this->success($list);
    }

    /**
     * 获取供应商保证金分页列表
     * @param array $where
     * @param int $page
     * @param int $page_size
     * @param string $order
     * @param string $field
     * @return array
     */
    public function getDepositPageList($where = [], $page = 1, $page_size = PAGE_LIST_ROWS, $order = '', $field = '*')
    {
    
        $list = model('supply_deposit')->pageList($where, $field, $order, $page, $page_size);
        return $this->success($list);
    }
}
