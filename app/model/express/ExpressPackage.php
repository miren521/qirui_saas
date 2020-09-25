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


namespace app\model\express;

use app\model\BaseModel;

/**
 * 物流配送
 */
class ExpressPackage extends BaseModel
{

    /**
     * 获取物流包裹列表
     * @param $condition
     * @param string $field
     */
    public function getExpressDeliveryPackageList($condition, $field = "*"){
        $list = model("express_delivery_package")->getList($condition, $field);
        return $this->success($list);
    }

    /**
     * 获取包裹信息
     * @param $condition
     */
    public function package($condition, $mobile = ''){
        $list_result = $this->getExpressDeliveryPackageList($condition);
        $list = $list_result["data"];
        $trace_model = new Trace();
        foreach($list as $k => $v){
            $temp_array = explode(",", $v["goods_id_array"]);
            if(!empty($temp_array)){
                foreach($temp_array as $temp_k => $temp_v){
                    $temp_str = str_replace("http://", "http//", $temp_v);
                    $temp_str = str_replace("https://", "https//", $temp_str);
                    $temp_item = explode(":", $temp_str);
                    $sku_image = str_replace("https//", "https://", $temp_item["3"]);
                    $sku_image = str_replace("http//", "http://", $sku_image);
                    $list[$k]["goods_list"][] = ["sku_name" => $temp_item["2"], "num" => $temp_item["1"], "sku_image" => $sku_image, "sku_id" => $temp_item["0"]];
                }
            }

            $trace_list = $trace_model->trace($v["delivery_no"],$v["express_company_id"], $mobile);
            $list[$k]["trace"] = $trace_list["data"];
        }
        return $list;

    }

}