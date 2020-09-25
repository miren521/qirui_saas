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
class Trace extends BaseModel
{

    /**
     * 物流跟踪信息
     * @param $code
     */
    public function trace($code, $company_id, $mobile = '')
    {
//        $result = array(
//            "success" => true,//成功与否
//            "reason" => '',//错误原因
//            "status" => '1',//物流状态:0-无轨迹,1-已揽收, 2-在途中 201-到达派件城市，3-签收,4-问题件
//            "status_name" => '已揽收',//物流状态名称:0-无轨迹,1-已揽收, 2-在途中 201-到达派件城市，3-签收,4-问题件,
//            "shipper_code" => 'SH',//快递公司编码
//            "logistic_code" => $code,//物流运单号
//            "list" => array(
//                [
//                    "datetime" => "2015-03-08 01:15:00",
//                    "remark" => "离开广州市 发往北京市（经转）",
//                ]
//            )
//        );
        $express_company_model = new ExpressCompany();
        $company_info_result = $express_company_model->getExpressCompanyInfo([["company_id", "=", $company_id]]);
        if(empty($company_info_result["data"]))
            return $this->success(["success" => false, "reason" => "物流公司信息不完整!"]);

        $result = event("Trace", ["code" => $code, "express_no_data" => $company_info_result["data"], 'mobile' => $mobile], true);

        if(empty($result)) {
            $data = ["success" => false, "reason" => "抱歉，没有启用的物流方式"];
            return $this->success($data);
        }
        if($result['code'] < 0){
            $data = ["success" => false, "reason" => "抱歉，暂无物流记录"];
            return $this->success($data);
        }
        return $result;
    }
    
}