<?php
/**
 * Shopgroup.php
 * Niushop商城系统 - 团队十年电商经验汇集巨献!
 * =========================================================
 * Copy right 2015-2025 山西牛酷信息科技有限公司, 保留所有权利。
 * ----------------------------------------------
 * 官方网址: https://www.niushop.com
 * 这不是一个自由软件！您只能在不用于商业目的的前提下对程序代码进行修改和使用。
 * 任何企业和个人不允许对程序代码以任何形式任何目的再发布。
 * =========================================================
 * @author : niuteam
 * @date : 2015.1.17
 * @version : v1.0.0.0
 */

namespace app\api\controller;

use app\model\shop\ShopGroup as ShopGroupModel;

/**
 * 店铺等级
 * Class Shopcategory
 * @package app\api\controller
 */
class Shopgroup extends BaseApi
{
	
	public function page()
	{
		$page = isset($this->params['page']) ? $this->params['page'] : 1;
		$page_size = isset($this->params['page_size']) ? $this->params['page_size'] : PAGE_LIST_ROWS;
		$condition = [
			[ 'is_own', '=', 1 ]
		];
		$order = 'is_own asc,fee asc';
		$field = 'group_id,group_name,fee,remark';
		$shop_group_model = new ShopGroupModel();
		$list = $shop_group_model->getGroupPageList($condition, $page, $page_size, $order, $field);
		return $this->response($list);
	}
	
}