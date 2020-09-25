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


namespace addon\qiniu\model;

use app\model\system\Config as ConfigModel;
use app\model\BaseModel;

/**
 * 七牛云配置
 */
class Config extends BaseModel
{
	/**
	 * 设置七牛云上传配置
	 * array $data
	 */
	public function setQiniuConfig($data, $status)
	{
	    if($status == 1){
                event("CloseOss", []);//同步关闭所有云上传
          }
		$config = new ConfigModel();
		$res = $config->setConfig($data, '七牛云上传配置', $status, [ [ 'site_id', '=', 0 ], [ 'app_module', '=', 'admin' ], [ 'config_key', '=', 'QINIU_CONFIG' ] ]);
		return $res;
	}
	
	/**
	 * 获取七牛云上传配置
	 */
	public function getQiniuConfig()
	{
		$config = new ConfigModel();
		$res = $config->getConfig([ [ 'site_id', '=', 0 ], [ 'app_module', '=', 'admin' ], [ 'config_key', '=', 'QINIU_CONFIG' ] ]);
		return $res;
	}

    /**
     * 配置七牛云开关状态
     * @param $status
     */
    public function modifyConfigIsUse($status){
        $config = new ConfigModel();
        $res = $config->modifyConfigIsUse($status, [ [ 'site_id', '=', 0 ], [ 'app_module', '=', 'admin' ], [ 'config_key', '=', 'QINIU_CONFIG' ] ]);
        return $res;
    }
}