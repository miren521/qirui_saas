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


namespace app\model\member;

use app\model\system\Document;
use app\model\system\Config as ConfigModel;
use app\model\BaseModel;

/**
 * 会员设置
 */
class Config extends BaseModel
{
	/**
	 * 注册协议
	 * @param unknown $site_id
	 * @param unknown $name
	 * @param unknown $value
	 */
	public function setRegisterDocument($title, $content)
	{
		$document = new Document();
		$res = $document->setDocument($title, $content, [ [ 'site_id', '=', 0 ], [ 'app_module', '=', 'admin' ], [ 'document_key', '=', 'REGISTER_AGREEMENT' ] ]);
		return $res;
	}

	/**
	 * 查询注册协议
	 * @param unknown $where
	 * @param unknown $field
	 * @param unknown $value
	 */
	public function getRegisterDocument()
	{
		$document = new Document();
		$info = $document->getDocument([ [ 'site_id', '=', 0 ], [ 'app_module', '=', 'admin' ], [ 'document_key', '=', 'REGISTER_AGREEMENT' ] ]);
		return $info;
	}

	/**
	 * 注册规则
	 * array $data
	 */
	public function setRegisterConfig($data)
	{
		$config = new ConfigModel();
		$res = $config->setConfig($data, '注册规则', 1, [ [ 'site_id', '=', 0 ], [ 'app_module', '=', 'admin' ], [ 'config_key', '=', 'REGISTER_CONFIG' ] ]);
		return $res;
	}

	/**
	 * 查询注册规则
	 */
	public function getRegisterConfig()
	{
		$config = new ConfigModel();
		$res = $config->getConfig([ [ 'site_id', '=', 0 ], [ 'app_module', '=', 'admin' ], [ 'config_key', '=', 'REGISTER_CONFIG' ] ]);
		if (empty($res[ 'data' ][ 'value' ])) {
			//默认值设置
			$res[ 'data' ][ 'value' ] = [
				'is_enable' => 1,
				'type' => 'plain',
				'keyword' => '',
				'pwd_len' => 6,
				'pwd_complexity' => '',
				'dynamic_code_login' => 0
			];
		}
		return $res;
	}
}