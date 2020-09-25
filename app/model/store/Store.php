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


namespace app\model\store;


use app\model\shop\Shop;
use app\model\system\Group;
use think\facade\Cache;
use app\model\BaseModel;
use app\model\system\Cron;
use think\facade\Db;

/**
 * 门店管理
 */
class Store extends BaseModel
{
	
	/**
	 * 添加门店
	 * @param unknown $data
	 */
	public function addStore($data, $user_data = [], $is_store = 0)
	{
		$site_id = isset($data['site_id']) ? $data['site_id'] : '';
		if ($site_id === '') {
			return $this->error('', 'REQUEST_SITE_ID');
		}
		$data['create_time'] = time();
		
		$shop_model = new Shop();
		$shop_info_result = $shop_model->getShopInfo([ [ "site_id", "=", $site_id ] ], "site_name");
		$shop_info = $shop_info_result["data"];
		$data["site_name"] = $shop_info["site_name"];
		model('store')->startTrans();
		
		try {
			if ($is_store == 1) {
				$data['username'] = $is_store == 1 ? $user_data['username'] : '';
				$data['store_id'] = model("site")->add([ 'site_type' => 'store' ]);
				$store_id = model('store')->add($data);
				Cache::tag("store")->clear();
				//添加系统用户组
				$group = new Group();
				$group_data = [
					'site_id' => $store_id,
					'app_module' => 'store',
					'group_name' => '管理员组',
					'is_system' => 1,
					'create_time' => time()
				];
				$group_id = $group->addGroup($group_data)['data'];
				
				//用户检测
				if (empty($user_data['username'])) {
					model("store")->rollback();
					return $this->error('', 'USER_NOT_EXIST');
				}
				$user_count = model("user")->getCount([ [ 'username', '=', $user_data['username'] ], [ 'app_module', '=', 'store' ] ]);
				if ($user_count > 0) {
					model("store")->rollback();
					return $this->error('', 'USERNAME_EXISTED');
				}
				
				//添加用户
				$data_user = [
					'app_module' => 'store',
					'app_group' => 0,
					'is_admin' => 1,
					'group_id' => $group_id,
					'group_name' => '管理员组',
					'site_id' => $store_id
				];
				$user_info = array_merge($data_user, $user_data);
				model("user")->add($user_info);
			} else {
				$store_id = model('store')->add($data);
				Cache::tag("store")->clear();
			}
			event("AddStore", [ 'store_id' => $store_id ]);
			model('store')->commit();
			return $this->success($store_id);
		} catch (\Exception $e) {
			
			model('store')->rollback();
			return $this->error('', $e->getMessage());
		}
		
	}
	
	/**
	 * 修改门店
	 * @param unknown $data
	 * @return multitype:string
	 */
	public function editStore($data, $condition)
	{
		$check_condition = array_column($condition, 2, 0);
		$site_id = isset($check_condition['site_id']) ? $check_condition['site_id'] : '';
		if ($site_id === '') {
			return $this->error('', 'REQUEST_SITE_ID');
		}
		$data["modify_time"] = time();
		$res = model('store')->update($data, $condition);
		Cache::tag("store")->clear();
		return $this->success($res);
	}
	
	/**
	 * 删除门店
	 * @param unknown $condition
	 */
	public function deleteStore($condition)
	{
		$check_condition = array_column($condition, 2, 0);
		$site_id = isset($check_condition['site_id']) ? $check_condition['site_id'] : '';
		if ($site_id === '') {
			return $this->error('', 'REQUEST_SITE_ID');
		}
		$res = model('store')->delete($condition);
		Cache::tag("store")->clear();
		return $this->success($res);
	}
	
	/**
	 * @param $condition
	 * @param $is_frozen
	 */
	public function frozenStore($condition, $is_frozen)
	{
		$check_condition = array_column($condition, 2, 0);
		$site_id = isset($check_condition['site_id']) ? $check_condition['site_id'] : '';
		if ($site_id === '') {
			return $this->error('', 'REQUEST_SITE_ID');
		}
		$res = model('store')->update([ 'is_frozen' => $is_frozen == 1 ? 0 : 1 ], $condition);
		Cache::tag("store")->clear();
		return $this->success($res);
	}
	
	/**
	 * 获取门店信息
	 * @param array $condition
	 * @param string $field
	 */
	public function getStoreInfo($condition, $field = '*')
	{
		$data = json_encode([ $condition, $field ]);
		$cache = Cache::get("store_" . $data);
		if (!empty($cache)) {
			return $this->success($cache);
		}
		$res = model('store')->getInfo($condition, $field);
		Cache::tag("store")->set("store_" . $data, $res);
		return $this->success($res);
	}
	
	/**
	 * 获取门店列表
	 * @param array $condition
	 * @param string $field
	 * @param string $order
	 * @param string $limit
	 */
	public function getStoreList($condition = [], $field = '*', $order = '', $limit = null)
	{
		$data = json_encode([ $condition, $field, $order, $limit ]);
		$cache = Cache::get("store_getStoreList_" . $data);
		if (!empty($cache)) {
			return $this->success($cache);
		}
		$list = model('store')->getList($condition, $field, $order, '', '', '', $limit);
		Cache::tag("store")->set("store_getStoreList_" . $data, $list);
		
		return $this->success($list);
	}
	
	/**
	 * 获取门店分页列表
	 * @param array $condition
	 * @param number $page
	 * @param string $page_size
	 * @param string $order
	 * @param string $field
	 */
	public function getStorePageList($condition = [], $page = 1, $page_size = PAGE_LIST_ROWS, $order = '', $field = '*')
	{
		$data = json_encode([ $condition, $field, $order, $page, $page_size ]);
		$cache = Cache::get("store_getStorePageList_" . $data);
		if (!empty($cache)) {
			return $this->success($cache);
		}
		$list = model('store')->pageList($condition, $field, $order, $page, $page_size);
		Cache::tag("store")->set("store_getStorePageList_" . $data, $list);
		return $this->success($list);
	}

    /**
     * 查询门店  带有距离
     * @param $condition
     * @param $lnglat
     */
    public function getLocationStoreList($condition, $field, $lnglat)
    {
        $order = '';
        if($lnglat['lat'] !== null && $lnglat['lng'] !== null){
            $field .= ' , FORMAT(st_distance ( point ( ' . $lnglat['lng'] . ', ' . $lnglat['lat'] . ' ), point ( longitude, latitude ) ) * 111195 / 1000, 2) as distance ';
            $condition[] = ['', 'exp', Db::raw(' FORMAT(st_distance ( point ( ' . $lnglat['lng'] . ', ' . $lnglat['lat'] . ' ), point ( longitude, latitude ) ) * 111195 / 1000, 2) < 10000')];
            $order = 'distance asc';
        }

        $list = model('store')->getList($condition, $field, $order);
        return $this->success($list);
    }

    /**
     * 查询门店  带有距离
     * @param $condition
     * @param $lnglat
     */
    public function getLocationStorePageList($condition, $page = 1, $page_size = PAGE_LIST_ROWS, $field, $lnglat)
    {
        $order = '';
        if($lnglat['lat'] !== null && $lnglat['lng'] !== null){
            $field .= ',FORMAT(st_distance ( point ( ' . $lnglat['lng'] . ', ' . $lnglat['lat'] . ' ), point ( longitude, latitude ) ) * 111195 / 1000, 2) as distance';
            $condition[] = ['', 'exp', Db::raw(' FORMAT(st_distance ( point ( ' . $lnglat['lng'] . ', ' . $lnglat['lat'] . ' ), point ( longitude, latitude ) ) * 111195 / 1000, 2) < 10000')];
            $order = Db::raw(' st_distance ( point ( ' . $lnglat['lng'] . ', ' . $lnglat['lat'] . ' ), point ( longitude, latitude ) ) * 111195 / 1000 asc');
        }
        $list = model('store')->pageList($condition, $field, $order, $page, $page_size);
        return $this->success($list);
    }
}