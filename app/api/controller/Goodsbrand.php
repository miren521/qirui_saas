<?php

namespace app\api\controller;

use app\model\goods\GoodsBrand as GoodsBrandModel;

/**
 * 商品品牌
 * Class Goodsbrand
 * @package app\api\controller
 */
class Goodsbrand extends BaseApi
{
	/**
	 * 分页列表信息
	 * @return string
	 */
	public function page()
	{
		$page = isset($this->params['page']) ? $this->params['page'] : 1;
		$page_size = isset($this->params['page_size']) ? $this->params['page_size'] : PAGE_LIST_ROWS;
		$site_id = isset($this->params['site_id']) ? $this->params['site_id'] : 0;
		$goods_brand_model = new GoodsBrandModel();
		$condition = [
			['ngb.site_id', '=', $site_id],
            ['ngb.is_recommend', '=', 1]
		];
		$list = $goods_brand_model->getBrandPageList($condition, $page, $page_size, 'ngb.sort desc,ngb.create_time desc', 'ngb.brand_id, ngb.brand_name, ngb.brand_initial,ngb.site_id, ngb.image_url');
		return $this->response($list);
	}
}
