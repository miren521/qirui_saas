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

namespace addon\pintuan\model;

use app\model\BaseModel;
use app\model\system\Cron;
use think\facade\Db;

/**
 * 拼团活动
 */
class Pintuan extends BaseModel
{
	/**
	 * 添加拼团
	 * @param unknown $pintuan_data
	 * @param unknown $sku_list
	 */
	public function addPintuan($pintuan_data, $sku_list)
	{
		
		$pintuan_data['create_time'] = time();
		
		if ($pintuan_data['pintuan_time'] == 0) {
			return $this->error('', "拼团有效期时长不能为0");
		}
        $pintuan_data['start_time'] = date_to_time($pintuan_data['start_time']);
        $pintuan_data['end_time'] = date_to_time($pintuan_data['end_time']);
		//查询该商品是否存在拼团
		$promotion_info = model('promotion_pintuan')->getInfo([
			[ 'site_id', '=', $pintuan_data['site_id'] ],
			[ 'status', 'in', '0,1' ],
			[ 'goods_id', '=', $pintuan_data['goods_id'] ],
            ['', 'exp', Db::raw('not ( (`start_time` > '.$pintuan_data['end_time'].' and `start_time` > '.$pintuan_data['start_time'].' )  or (`end_time` < '.$pintuan_data['start_time'].' and `end_time` < '.$pintuan_data['end_time'].'))')]//todo  修正  所有的优惠都要一样
		], 'pintuan_id,status');
		if (!empty($promotion_info)) {
			return $this->error('', "当前商品在当前时间段内已经存在拼团活动");
		}
		

		if ($pintuan_data['start_time'] <= time()) {
			$pintuan_data['status'] = 1;
		} else {
			$pintuan_data['status'] = 0;
		}
		
		model("promotion_pintuan")->startTrans();
		
		try {
			
			$pintuan_id = model("promotion_pintuan")->add($pintuan_data);
			
			foreach ($sku_list as $k => $v) {
				
				$data = [
					'pintuan_id' => $pintuan_id,
					'goods_id' => $pintuan_data['goods_id'],
					'sku_id' => $v['sku_id'],
					'pintuan_price' => $v['pintuan_price'],
					'promotion_price' => $v['promotion_price'] == '' ? $v['pintuan_price'] : $v['promotion_price']
				];
				model("promotion_pintuan_goods")->add($data);
			}
			
			$cron = new Cron();
			if ($pintuan_data['status'] == 1) {
				$cron->addCron(1, 0, "拼团活动关闭", "ClosePintuan", $pintuan_data['end_time'], $pintuan_id);
			} else {
				$cron->addCron(1, 0, "拼团活动开启", "OpenPintuan", $pintuan_data['start_time'], $pintuan_id);
				$cron->addCron(1, 0, "拼团活动关闭", "ClosePintuan", $pintuan_data['end_time'], $pintuan_id);
			}
			
			model('promotion_pintuan')->commit();
			return $this->success($pintuan_id);
			
		} catch (\Exception $e) {
			model('promotion_pintuan')->rollback();
			return $this->error('', $e->getMessage());
		}
	}
	
	/**
	 * 编辑拼团
	 * @param unknown $pintuan_id
	 * @param unknown $site_id
	 * @param unknown $pintuan_data
	 * @param unknown $sku_list
	 */
	public function editPintuan($pintuan_id, $site_id, $pintuan_data, $sku_list)
	{
		//查询该商品是否存在拼团
        $pintuan_data['start_time'] = strtotime($pintuan_data['start_time']);
        $pintuan_data['end_time'] = strtotime($pintuan_data['end_time']);
		$promotion_info = model('promotion_pintuan')->getInfo([
			[ 'site_id', '=', $site_id ],
			[ 'status', 'in', '0,1' ],
			[ 'goods_id', '=', $pintuan_data['goods_id'] ],
            [ 'pintuan_id', '<>', $pintuan_id],
            [ '', 'exp', Db::raw('not ( (`start_time` > '.$pintuan_data['end_time'].' and `start_time` > '.$pintuan_data['start_time'].' )  or (`end_time` < '.$pintuan_data['start_time'].' and `end_time` < '.$pintuan_data['end_time'].'))')]//todo  修正  所有的优惠都要一样
		], 'pintuan_id,status');
		if (!empty($promotion_info)) {
			if ($promotion_info['pintuan_id'] != $pintuan_id) {
				return $this->error('', "当前商品在当前时间段内已经存在拼团活动");
			}
		}
		
		$pintuan_info = model("promotion_pintuan")->getInfo([ [ 'pintuan_id', '=', $pintuan_id ], [ 'site_id', '=', $site_id ] ], 'status');
		if ($pintuan_info['status'] == 1) {
			return $this->error('', "当前活动再进行中，不能修改");
		}
		
		$cron = new Cron();

		if ($pintuan_data['start_time'] <= time()) {
			
			$pintuan_data['status'] = 1;
		} else {
			$pintuan_data['status'] = 0;
		}
		
		$pintuan_data['modify_time'] = time();
		
		$res = model("promotion_pintuan")->update($pintuan_data, [ [ 'pintuan_id', '=', $pintuan_id ], [ 'site_id', '=', $site_id ] ]);
		if ($res) {
			model("promotion_pintuan_goods")->delete([ [ 'pintuan_id', '=', $pintuan_id ] ]);
			foreach ($sku_list as $k => $v) {
				$data = [
					'pintuan_id' => $pintuan_id,
					'goods_id' => $pintuan_data['goods_id'],
					'sku_id' => $v['sku_id'],
					'pintuan_price' => $v['pintuan_price'],
					'promotion_price' => $v['promotion_price'] == '' ? $v['pintuan_price'] : $v['promotion_price']
				];
				model("promotion_pintuan_goods")->add($data);
			}
		}
		if ($pintuan_data['start_time'] <= time()) {
			//活动商品启动
			$this->cronOpenPintuan($pintuan_id);
			$cron->deleteCron([ [ 'event', '=', 'OpenPintuan' ], [ 'relate_id', '=', $pintuan_id ] ]);
			$cron->deleteCron([ [ 'event', '=', 'ClosePintuan' ], [ 'relate_id', '=', $pintuan_id ] ]);
			
			$cron->addCron(1, 0, "拼团活动关闭", "ClosePintuan", $pintuan_data['end_time'], $pintuan_id);
		} else {
			$cron->deleteCron([ [ 'event', '=', 'OpenPintuan' ], [ 'relate_id', '=', $pintuan_id ] ]);
			$cron->deleteCron([ [ 'event', '=', 'ClosePintuan' ], [ 'relate_id', '=', $pintuan_id ] ]);
			
			$cron->addCron(1, 0, "拼团活动开启", "OpenPintuan", $pintuan_data['start_time'], $pintuan_id);
			$cron->addCron(1, 0, "拼团活动关闭", "ClosePintuan", $pintuan_data['end_time'], $pintuan_id);
		}
		
		return $this->success($res);
	}
	
	/**
	 * 增加拼团组人数及购买人数
	 * @param array $data
	 * @param array $condition
	 * @return array
	 */
	public function editPintuanNum($data = [], $condition = [])
	{
		$res = model('promotion_pintuan')->update($data, $condition);
		return $this->success($res);
	}
	
	/**
	 * 删除拼团
	 * @param unknown $pintuan_id
	 * @param unknown $site_id
	 */
	public function deletePintuan($pintuan_id, $site_id)
	{
		$pintuan_info = model("promotion_pintuan")->getInfo([ [ 'pintuan_id', '=', $pintuan_id ], [ 'site_id', '=', $site_id ] ]);
		if ($pintuan_info['status'] == 1) {
			return $this->error('', "当前活动再进行中，不能删除");
		}
		$res = model("promotion_pintuan")->delete([ [ 'pintuan_id', '=', $pintuan_id ], [ 'site_id', '=', $site_id ] ]);
		if ($res) {
			model("promotion_pintuan_goods")->delete([ [ 'pintuan_id', '=', $pintuan_id ] ]);
			$cron = new Cron();
			$cron->deleteCron([ [ 'event', '=', 'OpenPintuan' ], [ 'relate_id', '=', $pintuan_id ] ]);
			$cron->deleteCron([ [ 'event', '=', 'ClosePintuan' ], [ 'relate_id', '=', $pintuan_id ] ]);
		}
		return $this->success($res);
	}
	
	/**
	 * 拼团失效
	 * @param unknown $pintuan_id
	 * @param unknown $site_id
	 */
	public function invalidPintuan($pintuan_id, $site_id)
	{
		model('promotion_pintuan')->startTrans();
		try {
			$pintuan_info = model("promotion_pintuan")->getInfo([ [ 'pintuan_id', '=', $pintuan_id ], [ 'site_id', '=', $site_id ] ]);
			
			$res = model("promotion_pintuan")->update(
				[ 'status' => 3, 'modify_time' => time() ],
				[ [ 'pintuan_id', '=', $pintuan_id ], [ 'site_id', '=', $site_id ] ]
			);
			
			if ($pintuan_info['group_num'] > 0) {//有人拼团
				//查询所有拼团组
				$group_model = new PintuanGroup();
				$group_info = $group_model->getPintuanGroupList([ [ 'pintuan_id', '=', $pintuan_id ] ], 'group_id');
				$group = $group_info['data'];
				
				if (!empty($group)) {
					foreach ($group as $v) {
						
						$result = $group_model->cronClosePintuanGroup($v['group_id']);
						if ($result['code'] < 0) {
							model('promotion_pintuan')->rollback();
							return $result;
						}
					}
				}
			}
			
			model('promotion_pintuan')->commit();
			return $this->success($res);
			
		} catch (\Exception $e) {
			
			model('promotion_pintuan')->rollback();
			return $this->error($e->getMessage());
		}
		
	}
	
	/**
	 * 获取拼团信息
	 * @param array $condition
	 * @param string $field
	 * @return array
	 */
	public function getPintuanInfo($condition = [], $field = '*')
	{
		//拼团信息
		$pintuan_info = model("promotion_pintuan")->getInfo($condition, $field);
		
		return $this->success($pintuan_info);
	}
	
	/**
	 * 获取拼团详细信息
	 * @param $pintuan_id
	 * @return array
	 */
	public function getPintuanDetail($pintuan_id)
	{
		//拼团信息
		$pintuan_info = model("promotion_pintuan")->getInfo([ [ 'pintuan_id', '=', $pintuan_id ] ]);
		//商品sku信息
		
		$field = 'ps.*,gs.sku_name,gs.price,gs.stock';
		$alias = 'ps';
		$join = [
			[
				'goods_sku gs',
				'ps.sku_id = gs.sku_id',
				'inner'
			]
		];
		$condition[] = [ 'ps.pintuan_id', '=', $pintuan_id ];
		$sku_list = model("promotion_pintuan_goods")->getList($condition, $field, '', $alias, $join);
		$pintuan_info['sku_list'] = $sku_list;
		
		return $this->success($pintuan_info);
	}
	
	/**
	 * 拼团商品详情
	 * @param array $condition
	 * @return array
	 */
	public function getPintuanGoodsDetail($condition = [])
	{
		$field = 'ppg.id,ppg.pintuan_id,ppg.goods_id,ppg.sku_id,ppg.pintuan_price,ppg.promotion_price,pp.pintuan_name,pp.pintuan_num,pp.start_time,pp.end_time,pp.buy_num,pp.is_single_buy,pp.is_promotion,pp.group_num,pp.order_num,sku.site_id,sku.sku_name,sku.sku_spec_format,sku.price,sku.promotion_type,sku.stock,sku.click_num,sku.sale_num,sku.collect_num,sku.sku_image,sku.sku_images,sku.goods_id,sku.site_id,sku.goods_content,sku.goods_state,sku.verify_state,sku.is_virtual,sku.is_free_shipping,sku.goods_spec_format,sku.goods_attr_format,sku.introduction,sku.unit,sku.video_url,sku.evaluate,sku.category_id,sku.category_id_1,sku.category_id_2,sku.category_id_3,sku.category_name';
		$alias = 'ppg';
		$join = [
			[ 'goods_sku sku', 'ppg.sku_id = sku.sku_id', 'inner' ],
			[ 'promotion_pintuan pp', 'ppg.pintuan_id = pp.pintuan_id', 'inner' ],
		];
		$pintuan_goods_info = model('promotion_pintuan_goods')->getInfo($condition, $field, $alias, $join);
		return $this->success($pintuan_goods_info);
	}
	
	/**
	 * 获取拼团列表
	 * @param array $condition
	 * @param string $field
	 * @param string $order
	 * @param string $limit
	 */
	public function getPintuanList($condition = [], $field = '*', $order = '', $limit = null)
	{
		
		$list = model('promotion_pintuan')->getList($condition, $field, $order, '', '', '', $limit);
		return $this->success($list);
	}
	
	/**
	 * 获取拼团分页列表
	 * @param array $condition
	 * @param number $page
	 * @param string $page_size
	 * @param string $order
	 * @param string $field
	 */
	public function getPintuanPageList($condition = [], $page = 1, $page_size = PAGE_LIST_ROWS, $order = '', $field = '*')
	{
		$field = 'p.*,g.goods_name,g.goods_image';
		$alias = 'p';
		$join = [
			[
				'goods g',
				'p.goods_id = g.goods_id',
				'inner'
			]
		];
		
		$list = model('promotion_pintuan')->pageList($condition, $field, $order, $page, $page_size, $alias, $join);
		return $this->success($list);
	}
	
	/**
	 * 获取拼团商品分页列表
	 * @param array $condition
	 * @param number $page
	 * @param string $page_size
	 * @param string $order
	 * @param string $field
	 */
	public function getPintuanGoodsPageList($condition = [], $page = 1, $page_size = PAGE_LIST_ROWS, $order = 'pp.is_recommend desc,pp.start_time desc', $field = 'pp.site_id,pp.pintuan_name,pp.is_virtual_goods,pp.pintuan_num,pp.pintuan_time,pp.is_recommend,pp.status,pp.group_num,pp.order_num,ppg.id,ppg.pintuan_id,ppg.pintuan_price,ppg.promotion_price,sku.sku_id,sku.sku_name,sku.price,sku.sku_image,g.goods_id,g.goods_name', $alias = '', $join = '')
	{

		
		$list = model('promotion_pintuan')->pageList($condition, $field, $order, $page, $page_size, $alias, $join);
		return $this->success($list);
	}
	
	/**
	 * 开启拼团活动
	 * @param $pintuan_id
	 * @return array|\multitype
	 */
	public function cronOpenPintuan($pintuan_id)
	{
		$pintuan_info = model('promotion_pintuan')->getInfo([
				[ 'pintuan_id', '=', $pintuan_id ] ]
			, 'start_time,status'
		);
		if (!empty($pintuan_info)) {
			if ($pintuan_info['start_time'] <= time() && $pintuan_info['status'] == 0) {
				$res = model('promotion_pintuan')->update([ 'status' => 1 ], [ [ 'pintuan_id', '=', $pintuan_id ] ]);
				
				return $this->success($res);
			} else {
				return $this->error("", "拼团活动已开启或者关闭");
			}
			
		} else {
			return $this->error("", "拼团活动不存在");
		}
		
	}
	
	/**
	 * 关闭拼团活动
	 * @param $pintuan_id
	 * @return array|\multitype
	 */
	public function cronClosePintuan($pintuan_id)
	{
		$pintuan_info = model('promotion_pintuan')->getInfo([
				[ 'pintuan_id', '=', $pintuan_id ] ]
			, 'site_id,start_time,status'
		);
		if (!empty($pintuan_info)) {
			
			return $this->invalidPintuan($pintuan_id, $pintuan_info['site_id']);
		} else {
			return $this->error("", "拼团活动不存在");
		}
	}
}