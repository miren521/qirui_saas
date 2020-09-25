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


namespace addon\platformcoupon\model;

use app\model\BaseModel;
use app\model\system\Cron;

/**
 * 优惠券活动
 */
class PlatformcouponType extends BaseModel
{
	//优惠券类型状态
	private $platformcoupon_type_status = [
		1 => '进行中',
		2 => '已结束',
		-1 => '已关闭',
	];
	
	public function getPlatformcouponTypeStatus()
	{
		return $this->platformcoupon_type_status;
	}
	
	/**
	 * 添加优惠券活动
	 * @param unknown $data
	 * @return multitype:string
	 */
	public function addPlatformcouponType($data)
	{
		//只要创建了就是进行中
		$data['status'] = 1;
		$res = model("promotion_platformcoupon_type")->add($data);
		if ($data['validity_type'] == 0) {
			$cron = new Cron();
			$cron->addCron(1, 1, '优惠券活动定时结束', 'CronPlatformcouponTypeEnd', $data['end_time'], $res);
		}
		$this->qrcode($res, 'all', 'create');
		return $this->success($res);
	}
	
	/**
	 * 编辑优惠券活动
	 * @param unknown $data
	 * @param unknown $platformcoupon_type_id
	 * @return multitype:string
	 */
	public function editPlatformcouponType($data, $platformcoupon_type_id)
	{
		$res = model("promotion_platformcoupon_type")->update($data, [ [ 'platformcoupon_type_id', '=', $platformcoupon_type_id ] ]);
		$cron = new Cron();
		$cron->deleteCron([ ['event', '=', 'CronPlatformcouponTypeEnd'], [ 'relate_id', '=', $platformcoupon_type_id ] ]);
		if ($data['validity_type'] == 0) {
			$cron = new Cron();
			$cron->addCron(1, 1, '优惠券活动定时结束', 'CronPlatformcouponTypeEnd', $data['end_time'], $platformcoupon_type_id);
		}
		return $this->success($res);
	}
	
	/**
	 * 关闭优惠券
	 * @param $platformcoupon_type_id
	 * @return array|\multitype
	 */
	public function closePlatformcouponType($platformcoupon_type_id)
	{
		$res = model('promotion_platformcoupon_type')->update([ 'status' => -1 ], [ [ 'platformcoupon_type_id', '=', $platformcoupon_type_id ] ]);
		$cron = new Cron();
		$cron->deleteCron([ ['event', '=', 'CronPlatformcouponTypeEnd'], [ 'relate_id', '=', $platformcoupon_type_id ] ]);
		return $this->success($res);
	}
	
	/**
	 * 删除优惠券活动
	 * @param unknown $platformcoupon_type_id
	 * @return multitype:string
	 */
	public function deletePlatformcouponType($platformcoupon_type_id)
	{
		$res = model("promotion_platformcoupon_type")->delete([ [ 'platformcoupon_type_id', '=', $platformcoupon_type_id ] ]);
		$cron = new Cron();
		$cron->deleteCron([ ['event', '=', 'CronPlatformcouponTypeEnd'], [ 'relate_id', '=', $platformcoupon_type_id ] ]);
		if ($res) {
			model("promotion_platformcoupon")->delete([ [ 'platformcoupon_type_id', '=', $platformcoupon_type_id ] ]);
		}
		
		return $this->success($res);
	}
	
	/**
	 * 获取优惠券活动详情
	 * @param int $discount_id
	 * @return multitype:string mixed
	 */
	public function getPlatformcouponTypeInfo($platformcoupon_type_id)
	{
		$res = model('promotion_platformcoupon_type')->getInfo([ [ 'platformcoupon_type_id', '=', $platformcoupon_type_id ] ]);
		return $this->success($res);
	}

    /**
     * 获取优惠券活动详情
     * @param int $discount_id
     * @return multitype:string mixed
     */
    public function getInfo($condition = [], $field= '*')
    {
        $info = model('promotion_platformcoupon_type')->getInfo($condition, $field);
        return $this->success($info);
    }

	/**
	 * 获取 优惠券类型列表
	 * @param array $condition
	 * @param string $field
	 * @param string $order
	 * @param string $limit
	 */
	public function getPlatformcouponTypeList($condition = [], $field = '*', $order = '', $limit = null)
	{
		$res = model('promotion_platformcoupon_type')->getList($condition, $field, $order, '', '', '', $limit);
		return $this->success($res);
	}
	
	/**
	 * 获取优惠券活动分页列表
	 * @param array $condition
	 * @param number $page
	 * @param string $page_size
	 * @param string $order
	 * @param string $field
	 */
	public function getPlatformcouponTypePageList($condition = [], $page = 1, $page_size = PAGE_LIST_ROWS, $order = '', $field = '*')
	{
		$list = model('promotion_platformcoupon_type')->pageList($condition, $field, $order, $page, $page_size);
		return $this->success($list);
	}
	
	/**
	 * 生成优惠券二维码
	 * @param $platformcoupon_type_id
	 * @param string $app_type all为全部
	 * @param string $type 类型 create创建 get获取
	 * @return mixed|array
	 */
	public function qrcode($platformcoupon_type_id, $app_type, $type)
	{
		$res = event('Qrcode', [
			'app_type' => $app_type,
			'type' => $type,
			'data' => [
				'platformcoupon_type_id' => $platformcoupon_type_id
			],
			'page' => '/otherpages/goods/platformcoupon_receive/platformcoupon_receive',
			'qrcode_path' => 'upload/qrcode/platformcoupon',
			'qrcode_name' => 'platformcoupon_type_code_' . $platformcoupon_type_id,
		], true);
		return $res;
	}
	
	/**
	 * 优惠券定时结束
	 * @param unknown $platformcoupon_type_id
	 */
	public function platformcouponCronEnd($platformcoupon_type_id){
		$res = model('promotion_platformcoupon_type')->update([ 'status' => 2 ], [ [ 'platformcoupon_type_id', '=', $platformcoupon_type_id ] ]);
		return $this->success($res);
	}



}