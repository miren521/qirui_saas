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

use app\model\system\Config as ConfigModel;
use app\model\BaseModel;
use extend\Kdbird as KdbirdExtend;
/**
 * 快递鸟
 */
class Kdbird extends BaseModel
{
    
    /*********************************************************************** 快递100 start ***********************************************************************/
    /**
     * 快递鸟配置
     * @param $site_id
     * @return \multitype
     */
    public function getKdbirdConfig(){
        $config = new ConfigModel();
        $res = $config->getConfig([['app_module', '=', 'admin'],["site_id", "=", 0], ['config_key', '=', 'EXPRESS_KDBIRD_CONFIG']]);
        return $res;
    }

    /**
     * 设置物流配送配置
     * @param $data
     * @return \multitype
     */
    public function setKdbirdConfig($data, $is_use)
    {
        if($is_use > 0){
            event("CloseTrace", []);
        }
        $config = new ConfigModel();
        $res = $config->setConfig($data, '快递鸟设置', $is_use, [['app_module', '=', 'admin'],["site_id", "=", 0], ['config_key', '=', 'EXPRESS_KDBIRD_CONFIG']]);
        return $res;
    }

    /**
     * 开关状态
     * @param $is_use
     * @return array
     */
    public function modifyStatus($is_use){
        $config = new ConfigModel();
        $res = $config->modifyConfigIsUse($is_use, [['app_module', '=', 'admin'],["site_id", "=", 0], ['config_key', '=', 'EXPRESS_KDBIRD_CONFIG']]);
        return $res;
    }
    /*********************************************************************** 快递100 end ***********************************************************************/

    /**
     * 查询物流轨迹 并且转化为兼容数据结构
     * @param $code
     * @param $express_no
     * @return array
     */
    public function trace($code, $express_no, $customer_name = ''){
        $config_result = $this->getKdbirdConfig();
        $config = $config_result["data"];
        if($config["is_use"]) {
            $kd100_extend = new KdbirdExtend($config["value"]);

            $customer_name = substr($customer_name,-4);//截取手机号后四位
            $result = $kd100_extend->orderTracesSubByJson($express_no, $code, $customer_name);
            if (isset($result['success']) && $result['success']) {
                return $this->success($result);
            } else {
                return $this->error($result,$result['reason']);
            }
        }
    }
}