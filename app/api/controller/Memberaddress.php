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

use app\model\member\MemberAddress as MemberAddressModel;
use app\model\system\Address;

class Memberaddress extends BaseApi
{
	/**
	 * 添加信息
	 */
	public function add()
	{
		$token = $this->checkToken();
		if ($token['code'] < 0) return $this->response($token);
		
		$data = [
			'member_id' => $token['data']['member_id'],
			'name' => $this->params['name'],
			'mobile' => $this->params['mobile'],
			'telephone' => $this->params['telephone'],
			'province_id' => $this->params['province_id'],
			'city_id' => $this->params['city_id'],
			'district_id' => $this->params['district_id'],
			'community_id' => $this->params['community_id'],
			'address' => $this->params['address'],
			'full_address' => $this->params['full_address'],
			'longitude' => $this->params['longitude'],
			'latitude' => $this->params['latitude'],
			'is_default' => $this->params['is_default']
		];
		$member_address = new MemberAddressModel();
		$res = $member_address->addMemberAddress($data);
		return $this->response($res);
	}
	
	/**
	 * 编辑信息
	 */
	public function edit()
	{
		$token = $this->checkToken();
		if ($token['code'] < 0) return $this->response($token);
		
		$data = [
			'id' => $this->params['id'],
			'member_id' => $token['data']['member_id'],
			'name' => $this->params['name'],
			'mobile' => $this->params['mobile'],
			'telephone' => $this->params['telephone'],
			'province_id' => $this->params['province_id'],
			'city_id' => $this->params['city_id'],
			'district_id' => $this->params['district_id'],
			'community_id' => $this->params['community_id'],
			'address' => $this->params['address'],
			'full_address' => $this->params['full_address'],
			'longitude' => $this->params['longitude'],
			'latitude' => $this->params['latitude'],
			'is_default' => $this->params['is_default']
		];
		$member_address = new MemberAddressModel();
		$res = $member_address->editMemberAddress($data);
		return $this->response($res);
	}
	
	/**
	 * 设置默认地址
	 * @return string
	 */
	public function setdefault()
	{
		$token = $this->checkToken();
		if ($token['code'] < 0) return $this->response($token);
		
		$id = isset($this->params['id']) ? $this->params['id'] : 0;
		if (empty($id)) {
			return $this->response($this->error('', 'REQUEST_ID'));
		}
		$member_address = new MemberAddressModel();
		$res = $member_address->setMemberDefaultAddress($id, $token['data']['member_id']);
		return $this->response($res);
	}
	
	/**
	 * 删除信息
	 */
	public function delete()
	{
		$token = $this->checkToken();
		if ($token['code'] < 0) return $this->response($token);
		
		$id = isset($this->params['id']) ? $this->params['id'] : 0;
		if (empty($id)) {
			return $this->response($this->error('', 'REQUEST_ID'));
		}
		
		$condition = [
			'id' => $id,
			'member_id' => $token['data']['member_id']
		];
		$member_address = new MemberAddressModel();
		$res = $member_address->deleteMemberAddress($condition);
		return $this->response($res);
	}
	
	/**
	 * 基础信息
	 */
	public function info()
	{
		$token = $this->checkToken();
		if ($token['code'] < 0) return $this->response($token);
		
		$id = isset($this->params['id']) ? $this->params['id'] : 0;
		if (empty($id)) {
			return $this->response($this->error('', 'REQUEST_ID'));
		}
		
		$default = isset($this->params['default']) ? $this->params['default'] : 0;
		if ($default) {
			$condition = [
				[ 'is_default', '=', 1 ],
				[ 'member_id', '=', $token['data']['member_id'] ],
			];
		} else {
			$condition = [
				[ 'id', '=', $id ],
				[ 'member_id', '=', $token['data']['member_id'] ],
			];
		}
		
		$member_address = new MemberAddressModel();
		$res = $member_address->getMemberAddressInfo($condition, 'id, member_id, name, mobile, telephone, province_id, district_id, city_id, community_id, address, full_address, longitude, latitude, is_default');
		return $this->response($res);
	}
	
	/**
	 * 分页列表信息
	 */
	public function page()
	{
		$token = $this->checkToken();
		if ($token['code'] < 0) return $this->response($token);
		
		$page = isset($this->params['page']) ? $this->params['page'] : 1;
		$page_size = isset($this->params['page_size']) ? $this->params['page_size'] : PAGE_LIST_ROWS;
		
		$member_address = new MemberAddressModel();
		$list = $member_address->getMemberAddressPageList([ [ 'member_id', '=', $token['data']['member_id'] ] ], $page, $page_size);
		return $this->response($list);
	}
	
	/**
	 * 添加第三方收货地址
	 */
	public function addThreeParties()
	{
		$token = $this->checkToken();
		if ($token['code'] < 0) return $this->response($token);
		
		$address = new Address();
		$province_info = $address->getAreasInfo([ [ 'name', 'like', '%' . $this->params['province'] . '%' ], [ 'level', '=', 1 ] ], 'id');
		if ($province_info['code'] < 0) return $this->response(error('', '地址库中未获取到' . $this->params['province'] . '的信息'));
		$city_info = $address->getAreasInfo([ [ 'name', 'like', '%' . $this->params['city'] . '%' ], [ 'level', '=', 2 ] ], 'id');
		if ($city_info['code'] < 0) return $this->response(error('', '地址库中未获取到' . $this->params['city'] . '的信息'));
		$district_info = $address->getAreasInfo([ [ 'name', 'like', '%' . $this->params['district'] . '%' ], [ 'level', '=', 3 ] ], 'id');
		if ($district_info['code'] < 0) return $this->response(error('', '地址库中未获取到' . $this->params['district'] . '的信息'));
		
		$data = [
			'member_id' => $token['data']['member_id'],
			'name' => $this->params['name'],
			'mobile' => $this->params['mobile'],
			'telephone' => $this->params['telephone'] ?? '',
			'province_id' => $province_info['data']['id'],
			'city_id' => $city_info['data']['id'],
			'district_id' => $district_info['data']['id'],
			'community_id' => $this->params['community_id'] ?? 0,
			'address' => $this->params['address'],
			'full_address' => $this->params['full_address'],
			'longitude' => $this->params['longitude'] ?? '',
			'latitude' => $this->params['longitude'] ?? '',
			'is_default' => $this->params['is_default'] ?? 0
		];
		$member_address = new MemberAddressModel();
		$res = $member_address->addMemberAddress($data);
		return $this->response($res);
	}
}