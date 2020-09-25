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
 * 店铺入驻费用
 */
class ShopOpenAccount extends BaseModel
{

	/**
	 * 获取入驻费用列表
	 * @param array $condition
	 * @param string $field
	 * @param string $order
	 * @param string $limit
	 */
	public function getShopOpenAccountList($condition = [], $field = '*', $order = '', $limit = null)
	{
		
		$list = model('shop_open_account')->getList($condition, $field, $order, '', '', '', $limit);
		return $this->success($list);
	}


	/**
	 * 获取入驻费用分页列表
	 * @param array $condition
	 * @param number $page
	 * @param string $page_size
	 * @param string $order
	 * @param string $field
	 */
	public function getShopOpenAccountPageList($condition = [], $page = 1, $page_size = PAGE_LIST_ROWS, $order = '', $field = '*')
	{
		
		$list = model('shop_open_account')->pageList($condition, $field, $order, $page, $page_size);
		return $this->success($list);
	}
	

}