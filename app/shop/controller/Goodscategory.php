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

use app\model\goods\GoodsAttribute as GoodsAttributeModel;
use app\model\goods\GoodsShopCategory as GoodsShopCategoryModel;
use app\model\goods\GoodsCategory as GoodsategoryModel;

/**
 * 商品分类管理 控制器
 */
class Goodscategory extends BaseShop
{
	/**
	 * 商品分类列表
	 */
	public function lists()
	{
		$goods_category_model = new GoodsShopCategoryModel();
		$list = $goods_category_model->getShopCategoryTree([ [ 'site_id', "=", $this->site_id ] ]);
		$list = $list['data'];
		$this->assign("list", $list);
		return $this->fetch('goodscategory/lists');
	}
	
	/**
	 * 商品分类添加
	 */
	public function addCategory()
	{
		$goods_category_model = new GoodsShopCategoryModel();
		if (request()->isAjax()) {
			
			$category_name = input('category_name', '');// 分类名称
			$short_name = input('short_name', '');// 简称
			$pid = input('pid', 0);//默认添加的商品分类为顶级
			$level = input('level', 1);// 层级
			$is_show = input('is_show', '');// 是否显示
			$sort = input('sort', '');// 排序
			$image = input('image', '');// 分类图片
			$keywords = input('keywords', '');// 分类页面关键字
			$description = input('description', '');// 分类介绍
			$category_id_1 = input('category_id_1', 0);// 一级分类id
			$category_id_2 = input('category_id_2', 0);// 二级分类id
			$category_full_name = input('category_full_name', '');;// 组装名称
			
			$data = [
				'site_id' => $this->site_id,
				'category_name' => $category_name,
				'short_name' => $short_name,
				'pid' => $pid,
				'level' => $level,
				'is_show' => $is_show,
				'sort' => $sort,
				'image' => $image,
				'keywords' => $keywords,
				'description' => $description,
				'category_id_1' => $category_id_1,
				'category_id_2' => $category_id_2,
				'category_full_name' => $category_full_name
			];
			$res = $goods_category_model->addShopCategory($data);
			if (!empty($res['data'])) {
				
				//修改category_id_
				$update_data = [
					'category_id' => $res['data'],
					'category_id_' . $level => $res['data']
				];
				$goods_category_model->editShopCategory($update_data);
				
			}
			return $res;
		} else {
			return $this->fetch('goodscategory/add_category');
		}
	}
	
	/**
	 * 商品分类编辑
	 */
	public function editCategory()
	{
		$goods_category_model = new GoodsShopCategoryModel();
		if (request()->isAjax()) {
			$category_id = input('category_id', '');// 分类id
			$category_name = input('category_name', '');// 分类名称
			$short_name = input('short_name', '');// 简称
			$pid = input('pid', 0);//默认添加的商品分类为顶级
			$level = input('level', 1);// 层级
			$is_show = input('is_show', '');// 是否显示
			$sort = input('sort', '');// 排序
			$image = input('image', '');// 分类图片
			$keywords = input('keywords', '');// 分类页面关键字
			$description = input('description', '');// 分类介绍
			$category_id_1 = input('category_id_1', 0);// 一级分类id
			$category_id_2 = input('category_id_2', 0);// 二级分类id
			$category_full_name = input('category_full_name', '');;// 组装名称
			
			$data = [
				'site_id' => $this->site_id,
				'category_id' => $category_id,
				'category_name' => $category_name,
				'short_name' => $short_name,
				'pid' => $pid,
				'level' => $level,
				'is_show' => $is_show,
				'sort' => $sort,
				'image' => $image,
				'keywords' => $keywords,
				'description' => $description,
				'category_id_1' => $category_id_1,
				'category_id_2' => $category_id_2,
				'category_full_name' => $category_full_name
			];
			
			$res = $goods_category_model->editShopCategory($data);
			return $res;
		} else {
			
			$category_id = input('category_id', '');// 分类id
			
			if (empty($category_id)) {
				$this->error("缺少参数category_id");
			}
			
			$goods_category_info = $goods_category_model->getShopCategoryInfo([ [ 'category_id', '=', $category_id ] ]);
			$goods_category_info = $goods_category_info['data'];
			$this->assign("goods_category_info", $goods_category_info);
			
			//父级
			$goods_category_parent_info = $goods_category_model->getShopCategoryInfo([ [ 'category_id', '=', $goods_category_info['pid'] ] ], 'category_name');
			$goods_category_parent_info = $goods_category_parent_info['data'];
			$this->assign("goods_category_parent_info", $goods_category_parent_info);
			
			$goods_attribute_model = new GoodsAttributeModel();
			
			// 商品类型列表
			$attr_class_list = $goods_attribute_model->getAttrClassList([], 'class_id,class_name');
			$attr_class_list = $attr_class_list['data'];
			$this->assign("attr_class_list", $attr_class_list);
			
			return $this->fetch('goodscategory/edit_category');
		}
	}
	
	/**
	 * 商品分类删除
	 */
	public function deleteCategory()
	{
		if (request()->isAjax()) {
			$category_id = input('category_id', '');// 分类id
			$goods_category_model = new GoodsShopCategoryModel();
			$res = $goods_category_model->deleteShopCategory($category_id);
			return $res;
		}
	}
	
	/**
	 * 获取商品分类列表
	 * @return \multitype
	 */
	public function getCategoryList()
	{
		$pid = input('pid', 0);// 上级id
		$level = input('level', 0);// 层级
		$goods_category_model = new GoodsShopCategoryModel();

		$condition[] = ['site_id','=',$this->site_id];
		if (!empty($level)) {
			$condition[] = [ 'level', '=', $level ];
		} else {
			$condition[] = [ 'pid', '=', $pid ];
		}
		$list = $goods_category_list = $goods_category_model->getShopCategoryList($condition, 'category_id,category_name,pid,level,category_id_1,category_id_2');
		return $list;
	}
	
	/**
	 * 获取商品分类信息
	 * @return \multitype
	 */
	public function getCategoryInfo()
	{
		$category_id = input('category_id', '');// 分类id
		$goods_category_model = new GoodsShopCategoryModel();
		$condition = [
			[ 'category_id', '=', $category_id ]
		];
		$res = $goods_category_model->getShopCategoryInfo($condition, 'category_name');
		return $res;
	}

	/**
     * 修改店内分类排序
     */
	public function editCategorySort()
    {
        if(request()->isAjax()){

            $category_id = input('category_id', '');// 分类id
            $sort = input('sort',0);
            $goods_category_model = new GoodsShopCategoryModel();

            $data = [
                'category_id' => $category_id,
                'sort' => $sort
            ];
            return $goods_category_model->editShopCategory($data);
        }

    }



    /**
     * 获取商品分类
     * @return \multitype
     */
    public function getCategoryByParent()
    {
        $pid = input('pid', 0);// 上级id
        $level = input('level', 0);// 层级
        $goods_category_model = new GoodsategoryModel();
        if (!empty($level)) {
            $condition[] = [ 'level', '=', $level ];
        }
        if (!empty($pid)) {
            $condition[] = [ 'pid', '=', $pid ];
        }
        $list = $goods_category_list = $goods_category_model->getCategoryByParent($condition, 'category_id,category_name,pid,level,category_id_1,category_id_2,category_id_3');
        return $list;
    }
}