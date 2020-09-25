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

use app\model\shop\ShopMember as ShopMemberModel;

class Shopmember extends BaseApi
{
	/**
	 * 添加店铺关注
	 */
	public function add()
	{
		$token = $this->checkToken();
		if ($token['code'] < 0) return $this->response($token);
		
		$site_id = isset($this->params['site_id']) ? $this->params['site_id'] : 0;//站点id
		
		if ($site_id === '') {
			return $this->error('', 'REQUEST_SITE_ID');
		}
		
		$shop_member_model = new ShopMemberModel();
		$res = $shop_member_model->addShopMember($site_id, $token['data']['member_id']);
		return $this->response($res);
		
	}
	
	/**
	 * 取消店铺关注
	 */
	public function delete()
	{
		$token = $this->checkToken();
		if ($token['code'] < 0) return $this->response($token);
		
		$site_id = isset($this->params['site_id']) ? $this->params['site_id'] : 0;//站点id
		
		if ($site_id === '') {
			return $this->error('', 'REQUEST_SITE_ID');
		}
		
		$shop_member_model = new ShopMemberModel();
		$res = $shop_member_model->deleteShopMember($site_id, $token['data']['member_id']);
		return $this->response($res);
	}
	
	/**
	 * 检测是否关注
	 */
	public function issubscribe()
	{
		$token = $this->checkToken();
		if ($token['code'] < 0) return $this->response($token);
		
		$site_id = isset($this->params['site_id']) ? $this->params['site_id'] : 0;//站点id
		
		if ($site_id === '') {
			return $this->error('', 'REQUEST_SITE_ID');
		}
		
		$shop_member_model = new ShopMemberModel();
		$res = $shop_member_model->isSubscribe($site_id, $token['data']['member_id']);
		return $this->response($res);
	}

    /**
     * 获取会员店铺分页列表
     * @return false|string
     */
	public function membershoppages()
	{
		$token = $this->checkToken();
		if ($token['code'] < 0) return $this->response($token);
		
		$page = isset($this->params['page']) ? $this->params['page'] : 1;
		$page_size = isset($this->params['page_size']) ? $this->params['page_size'] : PAGE_LIST_ROWS;
		
		$shop_member_model = new ShopMemberModel();
		$condition = [
			[ 'nsm.member_id', '=', $token['data']['member_id'] ],
			[ 'nsm.is_subscribe', '=', 1 ]
		];
		$res = $shop_member_model->getMemberShopPageList($condition, $page, $page_size, 'nsm.subscribe_time');
		return $this->response($res);
		
	}
	
}