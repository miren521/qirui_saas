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


namespace addon\supply\model;


use think\facade\Cache;
use app\model\BaseModel;

/**
 * 供应商主营行业类型
 */

class SupplyCategory extends BaseModel
{

    /**
     * 添加供应商主营行业
     * @param array $data
     */
    public function addCategory($data)
    {
        $res = model('supply_category')->add($data);
        Cache::tag("supply_category")->clear();
        return $this->success($res);
    }

    /**
     * 修改供应商主营行业
     * @param array $data
     */
    public function editCategory($data)
    {
        $res = model('supply_category')->update($data, [['category_id', '=', $data['category_id'] ]]);
        //修改对应供应商
        model('supplier')->update(['category_name' => $data['category_name']], [['category_id', '=', $data['category_id']]]);
        model('supply_apply')->update(['category_name' => $data['category_name']], [[ 'category_id', '=', $data['category_id'] ]]);
        Cache::tag("supply_category")->clear();
        return $this->success($res);
    }

    /**
     * 删除供应商主营行业
     * @param $condition
     * @return array
     */
    public function deleteCategory($condition)
    {
        $check_condition = array_column($condition, 2, 0);
        $category_id = $check_condition['category_id'];
        $count = model('supplier')->getCount([['category_id','in',$category_id]]);
        if($count==0) {
            $res = model('supply_category')->delete($condition);
            Cache::tag("supply_category")->clear();
            return $this->success($res);
        }else{
            return $this->error('','该主营行业存在其他供应商，无法删除');
        }
    }

    /**
     * 获取供应商主营行业信息
     * @param $condition
     * @param string $field
     */
    public function getCategoryInfo($condition, $field = '*')
    {
        $data = json_encode([ $condition, $field]);
        $cache = Cache::get("supply_category_getCategoryInfo_" . $data);
        if (!empty($cache)) {
            return $this->success($cache);
        }
        $res = model('supply_category')->getInfo($condition, $field);
        Cache::tag("supply_category")->set("supply_category_getCategoryInfo_" . $data, $res);
        return $this->success($res);
    }

    /**
     * 获取供应商主营行业列表
     * @param array $condition
     * @param string $field
     * @param string $order
     * @param null $limit
     * @return array
     */
    public function getCategoryList($condition = [], $field = '*', $order = '', $limit = null)
    {

        $data = json_encode([ $condition, $field, $order, $limit ]);
        $cache = Cache::get("supply_category_getCategoryList_" . $data);
        if (!empty($cache)) {
            return $this->success($cache);
        }
        $list = model('supply_category')->getList($condition, $field, $order, '', '', '', $limit);
        Cache::tag("supply_category")->set("supply_category_getCategoryList_" . $data, $list);

        return $this->success($list);
    }

    /**
     * 获取供应商主营行业分页列表
     * @param array $condition
     * @param int $page
     * @param int $page_size
     * @param string $order
     * @param string $field
     * @return array
     */
    public function getCategoryPageList($condition = [], $page = 1, $page_size = PAGE_LIST_ROWS, $order = '', $field = '*')
    {
        $data = json_encode([ $condition, $field, $order, $page, $page_size ]);
        $cache = Cache::get("supply_category_getCategoryPageList_" . $data);
        if (!empty($cache)) {
            return $this->success($cache);
        }
        $list = model('supply_category')->pageList($condition, $field, $order, $page, $page_size);
        Cache::tag("supply_category")->set("supply_category_getCategoryPageList_" . $data, $list);
        return $this->success($list);
    }
}