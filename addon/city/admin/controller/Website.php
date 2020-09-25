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


namespace addon\city\admin\controller;

use app\admin\controller\BaseAdmin;
use app\model\web\WebSite as WebsiteModel;
use app\model\system\Address as AddressModel;
use addon\city\model\CityAccount;

/**
 * 跳转页
 */
class Website extends BaseAdmin
{
	/**
	 * 首页跳转
	 */
	public function lists()
	{
		
		$website_model = new WebsiteModel();
		
		if (request()->isAjax()) {
			
			$condition[] = [ 'site_id', '>', 0 ];
			$page = input('page', 1);
			$page_size = input('page_size', PAGE_LIST_ROWS);
			
			$site_area_name = input('site_area_name', '');
			if ($site_area_name) {
				$condition[] = [ 'site_area_name', 'like', '%' . $site_area_name . '%' ];
			}
			
			$web_phone = input('web_phone', '');
			if ($web_phone) {
				$condition[] = [ 'web_phone', 'like', '%' . $web_phone . '%' ];
			}
			
			$status = input('status', 0);
			if ($status) {
				$condition[] = [ 'status', '=', $status ];
			}
			
			$start_time = input('start_time', '');
			$end_time = input('end_time', '');
			if (!empty($start_time) && empty($end_time)) {
				$condition[] = [ "create_time", ">=", strtotime($start_time) ];
			} elseif (empty($start_time) && !empty($end_time)) {
				$condition[] = [ "create_time", "<=", strtotime($end_time) ];
			} elseif (!empty($start_time) && !empty($end_time)) {
				$condition[] = [ "create_time", "between", [ strtotime($start_time), strtotime($end_time) ] ];
			}
			
			$list = $website_model->getWebsitePageList($condition, $page, $page_size, 'status desc,site_id desc');
			return $list;
		} else {
			
			return $this->fetch('website/lists');
		}
		
	}
	
	/**
	 * 添加城市分站
	 */
	public function add()
	{
		$website_model = new WebsiteModel();
		if (request()->isAjax()) {
			
			$data = [
				'title' => input('title', ''),
				'logo' => input('logo', ''),
				'desc' => input('desc', ''),
				'keywords' => input('keywords', ''),
				'web_address' => input('web_address', ''),
				'web_qrcode' => input('web_qrcode', ''),
				'web_email' => input('web_email', ''),
				'web_phone' => input('web_phone', ''),
				'web_qq' => input('web_qq', ''),
				'web_weixin' => input('web_weixin', ''),
				'wap_status' => input('wap_status', ''),
				'wap_domain' => input('wap_domain', ''),
				'site_area_id' => input('site_area_id', ''),
				'site_area_name' => input('site_area_name', ''),
				'username' => input('username', ''),
				'shop_rate' => input('shop_rate', ''),
				'order_rate' => input('order_rate', ''),
				'settlement_bank_account_name' => input('settlement_bank_account_name', ''),
				'settlement_bank_account_number' => input('settlement_bank_account_number', ''),
				'settlement_bank_name' => input('settlement_bank_name', ''),
				'settlement_bank_address' => input('settlement_bank_address', ''),
			];
			
			$user_data = [
				'username' => input('username', ''),
				'password' => data_md5(input('password', '')),
			];
			
			$res = $website_model->addWebsite($data, $user_data);
			return $res;
			
		} else {
			
			//查询省级数据列表
			$address_model = new AddressModel();
			$list = $address_model->getAreaList([ [ "pid", "=", 0 ], [ "level", "=", 1 ] ]);
			$this->assign("province_list", $list["data"]);
			
			return $this->fetch('website/add');
		}
	}
	
	/**
	 * 编辑城市分站
	 */
	public function edit()
	{
		$website_model = new WebsiteModel();
		
		if (request()->isAjax()) {
			
			$data = [
				'title' => input('title', ''),
				'logo' => input('logo', ''),
				'desc' => input('desc', ''),
				'keywords' => input('keywords', ''),
				'web_address' => input('web_address', ''),
				'web_qrcode' => input('web_qrcode', ''),
				'web_email' => input('web_email', ''),
				'web_phone' => input('web_phone', ''),
				'web_qq' => input('web_qq', ''),
				'web_weixin' => input('web_weixin', ''),
				'wap_domain' => input('wap_domain', ''),
				'shop_rate' => input('shop_rate', ''),
				'order_rate' => input('order_rate', ''),
				'settlement_bank_account_name' => input('settlement_bank_account_name', ''),
				'settlement_bank_account_number' => input('settlement_bank_account_number', ''),
				'settlement_bank_name' => input('settlement_bank_name', ''),
				'settlement_bank_address' => input('settlement_bank_address', ''),
			];
			
			$website_id = input('website_id');
			$condition[] = [ 'site_id', '=', $website_id ];
			$res = $website_model->setWebSite($data, $condition);
			return $res;
			
		} else {
			
			$website_id = input('website_id');
			//获取城市分站信息
			$city_info = $website_model->getWebSite([ [ 'site_id', '=', $website_id ] ]);
			$this->assign('city_info', $city_info);
			
			//查询省级数据列表
			$address_model = new AddressModel();
			$list = $address_model->getAreaList([ [ "pid", "=", 0 ], [ "level", "=", 1 ] ]);
			$this->assign("province_list", $list["data"]);
			
			return $this->fetch('website/edit');
		}
	}
	
	/**
	 * 删除分站
	 */
	public function delete()
	{
		$website_model = new WebSiteModel();
		
		$website_id = input('website_id', '');
		
		return $website_model->deleteWebsite($website_id);
	}
	
	/**
	 * 冻结分站
	 */
	public function frozen()
	{
		$website_model = new WebSiteModel();
		
		$website_id = input('website_id', '');
		
		return $website_model->frozenWebsite($website_id);
	}
	
	/**
	 * 解冻分站
	 */
	public function unfrozen()
	{
		$website_model = new WebSiteModel();
		
		$website_id = input('website_id', '');
		
		return $website_model->unfrozenWebsite($website_id);
	}
	
	/**
	 * 城市分站详情
	 */
	public function detail()
	{
		$website_model = new WebsiteModel();
		
		$website_id = input('website_id');
		//获取城市分站信息
		$city_info = $website_model->getWebSite([ [ 'site_id', '=', $website_id ] ]);
		$this->assign('city_info', $city_info['data']);
		
		$this->forthMenu([ 'website_id' => $website_id ]);
		return $this->fetch('website/detail');
	}
	
	/**
	 * 城市分站账户面板
	 */
	public function dashboard()
	{
		$city_account_model = new CityAccount();
		
		$website_id = input('website_id');
		//站点信息
		$website_model = new WebsiteModel();
		$website_info = $website_model->getWebSite([ [ 'site_id', '=', $website_id ] ], 'account,account_withdraw,account_shop,account_order');
		$total_account = $website_info['data']['account'] + $website_info['data']['account_withdraw'];
		$this->assign('total_account', number_format($total_account, 2, '.', ''));
		$this->assign('website_info', $website_info['data']);
		
		//账户收支
		if (request()->isAjax()) {
			
			$page = input('page', 1);
			$page_size = input('page_size', PAGE_LIST_ROWS);
			
			$condition[] = [ 'site_id', '=', $website_id ];
			$type = input('type', '');//收支类型（1收入  2支出）
			if (!empty($type)) {
				switch ($type) {
					case 1:
						$condition[] = [ 'account_data', '>', 0 ];
						break;
					case 2:
						$condition[] = [ 'account_data', '<', 0 ];
						break;
				}
			}
			$start_time = input('start_time', '');
			$end_time = input('end_time', '');
			if (!empty($start_time) && empty($end_time)) {
				$condition[] = [ 'create_time', '>=', $start_time ];
			} elseif (empty($start_time) && !empty($end_time)) {
				$condition[] = [ 'create_time', '<=', $end_time ];
			} elseif (!empty($start_time) && !empty($end_time)) {
				$condition[] = [ 'create_time', 'between', [ $start_time, $end_time ] ];
			}
			$field = 'account_no,site_id,account_type,account_data,from_type,type_name,relate_tag,create_time,remark';
			return $city_account_model->getCityAccountPageList($condition, $page, $page_size, 'id desc', $field);
		} else {
			
			$this->assign('website_id', $website_id);
			$this->forthMenu([ 'website_id' => $website_id ]);
			return $this->fetch("website/dashboard");
		}
		
		
	}
}