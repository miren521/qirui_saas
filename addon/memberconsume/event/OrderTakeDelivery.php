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

declare (strict_types = 1);

namespace addon\memberconsume\event;

use addon\memberconsume\model\Consume as ConsumeModel;

/**
 * 订单收货事件
 */
class OrderTakeDelivery
{
    
	public function handle($data)
	{
 		$consume_model = new ConsumeModel();
		$res = $consume_model->memberConsume(['order_id' => $data['order_id'], 'status' => 'receive']);
		return $res; 
	}
}