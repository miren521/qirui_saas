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

use app\model\system\Config as ConfigModel;
use app\model\BaseModel;
/**
 * 接口api配置
 */
class Api extends BaseModel
{


    /***************************************************************接口api 开始********************************************************/
    /**
     * 获取api配置
     */
    public function getApiConfig(){
        $config = new ConfigModel();
        $res = $config->getConfig([['site_id', '=',  0], ['app_module', '=', 'admin'], ['config_key', '=', 'API_CONFIG']]);
        return $res;
    }

    /**
     * 设置api配置
     * @param $data
     * @return \multitype
     */
    public function setApiConfig($data, $is_use)
    {
        $config = new ConfigModel();
        $res = $config->setConfig($data, 'api配置', $is_use, [['site_id', '=',  0], ['app_module', '=', 'admin'], ['config_key', '=', 'API_CONFIG']]);
        return $res;
    }
	/***************************************************************接口api 结束********************************************************/
	
}