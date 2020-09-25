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


namespace app\model\system;

use think\facade\Cache;
use app\model\BaseModel;
/**
 * 地区表
 */
class Address extends BaseModel
{
    /**
     * 获取地区列表
     * @param unknown $condition
     * @param string $field
     * @param string $order
     * @param string $limit
     * @return multitype:string mixed
     */
    public function getAreaList($condition = [], $field = '*', $order = '', $limit = null){
        
        $data = json_encode([$condition, $field, $order, $limit]);
        $cache = Cache::get("area_getAreaList_".$data);
        if(!empty($cache))
        {
            return $this->success($cache);
        }
        $area_list = model("area")->getList($condition, $field, $order, $limit);
        Cache::tag("area")->set("area_getAreaList_".$data, $area_list);
        return $this->success($area_list);
    }


    /**
     * 获取地区详情
     */
    public function getAreaInfo($circle){
        
        $cache = Cache::get("area_getAreaInfo_".$circle);
        if(!empty($cache))
        {
            return $this->success($cache);
        }
        $info = model("area")->getInfo([['id', '=', $circle]]);
        Cache::tag("area")->set("area_getAreaInfo_".$circle, $info);
        return $this->success($info);
        
        
    }

    /**
     * 获取省市子项
     */
    public function getAreas($circle = 0){
        
        $cache = Cache::get("area_getAreas_".$circle);
        if(!empty($cache))
        {
            return $this->success($cache);
        }
        $list = model("area")->getList([['pid', '=', $circle]]);
        Cache::tag("area")->set("area_getAreas_".$circle, $list);
        return $this->success($list);
    }

    /**
     * 获取整理后的地址
     */
    public function getAddressTree($level = 4) {
        $condition = [['level', '<=', $level]];
        $json_condition = json_encode($condition);
        $cache = Cache::get("area_getAddressTree".$json_condition);
        if(!empty($cache))
        {
            return $this->success($cache);
        }
        $area_list = $this->getAreaList($condition, "id, pid, name, level", "id asc")['data'];
        //组装数据
        $refer_list = [];
        foreach($area_list as $key=>$val){
            $refer_list[$val['level']][$val['pid']]['child_list'][$val['id']] =  $area_list[$key];
            if(isset($refer_list[$val['level']][$val['pid']]['child_num'])) {
                $refer_list[$val['level']][$val['pid']]['child_num'] += 1;
            }else {
                $refer_list[$val['level']][$val['pid']]['child_num'] = 1;
            }
        }
        Cache::tag("area")->set("area_getAddressTree".$json_condition, $refer_list);
        return $this->success($refer_list);
    }
    
    /**
     * 获取地址
     * @param array $condition
     * @param string $field
     * @return multitype:number unknown
     */
    public function getAreasInfo(array $condition, string $field = '*'){
        $info = model("area")->getInfo($condition, $field);
        if ($info) return $this->success($info);
        return $this->error();
    }
}
