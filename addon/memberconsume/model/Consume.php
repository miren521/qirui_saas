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


namespace addon\memberconsume\model;

use app\model\member\MemberAccount as MemberAccountModel;
use app\model\order\OrderCommon as OrderCommonModel;
use app\model\system\Config as ConfigModel;
use app\model\BaseModel;

/**
 * 会员消费
 */
class Consume extends BaseModel
{
	/**
	 * 会员消费设置
	 * array $data
	 */
	public function setConfig($data, $is_use)
	{
		$config = new ConfigModel();
		$res = $config->setConfig($data, '会员消费设置', $is_use, [ [ 'site_id', '=', 0 ], [ 'app_module', '=', 'admin' ], [ 'config_key', '=', 'MEMBER_CONSUME_CONFIG' ] ]);
		return $res;
	}

	/**
	 * 会员消费设置
	 */
	public function getConfig()
	{
		$config = new ConfigModel();
		$res = $config->getConfig([ [ 'site_id', '=', 0 ], [ 'app_module', '=', 'admin' ], [ 'config_key', '=', 'MEMBER_CONSUME_CONFIG' ] ]);
		if (empty($res['data']['value'])) {
			$res['data']['value'] = [
				'is_return_point' => 0,
				'return_point_status' => 'complete',
				'return_point_rate' => 0,
                'return_growth_rate' => 0
			];
		}
		return $res;
	}

	/**
	 * 订单支付完成
	 * @param unknown $data 订单数据
	 */
	public function orderPay()
	{
		$config = $this->getConfig();
		return $this->success();
	}

	/**
	 * 订单收货完成
	 * @param unknown $data
	 */
	public function orderDelivery()
	{
		$config = $this->getConfig();
		return $this->success();
	}

	/**
	 * 订单完成
	 * @param unknown $data
	 */
	public function orderComplete()
	{
		$config = $this->getConfig();
		return $this->success();
	}

    /**
     * memberConsume 计算成长值
     * @return array
     */
    public function memberConsume($param) {
        $member_account_model = new MemberAccountModel();
        $order_model = new OrderCommonModel();
        $consume_config = $this->getConfig();
        $consume_config = $consume_config['data'];
        if ($consume_config['is_use'] && $consume_config['value']['return_point_status'] == $param['status']) {
            $order_info = $order_model->getOrderInfo([['order_id', '=', $param['order_id']]]);
            $order_info = $order_info['data'];
            $consume_config = $consume_config['value'];
            if(!empty($consume_config['return_point_rate'])) {
                $adjust_num = intval($consume_config['return_point_rate']/100 * $order_info['order_money']);
                if ($adjust_num > 0) {
                    $remark = '订单' . $order_info['order_no'] . $this->returnStatusToZh($param['status']) . '送' . $adjust_num . '积分';
                    $member_account_model->addMemberAccount($order_info['member_id'], 'point', $adjust_num, 'order', $param['order_id'], $remark);
                }
            }
            if (!empty($consume_config['return_growth_rate'])) {
                $adjust_num = intval($consume_config['return_growth_rate']/100 * $order_info['order_money']);
                if ($adjust_num > 0) {
                    $remark = '订单' . $order_info['order_no'] . $this->returnStatusToZh($param['status']) . '送' . $adjust_num . '成长值';
                    $member_account_model->addMemberAccount($order_info['member_id'], 'growth', $adjust_num, 'order', $param['order_id'], $remark);
                }
            }
        }
        return $this->success();
    }

    private function returnStatusToZh($status) {
        $status_zh = [
            'pay' => '付款',
            'receive' => '收货',
            'complete' => '完成'
        ];
        return $status_zh[$status];
    }
}