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


namespace app\api\controller;

use app\model\goods\VirtualGoods as VirtualGoodsModel;

class Virtualgoods extends BaseApi
{
	
	/**
	 * 我的虚拟商品
	 */
	public function lists()
	{
		$token = $this->checkToken();
		if ($token['code'] < 0) return $this->response($token);
		$virtual_goods_model = new VirtualGoodsModel();
		$condition = array(
			[ "member_id", "=", $this->member_id ],
		);
		$is_verify = isset($this->params['is_verify']) ? $this->params['is_verify'] : 'all';//是否已核销
		if ($is_verify != "all") {
			$condition[] = [ "is_verify", "=", $is_verify ];
		}
		
		$page_index = isset($this->params['page']) ? $this->params['page'] : 1;
		$page_size = isset($this->params['page_size']) ? $this->params['page_size'] : PAGE_LIST_ROWS;
		$res = $virtual_goods_model->getVirtualGoodsPageList($condition, $page_index, $page_size, "id desc");
		return $this->response($res);
	}
	
}