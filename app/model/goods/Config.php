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


namespace app\model\goods;

use app\model\system\Config as ConfigModel;
use app\model\BaseModel;
use app\model\system\Document as DocumentModel;

/**
 * 商品设置
 */
class Config extends BaseModel
{

	/**
	 * 商品审核设置
	 * array $data
	 */
	public function setVerifyConfig($data)
	{
		$config = new ConfigModel();
		$res = $config->setConfig($data, '商品审核设置', 1, [ [ 'site_id', '=', 0 ], [ 'app_module', '=', 'shop' ], [ 'config_key', '=', 'GOODS_VERIFY_CONFIG' ] ]);
		return $res;
	}
	
	/**
	 * 查询商品审核设置
	 */
	public function getVerifyConfig()
	{
		$config = new ConfigModel();
		$res = $config->getConfig([ [ 'site_id', '=', 0 ], [ 'app_module', '=', 'shop' ], [ 'config_key', '=', 'GOODS_VERIFY_CONFIG' ] ]);
		return $res;
	}
	
	/**
	 * 获取售后保障设置
	 */
	public function getAfterSaleConfig()
	{
        $document = new DocumentModel();
		$info = $document->getDocument([ [ 'site_id', '=', 0 ], [ 'app_module', '=', 'admin' ], [ 'document_key', '=', "GOODS_AFTER_SALE" ] ]);
		return $info;
	}
	
	/**
	 * 设置售后保障
	 * @param unknown $content
	 */
	public function setAfterSaleConfig($title,$content)
	{
	    $document = new DocumentModel();
	    $res = $document->setDocument($title, $content, [ [ 'site_id', '=', 0 ], [ 'app_module', '=', 'admin' ], [ 'document_key', '=', "GOODS_AFTER_SALE" ] ]);
	    return $res;
	}
	
}