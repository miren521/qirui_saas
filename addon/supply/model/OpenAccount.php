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
 * 供应商入驻费用
 */
class OpenAccount extends BaseModel
{
    /**
     * 获取入驻费用列表
     * @param array $where
     * @param string $field
     * @param string $order
     * @param null $limit
     * @return array
     */
    public function getOpenAccountList($where = [], $field = '*', $order = '', $limit = null)
    {

        $list = model('supply_open_account')->getList($where, $field, $order, '', '', '', $limit);
        return $this->success($list);
    }

    /**
     * 获取入驻费用分页列表
     * @param array $where
     * @param int $page
     * @param int $size
     * @param string $order
     * @param string $field
     * @return array
     */
    public function getOpenAccountPageList($where = [], $page = 1, $size = PAGE_LIST_ROWS, $order = '', $field = '*')
    {

        $list = model('supply_open_account')->pageList($where, $field, $order, $page, $size);
        return $this->success($list);
    }
}
