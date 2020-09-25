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

use app\model\BaseModel;
use app\model\member\MemberAccount;


/**
 * 分销商提现
 */
class FenxiaoWithdraw extends BaseModel
{
	//提现类型
	public $withdraw_type = [
		'balance' => '余额',
		'weixin' => '微信',
		'alipay' => '支付宝',
		'bank' => '银行卡',
	];
	
	/**
	 * 分销商申请提现
	 * @param $data
	 * @return array
	 */
	public function addFenxiaoWithdraw($data)
	{
        //获取分销商信息
        $fenxiao_model = new Fenxiao();
        $fenxiao_info = $fenxiao_model->getFenxiaoInfo([ [ 'member_id', '=', $data['member_id'] ] ], 'fenxiao_id,fenxiao_name,account');
        if($fenxiao_info['data']['account'] < $data['money']){
            return $this->error('', '提现金额大于可提现金额');
        }
        //获取提现配置信息
		$config_model = new Config();
		$config = $config_model->getFenxiaoWithdrawConfig();
		$config_info = $config['data']['value'];
		if ($config_info['withdraw'] > $data['money']) {
			return $this->error('', '提现金额小于最低提现金额');
		}
		if ($data['money'] >= $config_info['min_no_fee'] && $data['money'] <= $config_info['max_no_fee']) {
			$data['withdraw_rate'] = 0;
			$data['withdraw_rate_money'] = 0;
			$data['real_money'] = $data['money'];
		} else {
			$data['withdraw_rate'] = $config_info['withdraw_rate'];
			if ($config_info['withdraw_rate'] == 0) {
				$data['withdraw_rate'] = 0;
				$data['withdraw_rate_money'] = 0;
				$data['real_money'] = $data['money'];
			} else {
				$data['withdraw_rate'] = $config_info['withdraw_rate'];
				$data['withdraw_rate_money'] = round($data['money'] * $config_info['withdraw_rate'] / 100, 2);
				$data['real_money'] = $data['money'] - $data['withdraw_rate_money'];
			}
		}
		
		$data['withdraw_no'] = date('YmdHis') . rand(1000, 9999);
		$data['create_time'] = time();
		
		model('fenxiao_withdraw')->startTrans();
		try {
			

			$data['fenxiao_id'] = $fenxiao_info['data']['fenxiao_id'];
			$data['fenxiao_name'] = $fenxiao_info['data']['fenxiao_name'];
			
			$res = model('fenxiao_withdraw')->add($data);
			
			//判断是否需要审核
			if ($config_info['withdraw_status'] == 2) {//不需要
				
				$result = $this->withdrawPass($res);
				if ($result['code'] < 0) {
					model('fenxiao_withdraw')->rollback();
					return $result;
				}
			}
			
			//修改分销商提现中金额
			model('fenxiao')->setInc([ [ 'member_id', '=', $data['member_id'] ] ], 'account_withdraw_apply', $data['money']);
            //修改分销商可提现金额
            model('fenxiao')->setDec([ [ 'member_id', '=', $data['member_id'] ] ], 'account', $data['money']);

            model('fenxiao_withdraw')->commit();
			
			return $this->success($res);
		} catch (\Exception $e) {
			model('fenxiao_withdraw')->rollback();
			return $this->error('', $e->getMessage());
		}
		
	}
	
	/**
	 * 提现审核通过
	 * @param $ids
	 * @return array
	 */
	public function withdrawPass($ids)
	{
		model('fenxiao_withdraw')->startTrans();
		try {

			$withdraw_list = $this->getFenxiaoWithdrawList([ [ 'id', 'in', $ids ] ], 'id,fenxiao_id,fenxiao_name,member_id,money,real_money');
			foreach ($withdraw_list['data'] as $k => $v) {
				
				//修改分销商提现中金额
				model('fenxiao')->setDec([ [ 'fenxiao_id', '=', $v['fenxiao_id'] ] ], 'account_withdraw_apply', $v['money']);
				//修改分销商已提现金额
				model('fenxiao')->setInc([ [ 'fenxiao_id', '=', $v['fenxiao_id'] ] ], 'account_withdraw', $v['money']);
				
				//添加会员账户流水
				$member_account = new MemberAccount();
				$member_result = $member_account->addMemberAccount($v['member_id'], 'balance_money', $v['real_money'], 'fenxiao', '佣金提现', '分销佣金提现');
				if ($member_result['code'] != 0) {
					model('fenxiao_withdraw')->rollback();
					return $member_result;
				}
				
				$account_model = new FenxiaoAccount();
                $account_result = $account_model->addAccountLog($v['fenxiao_id'], $v['fenxiao_name'], 'withdraw', '-' . $v['money'], $v['id']);
				if ($account_result['code'] != 0) {
					model('fenxiao_withdraw')->rollback();
					return $account_result;
				}
			}

            //修改提现状态
            $data = [
                'status' => 2,
                'payment_time' => time(),
                'modify_time' => time(),
            ];
            model('fenxiao_withdraw')->update($data, [ [ 'id', 'in', $ids ] ]);

			model('fenxiao_withdraw')->commit();
			return $this->success();
		} catch (\Exception $e) {
			model('fenxiao_withdraw')->rollback();
			return $this->error('', $e->getMessage());
		}
	}
	
	/**
	 * 提现审核拒绝
	 * @param $id
	 * @return array
	 */
	public function withdrawRefuse($id)
	{
		$data = [
			'status' => -1,
			'payment_time' => time()
		];
		
		$info = model('fenxiao_withdraw')->getInfo([ [ 'id', '=', $id ] ], 'fenxiao_id,money');
		model('fenxiao_withdraw')->startTrans();
		try {
			model('fenxiao_withdraw')->update($data, [ [ 'id', '=', $id ] ]);
			
			//修改分销商提现中金额
			model('fenxiao')->setDec([ [ 'fenxiao_id', '=', $info['fenxiao_id'] ] ], 'account_withdraw_apply', $info['money']);
			
			model('fenxiao_withdraw')->commit();
			return $this->success();
		} catch (\Exception $e) {
			model('fenxiao_withdraw')->rollback();
			return $this->error('', $e->getMessage());
		}
		
	}
	
	
	/**
	 * 获取提现详情
	 * @param array $condition
	 * @return array
	 */
	public function getFenxiaoWithdrawInfo($condition = [], $field = '*')
	{
		$res = model('fenxiao_withdraw')->getInfo($condition, $field);
		return $this->success($res);
	}
	
	/**
	 * 获取分销列表
	 * @param array $condition
	 * @param string $field
	 * @param string $order
	 * @param string $limit
	 */
	public function getFenxiaoWithdrawList($condition = [], $field = '*', $order = '', $limit = null)
	{
		
		$list = model('fenxiao_withdraw')->getList($condition, $field, $order, '', '', '', $limit);
		return $this->success($list);
	}
	
	/**
	 * 获取分销提现分页列表
	 * @param array $condition
	 * @param number $page
	 * @param string $page_size
	 * @param string $order
	 * @param string $field
	 */
	public function getFenxiaoWithdrawPageList($condition = [], $page = 1, $page_size = PAGE_LIST_ROWS, $order = '', $field = '*')
	{
		$list = model('fenxiao_withdraw')->pageList($condition, $field, $order, $page, $page_size);
		return $this->success($list);
	}
	
}