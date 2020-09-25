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

namespace addon\fenxiao\model;

use app\model\system\Config as ConfigModel;
use app\model\BaseModel;
use app\model\system\Document;

/**
 * 微信小程序配置
 */
class Config extends BaseModel
{
	/******************************************************************** 分销基本配置 start ****************************************************************************/
	/**
	 * 设置分销基本配置
	 * @return multitype:string mixed
	 */
	public function setFenxiaoBasicsConfig($data, $is_use)
	{
		$config = new ConfigModel();
		
		//分销基本设置
		$basics_data = [
			'level' => $data['level'],//分销层级
			'internal_buy' => $data['internal_buy'],//分销内购
			'is_examine' => $data['is_examine'],//是否需要审核
		];
		$config->setConfig($basics_data, '分销基本配置', $is_use, [ [ 'site_id', '=', 0 ], [ 'app_module', '=', 'admin' ], [ 'config_key', '=', 'FENXIAO_BASICS_CONFIG' ] ]);
		//分销商设置
		$fenxiao_data = [
			'fenxiao_condition' => $data['fenxiao_condition'],//成为分销商条件(0无条件 1申请 2消费次数 3消费金额 4购买商品)
			'consume_count' => $data['consume_count'],//消费次数
			'consume_money' => $data['consume_money'],//3消费金额
			'consume_condition' => $data['consume_condition'],//消费条件(1付款后 2订单完成)
			'perfect_info' => $data['perfect_info'],//完善资料
		];
		$config->setConfig($fenxiao_data, '分销商配置', $is_use, [ [ 'site_id', '=', 0 ], [ 'app_module', '=', 'admin' ], [ 'config_key', '=', 'FENXIAO_CONFIG' ] ]);
		//上下级关系
		$relation_data = [
			'child_condition' => $data['child_condition'],//成为下线条件
		];
		$res = $config->setConfig($relation_data, '分销上下级关系配置', $is_use, [ [ 'site_id', '=', 0 ], [ 'app_module', '=', 'admin' ], [ 'config_key', '=', 'FENXIAO_RELATION_CONFIG' ] ]);
		
		return $res;
	}
	
	/**
	 * 获取分销基本设置
	 * @return multitype:string mixed
	 */
	public function getFenxiaoBasicsConfig()
	{
		$config = new ConfigModel();
		$res = $config->getConfig([ [ 'site_id', '=', 0 ], [ 'app_module', '=', 'admin' ], [ 'config_key', '=', 'FENXIAO_BASICS_CONFIG' ] ]);
		
		if (empty($res['data']['value'])) {
			
			$res['data']['value'] = [
				'level' => 0,//分销层级
				'internal_buy' => 0,//分销内购
				'is_examine' => 1,//是否需要审核
			];
		}
		return $res;
	}
	
	/**
	 * 获取分销商设置
	 * @return multitype:string mixed
	 */
	public function getFenxiaoConfig()
	{
		$config = new ConfigModel();
		$res = $config->getConfig([ [ 'site_id', '=', 0 ], [ 'app_module', '=', 'admin' ], [ 'config_key', '=', 'FENXIAO_CONFIG' ] ]);
		if (empty($res['data']['value'])) {
			$res['data']['value'] = [
				'fenxiao_condition' => 0,//成为分销商条件(0无条件 1申请 2消费次数 3消费金额 4购买商品)
				//申请
				'is_agreement' => 0,//显示申请协议
				'agreement_title' => '',//协议标题
				'agreement_content' => '',//协议内容
				'consume_count' => 0,//消费次数
				'consume_money' => 0,//消费次数
				'consume_condition' => 1,//消费条件(1付款后 2订单完成)
				'img' => '',//申请页面顶部图片
				'perfect_info' => '',//完善资料
			];
			
		}
		return $res;
	}
	
	/**
	 * 获取上下级关系
	 * @return multitype:string mixed
	 */
	public function getFenxiaoRelationConfig()
	{
		$config = new ConfigModel();
		$res = $config->getConfig([ [ 'site_id', '=', 0 ], [ 'app_module', '=', 'admin' ], [ 'config_key', '=', 'FENXIAO_RELATION_CONFIG' ] ]);
		if (empty($res['data']['value'])) {
			$res['data']['value'] = [
				'child_condition' => 1,//成为下线条件
			];
		}
		return $res;
	}
	/******************************************************************** 分销基本配置 end ****************************************************************************/
	
	/******************************************************************** 分销协议配置 start ****************************************************************************/
	
	/**
	 * 设置分销协议配置
	 * @return multitype:string mixed
	 */
	public function setFenxiaoAgreementConfig($data, $is_use)
	{
		$config = new ConfigModel();
		
		$agreement_config = [
			'is_agreement' => $data['is_agreement'],//是否显示申请协议
			'img' => $data['img'],//申请页面顶部图片
		];
		$res = $config->setConfig($agreement_config, '分销协议配置', $is_use, [ [ 'site_id', '=', 0 ], [ 'app_module', '=', 'admin' ], [ 'config_key', '=', 'FENXIAO_AGREEMENT_CONFIG' ] ]);
		
		$document = new Document();
		$document->setDocument($data['agreement_title'], $data['agreement_content'], [ [ 'site_id', '=', 0 ], [ 'app_module', '=', 'admin' ], [ 'document_key', '=', "FENXIAO_AGREEMENT" ] ]);
		
		return $res;
	}
	
	/**
	 * 获取分销协议配置
	 * @return multitype:string mixed
	 */
	public function getFenxiaoAgreementConfig()
	{
		$config = new ConfigModel();
		$res = $config->getConfig([ [ 'site_id', '=', 0 ], [ 'app_module', '=', 'admin' ], [ 'config_key', '=', 'FENXIAO_AGREEMENT_CONFIG' ] ]);
		if (empty($res['data']['value'])) {
			$res['data']['value'] = [
				'is_agreement' => 0,//显示申请协议
				'img' => 'upload/default/fenxiao/apply_top_gg.png',//申请页面顶部图片
			];
		}
		return $res;
	}
	
	
	/******************************************************************** 分销协议配置 end ****************************************************************************/
	
	
	/******************************************************************** 分销结算配置 start ****************************************************************************/
	
	/**
	 * 设置分销结算配置
	 * @return multitype:string mixed
	 */
	public function setFenxiaoSettlementConfig($data, $is_use)
	{
		
		$config = new ConfigModel();
		//分销商结算配置
		$settlement_data = [
			'account_type' => $data['account_type'],//佣金计算方式
		];
		
		$config->setConfig($settlement_data, '分销结算配置', $is_use, [ [ 'site_id', '=', 0 ], [ 'app_module', '=', 'admin' ], [ 'config_key', '=', 'FENXIAO_SETTLEMENT_CONFIG' ] ]);
		//分销商提现配置
		$withdraw_data = [
			'withdraw' => $data['withdraw'],//最低提现额度
			'withdraw_rate' => $data['withdraw_rate'],//佣金提现手续费
			'min_no_fee' => $data['min_no_fee'],//最低免手续费区间
			'max_no_fee' => $data['max_no_fee'],//最高免手续费区间
			'withdraw_status' => $data['withdraw_status'],//提现审核
			'settlement_day' => $data['settlement_day'],//天数
			'withdraw_type' => $data['withdraw_type'],//提现方式
		];
		$res = $config->setConfig($withdraw_data, '分销提现配置', $is_use, [ [ 'site_id', '=', 0 ], [ 'app_module', '=', 'admin' ], [ 'config_key', '=', 'FENXIAO_WITHDRAW_CONFIG' ] ]);
		return $res;
	}
	
	/**
	 * 分销商结算配置
	 * @return multitype:string mixed
	 */
	public function getFenxiaoSettlementConfig()
	{
		$config = new ConfigModel();
		$res = $config->getConfig([ [ 'site_id', '=', 0 ], [ 'app_module', '=', 'admin' ], [ 'config_key', '=', 'FENXIAO_SETTLEMENT_CONFIG' ] ]);
		if (empty($res['data']['value'])) {
			$res['data']['value'] = [
				'account_type' => 0
			];
		}
		return $res;
	}
	
	/**
	 * 分销商提现配置
	 * @return multitype:string mixed
	 */
	public function getFenxiaoWithdrawConfig()
	{
		$config = new ConfigModel();
		$res = $config->getConfig([ [ 'site_id', '=', 0 ], [ 'app_module', '=', 'admin' ], [ 'config_key', '=', 'FENXIAO_WITHDRAW_CONFIG' ] ]);
		if (empty($res['data']['value'])) {
			$res['data']['value'] = [
				'withdraw' => 0,//最低提现额度
				'withdraw_rate' => 0,//佣金提现手续费
				'min_no_fee' => 0,//最低免手续费区间
				'max_no_fee' => 0,//最高免手续费区间
				'withdraw_status' => 1,//提现审核
				'withdraw_type' => 0,//提现方式
			];
		}
		return $res;
	}
	/******************************************************************** 分销结算配置 end ****************************************************************************/
	
	/******************************************************************** 分销文字配置 start ****************************************************************************/
	
	/**
	 * 设置分销文字配置
	 * @return multitype:string mixed
	 */
	public function setFenxiaoWordsConfig($data, $is_use)
	{
		$config = new ConfigModel();
		$res = $config->setConfig($data, '分销文字配置', $is_use, [ [ 'site_id', '=', 0 ], [ 'app_module', '=', 'admin' ], [ 'config_key', '=', 'FENXIAO_WORDS_CONFIG' ] ]);
		return $res;
	}
	
	/**
	 * 获取分销文字配置
	 * @return multitype:string mixed
	 */
	public function getFenxiaoWordsConfig()
	{
		$config = new ConfigModel();
		$res = $config->getConfig([ [ 'site_id', '=', 0 ], [ 'app_module', '=', 'admin' ], [ 'config_key', '=', 'FENXIAO_WORDS_CONFIG' ] ]);
		if (empty($res['data']['value'])) {
			$res['data']['value'] = [
				'concept' => '分销',// 分销概念
				'fenxiao_name' => '分销商',// 分销商名称
				'withdraw' => '提现',// 提现名称
				'account' => '佣金',// 佣金
				'my_team' => '团队',// 我的团队
				'child' => '下线',// 下线
			];
		}
		return $res;
	}
	/******************************************************************** 分销文字配置 end ****************************************************************************/
	
}