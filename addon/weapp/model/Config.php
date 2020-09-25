<?php
// +---------------------------------------------------------------------+
// | NiuCloud | [ WE CAN DO IT JUST NiuCloud ]                |
// +---------------------------------------------------------------------+
// | Copy right 2019-2029 www.niucloud.com                          |
// +---------------------------------------------------------------------+
// | Author | NiuCloud <niucloud@outlook.com>                       |
// +---------------------------------------------------------------------+
// | Repository | https://github.com/niucloud/framework.git          |
// +---------------------------------------------------------------------+

namespace addon\weapp\model;

use app\model\system\Config as ConfigModel;
use app\model\BaseModel;
/**
 * 微信小程序配置
 */
class Config extends BaseModel
{
	/******************************************************************** 微信小程序配置 start ****************************************************************************/
	/**
	 * 设置微信小程序配置
	 * @return multitype:string mixed
	 */
	public function setWeappConfig($data, $is_use)
	{
	    $config = new ConfigModel();
	    $res = $config->setConfig($data, '微信公小程序设置', $is_use, [['site_id', '=',  0], ['app_module', '=', 'admin'], ['config_key', '=', 'WEAPP_CONFIG']]);
	    return $res;
	}
	
	/**
	 * 获取微信小程序配置信息
	 * @return multitype:string mixed
	 */
	public function getWeappConfig()
	{
	    $config = new ConfigModel();
	    $res = $config->getConfig([['site_id', '=',  0], ['app_module', '=', 'admin'], ['config_key', '=', 'WEAPP_CONFIG']]);
	    return $res;
	}
    /******************************************************************** 微信小程序配置 end ****************************************************************************/

}