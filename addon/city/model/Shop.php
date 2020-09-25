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

/**
 * 分站关于店铺的处理
 */
class Shop extends BaseModel
{

	/**
	 * 获取店铺分页列表
	 * @param array $condition
	 * @param number $page
	 * @param string $page_size
	 * @param string $order
	 */
	public function getShopPageList($condition = [], $page = 1, $page_size = PAGE_LIST_ROWS, $order = '')
	{
		$field = '
		        s.site_id,s.expire_time,s.site_name,s.username,s.website_id,s.cert_id,s.is_own,s.category_name,s.group_name,s.shop_status,
		        s.create_time,
		        w.site_area_name
    	        ';
		$alias = 's';
        $join = [
            [
                'website w',
                'w.site_id = s.website_id',
                'left'
            ]
        ];
		$list = model('shop')->pageList($condition, $field, $order, $page, $page_size,$alias,$join);
		return $this->success($list);
	}

    /**
     * 获取店铺申请分页列表
     * @param array $condition
     * @param number $page
     * @param string $page_size
     * @param string $order
     */
    public function getShopApplyPageList($condition = [], $page = 1, $page_size = PAGE_LIST_ROWS, $order = '')
    {
        $field = '
		        sa.apply_id,sa.member_id,sa.member_name,sa.cert_id,sa.shop_name,sa.apply_state,sa.apply_message,sa.apply_year,sa.category_name,
		        sa.paying_money_certificate,sa.group_name,sa.audit_time,sa.finish_time,sa.create_time,sa.username,sa.paying_apply,sa.paying_deposit,
		        sa.paying_amount,
		        w.site_area_name
    	        ';
        $alias = 'sa';
        $join = [
            [
                'website w',
                'w.site_id = sa.website_id',
                'left'
            ]
        ];
        $list = model('shop_apply')->pageList($condition, $field, $order, $page, $page_size,$alias,$join);
        return $this->success($list);
    }

    /**
     * 获取店铺申请续签分页列表
     * @param array $condition
     * @param number $page
     * @param string $page_size
     * @param string $order
     * @param string $field
     */
    public function getShopReopenPageList($condition = [], $page = 1, $page_size = PAGE_LIST_ROWS)
    {
        $field = '
                r.*,
                s.site_name,s.category_name,s.group_name,
                w.site_area_name
               ';
        $alias = 'r';
        $join = [
            [
                'shop s',
                'r.site_id = s.site_id',
                'left'
            ],
            [
                'website w',
                'w.site_id = r.website_id',
                'left'
            ]
        ];
        $list = model("shop_reopen")->pageList($condition, $field,
            'r.apply_state,r.create_time desc', $page, $page_size, $alias, $join);
        return $this->success($list);

    }

}