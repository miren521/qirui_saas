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


namespace app\shop\controller;

use app\model\system\Addon;
use app\model\web\DiyView as DiyViewModel;

/**
 * 网站装修控制器
 */
class Diy extends BaseShop
{
	/**
	 * 网站主页
	 */
	public function index()
	{
		$diy_view = new DiyViewModel();
		$page = $diy_view->getPage();

		// 查询公共组件和支持的页面
		$condition = [
			[ 'support_diy_view', 'like', [ $page[ 'shop' ][ 'index' ][ 'name' ], '%' . $page[ 'shop' ][ 'index' ][ 'name' ] . ',%', '%' . $page[ 'shop' ][ 'index' ][ 'name' ], '%,' . $page[ 'shop' ][ 'index' ][ 'name' ] . ',%', 'DIY_VIEW_SHOP', '' ], 'or' ]
		];

		$data = [
			'app_module' => $this->app_module,
			'site_id' => $this->site_id,
			'name' => $page[ 'shop' ][ 'index' ][ 'name' ],
			'condition' => $condition
		];
		$edit_view = event('DiyViewEdit', $data, true);
		return $edit_view;
	}

	/**
	 * 商品分类页面
	 */
	public function goodsCategory()
	{
		$diy_view = new DiyViewModel();
		$page = $diy_view->getPage();
		// 查询公共组件和支持的页面
		$condition = [
			[ 'name', '=', 'GOODS_CATEGORY' ]
		];

		$data = [
			'app_module' => $this->app_module,
			'site_id' => $this->site_id,
			'name' => $page[ 'shop' ][ 'goods_category' ][ 'name' ],
			'condition' => $condition
		];
		$edit_view = event('DiyViewEdit', $data, true);
		return $edit_view;
	}

	/**
	 * 编辑
	 */
	public function edit()
	{
		if (request()->isAjax()) {
			$res = 0;
			$data = array ();
			$id = input("id", 0);
			$name = input("name", "");
			$title = input("title", "");
			$value = input("value", "");
			if (!empty($name) && !empty($title) && !empty($value)) {
				$diy_view = new DiyViewModel();
				$page = $diy_view->getPage();
				$data[ 'site_id' ] = $this->site_id;
				$data[ 'name' ] = $name;
				$data[ 'title' ] = $title;
				$data[ 'type' ] = $page[ 'shop' ][ 'port' ];
				$data[ 'value' ] = $value;
				if ($id == 0) {
					$data[ 'create_time' ] = time();
					$res = $diy_view->addSiteDiyView($data);
				} else {
					$data[ 'update_time' ] = time();
					$res = $diy_view->editSiteDiyView($data, [
						[ 'id', '=', $id ]
					]);
				}
			}

			return $res;
		} else {

			$id = input("id", 0);
			//查询公共系统组件
			$condition = [
				[ 'support_diy_view', 'like', [ '', 'DIY_VIEW_SHOP' ], 'or' ]
			];
			$data = [
				'app_module' => $this->app_module,
				'site_id' => $this->site_id,
				'id' => $id,
				'condition' => $condition
			];
			$edit_view = event('DiyViewEdit', $data, true);
			return $edit_view;
		}
	}

	/**
	 * 微页面
	 */
	public function lists()
	{
		if (request()->isAjax()) {
			$page_index = input('page', 1);
			$page_size = input('page_size', PAGE_LIST_ROWS);
			$diy_view = new DiyViewModel();
			$page = $diy_view->getPage();
			$condition = array (
				[ 'sdv.site_id', '=', $this->site_id ],
				[ 'sdv.type', '=', $page[ 'shop' ][ 'port' ] ],
				[ 'sdv.name', 'like', '%DIY_VIEW_RANDOM_%' ]
			);
			$list = $diy_view->getSiteDiyViewPageList($condition, $page_index, $page_size, "sdv.create_time desc");
			return $list;
		} else {
			return $this->fetch('diy/lists');
		}
	}

	/**
	 * 链接选择
	 */
	public function link()
	{
        $link = input("link", '');
        $support_diy_view = input("support_diy_view", 'DIY_VIEW_SHOP'); //支持的自定义页面（为空表示都支持）
        $diy_view_model = new DiyViewModel();
        $link_condition = [
            [ 'lk.support_diy_view', 'like', [ $support_diy_view, '' ], 'or' ]
        ];
        $res = $diy_view_model->getDiyLinkList($link_condition);
        $res = $res[ 'data' ];
        $link_game = [];
        // 调整链接结构，先遍历，插件分类
        foreach ($res as $k => $v) {
            $value = [];
            $value['title'] = $v['title'];
            $value['name'] = $v['name'];
            $value['addon_name'] = $v['addon_name'];
            $value['addon_title'] = $v['addon_title'];
            $value['icon'] = $v['addon_icon'];
            $value['list'] = [];
            if (empty($v[ 'addon_name' ])) {
                continue;
            }
            if (strpos($v[ 'name' ],'GAME') !== false) {
                array_push($link_game, $value);
                continue;
            }
        }

        $link_config = config('diy_view');
        $list = $link_config['link_construct'];
        if(empty($link_game)) {
            unset($list[2]);
        }else{
            $list[2]['child_list'] = $link_game;
        }
        $this->assign("list", $list);
        $this->assign("link", $link);

        return $this->fetch('diy/link');
	}

	/**
	 * 删除自定义模板页面
	 */
	public function deleteSiteDiyView()
	{
		if (request()->isAjax()) {
			$diy_view = new DiyViewModel();
			$id_array = input("id", 0);
			$condition = [
				[ 'id', 'in', $id_array ]
			];
			$res = $diy_view->deleteSiteDiyView($condition);
			return $res;
		}
	}

	/**
	 * 底部导航
	 */
	public function bottomNavDesign()
	{
		$diy_view = new DiyViewModel();
		if (request()->isAjax()) {
			$value = input("value", "");
			$res = $diy_view->setShopBottomNavConfig($value, $this->site_id);
			return $res;
		} else {
			$bottom_nav_info = $diy_view->getShopBottomNavConfig($this->site_id);
			$this->assign("bottom_nav_info", $bottom_nav_info[ 'data' ][ 'value' ]);
			return $this->fetch('diy/bottom_nav_design');
		}

	}

	/**
	 * 推广链接
	 */
	public function promote()
	{
		if (request()->isAjax()) {
			$id = input("id", 0);
			$diy_view = new DiyViewModel();
			$res = $diy_view->qrcode([
				[ 'site_id', '=', $this->site_id ],
				[ 'id', '=', $id ]
			]);
			return $res;
		}
	}

    /**
     * 获取子级链接
     * @return array
     */
    public function childLink()
    {
        $link = input("link", []);
        $support_diy_view = input("support_diy_view", 'DIY_VIEW_SHOP');//支持的自定义页面（为空表示都支持）
        $name = input('name', '');
        $is_array = true;//记录是否是数组，后续判断受该变量影响
        if (!empty($link)) {
            $link = json_decode($link, true);
            $is_array = is_array($link);
        }

        // 通过所需类型得到链接数组
        $list = [];
        switch ($name){
            case 'MALL_PAGE':
                // 商城链接
                $diy_view_model = new DiyViewModel();
                $link_condition = [['lk.support_diy_view', 'like', [$support_diy_view, ''], 'or' ]];
                $res = $diy_view_model->getDiyLinkList($link_condition);

                // 筛选链接类型
                $link_shop = [];
                foreach ($res['data'] as $k => $v) {
                    $value = [];
                    $value['title'] = $v['title'];
                    $value['name'] = $v['name'];
                    $value['addon_name'] = $v['addon_name'];
                    $value['addon_title'] = $v['addon_title'];
                    $value['icon'] = $v['addon_icon'];
                    $value['wap_url'] = $v['wap_url'];
                    $value['list'] = [];
                    if (empty($v['addon_name'])) {
                        array_push($link_shop, $value);
                        continue;
                    }
                    if (strpos($v['name'],'GAME') !== false) continue;
                }
                $list = $link_shop;
                break;
            case 'MIC_PAGE':
                // 遍历微页面
                $diy_view_model = new DiyViewModel();
                $page = $diy_view_model->getPage();
                $condition = [
                    [ 'sdv.site_id', '=', $this->site_id ],
                    [ 'sdv.type', '=', $page[ 'shop' ][ 'port' ] ],
                    [ 'sdv.name', 'like', '%DIY_VIEW_RANDOM_%' ]
                ];
                $site_diy_view_list = $diy_view_model->getSiteDiyViewPageList($condition, 1, 0, "sdv.create_time desc");
                $site_diy_view_list = $site_diy_view_list[ 'data' ][ 'list' ];
                $link_mic = [];
                foreach ($site_diy_view_list as $page_k => $page_v) {
                    $title = $page_v[ 'title' ];
                    $item = [
                        'id' => $page_v[ 'id' ],
                        'name' => $page_v[ 'name' ],
                        'title' => $title,
                        'addon_icon' => "",
                        'addon_name' => isset($page_v[ 'addon_name' ])?$page_v[ 'addon_name' ]:'',
                        'addon_title' => '',
                        'web_url' => '',
                        'wap_url' => '/otherpages/diy/diy/diy?name=' . $page_v[ 'name' ],
                        'icon' => '',
                        'type' => 0,
                        'diy' => 1
                    ];

                    if (!empty($link) && $is_array && $link[ 'name' ] == $page_v[ 'name' ]) {
                        //对象方式匹配
                        $item[ 'selected' ] = true;
                    } elseif (!empty($link) && !$is_array && strtolower($link) == strtolower($page_v[ 'wap_url' ])) {
                        //字符串方式匹配
                        $item[ 'selected' ] = true;
                    } else {
                        $item[ 'selected' ] = false;
                    }
                    array_push($link_mic, $item);
                }
                $list = $link_mic;
                break;
            case 'ADDON_PAGE':
                // 遍历插件链接
                $diy_view_model = new DiyViewModel();
                $link_condition = [['lk.support_diy_view', 'like', [$support_diy_view, ''], 'or' ]];
                $res = $diy_view_model->getDiyLinkList($link_condition);
                // 筛选链接类型
                $link_shop = [];
                $link_addon = [];
                foreach ($res['data'] as $k => $v) {
                    $value = [];
                    $value['title'] = $v['title'];
                    $value['name'] = $v['name'];
                    $value['addon_name'] = $v['addon_name'];
                    $value['addon_title'] = $v['addon_title'];
                    $value['icon'] = $v['addon_icon'];
                    $value['wap_url'] = $v['wap_url'];
                    $value['list'] = [];
                    if (empty($v['addon_name'])) continue;
                    if (strpos($v['name'],'GAME') !== false) continue;
                    array_push($link_addon, $value);
                }
                $list = $link_addon;
                break;
            default:
                break;
        }

        return ['list' => $list, 'link' => $link];
    }
}