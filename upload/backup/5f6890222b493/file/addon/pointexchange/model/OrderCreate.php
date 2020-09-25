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

namespace addon\pointexchange\model;

use addon\gift\model\Gift;
use addon\platformcoupon\model\Platformcoupon;
use app\model\BaseModel;
use addon\coupon\model\Coupon;
use app\model\member\MemberAccount;
use app\model\member\MemberAddress;
use app\model\system\Pay;
use think\facade\Cache;
use app\model\system\Cron;
use app\model\order\Config;

/**
 * 积分兑换
 */
class OrderCreate extends BaseModel
{
	private $error = 0;  //是否有错误
	private $error_msg = '';  //错误描述
	
	/**
	 * 创建订单
	 * @param $data
	 */
	public function create($data)
	{
		$calculate_data = $this->calculate($data);//计算并查询套餐信息
		if (isset($calculate_data['code']) && $calculate_data['code'] < 0)
			return $calculate_data;
		
		if ($this->error > 0) {
			return $this->error("", $this->error_msg);
		}
		$pay_model = new Pay();
		$out_trade_no = $pay_model->createOutTradeNo();
		model("promotion_exchange_order")->startTrans();
		//循环生成多个订单
		try {
			$order_no = $this->createOrderNo(0);
			$exchange_info = $calculate_data["exchange_info"] ?? [];
			$order_data = array(
				"order_no" => $order_no,
				"member_id" => $data["member_id"],
				"out_trade_no" => $out_trade_no,
				"point" => $calculate_data["point"],
				"exchange_price" => $exchange_info["market_price"],
				"delivery_price" => $exchange_info["delivery_price"],
				"price" => $calculate_data["price"],
				"create_time" => time(),
				"exchange_id" => $exchange_info["id"],
				"exchange_name" => $exchange_info["name"],
				"exchange_image" => $exchange_info["image"],
				"num" => $calculate_data["num"],
				"order_status" => 0,
				"type" => $exchange_info["type"],
				"type_name" => $exchange_info["type_name"],
				'name' => $calculate_data['member_address']['name'] ?? '',
				'mobile' => $calculate_data['member_address']['mobile'] ?? '',
				'telephone' => $calculate_data['member_address']['telephone'] ?? '',
				'province_id' => $calculate_data['member_address']['province_id'] ?? '',
				'city_id' => $calculate_data['member_address']['city_id'] ?? '',
				'district_id' => $calculate_data['member_address']['district_id'] ?? '',
				'community_id' => $calculate_data['member_address']['community_id'] ?? '',
				'address' => $calculate_data['member_address']['address'] ?? '',
				'full_address' => $calculate_data['member_address']['full_address'] ?? '',
				'longitude' => $calculate_data['member_address']['longitude'] ?? '',
				'latitude' => $calculate_data['member_address']['latitude'] ?? '',
				'order_from' => $data['order_from'],
				'order_from_name' => $data['order_from_name'],
				"buyer_message" => $calculate_data["buyer_message"],
				"type_id" => $exchange_info["type_id"],
				"balance" => $calculate_data["balance"],
			);
			$order_id = model("promotion_exchange_order")->add($order_data);


            //减去套餐的库存
            $exchange_model = new Exchange();
            $exchange_result = $exchange_model->decStock(["id" => $exchange_info["id"], "num" => $calculate_data["num"]]);
            if ($exchange_result['code'] < 0) {
                model("promotion_exchange_order")->rollback();
                return $exchange_result;
            }

			//判断库存
			switch ($exchange_info["type"]) {//兑换类型
				case "1"://礼品
					$gift_model = new Gift();
					$result = $gift_model->decStock([ "gift_id" => $exchange_info["type_id"], "num" => $calculate_data["num"] ]);
					break;
				case "2"://优惠券
					$coupon_model = new Platformcoupon();
					$result = $coupon_model->decStock([ "platformcoupon_type_id" => $exchange_info["type_id"], "num" => $calculate_data["num"] ]);
					break;
				default:
					$result = success();
                    break;
			}
			if ($result["code"] < 0) {
				model("promotion_exchange_order")->rollback();
				return $result;
			}
			//扣除积分
			$member_account_model = new MemberAccount();
			$member_account_result = $member_account_model->addMemberAccount($data["member_id"], "point", -$calculate_data["point"], "order", "积分兑换", "积分兑换,扣除积分:" . $calculate_data["point"]);
			if ($member_account_result["code"] < 0) {
				model("promotion_exchange_order")->rollback();
				return $member_account_result;
			}
			
			//生成整体支付单据
			$pay_model = new Pay();
			$pay_model->addPay(0, $out_trade_no, "", $calculate_data["order_name"], $calculate_data["order_name"], $calculate_data["price"], '', 'PointexchangeOrderPayNotify', '');
			
			$this->addOrderCronClose($order_id);//增加关闭订单自动事件
//			$this->checkFree($order_data);
			model("promotion_exchange_order")->commit();
			return $this->success($out_trade_no);
			
		} catch (\Exception $e) {
			model("promotion_exchange_order")->rollback();
			return $this->error('', $e->getMessage());
		}
	}
	
	/**
	 * 待支付订单
	 * @param $data
	 */
	public function payment($data)
	{
		$calculate_data = $this->calculate($data);//计算并查询套餐信息
		if (isset($calculate_data['code']) && $calculate_data['code'] < 0)
			return $calculate_data;
		
		return $this->success($calculate_data);
		
	}
	
	/**
	 * 计算
	 * @param $data
	 */
	public function calculate($data)
	{
		$data = $this->initMemberAddress($data);
		$id = $data["id"];
		$exchange_model = new Exchange();
		$exchange_info_result = $exchange_model->getExchangeInfo($id);
		$exchange_info = $exchange_info_result["data"];
		if (empty($exchange_info))
			return $this->error('', "找不到对应的积分兑换活动!");
		
		$data["exchange_info"] = $exchange_info;
		if ($exchange_info["state"] == 0) {
			$this->error = 1;
			$this->error_msg = "当前兑换活动未开启!";
		}
		
		if ($exchange_info["stock"] <= 0) {
			$this->error = 1;
			$this->error_msg = "当前兑换库存不足!";
		}
		
		$point = $exchange_info["point"];
		$price = $exchange_info["price"];
		$balance = $exchange_info["balance"];
		$gooods_num = $data["num"];
		$order_name = $exchange_info["name"] . "【" . $exchange_info["type_name"] . "】";
		$data['point'] = $point * $data["num"];
		$data['price'] = $price * $data["num"];
		$data['goods_num'] = $gooods_num;
		$data['order_name'] = $order_name;
		$data['balance'] = $balance * $data["num"];
		return $data;
	}
	
	/**
	 * 初始化收货地址
	 * @param unknown $data
	 */
	public function initMemberAddress($data)
	{
		//收货人地址管理
		if (empty($data['member_address'])) {
			$member_address = new MemberAddress();
			$address = $member_address->getMemberAddressInfo([ [ 'member_id', '=', $data['member_id'] ], [ 'is_default', '=', 1 ] ]);
			$data['member_address'] = $address['data'];
		}
		return $data;
	}
	
	/**
	 * 增加订单自动关闭事件
	 * @param $order_id
	 */
	public function addOrderCronClose($order_id)
	{
		//计算订单自动关闭时间
		$config_model = new Config();
		$order_config_result = $config_model->getOrderEventTimeConfig();
		$order_config = $order_config_result["data"];
		$now_time = time();
		if (!empty($order_config)) {
			$execute_time = $now_time + $order_config["value"]["auto_close"] * 60;//自动关闭时间
		} else {
			$execute_time = $now_time + 3600;//尚未配置  默认一天
		}
		$cron_model = new Cron();
		$cron_model->addCron(1, 0, "积分兑换订单自动关闭", "CronExchangeOrderClose", $execute_time, $order_id);
	}
	
	/**
	 * 验证订单支付金额知否为0  如果为0  立即支付完成
	 * @param $order_data
	 */
	public function checkFree($order_data)
	{
		if ($order_data["price"] == 0) {
			$pay_model = new Pay();
			$pay_model->onlinePay($order_data["out_trade_no"], "ONLINE_PAY", '', '');
		}
		
	}
	
	/**
	 * 生成订单编号
	 *
	 * @param array $site_id
	 */
	public function createOrderNo($site_id)
	{
		$time_str = date('YmdHi');
		$num = 0;
		$max_no = Cache::get($site_id . "_" . $time_str);
		if (!isset($max_no) || empty($max_no)) {
			$max_no = 1;
		} else {
			$max_no = $max_no + 1;
		}
		$order_no = $time_str . sprintf("%04d", $max_no);
		Cache::set($site_id . "_" . $time_str, $max_no);
		return $order_no;
	}
}