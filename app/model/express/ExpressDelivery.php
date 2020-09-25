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

use app\model\member\Member;
use app\model\BaseModel;
use addon\electronicsheet\model\ExpressElectronicsheet;

/**
 * 物流配送
 */
class ExpressDelivery extends BaseModel
{

    /**
     * 物流配送
     */
    public function delivery($param)
    {
        $order_id = $param[ "order_id" ] ?? 0;//订单id
        $order_goods_id_array = $param[ "order_goods_id_array" ];
        $goods_id_array = $param[ "goods_id_array" ];
        $delivery_type = $param[ "delivery_type" ];//物流方式  1 物流配送  0 无需物流
        $delivery_no = $param[ "delivery_no" ] ?? '';//物流单号
        $member_id = $param[ "member_id" ];
        $site_id = $param[ "site_id" ];
        $member_name = $param[ "member_name" ] ?? '';

        if ($param[ 'type' ] == 'manual') {
            $express_company_id = $param[ "express_company_id" ] ?? 0;
            $express_company_name = "";

            if ($express_company_id > 0) {
                $express_company_info = model("express_company")->getInfo([["company_id", "=", $express_company_id]], "company_name");
                $express_company_name = $express_company_info[ "company_name" ];
            }
            $template_id = 0;
            $template_name = '';
        } else {
            $delivery_type = 1;
            //获取模板信息
            $template_model = new ExpressElectronicsheet();
            $template_info = $template_model->getExpressElectronicsheetInfo(
                [
                    ['id', '=', $param[ 'template_id' ]],
                    ['site_id', '=', $site_id]
                ],
                'template_name,company_id,company_name'
            );
            $template_id = $param[ 'template_id' ];
            $template_name = $template_info[ 'data' ][ 'template_name' ];
            $express_company_id = $template_info[ 'data' ][ "company_id" ];
            $express_company_name = $template_info[ 'data' ][ "company_name" ];
        }

        //查询物流单号是否已存在,如果存在就合并入已存在的数据
        $condition = array(
            ["site_id", "=", $site_id],
            ["delivery_no", "=", $delivery_no],
            ["order_id", "=", $order_id],
            ["delivery_type", "=", $delivery_type],
            ["express_company_id", "=", $express_company_id],
            ["member_id", "=", $member_id]
        );
        $info = model("express_delivery_package")->getInfo($condition, "*");

        if (empty($info)) {
            if ($delivery_type > 0) {
                $count = model("express_delivery_package")->getCount([["site_id", "=", $site_id], ["order_id", "=", $order_id], ["delivery_type", "=", $delivery_type]]);
                $num = $count + 1;
                $package_name = "包裹" . $num;
            } else {
                $package_name = "无需物流";
            }
            $express_company_info = model("express_company")->getInfo([["company_id", "=", $express_company_id]], "logo");
            $express_company_image = empty($express_company_info) ? '' : $express_company_info[ "logo" ];
            $data = array(
                "order_id" => $order_id,
                "order_goods_id_array" => implode(",", $order_goods_id_array),
                "goods_id_array" => implode(",", $goods_id_array),
                "delivery_no" => $delivery_no,
                "site_id" => $site_id,
                "member_id" => $member_id,
                "member_name" => $member_name,
                "delivery_type" => $delivery_type,
                "express_company_id" => $express_company_id,
                "express_company_name" => $express_company_name,
                "package_name" => $package_name,
                "delivery_time" => time(),
                "express_company_image" => $express_company_image,
                "type" => $param[ 'type' ],
                "template_id" => $template_id,
                "template_name" => $template_name
            );
            $result = model("express_delivery_package")->add($data);
        } else {
            $temp_order_goods_id_arr = explode(",", $info[ "order_goods_id_array" ]);
            $temp_goods_id_arr = explode(",", $info[ "goods_id_array" ]);

            $order_goods_id_array = implode(",", array_unique(array_merge($temp_order_goods_id_arr, $order_goods_id_array)));
            $goods_id_array = implode(",", array_merge($temp_goods_id_arr, $goods_id_array));
            $data = array(
                "order_goods_id_array" => $order_goods_id_array,
                "goods_id_array" => $goods_id_array,
            );
            $result = model("express_delivery_package")->update($data, $condition);
        }
        return $result;
    }

}