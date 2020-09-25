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


namespace app\model\shop;


use app\model\system\Config as ConfigModel;
use app\model\system\Document as DocumentModel;
use app\model\BaseModel;

/**
 * 店铺设置信息
 */
class Config extends BaseModel
{
    /**
     * 获取系统银行账户
     */
    public function getSystemBankAccount()
    {
        $config = new ConfigModel();
        $res = $config->getConfig([ [ 'site_id', '=', 0 ], [ 'app_module', '=', 'admin' ], [ 'config_key', '=', 'SYSTEM_BANK_ACCOUNT' ] ]);
        if(empty($res['data']['value']))
        {
            $res['data']['value'] = [
                'bank_account_name' => '',
                'bank_account_no' => '',
                'bank_name' => '',
                'bank_address' => ''
            ];
        }
        return $res;
    }
    
    /**
     * 设置系统银行账户
     * @param unknown $data
     * @return Ambigous <multitype:unknown , multitype:number unknown >
     */
    public function setSystemBankAccount($data)
    {
        $config = new ConfigModel();
        $res = $config->setConfig($data, '平台银行账户', 1, [ [ 'site_id', '=', 0 ], [ 'app_module', '=', 'admin' ], [ 'config_key', '=', 'SYSTEM_BANK_ACCOUNT' ] ]);
        return $res;
    }
	/**
	 * 获取商家入驻广告设置
	 */
	public function getShopJoinAdvConfig()
	{
		$config = new ConfigModel();
		$res = $config->getConfig([ [ 'site_id', '=', 0 ], [ 'app_module', '=', 'admin' ], [ 'config_key', '=', 'SHOP_JOIN_ADV_CONFIG' ] ]);
		return $res;
	}
	
	/**
	 * 设置商家入驻广告
	 */
	public function setShopJoinAdvConfig($data)
	{
		$config = new ConfigModel();
		$res = $config->setConfig($data, '商家入驻广告设置', 1, [ [ 'site_id', '=', 0 ], [ 'app_module', '=', 'admin' ], [ 'config_key', '=', 'SHOP_JOIN_ADV_CONFIG' ] ]);
		return $res;
	}
	
	/**
	 * 获取入驻协议
	 */
	public function getShopApplyAgreement()
	{
		$document = new DocumentModel();
		$info = $document->getDocument([ [ 'site_id', '=', 0 ], [ 'app_module', '=', 'admin' ], [ 'document_key', '=', "SHOP_APPLY_AGREEMENT" ] ]);
		return $info;
	}
	
	/**
	 * 设置入驻协议
	 */
	public function setShopApplyAgreement($title, $content)
	{
		$document = new DocumentModel();
		$res = $document->setDocument($title, $content, [ [ 'site_id', '=', 0 ], [ 'app_module', '=', 'admin' ], [ 'document_key', '=', "SHOP_APPLY_AGREEMENT" ] ]);
		return $res;
	}
	/**
	 * 设置商家入驻指南
	 * @param $title
	 * @param $content
	 * @param $guide_index
	 * @return \app\model\system\multitype
	 */
	public function setShopJoinGuideDocument($title, $content, $guide_index)
	{
		$document = new DocumentModel();
		$res = $document->setDocument($title, $content, [ [ 'site_id', '=', 0 ], [ 'app_module', '=', 'admin' ], [ 'document_key', '=', "SHOP_JOIN_GUIDE_{$guide_index}" ] ]);
		return $res;
	}
	
	/**
	 * 获取商家入驻指南
	 * @param $guide_index
	 * @return array|\multitype
	 */
	public function getShopJoinGuideDocument($guide_index)
	{
		$document = new DocumentModel();
		$info = $document->getDocument([ [ 'site_id', '=', 0 ], [ 'app_module', '=', 'admin' ], [ 'document_key', '=', "SHOP_JOIN_GUIDE_{$guide_index}" ] ]);
		return $info;
	}
	
	/**
	 * 获取所有商家入驻指南
	 * @return array
	 */
	public function getShopJoinGuide()
	{
		$guide_num = 4;
		$guide_list = [];
		for ($index = 1; $index <= $guide_num; $index++) {
			$guide = $this->getShopJoinGuideDocument($index);
			if (empty($guide['data']['title'])) {
				$guide['data']['title'] = '入驻指南' . $index;
			}
			$guide['data']['guide_index'] = $index;
			$guide_list[] = $guide['data'];
		}
		return $this->success($guide_list);
	}
}