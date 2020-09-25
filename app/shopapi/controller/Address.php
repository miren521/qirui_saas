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


namespace app\shopapi\controller;

use app\model\system\Address as AddressModel;

/**
 * 地址管理
 * @author Administrator
 *
 */
class Address extends BaseApi
{
	
	/**
	 * 基础信息
	 */
	public function info()
	{
		$id = $this->params['id'];
		$address = new AddressModel();
		$info = $address->getAreaInfo($id);
		return $this->response($info);
	}
	
	/**
	 * 列表信息
	 */
	public function lists()
	{
		$pid = isset($this->params['pid']) ? $this->params['pid'] : 0;
		$address = new AddressModel();
		$list = $address->getAreas($pid);
		return $this->response($list);
	}
	
	/**
	 * 树状结构信息
	 */
	public function tree()
	{
		$id = $this->params['id'];
		$address = new AddressModel();
		$tree = $address->getAreas($id);
		return $this->response($tree);
	}
	
	/**
	 * 获取全部城市列表
	 */
	public function city(){
		$address = new AddressModel();
		$data = $address->getAreaList([ ['level', '=', 2], ['status', '=', 1] ], 'id,shortname as title', 'sort asc');
		return $this->response($data);
	}
	
	/**
	 * 根据城市名称获取城市
	 */
	public function cityByName(){
		$name = $this->params['city'] ?? '';
		$address = new AddressModel();
		$data = $address->getAreasInfo([ ['name', 'like', "%{$name}%"], ['level', '=', 2], ['status', '=', 1] ], 'id,shortname as title');
		return $this->response($data);
	}
}