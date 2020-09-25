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

use app\model\web\DiyView as DiyViewModel;

/**
 * 网站装修控制器
 */
class Diy extends BaseCity
{
	private $page = "DIYVIEW_INDEX";

	/**
	 * 网站主页
	 */
	public function index()
	{

		// 查询公共组件和支持的页面
		$condition = [
			[ 'support_diy_view', 'like', [ $this->page, '%' . $this->page . ',%', '%' . $this->page, '%,' . $this->page . ',%', '' ], 'or' ]
		];
		$this->assign("name", $this->page);
		$this->editData($condition, 0, $this->page);
		return $this->fetch('diy/edit', [], $this->replace);
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
				$data[ 'site_id' ] = $this->site_id;
				$data[ 'name' ] = $name;
				$data[ 'title' ] = $title;
				$data[ 'type' ] = 'city';
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
				[ 'support_diy_view', '=', '' ]
			];
			$this->editData($condition, $id);
			return $this->fetch('diy/edit', [], $this->replace);
		}
	}

	/**
	 * 编辑时用到的数据
	 * @param $condition
	 * @param $id
	 * @param string $name
	 */
	private function editData($condition, $id, $name = '')
	{
		$diy_view = new DiyViewModel();

		// 自定义模板组件集合
		$utils = $diy_view->getDiyViewUtilList($condition);

		$diy_view_info = [];
		// 推广码
		$qrcode_info = [];
		if (!empty($id)) {
			$diy_view_info = $diy_view->getSiteDiyViewDetail([
				[ 'sdv.site_id', '=', $this->site_id ],
				[ 'sdv.id', '=', $id ]
			]);
			$qrcode_info = $diy_view->qrcode([
				[ 'site_id', '=', $this->site_id ],
				[ 'id', '=', $id ]
			]);
		} elseif (!empty($name)) {
			$condition = [
				[ 'sdv.site_id', '=', $this->site_id ],
				[ 'sdv.name', '=', $name ]
			];
			$qrcode_info = $diy_view->qrcode([
				[ 'site_id', '=', $this->site_id ],
				[ 'name', '=', $name ]
			]);
			$diy_view_info = $diy_view->getSiteDiyViewDetail($condition);
		}

		if (!empty($diy_view_info) && !empty($diy_view_info[ 'data' ])) {
			$diy_view_info = $diy_view_info[ 'data' ];
		}

		if (!empty($qrcode_info)) {
			$qrcode_info = $qrcode_info[ 'data' ];
			// 目前只支持H5
			if ($qrcode_info[ 'path' ][ 'h5' ][ 'status' ] != 1) {
				$qrcode_info = [];
			}
		}

		$diy_view_utils = array ();
		if (!empty($utils[ 'data' ])) {

			// 先遍历，组件分类
			foreach ($utils[ 'data' ] as $k => $v) {
				$value = array ();
				$value[ 'type' ] = $v[ 'type' ];
				$value[ 'type_name' ] = $diy_view->getTypeName($v[ 'type' ]);
				$value[ 'list' ] = [];
				if (!in_array($value, $diy_view_utils)) {
					array_push($diy_view_utils, $value);
				}
			}

			// 遍历每一个组件，将其添加到对应的分类中
			foreach ($utils[ 'data' ] as $k => $v) {
				foreach ($diy_view_utils as $diy_k => $diy_v) {
					if ($diy_v[ 'type' ] == $v[ 'type' ]) {
						array_push($diy_view_utils[ $diy_k ][ 'list' ], $v);
					}
				}
			}
		}

		$this->assign("time", time());
		$this->assign("qrcode_info", $qrcode_info);
		$this->assign('diy_view_utils', $diy_view_utils);
		$this->assign("diy_view_info", $diy_view_info);
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
			$condition = array (
				[ 'sdv.site_id', '=', $this->site_id ],
				[ 'sdv.type', '=', 'city' ],
				[ 'sdv.name', 'like', '%DIY_VIEW_RANDOM_%' ]
			);
			$list = $diy_view->getSiteDiyViewPageList($condition, $page_index, $page_size, "sdv.create_time desc");
			return $list;
		} else {
			return $this->fetch('diy/lists', [], $this->replace);
		}
	}

    /**
     * 链接选择
     * @return mixed
     */
	public function link()
	{
        $link = input("link", '');

        $link_config = config('diy_view');
        $list = $link_config['link_construct'];
        unset($list[0]['child_list'][1]);   //去除微页面（城市分站没有）
        $list[0]['child_list'] = array_values($list[0]['child_list']);
        unset($list[2]);
        $list = array_values($list);

        $this->assign("list", $list);
        $this->assign("link", $link);
		return $this->fetch('diy/link', [], $this->replace);
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
        $support_diy_view = input("support_diy_view", 'DIY_VIEW_INDEX');//支持的自定义页面（为空表示都支持）
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
                $link_condition = [
                    [ 'lk.support_diy_view', 'like', [ $support_diy_view, '' ], 'or' ]
                ];
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
            case 'ADDON_PAGE':
                // 遍历插件链接
                $diy_view_model = new DiyViewModel();
                $link_condition = [
                    [ 'lk.support_diy_view', 'like', [ $support_diy_view, '' ], 'or' ]
                ];
                $res = $diy_view_model->getDiyLinkList($link_condition);
                // 筛选链接类型
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

        if (!$is_array) {
            $link = [];
        }

        return [
            'list' => $list,
            'link' => $link
        ];
    }
}