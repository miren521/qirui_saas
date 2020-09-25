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


namespace addon\fenxiao\admin\controller;

use addon\fenxiao\model\Config as ConfigModel;
use app\admin\controller\BaseAdmin;
use app\model\system\Document;

/**
 *  分销设置
 */
class Config extends BaseAdmin
{
	
	/**
	 *  分销基础设置
	 */
	public function basics()
	{
		$model = new ConfigModel();
		
		if (request()->isAjax()) {
			
			$data = [
				'level' => input('level', ''),//分销层级
				'internal_buy' => input('internal_buy', ''),//分销内购
				'is_examine' => input('is_examine', ''),//是否需要审核（0关闭 1开启）
				'fenxiao_condition' => input('fenxiao_condition', ''),//成为分销商条件(0无条件 1申请 2消费次数 3消费金额)
				'consume_count' => input('consume_count', ''),//消费次数
				'consume_money' => input('consume_money', ''), //消费金额
				'consume_condition' => input('consume_condition', ''),//消费条件(1付款后 2订单完成)
				'perfect_info' => input('perfect_info', ''),//完善资料
				'child_condition' => input('child_condition', ''),//成为下线条件
			];
			
			$res = $model->setFenxiaoBasicsConfig($data, 1);
			return $res;
		} else {
			$basics = $model->getFenxiaoBasicsConfig();
			$this->assign("basics_info", $basics['data']['value']);
			
			$fenxiao = $model->getFenxiaoConfig();
			$this->assign("fenxiao_info", $fenxiao['data']['value']);
			
			$relation = $model->getFenxiaoRelationConfig();
			$this->assign("relation_info", $relation['data']['value']);
			
			$this->forthMenu();
			return $this->fetch('config/basics');
		}
		
	}
	
	/**
	 * 分销协议设置
	 */
	public function agreement()
	{
		$model = new ConfigModel();
		
		if (request()->isAjax()) {
			
			$data = [
				'is_agreement' => input('is_agreement', ''),//是否显示申请协议
				'agreement_title' => input('agreement_title', ''),//协议标题
				'agreement_content' => input('agreement_content', ''),//协议内容
				'img' => input('img', ''),//申请页面顶部图片
			];
			$res = $model->setFenxiaoAgreementConfig($data, 1);
			return $res;
			
		} else {
			$agreement = $model->getFenxiaoAgreementConfig();
			$this->assign("agreement_info", $agreement['data']['value']);
			
			$document_model = new Document();
			$document = $document_model->getDocument([ [ 'site_id', '=', 0 ], [ 'app_module', '=', 'admin' ], [ 'document_key', '=', "FENXIAO_AGREEMENT" ] ]);
			$this->assign('document', $document['data']);
			
			$this->forthMenu();
			return $this->fetch('config/agreement');
		}
	}
	
	/**
	 *  分销结算设置
	 */
	public function settlement()
	{
		$model = new ConfigModel();
		if (request()->isAjax()) {
			
			$data = [
				'account_type' => input('account_type', ''),//佣金计算方式
				'settlement_day' => input('settlement_day', ''),//天数
				'withdraw' => input('withdraw', ''),//最低提现额度
				'withdraw_rate' => input('withdraw_rate', ''),//佣金提现手续费
				'min_no_fee' => input('min_no_fee', ''),//最低免手续费区间
				'max_no_fee' => input('max_no_fee', ''),//最高免手续费区间
				'withdraw_status' => input('withdraw_status', ''),//提现审核
				'withdraw_type' => input('withdraw_type', ''),//提现方式
			];
			$res = $model->setFenxiaoSettlementConfig($data, 1);
			return $res;
		} else {
			$settlement = $model->getFenxiaoSettlementConfig();
			$this->assign("settlement_info", $settlement['data']['value']);
			$withdraw = $model->getFenxiaoWithdrawConfig();
			$this->assign("withdraw_info", $withdraw['data']['value']);
			
			$this->forthMenu();
			return $this->fetch('config/settlement');
		}
		
	}
	
	/**
	 *  分销文字设置
	 */
	public function words()
	{
		$model = new ConfigModel();
		if (request()->isAjax()) {
			$data = [
				'concept' => input('concept', ''),//分销概念
				'fenxiao_name' => input('fenxiao_name', ''),//分销商名称
				'withdraw' => input('withdraw', ''),//提现名称
				'account' => input('account', ''),//佣金
				'my_team' => input('my_team', ''),//我的团队
				'child' => input('child', ''),//下线
			];
			
			$res = $model->setFenxiaoWordsConfig($data, 1);
			return $res;
		} else {
			$fenxiao_config_result = $model->getFenxiaoWordsConfig();
			$config_info = $fenxiao_config_result['data']["value"];
			$this->assign("config_info", $config_info);
			
			$this->forthMenu();
			return $this->fetch('config/words');
		}
		
	}
	
}