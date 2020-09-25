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


namespace addon\live\model;

use app\model\BaseModel;
use app\model\system\Config as ConfigModel;

/**
 *  好物圈
 */
class Config extends BaseModel
{
    /******************************************************************** 微信好物圈配置 start ****************************************************************************/
    /**
     * 设置微信好物圈配置
     * @return multitype:string mixed
     */
    public function setGoodscircleConfig($data, $is_use, $site_id)
    {
        $config = new ConfigModel();
        $res = $config->setConfig($data, '微信好物圈设置', $is_use, [['site_id', '=', $site_id], ['app_module', '=', 'shop'], ['config_key', '=', 'GOODSCIRCLE_CONFIG']]);
        return $res;
    }

    /**
     * 获取微信好物圈配置信息
     * @return multitype:string mixed
     */
    public function getGoodscircleConfig($site_id)
    {
        $config = new ConfigModel();
        $res = $config->getConfig([['site_id', '=', $site_id], ['app_module', '=', 'shop'], ['config_key', '=', 'GOODSCIRCLE_CONFIG']]);
        return $res;
    }
    /******************************************************************** 微信好物圈配置 end ****************************************************************************/

}