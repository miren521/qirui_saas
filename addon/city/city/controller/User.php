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


namespace addon\city\city\controller;

use app\model\system\Group;
use app\model\system\Menu;
use app\model\system\User as UserModel;

/**
 * 管理员 控制器
 */
class User extends BaseCity
{
	/**
	 * 用户列表
	 */
	public function user()
	{
		if (request()->isAjax()) {
			$page = input('page', 1);
			$page_size = input('page_size', PAGE_LIST_ROWS);
			$condition = [];
			$condition[] = [ "site_id", "=", $this->site_id ];
			$condition[] = [ "app_module", "=", $this->app_module ];
			$status = input('status', 'all');
			$search_keys = input('search_keys', "");
			if (!empty($search_keys)) {
				$condition[] = [ 'username', 'like', '%' . $search_keys . '%' ];
			}
			if ($status != "all" && $status != "") {
				$condition["status"] = [ "status", "=", $status ];
			}
			$user_model = new UserModel();
			$list = $user_model->getUserPageList($condition, $page, $page_size, "create_time desc");
			return $list;
		} else {
			 
			return $this->fetch('user/user',[],$this->replace);
		}
	}
	
	/**
	 * 添加用户
	 * @return mixed
	 */
	public function addUser()
	{
		if (request()->isAjax()) {
			$username = trim(input("username", ""));
			$password = trim(input("password", ""));
			$group_id = trim(input("group_id", ""));
			$user_model = new UserModel();
			$data = array(
				"username" => $username,
				"password" => $password,
				"group_id" => $group_id,
				"app_module" => $this->app_module,
				"site_id" => $this->site_id
			);
			$result = $user_model->addUser($data);
			return $result;
		} else {
			$group_model = new Group();
			$group_list_result = $group_model->getGroupList([ [ "site_id", "=", $this->site_id ], [ "app_module", "=", $this->app_module ] ]);
			$group_list = $group_list_result["data"];
			$this->assign("group_list", $group_list);
			return $this->fetch('user/add_user',[],$this->replace);
		}
	}
	
	/**
	 * 编辑用户
	 * @return mixed
	 */
	public function editUser()
	{
		$uid = input("uid", 0);
		$user_model = new UserModel();
		if (request()->isAjax()) {
			$group_id = input("group_id", "");
			$status = input("status", "");
			$condition = array(
				[ "uid", "=", $uid ],
				[ "site_id", "=", $this->site_id ],
				[ "app_module", "=", $this->app_module ],
			);
			$data = array(
				"group_id" => $group_id,
				"status" => $status
			);
			$result = $user_model->editUser($data, $condition);
			return $result;
		} else {
			$condition = array(
				[ "uid", "=", $uid ],
				[ "site_id", "=", $this->site_id ],
				[ "app_module", "=", $this->app_module ],
			);
			$user_info_result = $user_model->getUserInfo($condition);
			$user_info = $user_info_result["data"];
			$this->assign("user_info", $user_info);
			$this->assign("uid", $uid);
			$group_model = new Group();
			$group_list_result = $group_model->getGroupList([ [ "site_id", "=", $this->site_id ], [ "app_module", "=", $this->app_module ] ]);
			$group_list = $group_list_result["data"];
			$this->assign("group_list", $group_list);
			return $this->fetch('user/edit_user',[],$this->replace);
		}
	}
	
	/**
	 * 修改用户状态
	 * @return mixed
	 */
	public function modifyUserStatus()
	{
		if (request()->isAjax()) {
			$uid = input('uid', 0);
			$status = input('status', 0);
			$user_model = new UserModel();
			$condition = array(
				[ "uid", "=", $uid ],
				[ "site_id", "=", $this->site_id ],
				[ "app_module", "=", $this->app_module ],
			);
			$result = $user_model->modifyUserStatus($status, $condition);
			return $result;
		}
	}
	
	/**
	 * 重置密码
	 */
	public function modifyPassword()
	{
		if (request()->isAjax()) {
			$password = trim(input('password', '123456'));
			$uid = input('uid', 0);
			$site_id = $this->site_id;
			$user_model = new UserModel();
			return $user_model->modifyUserPassword($password, [ [ 'uid', '=', $uid ], [ 'site_id', '=', $site_id ] ]);
		}
	}
	
	/**
	 * 删除用户
	 */
	public function deleteUser()
	{
		if (request()->isAjax()) {
			$uid = input("uid", 0);
			$user_model = new UserModel();
			$condition = array(
				[ "uid", "=", $uid ],
				[ "app_module", "=", $this->app_module ],
				[ "site_id", "=", $this->site_id ],
			);
			$result = $user_model->deleteUser($condition);
			return $result;
		}
	}
	
	
	/**
	 * 用户组
	 * @return mixed
	 */
	public function group()
	{
		if (request()->isAjax()) {
			$page = input('page', 1);
			$limit = input('page_size', PAGE_LIST_ROWS);
			$condition = array(
				[ 'site_id', "=", $this->site_id ],
				[ "app_module", "=", $this->app_module ]
			);
			$search_keys = input('search_keys', "");
			if (!empty($search_keys)) {
				$condition[] = [ 'desc', 'like', '%' . $search_keys . '%' ];
			}
			$group_model = new Group();
			$list = $group_model->getGroupPageList($condition, $page, $limit);
			return $list;
		} else {
			 
			return $this->fetch('user/group',[],$this->replace);
		}
	}
	
	/**
	 * 添加用户组
	 */
	public function addGroup()
	{
		if (request()->isAjax()) {
			$group_name = input('group_name', '');
			$menu_array = input('menu_array', '');
			$desc = input('desc', '');
			$group_model = new Group();
			$data = array(
				"group_name" => $group_name,
				"site_id" => $this->site_id,
				"app_module" => $this->app_module,
				"group_status" => 1,
				"menu_array" => $menu_array,
				"desc" => $desc,
				"is_system" => 0
			);
			$this->addLog("添加用户组:" . $data['group_name']);
			$result = $group_model->addGroup($data);
			return $result;
		} else {
			$menu_model = new Menu();
			$menu_list = $menu_model->getMenuList([ [ 'app_module', "=", $this->app_module ] ], "title, name, parent, level");
			$menu_tree = list_to_tree($menu_list['data'], 'name', 'parent', 'child_list', '');
			$this->assign('tree_data', $menu_tree);
			return $this->fetch('user/add_group',[],$this->replace);
		}
	}
	
	/**
	 * 编辑用户组
	 */
	public function editGroup()
	{
		$group_model = new Group();
		$group_id = input('group_id', 0);
		if (request()->isAjax()) {
			$group_name = input('group_name', '');
			$menu_array = input('menu_array', '');
			$desc = input('desc', '');
			$data = array(
				"group_name" => $group_name,
				"menu_array" => $menu_array,
				"desc" => $desc,
			);
			$condition = array(
				[ "group_id", "=", $group_id ],
				[ "site_id", "=", $this->site_id ],
				[ "app_module", "=", $this->app_module ]
			);
			$this->addLog("编辑用户组:" . $data['group_name']);
			$result = $group_model->editGroup($data, $condition);
			return $result;
		} else {
			$condition = array(
				[ "group_id", "=", $group_id ],
				[ "site_id", "=", $this->site_id ],
				[ "app_module", "=", $this->app_module ]
			);
			$group_info_result = $group_model->getGroupInfo($condition);
			$group_info = $group_info_result["data"];
			$this->assign("group_info", $group_info);
			$this->assign("group_id", $group_id);
			
			$menu_model = new Menu();
			$menu_list = $menu_model->getMenuList([ [ 'app_module', "=", $this->app_module ] ], "title, name, parent, level");
			$group_array = $group_info['menu_array'];
			$checked_array = explode(',', $group_array);
			foreach ($menu_list['data'] as $key => $val) {
				if (in_array($val['name'], $checked_array)) {
					$menu_list['data'][ $key ]['checked'] = true;
				} else {
					$menu_list['data'][ $key ]['checked'] = false;
				}
			}
			$menu_tree = list_to_tree($menu_list['data'], 'name', 'parent', 'child_list', '');
			$this->assign('tree_data', $menu_tree);
			return $this->fetch('user/edit_group',[],$this->replace);
		}
	}
	
	/**
	 * 修改用户组状态
	 */
	public function modifyGroupStatus()
	{
		if (request()->isAjax()) {
			$group_id = input('group_id', 0);
			$status = input('status', 0);
			$group_model = new Group();
			$condition = array(
				[ "group_id", "=", $group_id ],
				[ "site_id", "=", $this->site_id ],
				[ "app_module", "=", $this->app_module ],
			);
			$result = $group_model->modifyGroupStatus($status, $condition);
			return $result;
		}
	}
	
	/**
	 * 删除用户组
	 */
	public function delGroup()
	{
		if (request()->isAjax()) {
			$group_id = input('group_id', '');
			$condition = array(
				[ "group_id", "=", $group_id ],
				[ "site_id", "=", $this->site_id ],
				[ "app_module", "=", $this->app_module ],
			);
			$this->addLog("删除用户组id:" . $group_id);
			$group_model = new Group();
			$result = $group_model->deleteGroup($condition);
			return $result;
		}
	}
	
	/**
	 * 用户操作日志
	 */
	public function userLog()
	{
		$user_model = new UserModel();
		if (request()->isAjax()) {
			$page = input('page', 1);
			$page_size = input('page_size', PAGE_LIST_ROWS);
			$uid = input('uid', '0');
			$condition = [];
			$condition[] = [ "site_id", "=", $this->site_id ];
			$search_keys = input('search_keys', "");
			if (!empty($search_keys)) {
				$condition[] = [ 'action_name', 'like', '%' . $search_keys . '%' ];
			}
			if ($uid > 0) {
				$condition[] = [ 'uid', '=', $uid ];
			}
			$list = $user_model->getUserlogPageList($condition, $page, $page_size, "create_time desc");
			return $list;
		} else {
			 
			$condition = [];
			$condition[] = [ "site_id", "=", $this->site_id ];
			$condition[] = [ "app_module", "=", $this->app_module ];
			$user_list_result = $user_model->getUserList($condition);
			$user_list = $user_list_result["data"];
			$this->assign("user_list", $user_list);
			return $this->fetch('user/user_log',[],$this->replace);
		}
	}
	
	/**
	 * 批量删除日志
	 */
	public function deleteUserLog()
	{
		$user_model = new UserModel();
		$id = input("id", "");
		$condition = array(
			[ "id", "in", $id ],
			[ "site_id", '=', $this->site_id ]
		);
		$res = $user_model->deleteUserLog($condition);
		return $res;
	}
}