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


namespace addon\supply\model\web;


use think\facade\Cache;
use app\model\BaseModel;

/**
 * 广告位管理
 * @author Administrator
 *
 */
class AdvPosition extends BaseModel
{
    /**
     * 添加广告位
     * @param array $data
     */
    public function addAdvPosition($data)
    {
        $ap_id = model('supply_adv_position')->add($data);
        Cache::tag("supply_adv_position")->clear();
        return $this->success($ap_id);
    }

    /**
     * 修改广告位
     * @param array $data
     */
    public function editAdvPosition($data, $condition)
    {
        $res = model('supply_adv_position')->update($data, $condition);
        Cache::tag("supply_adv_position")->clear();
        return $this->success($res);
    }

    /**
     * 删除广告位
     * @param unknown $condition
     */
    public function deleteAdvPosition($condition)
    {
        $res = model('supply_adv_position')->delete($condition);
        Cache::tag("supply_adv_position")->clear();
        return $this->success($res);
    }

    /**
     * 获取广告位基础信息
     * @param $condition
     * @param string $file
     * @return array
     */
    public function getAdvPositionInfo($condition, $file = 'ap_id, keyword , ap_name, ap_intro, ap_height, ap_width, default_content, ap_background_color')
    {
        $data = json_encode([$condition]);
        $cache = Cache::get("supply_adv_position_getAdvPositionInfo_" . $data);
        if (!empty($cache)) {
            return $this->success($cache);
        }
        $res = model('supply_adv_position')->getInfo($condition, $file);
        Cache::tag("supply_adv_position")->set("supply_adv_position_getAdvPositionInfo_" . $data, $res);
        return $this->success($res);
    }

    /**
     * 获取广告位列表
     * @param array $condition
     * @param string $field
     * @param string $order
     * @param string $limit
     */
    public function getAdvPositionList($condition = [], $field = 'ap_id, keyword , ap_name, ap_intro, ap_height, ap_width, default_content, ap_background_color', $order = '', $limit = null)
    {

        $data = json_encode([$condition, $field, $order, $limit]);
        $cache = Cache::get("supply_adv_position_getAdvPositionList_" . $data);
        if (!empty($cache)) {
            return $this->success($cache);
        }
        $list = model('supply_adv_position')->getList($condition, $field, $order, '', '', '', $limit);
        Cache::tag("supply_adv_position")->set("supply_adv_position_getAdvPositionList_" . $data, $list);

        return $this->success($list);
    }

    /**
     * 获取广告位分页列表
     * @param array $condition
     * @param number $page
     * @param string $page_size
     * @param string $order
     * @param string $field
     */
    public function getAdvPositionPageList($condition = [], $page = 1, $page_size = PAGE_LIST_ROWS, $order = 'ap_id desc', $field = 'ap_id, keyword , ap_name, ap_intro, ap_height, ap_width, default_content, ap_background_color')
    {
        $data = json_encode([$condition, $field, $order, $page, $page_size]);
        $cache = Cache::get("supply_adv_position_getAdvPositionPageList_" . $data);
        if (!empty($cache)) {
            return $this->success($cache);
        }
        $list = model('supply_adv_position')->pageList($condition, $field, $order, $page, $page_size);
        Cache::tag("supply_adv_position")->set("supply_adv_position_getAdvPositionPageList_" . $data, $list);
        return $this->success($list);
    }

}
