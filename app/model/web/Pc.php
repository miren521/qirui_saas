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


namespace app\model\web;


use app\model\BaseModel;
use app\model\goods\Goods as GoodsModel;
use app\model\goods\GoodsBrand as GoodsBrandModel;
use app\model\goods\GoodsCategory as GoodsCategoryModel;
use app\model\system\Api;
use app\model\system\Config as ConfigModel;
use think\facade\Cache;

/**
 * PC端
 * @author Administrator
 *
 */
class Pc extends BaseModel
{

	private $link = [
		[
			'title' => '首页',
			'url' => '/index'
		],
		[
			'title' => '登录',
			'url' => '/login'
		],
		[
			'title' => '注册',
			'url' => '/register'
		],
		[
			'title' => '找回密码',
			'url' => '/find_pass'
		],
		[
			'title' => '公告列表',
			'url' => '/cms/notice'
		],
		[
			'title' => '帮助中心',
			'url' => '/cms/help'
		],
		[
			'title' => '购物车',
			'url' => '/cart'
		],
		[
			'title' => '品牌专区',
			'url' => '/brand'
		],
		[
			'title' => '领券中心',
			'url' => '/coupon'
		],
		[
			'title' => '商品分类',
			'url' => '/category'
		],
		[
			'title' => '团购专区',
			'url' => '/promotion/groupbuy'
		],
		[
			'title' => '秒杀专区',
			'url' => '/promotion/seckill'
		],
		[
			'title' => '会员中心',
			'url' => '/member/home/index'
		]

	];

	private $not_found_file_error = "未找到源码包，请检查目录文件";

	/*************************************************网站部署******************************************/

	/**
	 * 默认部署：无需下载，一键刷新，API接口请求地址为当前域名，编译代码存放到web文件夹中
	 * @return array
	 */
	public function downloadCsDefault()
	{
		try {

			$path = 'public/web/cs_default';
			$web_path = 'web'; // web端生成目录
			$config_path = 'web/assets/js'; // web模板文件目录
			if (!is_dir($path) || count(scandir($path)) <= 3) {
				return $this->error('', $this->not_found_file_error);
			}

			if (is_dir($web_path)) {
				// 先将之前的文件删除
				if (count(scandir($web_path)) > 1) deleteDir($web_path);
			} else {
				// 创建web目录
				mkdir($web_path, intval('0777', 8), true);
			}

			// 将原代码包拷贝到web目录下
			recurseCopy($path, $web_path);
			$this->copyFile($config_path);
			file_put_contents($web_path . '/refresh.log', time());
			return $this->success();
		} catch (\Exception $e) {
			return $this->error('', $e->getMessage() . $e->getLine());
		}
	}

	/**
	 * 独立部署：下载编译代码包，参考开发文档进行配置
	 * @param $domain
	 * @return array
	 */
	public function downloadCsIndep($domain)
	{
		try {

			$path = 'public/web/cs_indep';
			$source_file_path = 'upload/web/cs_indep'; // web端生成目录
			$config_path = $source_file_path . '/assets/js'; // h5模板文件目录
			if (!is_dir($path) || count(scandir($path)) <= 3) {
				return $this->error('', $this->not_found_file_error);
			}

			if (is_dir($source_file_path)) {
				// 先将之前的文件删除
				if (count(scandir($source_file_path)) > 2) deleteDir($source_file_path);
			} else {
				// 创建web目录
				mkdir($source_file_path, intval('0777', 8), true);
			}

			// 将原代码包拷贝到web目录下
			recurseCopy($path, $source_file_path);
			$this->copyFile($config_path, $domain);

			// 生成压缩包
			$file_arr = getFileMap($source_file_path);

			if (!empty($file_arr)) {
				$zipname = 'web_cs_indep_' . date('YmdHi') . '.zip';
				$zip = new \ZipArchive();
				$res = $zip->open($zipname, \ZipArchive::CREATE);
				if ($res === TRUE) {
					foreach ($file_arr as $file_path => $file_name) {
						if (is_dir($file_path)) {
							$file_path = str_replace($source_file_path . '/', '', $file_path);
							$zip->addEmptyDir($file_path);
						} else {
							$zip_path = str_replace($source_file_path . '/', '', $file_path);
							$zip->addFile($file_path, $zip_path);
						}
					}
					$zip->close();

					header("Content-Type: application/zip");
					header("Content-Transfer-Encoding: Binary");
					header("Content-Length: " . filesize($zipname));
					header("Content-Disposition: attachment; filename=\"" . basename($zipname) . "\"");
					readfile($zipname);
					@unlink($zipname);
				}
			}
			return $this->success();
		} catch (\Exception $e) {
			return $this->error('', $e->getMessage() . $e->getLine());
		}
	}

	/**
	 * 源码下载：下载开源代码包，参考开发文档进行配置，结合业务需求进行二次开发
	 * @return array
	 */
	public function downloadOs()
	{
		try {
			$source_file_path = 'public/web/os';
			if (!is_dir($source_file_path) || count(scandir($source_file_path)) <= 3) {
				return $this->error('', $this->not_found_file_error);
			}
			$file_arr = getFileMap($source_file_path);

			if (!empty($file_arr)) {
				$zipname = 'web_os_' . date('YmdHi') . '.zip';
				$zip = new \ZipArchive();
				$res = $zip->open($zipname, \ZipArchive::CREATE);
				if ($res === TRUE) {
					foreach ($file_arr as $file_path => $file_name) {
						if (is_dir($file_path)) {
							$file_path = str_replace($source_file_path . '/', '', $file_path);
							$zip->addEmptyDir($file_path);
						} else {
							$zip_path = str_replace($source_file_path . '/', '', $file_path);
							$zip->addFile($file_path, $zip_path);
						}
					}
					$zip->close();

					header("Content-Type: application/zip");
					header("Content-Transfer-Encoding: Binary");
					header("Content-Length: " . filesize($zipname));
					header("Content-Disposition: attachment; filename=\"" . basename($zipname) . "\"");
					readfile($zipname);
					@unlink($zipname);
				}
			}
		} catch (\Exception $e) {
			return $this->error('', $e->getMessage() . $e->getLine());
		}
	}

	/**
	 * 替换配置信息，API请求域名地址、图片、密钥等
	 * @param $source_path
	 * @param string $domain
	 */
	private function copyFile($source_path, $domain = ROOT_URL)
	{
		$files = scandir($source_path);
		foreach ($files as $path) {
			if ($path != '.' && $path != '..') {
				$temp_path = $source_path . '/' . $path;
				if (file_exists($temp_path)) {
					if (preg_match("/(app.)(\w{8})(.js.map)$/", $temp_path)) {
						$content = file_get_contents($temp_path);
						$content = $this->paramReplace($content, $domain);
						file_put_contents($temp_path, $content);
					}
					if (preg_match("/(app.)(\w{8})(.js)$/", $temp_path)) {
						$content = file_get_contents($temp_path);
						$content = $this->paramReplace($content, $domain);
						file_put_contents($temp_path, $content);
					}
				}
			}
		}
	}

	/**
	 * 参数替换
	 * @param $string
	 * @param string $domain
	 * @return string|string[]|null
	 */
	private function paramReplace($string, $domain = ROOT_URL)
	{
		$api_model = new Api();
		$api_config = $api_model->getApiConfig();
		$api_config = $api_config[ 'data' ];

		$patterns = [
			'/\{\{\$baseUrl\}\}/',
			'/\{\{\$imgDomain\}\}/',
			'/\{\{\$mpKey\}\}/',
			'/\{\{\$apiSecurity\}\}/',
			'/\{\{\$publicKey\}\}/'
		];
		$replacements = [
			$domain,
			$domain,
			'',
			$api_config[ 'is_use' ] ?? 0,
			$api_config[ 'value' ][ 'public_key' ] ?? ''
		];
		$string = preg_replace($patterns, $replacements, $string);
		return $string;
	}

	/**
	 * 获取PC端固定跳转链接
	 * @return array
	 */
	public function getLink()
	{
		foreach ($this->link as $k => $v) {
			if ($v[ 'url' ] == '/register' && addon_is_exit('memberregister') == 0) {
				unset($this->link[ $k ]);
			} elseif ($v[ 'url' ] == '/coupon' && addon_is_exit('coupon') == 0) {
				unset($this->link[ $k ]);
			} elseif ($v[ 'url' ] == '/promotion/seckill' && addon_is_exit('seckill') == 0) {
				unset($this->link[ $k ]);
			} elseif ($v[ 'url' ] == '/promotion/groupbuy' && addon_is_exit('groupbuy') == 0) {
				unset($this->link[ $k ]);
			}
		}
		$this->link = array_values($this->link);
		return $this->link;
	}

	/**
	 * 设置热门搜索关键词
	 * @param $data
	 * @return array
	 */
	public function setHotSearchWords($data)
	{
		$config = new ConfigModel();
		$res = $config->setConfig($data, 'PC端热门搜索关键词', 1, [ [ 'site_id', '=', 0 ], [ 'app_module', '=', 'admin' ], [ 'config_key', '=', 'PC_HOT_SEARCH_WORDS_CONFIG' ] ]);
		return $res;
	}

	/**
	 * 获取热门搜索关键词
	 * @param $data
	 * @return array
	 */
	public function getHotSearchWords()
	{
		$config = new ConfigModel();
		$res = $config->getConfig([ [ 'site_id', '=', 0 ], [ 'app_module', '=', 'admin' ], [ 'config_key', '=', 'PC_HOT_SEARCH_WORDS_CONFIG' ] ]);
		if (empty($res[ 'data' ][ 'value' ])) {
			$res[ 'data' ][ 'value' ] = [
				'words' => ''
			];
		}
		return $res;
	}

	/**
	 * 设置默认搜索关键词
	 * @param $data
	 * @return array
	 */
	public function setDefaultSearchWords($data)
	{
		$config = new ConfigModel();
		$res = $config->setConfig($data, 'PC端默认搜索关键词', 1, [ [ 'site_id', '=', 0 ], [ 'app_module', '=', 'admin' ], [ 'config_key', '=', 'PC_DEFAULT_SEARCH_WORDS_CONFIG' ] ]);
		return $res;
	}

	/**
	 * 获取默认搜索关键词
	 * @param $data
	 * @return array
	 */
	public function getDefaultSearchWords()
	{
		$config = new ConfigModel();
		$res = $config->getConfig([ [ 'site_id', '=', 0 ], [ 'app_module', '=', 'admin' ], [ 'config_key', '=', 'PC_DEFAULT_SEARCH_WORDS_CONFIG' ] ]);
		if (empty($res[ 'data' ][ 'value' ])) {
			$res[ 'data' ][ 'value' ] = [
				'words' => '搜索 商品/店铺'
			];
		}
		return $res;
	}

	/**
	 * 设置首页浮层
	 * @param $data
	 * @return array
	 */
	public function setFloatLayer($data)
	{
		$config = new ConfigModel();
		$res = $config->setConfig($data, 'PC端首页浮层', 1, [ [ 'site_id', '=', 0 ], [ 'app_module', '=', 'admin' ], [ 'config_key', '=', 'PC_INDEX_FLOAT_LAYER_CONFIG' ] ]);
		return $res;
	}

	/**
	 * 获取首页浮层
	 * @param $data
	 * @return array
	 */
	public function getFloatLayer()
	{
		$config = new ConfigModel();
		$res = $config->getConfig([ [ 'site_id', '=', 0 ], [ 'app_module', '=', 'admin' ], [ 'config_key', '=', 'PC_INDEX_FLOAT_LAYER_CONFIG' ] ]);
		if (empty($res[ 'data' ][ 'value' ])) {
			$res[ 'data' ][ 'value' ] = [
				'title' => '首页浮层',
				'url' => 'https://www.niushop.com',
				'is_show' => 1,
				'number' => '3',
				'img_url' => 'upload/default/pc/index_float_layer.png'
			];
		}
		return $res;
	}

	/*****************************************导航*****************************************/
	/**
	 * 添加导航
	 * @param array $data
	 */
	public function addNav($data)
	{
		$id = model('pc_nav')->add($data);
		Cache::tag("pc_nav")->clear();
		return $this->success($id);
	}

	/**
	 * 修改导航
	 * @param array $data
	 */
	public function editNav($data, $condition)
	{
		$res = model('pc_nav')->update($data, $condition);
		Cache::tag("pc_nav")->clear();
		return $this->success($res);
	}

	/**
	 * 删除导航
	 * @param unknown $coupon_type_id
	 */
	public function deleteNav($condition)
	{
		$res = model('pc_nav')->delete($condition);
		Cache::tag("pc_nav")->clear();
		return $this->success($res);
	}

	/**
	 * 获取导航详情
	 * @param int $id
	 * @return multitype:string mixed
	 */
	public function getNavInfo($id)
	{
		$cache = Cache::get("pc_nav_getNavInfo_" . $id);
		if (!empty($cache)) {
			return $this->success($cache);
		}
		$res = model('pc_nav')->getInfo([ [ 'id', '=', $id ] ], 'id, nav_title, nav_url, sort, is_blank, create_time, modify_time, nav_icon, is_show');
		Cache::tag("pc_nav")->set("pc_nav_getNavInfo_" . $id, $res);
		return $this->success($res);
	}

	/**
	 * 获取导航详情
	 * @param int $id
	 * @return multitype:string mixed
	 */
	public function getNavInfoByCondition($condition, $field = 'id, nav_title, nav_url, sort, is_blank, create_time, modify_time, nav_icon, is_show')
	{
		$res = model('pc_nav')->getInfo($condition, $field);
		return $this->success($res);
	}

	/**
	 * 获取导航分页列表
	 * @param array $condition
	 * @param number $page
	 * @param string $page_size
	 * @param string $order
	 * @param string $field
	 */
	public function getNavPageList($condition = [], $page = 1, $page_size = PAGE_LIST_ROWS, $order = 'create_time desc', $field = 'id, nav_title, nav_url, sort, is_blank, create_time, modify_time, nav_icon, is_show')
	{
		$data = json_encode([ $condition, $field, $order, $page, $page_size ]);
		$cache = Cache::get("pc_nav_getNavPageList_" . $data);
		if (!empty($cache)) {
			return $this->success($cache);
		}
		$list = model('pc_nav')->pageList($condition, $field, $order, $page, $page_size);
		Cache::tag("pc_nav")->set("pc_nav_getNavList_" . $data, $list);
		return $this->success($list);
	}

	/**
	 * 修改排序
	 * @param int $sort
	 * @param int $id
	 */
	public function modifyNavSort($sort, $id)
	{
		$res = model('pc_nav')->update([ 'sort' => $sort ], [ [ 'id', '=', $id ] ]);
		Cache::tag('pc_nav')->clear();
		return $this->success($res);
	}

	/*****************************************友情链接*****************************************/

	/**
	 * 添加友情链接
	 * @param array $data
	 */
	public function addLink($data)
	{
		$id = model('pc_friendly_link')->add($data);
		Cache::tag("pc_friendly_link")->clear();
		return $this->success($id);
	}

	/**
	 * 修改友情链接
	 * @param array $data
	 */
	public function editLink($data, $condition)
	{
		$res = model('pc_friendly_link')->update($data, $condition);
		Cache::tag("pc_friendly_link")->clear();
		return $this->success($res);
	}

	/**
	 * 删除友情链接
	 * @param unknown $coupon_type_id
	 */
	public function deleteLink($condition)
	{
		$res = model('pc_friendly_link')->delete($condition);
		Cache::tag("pc_friendly_link")->clear();
		return $this->success($res);
	}

	/**
	 * 获取友情链接详情
	 * @param int $id
	 * @return multitype:string mixed
	 */
	public function getLinkInfo($id)
	{
		$cache = Cache::get("pc_friendly_link_info" . $id);
		if (!empty($cache)) {
			return $this->success($cache);
		}
		$res = model('pc_friendly_link')->getInfo([ [ 'id', '=', $id ] ], 'id, link_title, link_url, link_pic, link_sort, is_blank, is_show');
		Cache::tag("pc_friendly_link")->set("pc_friendly_link_info" . $id, $res);
		return $this->success($res);
	}

	/**
	 * 获取导航分页列表
	 * @param array $condition
	 * @param number $page
	 * @param string $page_size
	 * @param string $order
	 * @param string $field
	 */
	public function getLinkPageList($condition = [], $page = 1, $page_size = PAGE_LIST_ROWS, $order = 'link_sort desc', $field = 'id, link_title, link_url, link_pic, link_sort, is_blank, is_show')
	{
		$data = json_encode([ $condition, $field, $order, $page, $page_size ]);
		$cache = Cache::get("pc_friendly_linkPageList_" . $data);
		if (!empty($cache)) {
			return $this->success($cache);
		}
		$list = model('pc_friendly_link')->pageList($condition, $field, $order, $page, $page_size);
		Cache::tag("pc_friendly_link")->set("pc_friendly_linkPageList_" . $data, $list);
		return $this->success($list);
	}

	/**
	 * 修改排序
	 * @param int $sort
	 * @param int $id
	 */
	public function modifyLinkSort($sort, $id)
	{
		$res = model('pc_friendly_link')->update([ 'link_sort' => $sort ], [ [ 'id', '=', $id ] ]);
		Cache::tag('pc_friendly_link')->clear();
		return $this->success($res);
	}

	/*****************************************首页楼层*****************************************/

	/**
	 * 添加楼层模板
	 * @param $data
	 * @return array
	 */
	public function addFloorBlockList($data)
	{
		$res = model("pc_floor_block")->addList($data);
		return $this->success($res);
	}

	/**
	 * 获取PC端首页楼层模板
	 * @return array
	 */
	public function getFloorBlockList()
	{
		$list = model('pc_floor_block')->getList([], 'id,name,title,value,sort');
		return $this->success($list);
	}

	/**
	 * 添加楼层
	 * @param $data
	 * @return array
	 */
	public function addFloor($data)
	{
		$data[ 'create_time' ] = time();
		$res = model("pc_floor")->add($data);
		return $this->success($res);
	}

	/**
	 * 编辑楼层
	 * @param $data
	 * @param $condition
	 * @return array
	 */
	public function editFloor($data, $condition)
	{
		$res = model("pc_floor")->update($data, $condition);
		return $this->success($res);
	}

	/**
	 * 修改首页楼层排序
	 * @param $sort
	 * @param $condition
	 * @return array
	 */
	public function modifyFloorSort($sort, $condition)
	{
		$res = model('pc_floor')->update([ 'sort' => $sort ], $condition);
		return $this->success($res);
	}

	/**
	 * 删除首页楼层
	 * @param $condition
	 * @return array
	 */
	public function deleteFloor($condition)
	{
		$res = model('pc_floor')->delete($condition);
		return $this->success($res);
	}

	/**
	 * 获取首页楼层信息
	 * @param $condition
	 * @param $field
	 * @return array
	 */
	public function getFloorInfo($condition, $field = 'id,block_id,title,value,state,create_time,sort')
	{
		$res = model("pc_floor")->getInfo($condition, $field);
		return $this->success($res);
	}

	/**
	 * 获取首页楼层详情
	 * @param $id
	 * @return array
	 */
	public function getFloorDetail($id)
	{
		$goods_model = new GoodsModel();
		$goods_brand_model = new GoodsBrandModel();
		$goods_category_model = new GoodsCategoryModel();
		$floor_info = model("pc_floor")->getInfo([ [ 'id', '=', $id ] ], 'id,block_id,title,value,state,sort');
		if (!empty($floor_info)) {
			$value = $floor_info[ 'value' ];
			if (!empty($value)) {
				$value = json_decode($value, true);
				foreach ($value as $k => $v) {
					if (!empty($v[ 'type' ])) {
						if ($v[ 'type' ] == 'goods') {
							// 商品
							$field = 'goods_id,goods_name,goods_image,price,create_time,sku_id,introduction,market_price';
							$order = 'sort desc,create_time desc';
							$list = $goods_model->getGoodsList([ [ 'sku_id', 'in', $v[ 'value' ][ 'sku_ids' ] ] ], $field, $order);
							$list = $list[ 'data' ];
							$value[ $k ][ 'value' ][ 'list' ] = $list;
						} elseif ($v[ 'type' ] == 'brand') {
							// 品牌
							$condition = [
								[ 'brand_id', 'in', $v[ 'value' ][ 'brand_ids' ] ]
							];
							$list = $goods_brand_model->getBrandList($condition, 'brand_id, brand_name, brand_initial,image_url', 'sort desc,create_time desc');
							$list = $list[ 'data' ];
							$value[ $k ][ 'value' ][ 'list' ] = $list;
						} elseif ($v[ 'type' ] == 'category') {
							// 商品分类
							$condition = [
								[ 'category_id', 'in', $v[ 'value' ][ 'category_ids' ] ]
							];
							$list = $goods_category_model->getCategoryList($condition, 'category_id,category_name,short_name,image,image_adv');
							$list = $list[ 'data' ];
							$value[ $k ][ 'value' ][ 'list' ] = $list;
						}
					}
				}
				$floor_info[ 'value' ] = json_encode($value);
			}
		}
		return $this->success($floor_info);
	}

	/**
	 * 获取PC端首页楼层列表
	 * @param array $condition
	 * @param string $field
	 * @param string $order
	 * @return array
	 */
	public function getFloorList($condition = [], $field = 'pf.id,pf.block_id,pf.title,pf.value,pf.state,pf.create_time,pf.sort,fb.name as block_name,fb.title as block_title', $order = 'pf.sort desc,pf.create_time desc')
	{
		$alias = 'pf';
		$join = [
			[ 'pc_floor_block fb', 'pf.block_id = fb.id', 'inner' ]
		];

		$list = model('pc_floor')->getList($condition, $field, $order, $alias, $join);
		return $this->success($list);
	}

	/**
	 * 获取PC端首页楼层分页列表
	 * @param array $condition
	 * @param int $page
	 * @param int $page_size
	 * @param string $order
	 * @param string $field
	 * @return array
	 */
	public function getFloorPageList($condition = [], $page = 1, $page_size = PAGE_LIST_ROWS, $order = 'pf.create_time desc', $field = 'pf.id,pf.block_id,pf.title,pf.value,pf.state,pf.create_time,pf.sort,fb.name as block_name,fb.title as block_title')
	{
		$alias = 'pf';
		$join = [
			[ 'pc_floor_block fb', 'pf.block_id = fb.id', 'inner' ]
		];

		$list = model('pc_floor')->pageList($condition, $field, $order, $page, $page_size, $alias, $join);
		return $this->success($list);
	}

}