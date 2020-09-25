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

use app\Controller;
use app\model\system\Addon;
use app\model\system\Group;
use app\model\system\Menu;
use app\model\system\User as UserModel;
use app\model\web\Config as ConfigModel;
use app\model\web\WebSite;
use app\model\system\User;

class BaseCity extends Controller
{
	
	protected $init_menu = [];
	protected $crumbs = [];
	protected $crumbs_array = [];
	
	protected $uid;
	protected $user_info;
	protected $url;
	protected $group_info;
	protected $menus;
	protected $site_id = 0;
	protected $app_module = "city";
	protected $addon = '';
    protected $replace;

	public function __construct()
	{
        $this->replace = [
            'CITY_CSS' => __ROOT__. '/addon/city/city/view/public/css',
            'CITY_JS' => __ROOT__. '/addon/city/city/view/public/js',
            'CITY_IMG' => __ROOT__. '/addon/city/city/view/public/img',
        ];

		//执行父类构造函数
		parent::__construct();

		$user = new UserModel();
		//检测基础登录
		$this->uid = $user->uid($this->app_module);
		
		$this->url = request()->parseUrl();
		$this->addon = request()->addon() ? request()->addon() : '';
		$this->user_info = $user->userInfo($this->app_module);
        $this->site_id = $this->user_info["site_id"];
		$this->assign("user_info", $this->user_info);
		$this->checkLogin();

        $website_model = new website();
        $city_info = $website_model->getWebSite([['site_id','=',$this->site_id]]);
        $this->assign('city_info',$city_info['data']);

		//检测用户组
		$this->getGroupInfo();

		if (!$this->checkAuth()) {
			if (!request()->isAjax()) {
				$this->error('权限不足');
			} else {
				echo json_encode(error('', '权限不足'));
				exit;
			}
			
		}

		if (!request()->isAjax()) {
			//获取菜单
			$this->menus = $this->getMenuList();
			$this->initBaseInfo();
		}
	}
	
	/**
	 * 加载基础信息
	 */
	private function initBaseInfo()
	{
		//获取一级权限菜单
		$this->getTopMenu();
		$menu_model = new Menu();
		$info = $menu_model->getMenuInfoByUrl($this->url, $this->app_module, $this->addon);
		if (!empty($info['data'])) {
			$this->getParentMenuList($info['data']['name']);
			$this->assign("menu_info", $info['data']);
		}
		
		//加载网站基础信息
		$website = new WebSite();
		$website_info = $website->getWebSite([ [ 'site_id', '=', 0 ] ], 'title,logo,desc,keywords,web_status,close_reason');
		$this->assign("website", $website_info['data']);
		//加载菜单树
		$init_menu = $this->initMenu($this->menus, '');
		
		// 应用下的菜单特殊处理
		if (!empty($this->crumbs) && $this->crumbs[0]['name'] == 'TOOL_ROOT') {
			
			//如果当前选择了【应用管理】，则只保留【应用管理】菜单
			if ($this->crumbs[1]['name'] == 'PROMOTION_TOOL') {
				foreach ($init_menu as $k => $v) {
					if ($v['selected']) {
						$init_menu[ $k ]['child_list'] = [ $v['child_list']['PROMOTION_TOOL'] ];
						break;
					}
				}
			} else {
				//选择了应用下的某个插件，则移除【应用管理】菜单，显示该插件下的菜单，并且标题名称改为插件名称
				$addon_model = new Addon();
				$addon_info = $addon_model->getAddonInfo([ [ 'name', '=', request()->addon() ] ], 'name,title');
				$addon_info = $addon_info['data'];
				foreach ($init_menu as $k => $v) {
					if ($v['selected']) {
						$this->crumbs[0]['title'] = $addon_info['title'];
						unset($init_menu[ $k ]['child_list']['PROMOTION_TOOL']);
						foreach ($init_menu[ $k ]['child_list'] as $ck => $cv) {
							if ($cv['addon'] != $addon_info['name']) {
								unset($init_menu[ $k ]['child_list'][ $ck ]);
							}
						}
						break;
					}
				}
			}
		}
		
		//加载版权信息
		$config_model = new ConfigModel();
		$copyright = $config_model->getCopyright();
		$this->assign('copyright', $copyright['data']['value']);
		$this->assign("url", $this->url);
		$this->assign("menu", $init_menu);
		
		$this->assign("crumbs", $this->crumbs);
		
	}
	
	/**
	 * layui化处理菜单数据
	 */
	public function initMenu($menus_list, $parent = "")
	{
		$temp_list = [];
		if (!empty($menus_list)) {
			foreach ($menus_list as $menu_k => $menu_v) {
				
				if (in_array($menu_v['name'], $this->crumbs_array)) {
					$selected = true;
				} else {
					$selected = false;
				}
				
				if ($menu_v["parent"] == $parent && $menu_v["is_show"] == 1) {
					$temp_item = array(
						'addon' => $menu_v['addon'],
						'selected' => $selected,
						'url' => addon_url($menu_v['url']),
						'title' => $menu_v['title'],
						'icon' => $menu_v['picture'],
						'icon_selected' => $menu_v['picture_select'],
						'target' => ''
					);
					
					$child = $this->initMenu($menus_list, $menu_v["name"]);//获取下级的菜单
					$temp_item["child_list"] = $child;
					$temp_list[ $menu_v["name"] ] = $temp_item;
				}
			}
		}
		return $temp_list;
	}
	
	/**
	 * 获取上级菜单列表
	 * @param number $menu_id
	 */
	private function getParentMenuList($name = '')
	{
		if (!empty($name)) {
			$menu_model = new Menu();
			$menu_info_result = $menu_model->getMenuInfo([ [ 'name', "=", $name ], [ 'app_module', '=', $this->app_module ] ]);
			$menu_info = $menu_info_result["data"];
			if (!empty($menu_info)) {
				$this->getParentMenuList($menu_info['parent']);
				$menu_info["url"] = addon_url($menu_info["url"]);
				$this->crumbs[] = $menu_info;
				$this->crumbs_array[] = $menu_info['name'];
			}
		}
		
	}
	
	
	/**
	 * 获取当前用户的用户组
	 */
	private function getGroupInfo()
	{
		$group_model = new Group();
		$group_info_result = $group_model->getGroupInfo([ [ "group_id", "=", $this->user_info["group_id"] ], [ "site_id", "=", $this->site_id ], [ "app_module", "=", $this->app_module ] ]);
		$this->group_info = $group_info_result["data"];
	}
	
	/**
	 * 验证登录
	 */
	private function checkLogin()
	{
		//验证基础登录
		if (!$this->uid) {
			$this->redirect(addon_url('city://city/login/login'));
		}
	}
	
	/**
	 * 检测权限
	 */
	private function checkAuth()
	{
		$user_model = new UserModel();
		$res = $user_model->checkAuth($this->url, $this->app_module, $this->group_info);
		return $res;
	}
	
	/**
	 * 获取菜单
	 */
	private function getMenuList()
	{
		$menu_model = new Menu();
		if ($this->group_info['is_system'] == 1) {
			$menus = $menu_model->getMenuList([ [ 'app_module', "=", $this->app_module ], [ 'is_show', "=", 1 ] ], '*', 'sort asc');
		} else {
			$menus = $menu_model->getMenuList([ [ 'name', 'in', $this->group_info['menu_array'] ], [ 'is_show', "=", 1 ], [ 'app_module', "=", $this->app_module ] ], '*', 'sort asc');
		}

		return $menus['data'];
	}
	
	/**
	 * 获取顶级菜单
	 */
	protected function getTopMenu()
	{
		$list = array_filter($this->menus, function ($v) {
			return $v['parent'] == '0';
		});
		return $list;
		
	}
	
	/**
	 * 四级菜单
	 * @param unknown $params
	 */
	protected function forthMenu($params = [])
	{
		$url = strtolower($this->url);
		$menu_model = new Menu();
		$menu_info = $menu_model->getMenuInfo([ [ 'url', "=", $url ], [ 'level', '=', 4 ] ], 'parent');
		if (!empty($menu_info['data'])) {
			$menus = $menu_model->getMenuList([ [ 'app_module', "=", $this->app_module ], [ 'is_show', "=", 1 ], [ 'parent', '=', $menu_info['data']['parent'] ] ], '*', 'sort asc');
			foreach ($menus['data'] as $k => $v) {
				$menus['data'][ $k ]['parse_url'] = addon_url($menus['data'][ $k ]['url'], $params);
				if ($menus['data'][ $k ]['url'] == $url) {
					$menus['data'][ $k ]['selected'] = 1;
				} else {
					$menus['data'][ $k ]['selected'] = 0;
				}
			}
			$this->assign('forth_menu', $menus['data']);
		}
	}
	
	/**
	 * 添加日志
	 * @param string $action_name
	 * @param array $data
	 */
	protected function addLog($action_name, $data = [])
	{
		$user = new User();
		$user->addUserLog($this->uid, $this->user_info['username'], $this->site_id, $action_name, $data);
	}
}