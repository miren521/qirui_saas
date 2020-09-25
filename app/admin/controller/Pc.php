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


namespace app\admin\controller;

use app\model\goods\Goods as GoodsModel;
use app\model\goods\GoodsBrand as GoodsBrandModel;
use app\model\goods\GoodsCategory as GoodsCategoryModel;
use app\model\web\Pc as PcModel;

/**
 * Pc端 控制器
 */
class Pc extends BaseAdmin
{
	private $pc_model;

	public function __construct()
	{
		$this->pc_model = new PcModel();
		parent::__construct();
	}

	/**
	 * 网站部署
	 * @return mixed
	 */
	public function deploy()
	{
		$refresh_time = 0;
		$path = 'web/refresh.log';
		if (file_exists($path)) {
			$refresh_time = file_get_contents($path);
		}
		$this->assign("root_url", ROOT_URL);
		$this->assign('refresh_time', $refresh_time);
		return $this->fetch('pc/deploy');
	}

	/**
	 * 默认部署：无需下载，一键刷新，API接口请求地址为当前域名，编译代码存放到web文件夹中
	 */
	public function downloadCsDefault()
	{
		return $this->pc_model->downloadCsDefault();
	}

	/**
	 * 独立部署：下载编译代码包，参考开发文档进行配置
	 */
	public function downloadCsIndep()
	{
		$domain = input("domain", ROOT_URL);
		$res = $this->pc_model->downloadCsIndep($domain);
		echo $res[ 'message' ];
	}

	/**
	 * 源码下载：下载开源代码包，参考开发文档进行配置，结合业务需求进行二次开发
	 */
	public function downloadOs()
	{
		$res = $this->pc_model->downloadOs();
		echo $res[ 'message' ];
	}

	/**
	 * 热门搜索关键词
	 * @return mixed
	 */
	public function hotSearchWords()
	{
		if (request()->isAjax()) {
		    $words = input("words", []);
			$data = [
				'words' => implode(',', $words)
			];
			$res = $this->pc_model->setHotSearchWords($data);
			return $res;
		} else {
			$hot_search_words = $this->pc_model->getHotSearchWords();
			$hot_search_words = $hot_search_words[ 'data' ][ 'value' ];

			$words_array = [];
			if(!empty($hot_search_words['words'])){
                $words_array = explode(',', $hot_search_words['words']);
            }
            $hot_search_words['words_array'] = $words_array;
			$this->assign("hot_search_words", $hot_search_words);
			return $this->fetch('pc/hot_search_words');
		}
	}

	/**
	 * 默认搜索关键词
	 * @return mixed
	 */
	public function defaultSearchWords()
	{
		if (request()->isAjax()) {
			$data = [
				'words' => input("words", "")
			];
			$res = $this->pc_model->setDefaultSearchWords($data);
			return $res;
		} else {
			$default_search_words = $this->pc_model->getDefaultSearchWords();
			$default_search_words = $default_search_words[ 'data' ][ 'value' ];
			$this->assign("default_search_words", $default_search_words);
			return $this->fetch('pc/default_search_words');
		}
	}

	/**
	 * 首页浮层
	 * @return mixed
	 */
	public function floatLayer()
	{
		if (request()->isAjax()) {
			$data = [
				'title' => input("title", ""),
				'url' => input("url", ""),
				'is_show' => input("is_show", 1),
				'number' => input("number", ""),
				'img_url' => input("img_url", "")
			];
			$res = $this->pc_model->setFloatLayer($data);
			return $res;
		} else {
			$link = $this->pc_model->getLink();
			$this->assign("link", $link);
			$float_layer = $this->pc_model->getFloatLayer();
			$float_layer = $float_layer[ 'data' ][ 'value' ];
			$this->assign("float_layer", $float_layer);
			return $this->fetch('pc/float_layer');
		}
	}

	/**
	 * 导航设置
	 * @return mixed
	 */
	public function navList()
	{
		if (request()->isAjax()) {
			$page = input('page', 1);
			$page_size = input('page_size', PAGE_LIST_ROWS);
			$search_text = input('search_text', '');

			$condition = [];
			$condition[] = [ 'nav_title', 'like', '%' . $search_text . '%' ];
			$order = 'create_time desc';

			$model = new PcModel();
			return $model->getNavPageList($condition, $page, $page_size, $order);
		} else {
			return $this->fetch('pc/nav_list');
		}
	}

	/**
	 * 添加导航
	 * @return mixed
	 */
	public function addNav()
	{
		$model = new PcModel();
		if (request()->isAjax()) {
			$data = [
				'nav_title' => input('nav_title', ''),
				'nav_url' => input('nav_url', ''),
				'sort' => input('sort', ''),
				'is_blank' => input('is_blank', ''),
				'nav_icon' => input('nav_icon', ''),
				'is_show' => input('is_show', ''),
				'create_time' => time(),
			];

			return $model->addNav($data);
		} else {
			$link_list = $model->getLink();
			$this->assign('link', $link_list);

			return $this->fetch('pc/add_nav');
		}
	}

	/**
	 * 编辑导航
	 * @return mixed
	 */
	public function editNav()
	{
		$model = new PcModel();
		if (request()->isAjax()) {
			$data = [
				'nav_title' => input('nav_title', ''),
				'nav_url' => input('nav_url', ''),
				'sort' => input('sort', ''),
				'is_blank' => input('is_blank', ''),
				'nav_icon' => input('nav_icon', ''),
				'is_show' => input('is_show', ''),
				'modify_time' => time(),
			];
			$id = input('id', 0);
			$condition = [ [ 'id', '=', $id ] ];

			return $model->editNav($data, $condition);
		} else {
			$link_list = $model->getLink();
			$this->assign('link', $link_list);

			$id = input('id', 0);
			$this->assign('id', $id);

			$nav_info = $model->getNavInfo($id);
			$this->assign('nav_info', $nav_info[ 'data' ]);

			return $this->fetch('pc/edit_nav');
		}
	}

	/**
	 * 删除导航
	 * @return mixed
	 */
	public function deleteNav()
	{
		if (request()->isAjax()) {
			$id = input('id', 0);
			$model = new PcModel();
			return $model->deleteNav([ [ 'id', '=', $id ] ]);
		}
	}

	/**
	 * 修改排序
	 */
	public function modifySort()
	{
		if (request()->isAjax()) {
			$sort = input('sort', 0);
			$id = input('id', 0);
			$model = new PcModel();
			return $model->modifyNavSort($sort, $id);
		}
	}

	/**
	 * 友情链接
	 * @return mixed
	 */
	public function linklist()
	{
		if (request()->isAjax()) {
			$page = input('page', 1);
			$page_size = input('page_size', PAGE_LIST_ROWS);
			$search_text = input('search_text', '');

			$condition = [];
			$condition[] = [ 'link_title', 'like', '%' . $search_text . '%' ];
			$order = 'link_sort desc';

			$model = new PcModel();
			return $model->getLinkPageList($condition, $page, $page_size, $order);
		} else {
			return $this->fetch('pc/link_list');
		}
	}

	/**
	 * 添加友情链接
	 * @return mixed
	 */
	public function addLink()
	{
		$model = new PcModel();
		if (request()->isAjax()) {
			$data = [
				'link_title' => input('link_title', ''),
				'link_url' => input('link_url', ''),
				'link_pic' => input('link_pic', ''),
				'link_sort' => input('link_sort', ''),
				'is_blank' => input('is_blank', ''),
				'is_show' => input('is_show', ''),
			];

			return $model->addLink($data);
		} else {
			return $this->fetch('pc/add_link');
		}
	}

	/**
	 * 编辑友情链接
	 * @return mixed
	 */
	public function editLink()
	{
		$model = new PcModel();
		if (request()->isAjax()) {
			$data = [
				'link_title' => input('link_title', ''),
				'link_url' => input('link_url', ''),
				'link_pic' => input('link_pic', ''),
				'link_sort' => input('link_sort', ''),
				'is_blank' => input('is_blank', ''),
				'is_show' => input('is_show', ''),
			];
			$id = input('id', 0);
			$condition = [ [ 'id', '=', $id ] ];

			return $model->editLink($data, $condition);
		} else {

			$id = input('id', 0);
			$this->assign('id', $id);

			$link_info = $model->getLinkInfo($id);
			$this->assign('link_info', $link_info[ 'data' ]);

			return $this->fetch('pc/edit_link');
		}
	}

	/**
	 * 删除友情链接
	 * @return mixed
	 */
	public function deleteLink()
	{
		if (request()->isAjax()) {
			$id = input('id', 0);
			$model = new PcModel();
			return $model->deleteLink([ [ 'id', '=', $id ] ]);
		}
	}

	/**
	 * 修改排序
	 */
	public function modifyLinkSort()
	{
		if (request()->isAjax()) {
			$sort = input('sort', 0);
			$id = input('id', 0);
			return $this->pc_model->modifyLinkSort($sort, $id);
		}
	}

	/**
	 * 首页楼层
	 * @return array|mixed
	 */
	public function floor()
	{
		if (request()->isAjax()) {
			$page = input('page', 1);
			$page_size = input('page_size', PAGE_LIST_ROWS);
			$search_text = input('search_text', '');
			$condition = [];
			$condition[] = [ 'pf.title', 'like', '%' . $search_text . '%' ];
			$list = $this->pc_model->getFloorPageList($condition, $page, $page_size);
			return $list;
		} else {
			return $this->fetch('pc/floor');
		}
	}

	/**
	 * 修改首页楼层排序
	 */
	public function modifyFloorSort()
	{
		if (request()->isAjax()) {
			$sort = input('sort', 0);
			$id = input('id', 0);
			$condition = array (
				[ 'id', '=', $id ],
			);
			$res = $this->pc_model->modifyFloorSort($sort, $condition);
			return $res;
		}
	}

	/**
	 * 删除首页楼层
	 * @return array
	 */
	public function deleteFloor()
	{
		if (request()->isAjax()) {
			$id = input('id', 0);
			$res = $this->pc_model->deleteFloor([ [ 'id', '=', $id ] ]);
			return $res;
		}
	}

	/**
	 * 编辑楼层
	 * @return mixed
	 */
	public function editFloor()
	{
		if (request()->isAjax()) {
			$id = input("id", 0);
			$data = [
				'block_id' => input("block_id", 0), //楼层模板关联id
				'title' => input("title", ''), // 楼层标题
				'value' => input("value", ''),
				'state' => input("state", 0),// 状态（0：禁用，1：启用）
				'sort' => input("sort", 0), //排序号
			];
			if ($id == 0) {
				$res = $this->pc_model->addFloor($data);
			} else {
				$res = $this->pc_model->editFloor($data, [ [ 'id', '=', $id ] ]);
			}
			return $res;
		} else {
			$id = input("id", 0);
			$this->assign("id", $id);

			if (!empty($id)) {
				$floor_info = $this->pc_model->getFloorDetail($id);
				$floor_info = $floor_info[ 'data' ];
				$this->assign("floor_info", $floor_info);
			}

			$floor_block_list = $this->pc_model->getFloorBlockList();
			$floor_block_list = $floor_block_list[ 'data' ];
			$this->assign("floor_block_list", $floor_block_list);

			$pc_link = $this->pc_model->getLink();
			$this->assign("pc_link", $pc_link);

			$goods_category_model = new GoodsCategoryModel();
			$category_list = $goods_category_model->getCategoryTree();
			$category_list = $category_list[ 'data' ];
			$this->assign("category_list", $category_list);
			return $this->fetch('pc/edit_floor');
		}
	}

	/**
	 * 商品选择组件
	 * @return \multitype
	 */
	public function goodsSelect()
	{
		if (request()->isAjax()) {
			$page = input('page', 1);
			$page_size = input('page_size', PAGE_LIST_ROWS);
			$goods_name = input('goods_name', '');
			$goods_ids = input('goods_ids', '');
			$is_virtual = input('is_virtual', '');// 是否虚拟类商品（0实物1.虚拟）
			$min_price = input('min_price', 0);
			$max_price = input('max_price', 0);
			$goods_class = input('goods_class', "");// 商品类型，实物、虚拟
			$category_id = input('category_id', "");// 商品分类id
			$promotion = input('promotion', '');//营销活动标识：pintuan、groupbuy、fenxiao、bargain
			$promotion_type = input('promotion_type', "");

			if (!empty($promotion) && addon_is_exit($promotion)) {
				$pintuan_name = input('pintuan_name', '');//拼团活动
				$goods_list = event('GoodsListPromotion', [ 'page' => $page, 'page_size' => $page_size, 'promotion' => $promotion, 'pintuan_name' => $pintuan_name, 'goods_name' => $goods_name ], true);
			} else {
				$condition = [
					[ 'is_delete', '=', 0 ],
					[ 'goods_state', '=', 1 ],
				];
				if (!empty($goods_name)) {
					$condition[] = [ 'goods_name', 'like', '%' . $goods_name . '%' ];
				}
				if ($is_virtual !== "") {
					$condition[] = [ 'is_virtual', '=', $is_virtual ];
				}
				if (!empty($goods_ids)) {
					$condition[] = [ 'goods_id', 'in', $goods_ids ];
				}
				if (!empty($category_id)) {
					$condition[] = [ 'category_id', 'like', [ $category_id, '%' . $category_id . ',%', '%' . $category_id, '%,' . $category_id . ',%' ], 'or' ];
				}

				if (!empty($promotion_type)) {
					$condition[] = [ 'promotion_addon', 'like', "%{$promotion_type}%" ];
				}

				if ($goods_class !== "") {
					$condition[] = [ 'goods_class', '=', $goods_class ];
				}

				if ($min_price != "" && $max_price != "") {
					$condition[] = [ 'price', 'between', [ $min_price, $max_price ] ];
				} elseif ($min_price != "") {
					$condition[] = [ 'price', '<=', $min_price ];
				} elseif ($max_price != "") {
					$condition[] = [ 'price', '>=', $max_price ];
				}

				$order = 'create_time desc';
				$goods_model = new GoodsModel();
				$field = 'goods_id,goods_name,goods_class_name,goods_image,price,goods_stock,create_time,is_virtual,sku_id,introduction,market_price';
				$goods_list = $goods_model->getGoodsPageList($condition, $page, $page_size, $order, $field);

				if (!empty($goods_list[ 'data' ][ 'list' ])) {
					foreach ($goods_list[ 'data' ][ 'list' ] as $k => $v) {
						$goods_sku_list = $goods_model->getGoodsSkuList([ [ 'goods_id', '=', $v[ 'goods_id' ] ] ], 'sku_id,sku_name,price,stock,sku_image,goods_id,goods_class_name,introduction,market_price,goods_name');
						$goods_sku_list = $goods_sku_list[ 'data' ];
						$goods_list[ 'data' ][ 'list' ][ $k ][ 'sku_list' ] = $goods_sku_list;
					}

				}
			}
			return $goods_list;
		} else {

			//已经选择的商品sku数据
			$select_id = input('select_id', '');
			$mode = input('mode', 'spu');
			$max_num = input('max_num', 0);
			$min_num = input('min_num', 0);
			$is_virtual = input('is_virtual', '');
			$disabled = input('disabled', 0);
			$promotion = input('promotion', '');//营销活动标识：pintuan、groupbuy、seckill、fenxiao

			$this->assign('select_id', $select_id);
			$this->assign('mode', $mode);
			$this->assign('max_num', $max_num);
			$this->assign('min_num', $min_num);
			$this->assign('is_virtual', $is_virtual);
			$this->assign('disabled', $disabled);
			$this->assign('promotion', $promotion);

			// 营销活动
			$goods_promotion_type = event('GoodsPromotionType');
			$this->assign('promotion_type', $goods_promotion_type);

			$goods_category_model = new GoodsCategoryModel();

			$field = 'category_id,category_name as title';
			$condition = [
				[ 'pid', '=', 0 ],
				[ 'level', '=', 1 ],
			];
			$list = $goods_category_list = $goods_category_model->getCategoryByParent($condition, $field);
			$list = $list[ 'data' ];
			if (!empty($list)) {
				foreach ($list as $k => $v) {
					$two_list = $goods_category_list = $goods_category_model->getCategoryByParent(
						[
							[ 'pid', '=', $v[ 'category_id' ] ],
							[ 'level', '=', 2 ],
						],
						$field
					);

					$two_list = $two_list[ 'data' ];
					if (!empty($two_list)) {

						foreach ($two_list as $two_k => $two_v) {
							$three_list = $goods_category_list = $goods_category_model->getCategoryByParent(
								[
									[ 'pid', '=', $two_v[ 'category_id' ] ],
									[ 'level', '=', 3 ],
								],
								$field
							);
							$two_list[ $two_k ][ 'children' ] = $three_list[ 'data' ];
						}
					}

					$list[ $k ][ 'children' ] = $two_list;
				}
			}

			$this->assign("category_list", $list);
			return $this->fetch("pc/goods_select");
		}
	}

	/**
	 * 品牌选择
	 * @return \multitype
	 */
	public function brandSelect()
	{
		if (request()->isAjax()) {
			$page = input('page', 1);
			$page_size = input('page_size', PAGE_LIST_ROWS);
			$brand_name = input('brand_name', '');
			$brand_ids = input('brand_ids', '');

			$condition = [];
			if (!empty($brand_name)) {
				$condition[] = [ 'ngb.brand_name', 'like', '%' . $brand_name . '%' ];
			}
			if (!empty($brand_ids)) {
				$condition[] = [ 'ngb.brand_id', 'in', $brand_ids ];
			}

			$goods_brand_model = new GoodsBrandModel();
			$brand_list = $goods_brand_model->getBrandPageList($condition, $page, $page_size);
			return $brand_list;
		} else {

			$select_id = input('select_id', '');
			$max_num = input('max_num', 0);
			$min_num = input('min_num', 0);
			$disabled = input('disabled', 0);

			$this->assign('select_id', $select_id);
			$this->assign('max_num', $max_num);
			$this->assign('min_num', $min_num);
			$this->assign('disabled', $disabled);
			return $this->fetch("pc/brand_select");
		}
	}
}
