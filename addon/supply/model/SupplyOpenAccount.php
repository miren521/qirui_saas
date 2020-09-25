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
 * 供货商入驻费用
 */
class SupplyOpenAccount extends BaseModel
{
    /**
     * 获取入驻费用列表
     * @param array $condition
     * @param string $field
     * @param string $order
     * @param null $limit
     * @return array
     */
    public function getSupplyOpenAccountList($condition = [], $field = '*', $order = '', $limit = null)
    {

        $list = model('supply_open_account')->getList($condition, $field, $order, '', '', '', $limit);
        return $this->success($list);
    }


    /**
     * 获取入驻费用分页列表
     * @param array $condition
     * @param int $page
     * @param int $page_size
     * @param string $order
     * @param string $field
     * @return array
     */
    public function getSupplyOpenAccountPageList($condition = [], $page = 1, $page_size = PAGE_LIST_ROWS, $order = '', $field = '*')
    {

        $list = model('supply_open_account')->pageList($condition, $field, $order, $page, $page_size);
        return $this->success($list);
    }
}
