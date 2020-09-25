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


namespace addon\fenxiao\api\controller;

use app\api\controller\BaseApi;
use addon\fenxiao\model\Config as ConfigModel;
use app\model\system\Document;


/**
 * 分销相关配置
 */
class Config extends BaseApi
{
	
	/**
	 * 提现配置
	 */
	public function withdraw()
	{
		$config = new ConfigModel();
		$res = $config->getFenxiaoWithdrawConfig();
		return $this->response($this->success($res['data']['value']));
	}
	
	/**
	 * 文字设置
	 * @return false|string
	 */
	public function words()
	{
		$config = new ConfigModel();
		$res = $config->getFenxiaoWordsConfig();
		return $this->response($this->success($res['data']['value']));
	}
	
	/**
	 * 申请协议
	 * @return false|string
	 */
	public function agreement()
	{
		$config = new ConfigModel();
		$agreement = $config->getFenxiaoAgreementConfig();
		$res = [];
		$res['agreement'] = $agreement['data']['value'];
		if ($agreement['data']['value']['is_agreement'] == 1) {
			$document_model = new Document();
			$document = $document_model->getDocument([ [ 'site_id', '=', 0 ], [ 'app_module', '=', 'admin' ], [ 'document_key', '=', "FENXIAO_AGREEMENT" ] ]);
			$res['document'] = $document['data'];
		}
		
		return $this->response($this->success($res));
	}
	
	/**
	 * 分销基本设置
	 * @return false|string
	 */
	public function basics()
	{
		$config = new ConfigModel();
		$res = $config->getFenxiaoBasicsConfig();
		return $this->response($this->success($res['data']['value']));
	}
	
	/**
	 * 分销商资格设置
	 * @return false|string
	 */
	public function fenxiao()
	{
		$config = new ConfigModel();
		$res = $config->getFenxiaoConfig();
		return $this->response($this->success($res['data']['value']));
		
	}
	
	/**
	 * 获取上下级关系设置
	 * @return false|string
	 */
	public function relation()
	{
		$config = new ConfigModel();
		$res = $config->getFenxiaoRelationConfig();
		return $this->response($this->success($res['data']['value']));
		
	}
	
}