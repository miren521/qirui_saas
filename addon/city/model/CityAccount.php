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


class CityAccount extends BaseModel
{


    /**
     * 获取账户分页列表
     * @param array $condition
     * @param number $page
     * @param string $page_size
     * @param string $order
     * @param string $field
     */
    public function getCityAccountPageList($condition = [], $page = 1, $page_size = PAGE_LIST_ROWS, $order = '', $field = '*')
    {

        $list = model('account')->pageList($condition, $field, $order, $page, $page_size);
        return $this->success($list);
    }

}