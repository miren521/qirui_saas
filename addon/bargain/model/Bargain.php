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

namespace addon\bargain\model;

use app\model\BaseModel;
use app\model\goods\Goods;
use app\model\system\Cron;
use think\Exception;
use think\facade\Db;

/**
 * 砍价活动
 */
class Bargain extends BaseModel
{
	
	private $status = [
		0 => '未开始',
		1 => '活动进行中',
		2 => '活动已结束',
		3 => '已关闭'
	];
	
	/**
	 * 获取砍价活动状态
	 * @return array
	 */
	public function getBargainStatus()
	{
		return $this->success($this->status);
	}
	
	/**
	 * 添加砍价
	 * @param $common_data
	 * @param $sku_list
	 * @return array
	 */
	public function addBargain($common_data, $sku_list)
	{
		
		$sku_ids = $common_data['sku_ids'];
		unset($common_data['sku_ids']);
		//时间段检测
		$bargain_count = model('promotion_bargain_goods')->getCount([
//			[ 'start_time|end_time', 'between', [ $common_data['start_time'], $common_data['end_time'] ] ],
			[ 'sku_id', 'in', $sku_ids ],
			[ 'status', 'in', '0,1' ],
			[ 'site_id', '=', $common_data['site_id'] ],
            ['', 'exp', Db::raw('not ( (`start_time` > '.$common_data['end_time'].' and `start_time` > '.$common_data['start_time'].' )  or (`end_time` < '.$common_data['start_time'].' and `end_time` < '.$common_data['end_time'].'))')]//todo  修正  所有的优惠都要一样
		]);
		if ($bargain_count > 0) {
			return $this->error('', '有商品已设置砍价活动，请不要重复设置');
		}
		
//		$bargain_count = model('promotion_bargain_goods')->getCount([
//			[ 'start_time', '<=', $common_data['start_time'] ],
//			[ 'end_time', '>=', $common_data['end_time'] ],
//			[ 'sku_id', 'in', $sku_ids ],
//			[ 'status', 'in', '0,1' ],
//			[ 'site_id', '=', $common_data['site_id'] ]
//		]);
//		if ($bargain_count > 0) {
//			return $this->error('', '有商品已设置砍价活动，请不要重复设置');
//		}
		
		$time = time();
		// 当前时间
		if ($time > $common_data['start_time'] && $time < $common_data['end_time']) {
			$common_data['status'] = 1;
			$common_data['status_name'] = $this->status[1];
		} else {
			$common_data['status'] = 0;
			$common_data['status_name'] = $this->status[0];
		}
		model('promotion_bargain')->startTrans();
		try {
			$bargain_data = $common_data;
			$common_data['create_time'] = $time;
			//添加砍价活动
			$bargain_id = model('promotion_bargain')->add($common_data);
			//添加砍价活动商品
            $goods_model = new Goods();
			foreach ($sku_list as $v) {
                $sku_info = $goods_model->getGoodsSkuInfo([['sku_id', '=', $v['sku_id']]], 'goods_id')['data'] ?? [];
                $goods_id = $sku_info['goods_id'] ?? 0;
				$bargain_data['bargain_id'] = $bargain_id;
                $v['goods_id'] = $goods_id;
				model('promotion_bargain_goods')->add(array_merge($bargain_data, $v));
			}
			$cron = new Cron();
			if ($bargain_data['status'] == 1) {
				
				$cron->addCron(1, 0, "砍价活动关闭", "CloseBargain", $common_data['end_time'], $bargain_id);
			} else {
				$cron->addCron(1, 0, "砍价活动开启", "OpenBargain", $common_data['start_time'], $bargain_id);
				$cron->addCron(1, 0, "砍价活动关闭", "CloseBargain", $common_data['end_time'], $bargain_id);
			}
			model('promotion_bargain')->commit();
			return $this->success();
			
		} catch (\Exception $e) {
			
			model('promotion_bargain')->rollback();
			return $this->error('', $e->getMessage());
		}
		
	}
	
	/**
	 * 编辑砍价
	 * @param array $condition
	 * @param $common_data
	 * @param $sku_list
	 * @return array
	 */
	public function editBargain($condition = [], $common_data, $sku_list)
	{
		$bargain_info = model('promotion_bargain')->getInfo($condition, 'status');
		if (empty($bargain_info)) {
			return $this->error('', '参数错误');
		}
		
		if ($bargain_info['status'] != 0) {
			return $this->error('', '只有未开始的活动才可以进行编辑');
		}
		
		$sku_ids = $common_data['sku_ids'];
		unset($common_data['sku_ids']);
		//时间段检测
		$bargain_count = model('promotion_bargain_goods')->getCount([
//			[ 'start_time|end_time', 'between', [ $common_data['start_time'], $common_data['end_time'] ] ],
			[ 'sku_id', 'in', $sku_ids ],
			[ 'status', 'in', '0,1' ],
			[ 'bargain_id', '<>', $common_data['bargain_id'] ],
			[ 'site_id', '=', $common_data['site_id'] ],
            ['', 'exp', Db::raw('not ( (`start_time` > '.$common_data['end_time'].' and `start_time` > '.$common_data['start_time'].' )  or (`end_time` < '.$common_data['start_time'].' and `end_time` < '.$common_data['end_time'].'))')]//todo  修正  所有的优惠都要一样
		]);
		if ($bargain_count > 0) {
			return $this->error('', '有商品已设置砍价活动，请不要重复设置');
		}
		
//		$bargain_count = model('promotion_bargain_goods')->getCount([
//			[ 'start_time', '<=', $common_data['start_time'] ],
//			[ 'end_time', '>=', $common_data['end_time'] ],
//			[ 'sku_id', 'in', $sku_ids ],
//			[ 'status', 'in', '0,1' ],
//			[ 'bargain_id', '<>', $common_data['bargain_id'] ],
//			[ 'site_id', '=', $common_data['site_id'] ]
//		]);
//		if ($bargain_count > 0) {
//			return $this->error('', '有商品已设置砍价活动，请不要重复设置');
//		}
		
		$time = time();
		// 当前时间
		if ($time > $common_data['start_time'] && $time < $common_data['end_time']) {
			$common_data['status'] = 1;
			$common_data['status_name'] = $this->status[1];
		} else {
			$common_data['status'] = 0;
			$common_data['status_name'] = $this->status[0];
		}
		model('promotion_bargain')->startTrans();
		try {
			$bargain_data = $common_data;
			$common_data['modify_time'] = $time;
			//添加砍价活动
			model('promotion_bargain')->update($common_data, $condition);
			
			model('promotion_bargain_goods')->delete($condition);
			//添加砍价活动商品
            $goods_model = new Goods();
			foreach ($sku_list as $v) {
                $sku_info = $goods_model->getGoodsSkuInfo([['sku_id', '=', $v['sku_id']]], 'goods_id')['data'] ?? [];
                $goods_id = $sku_info['goods_id'] ?? 0;
                $v['goods_id'] = $goods_id;
				model('promotion_bargain_goods')->add(array_merge($bargain_data, $v));
			}
			
			$cron = new Cron();
			if ($common_data['status'] == 1) {
				
				$cron->deleteCron([ [ 'event', '=', 'OpenBargain' ], [ 'relate_id', '=', $common_data['bargain_id'] ] ]);
				$cron->deleteCron([ [ 'event', '=', 'CloseBargain' ], [ 'relate_id', '=', $common_data['bargain_id'] ] ]);
				
				$cron->addCron(1, 0, "砍价活动关闭", "CloseBargain", $common_data['end_time'], $common_data['bargain_id']);
			} else {
				$cron->deleteCron([ [ 'event', '=', 'OpenBargain' ], [ 'relate_id', '=', $common_data['bargain_id'] ] ]);
				$cron->deleteCron([ [ 'event', '=', 'CloseBargain' ], [ 'relate_id', '=', $common_data['bargain_id'] ] ]);
				
				$cron->addCron(1, 0, "砍价活动开启", "OpenBargain", $common_data['start_time'], $common_data['bargain_id']);
				$cron->addCron(1, 0, "砍价活动关闭", "CloseBargain", $common_data['end_time'], $common_data['bargain_id']);
			}
			
			model('promotion_bargain')->commit();
			return $this->success();
		} catch (\Exception $e) {
			
			model('promotion_bargain')->rollback();
			return $this->error('', $e->getMessage());
		}
		
	}
	
	/**
	 * 删除砍价活动
	 * @param $bargain_id
	 * @param $site_id
	 * @return array|\multitype
	 */
	public function deleteBargain($bargain_id, $site_id)
	{
		//砍价信息
		$bargain_info = model('promotion_bargain')->getInfo([ [ 'bargain_id', '=', $bargain_id ], [ 'site_id', '=', $site_id ] ], 'status');
		if ($bargain_info) {
			
			if ($bargain_info['status'] != 1) {
				$res = model('promotion_bargain')->delete([ [ 'bargain_id', '=', $bargain_id ], [ 'site_id', '=', $site_id ] ]);
				if ($res) {
					model('promotion_bargain_goods')->delete([ [ 'bargain_id', '=', $bargain_id ], [ 'site_id', '=', $site_id ] ]);
					$cron = new Cron();
					$cron->deleteCron([ [ 'event', '=', 'OpenBargain' ], [ 'relate_id', '=', $bargain_id ] ]);
					$cron->deleteCron([ [ 'event', '=', 'CloseBargain' ], [ 'relate_id', '=', $bargain_id ] ]);
				}
				return $this->success($res);
			} else {
				return $this->error('', '砍价活动进行中,请先关闭该活动');
			}
			
		} else {
			return $this->error('', '砍价活动不存在');
		}
	}
	
	/**
	 * 关闭砍价活动
	 * @param $bargain_id
	 * @param $site_id
	 * @return array
	 */
	public function finishBargain($bargain_id, $site_id)
	{
		//砍价信息
		$bargain_info = model('promotion_bargain')->getInfo([ [ 'bargain_id', '=', $bargain_id ], [ 'site_id', '=', $site_id ] ], 'status');
		if (!empty($bargain_info)) {
			
			if ($bargain_info['status'] != 3) {
				$res = model('promotion_bargain')->update([ 'status' => 3, 'status_name' => $this->status[3] ], [ [ 'bargain_id', '=', $bargain_id ], [ 'site_id', '=', $site_id ] ]);
				if ($res) {
					
					model('promotion_bargain_goods')->update(
						[ 'status' => 3, 'status_name' => $this->status[3] ],
						[ [ 'bargain_id', '=', $bargain_id ], [ 'site_id', '=', $site_id ] ]
					);
					$cron = new Cron();
					$cron->deleteCron([ [ 'event', '=', 'OpenBargain' ], [ 'relate_id', '=', $bargain_id ] ]);
					$cron->deleteCron([ [ 'event', '=', 'CloseBargain' ], [ 'relate_id', '=', $bargain_id ] ]);
				}
				return $this->success($res);
			} else {
				$this->error('', '该砍价活动已关闭');
			}
			
		} else {
			$this->error('', '该砍价活动不存在');
		}
	}
	
	/**
	 * 获取砍价信息
	 * @param array $condition
	 * @param string $field
	 * @return array
	 */
	public function getBargainInfo($condition = [], $field = '*')
	{
		$bargain_info = model("promotion_bargain")->getInfo($condition, $field);
		if (!empty($bargain_info)) {
            $field = '
		         bg.first_bargain_price,bg.bargain_stock,bg.floor_price,bg.sku_id,
		         sku.sku_name,sku.price,sku.sku_images,sku.stock
		    ';
            $alias = 'bg';
            $join = [
                [ 'goods_sku sku', 'bg.sku_id = sku.sku_id', 'inner' ]
            ];
            $goods_list = model('promotion_bargain_goods')->getList(
                [ [ 'bg.bargain_id', '=', $bargain_info['bargain_id'] ] ],
                $field, '', $alias, $join);
            $bargain_info['goods_list'] = $goods_list;
        }
		return $this->success($bargain_info);
	}
	
    /**
     * 获取砍价商品信息
     * @param array $condition
     * @param string $field
     * @return array
     */
	public function getBargainGoodsDetail($condition = [], $field = 'pbg.id,pbg.bargain_id,pbg.goods_id,pbg.sku_id,pbg.floor_price,pbg.bargain_stock,pbg.bargain_name,pbg.start_time,pbg.end_time,pbg.buy_type,sku.site_id,sku.sku_name,sku.sku_spec_format,sku.price,sku.promotion_type,sku.stock,sku.click_num,sku.sale_num,sku.collect_num,sku.sku_image,sku.sku_images,sku.site_id,sku.goods_content,sku.goods_state,sku.is_virtual,sku.is_free_shipping,sku.goods_spec_format,sku.goods_attr_format,sku.introduction,sku.unit,sku.video_url,sku.evaluate'){
        $join = [
            [ 'goods_sku sku', 'pbg.sku_id = sku.sku_id', 'inner' ],
            [ 'promotion_bargain pb', 'pb.bargain_id = pbg.bargain_id', 'inner' ],
        ];
        $bargain_goods_info = model('promotion_bargain_goods')->getInfo($condition, $field, 'pbg', $join);
        return $this->success($bargain_goods_info);
    }
	
	/**
	 * 获取砍价列表
	 * @param array $condition
	 * @param string $field
	 * @param string $order
	 * @param string $limit
	 */
	public function getBargainList($condition = [], $field = '*', $order = '', $limit = null)
	{
		$list = model('promotion_bargain')->getList($condition, $field, $order, '', '', '', $limit);
		return $this->success($list);
	}
	
	/**
	 * 获取砍价分页列表
	 * @param array $condition
	 * @param number $page
	 * @param string $page_size
	 * @param string $order
	 * @param string $field
	 */
	public function getBargainPageList($condition = [], $page = 1, $page_size = PAGE_LIST_ROWS, $order = '', $field = '*')
	{
		$list = model('promotion_bargain')->pageList($condition, $field, $order, $page, $page_size);
		return $this->success($list);
	}

    /**
     * 获取砍价商品分页列表
     * @param array $condition
     * @param bool $field
     * @param string $order
     * @param int $page
     * @param int $list_rows
     * @param string $alias
     * @param array $join
     * @return array
     */
	public function getBargainGoodsPageList($condition = [], $field = true, $order = '', $page = 1, $list_rows = PAGE_LIST_ROWS, $alias = 'a', $join = []){
        $list = model('promotion_bargain_goods')->pageList($condition, $field, $order, $page, $list_rows, $alias, $join);
        return $this->success($list);
    }

	/**
	 * 开启砍价活动
	 * @param $bargain_id
	 * @return array|\multitype
	 */
	public function cronOpenBargain($bargain_id)
	{
		$bargain_info = model('promotion_bargain')->getInfo([ [ 'bargain_id', '=', $bargain_id ] ], 'status');
		if (!empty($bargain_info)) {
			
			if ($bargain_info['status'] == 0) {
				$res = model('promotion_bargain')->update([ 'status' => 1, 'status_name' => $this->status[1] ], [ [ 'bargain_id', '=', $bargain_id ] ]);
				if ($res) {
					model('promotion_bargain_goods')->update(
						[ 'status' => 1, 'status_name' => $this->status[1] ],
						[ [ 'bargain_id', '=', $bargain_id ] ]
					);
				}
				return $this->success($res);
			} else {
				return $this->error("", "砍价活动已开启或者关闭");
			}
			
		} else {
			return $this->error("", "砍价活动不存在");
		}
		
	}
	
	/**
	 * 关闭砍价活动
	 * @param $bargain_id
	 * @return array|\multitype
	 */
	public function cronCloseBargain($bargain_id)
	{
		$bargain_info = model('promotion_bargain')->getInfo([ [ 'bargain_id', '=', $bargain_id ] ], 'status');
		if (!empty($bargain_info)) {
			
			if ($bargain_info['status'] != 2) {
				$res = model('promotion_bargain')->update([ 'status' => 2, 'status_name' => $this->status[2] ], [ [ 'bargain_id', '=', $bargain_id ] ]);
				if ($res) {
					model('promotion_bargain_goods')->update(
						[ 'status' => 2, 'status_name' => $this->status[2] ],
						[ [ 'bargain_id', '=', $bargain_id ] ]
					);
				}
				return $this->success($res);
			} else {
				return $this->error("", "该活动已结束");
			}
		} else {
			return $this->error("", "砍价活动不存在");
		}
	}

    /**
     * 获取砍价发起信息
     * @param array $condition
     * @param string $field
     */
	public function getBargainLaunchDetail($condition = [], $field = '*'){
        $data = model('promotion_bargain_launch')->getInfo($condition, $field);
        if (!empty($data)) {
            return $this->success($data);
        } else {
            return $this->error();
        }
    }

    /**
     * 获取砍价发起分页列表
     * @param array $condition
     * @param bool $field
     * @param string $order
     * @param int $page
     * @param int $list_rows
     * @param string $alias
     * @param array $join
     * @return array
     */
    public function getBargainLaunchPageList($condition = [], $field = true, $order = '', $page = 1, $list_rows = PAGE_LIST_ROWS, $alias = 'a', $join = []){
        $data = model('promotion_bargain_launch')->pageList($condition, $field, $order, $page, $list_rows, $alias, $join);
        if (!empty($data['list'])) {
            foreach ($data['list'] as $k => $item) {
                $record_data = model('promotion_bargain_record')->pageList([ ['launch_id', '=', $item['launch_id'] ] ], 'headimg', 'id asc', 1, 6);
                $data['list'][$k]['bargain_record'] = $record_data['list'];
            }
        }
        return $this->success($data);
    }

    /**
     * 查询数据
     * @param array $condition
     */
    public function getBargainLaunchCount($condition = []){
        $count = model('promotion_bargain_launch')->getCount($condition, 'launch_id');
        return $this->success($count);
    }

    /**
     * 获取砍价记录
     * @param array $condition
     * @param bool $field
     * @param string $order
     * @param int $page
     * @param int $list_rows
     * @param string $alias
     * @param array $join
     * @return array
     */
    public function getBargainRecordPageList($condition = [], $field = true, $order = '', $page = 1, $list_rows = PAGE_LIST_ROWS, $alias = 'a', $join = []){
        $data = model('promotion_bargain_record')->pageList($condition, $field, $order, $page, $list_rows, $alias, $join);
        return $this->success($data);
    }

    /**
     * 获取砍价记录信息
     * @param array $condition
     * @param string $field
     */
    public function getBargainRecordInfo($condition = [], $field = '*'){
        $data = model('promotion_bargain_record')->getInfo($condition, $field);
        return $this->success($data);
    }

    /**
     * 发起砍价
     * @param $id
     * @param $member_id
     */
	public function launch($id, $member_id){
        $join = [
            [ 'goods_sku sku', 'pbg.sku_id = sku.sku_id', 'inner' ],
        ];
        $bargain_info = model('promotion_bargain_goods')->getInfo([ ['pbg.id', '=', $id], ['pbg.status', '=', 1] ], 'pbg.*, sku.sku_image,sku.sku_name,sku.price', 'pbg', $join);
        if (empty($bargain_info)) return $this->error('', '未查到到砍价活动信息');
        if (empty($bargain_info['bargain_stock'])) return $this->error('', '库存不足');

        $launch_info = model('promotion_bargain_launch')->getInfo([ ['bargain_id', '=', $bargain_info['bargain_id'] ], ['sku_id', '=', $bargain_info['sku_id'] ], ['member_id', '=', $member_id], ['status', '=', 0] ], 'launch_id');
        if (!empty($launch_info)) return $this->error('', '该商品正在砍价中');

        $member_info = model('member')->getInfo([ ['member_id', '=', $member_id] ], 'nickname,headimg');
        if (empty($member_info)) return $this->error('', '未获取到会员信息');

        try{
            $data = [
                'bargain_id' => $bargain_info['bargain_id'],
                'sku_id' => $bargain_info['sku_id'],
                'goods_id' => $bargain_info['goods_id'],
                'site_id' => $bargain_info['site_id'],
                'sku_name' => $bargain_info['sku_name'],
                'sku_image' => $bargain_info['sku_image'],
                'price' => $bargain_info['price'],
                'floor_price' => $bargain_info['floor_price'],
                'buy_type' => $bargain_info['buy_type'],
                'bargain_type' => $bargain_info['bargain_type'],
                'need_num' => $bargain_info['bargain_num'],
                'start_time' => time(),
                'end_time' => (time() + ($bargain_info['bargain_time'] * 3600)),
                'member_id' => $member_id,
                'nickname' => $member_info['nickname'],
                'headimg' => $member_info['headimg'],
                'is_fenxiao' => $bargain_info['is_fenxiao'],
                'first_bargain_price' => $bargain_info['first_bargain_price'],
                'curr_price' => $bargain_info['price'],
                'is_own' => $bargain_info['is_own']
            ];

            $launch_id = model('promotion_bargain_launch')->add($data);

            if ($launch_id) {
                // 减库存
                model('promotion_bargain_goods')->setDec([ ['id', '=', $id] ], 'bargain_stock');

                if ($bargain_info['is_own']) {
                    $this->bargain($launch_id, $member_id);
                }
                $cron = new Cron();
                $cron->addCron(1, 0, '砍价发起自动关闭', 'BargainLaunchClose', $data['end_time'], $launch_id);
                return $this->success($launch_id);
            } else {
                return $this->error();
            }
        } catch (\Exception $e) {
            return $this->error($e->getMessage());
        }
    }

    /**
     * 砍价
     * @param $launch_id
     * @param $member_id
     * @param $site_id
     */
    public function bargain($launch_id, $member_id){
        $launch_info = model('promotion_bargain_launch')->getInfo([ ['launch_id', '=', $launch_id ] ]);

        if (empty($launch_info)) return $this->error('', '未获取到砍价信息');
        if ($launch_info['status'] != 0) return $this->error('', '砍价已结束');
        if ($launch_info['is_own'] == 0 && $launch_info['member_id'] == $member_id) return $this->error('', '不支持给自己砍价');

        $member_info = model('member')->getInfo([ ['member_id', '=', $member_id] ], 'nickname,headimg');
        if (empty($member_info)) return $this->error('', '未获取到会员信息');

        $is_first = model('promotion_bargain_record')->getCount([ ['launch_id', '=', $launch_id ] ], 'id');
        if (!$is_first) {
            // 如果是首刀
            $bargain_money = $launch_info['first_bargain_price'] > 0 ? $launch_info['first_bargain_price'] : $this->bargainMoneyCalculate($launch_info);
        } else {
            $is_exist = model('promotion_bargain_record')->getCount([ ['launch_id', '=', $launch_id ], ['member_id', '=', $member_id] ], 'id');
            if ($is_exist) return $this->error('', '您已帮好友砍过价了！');
            $bargain_money = $this->bargainMoneyCalculate($launch_info);
        }

        if (($launch_info['curr_price'] - $bargain_money) < $launch_info['floor_price']) {
            $bargain_money = $launch_info['curr_price'] - $launch_info['floor_price'];
        }

        if ($bargain_money <= 0) return $this->error();

        try{
            $data = [
                'launch_id' => $launch_id,
                'member_id' => $member_id,
                'nickname' => $member_info['nickname'],
                'headimg' => $member_info['headimg'],
                'money' => $bargain_money,
                'bargain_time' => time()
            ];
            $record_id = model('promotion_bargain_record')->add($data);
            // 砍价人数自增
            model('promotion_bargain_launch')->setInc([ ['launch_id', '=', $launch_id ] ], 'curr_num');
            // 当前砍价金额自减
            model('promotion_bargain_launch')->setDec([ ['launch_id', '=', $launch_id ] ], 'curr_price', $bargain_money);
            // 砍价状态
            $status = 0;
            if (($launch_info['curr_price'] - $bargain_money) == $launch_info['floor_price']) {
                model('promotion_bargain_launch')->update(['status' => 1], [ ['launch_id', '=', $launch_id ] ]);
                $status = 1;
            }
            return $this->success(['bargain_money' => sprintf("%.2f", $bargain_money), 'status' => $status]);
        } catch (\Exception $e) {
            return $this->error('', $e->getMessage());
        }
    }

    /**
     * 砍价金额计算
     * @param $data
     */
    private function bargainMoneyCalculate($data){
        $bargain_money = 0;
        if ($data['bargain_type'] == 0) {
            // 固定金额
            if ($data['first_bargain_price'] > 0) {
                $bargain_money = round(($data['price'] - $data['first_bargain_price'] - $data['floor_price']) / ($data['need_num'] - 1), 2);
            } else {
                $bargain_money = round(($data['price'] - $data['floor_price']) / $data['need_num'], 2);
            }
        } else {
            $need_money = $data['curr_price'] - $data['floor_price']; // 剩余需砍金额
            if ($need_money > 0.01) {
                $need_num = $data['need_num'] - $data['curr_num']; // 剩余需帮砍人数
                if ($need_num > 0) {
                    $bargain_money = mt_rand(1, (round(($need_money / $need_num ), 2) * 100 ));
                } else {
                    $bargain_money = mt_rand(1, ($need_money * 100));
                }
                $bargain_money = $bargain_money / 100;
            } else {
                $bargain_money = 0.01;
            }
        }
        return $bargain_money;
    }

    /**
     * 关闭到了时间的砍价
     * @param $launch_id
     */
    public function cronCloseBargainLaunch($launch_id){
        $launch_info = model('promotion_bargain_launch')->getInfo([ ['launch_id', '=', $launch_id ], ['status', 'in', [0, 1] ] ]);
        if (!empty($launch_info)) {
            $data = ['status' => 2];
//            if ($launch_info['curr_price'] == $launch_info['floor_price']) {
//                $data = ['status' => 1];
//            } else {
//                // 砍到任意金额可买
//                if ($launch_info['buy_type'] == 0) {
//                    $data = ['status' => 1];
//                } else {
//                    $data = ['status' => 2];
//                    // 返还库存
//                    model('promotion_bargain_goods')->setInc([ ['bargain_id', '=', $launch_info['bargain_id'] ], ['sku_id', '=', $launch_info['sku_id'] ] ], 'bargain_stock');
//                }
//            }
            // 返还库存
            model('promotion_bargain_goods')->setInc([ ['bargain_id', '=', $launch_info['bargain_id'] ], ['sku_id', '=', $launch_info['sku_id'] ] ], 'bargain_stock');

            model('promotion_bargain_launch')->update($data, [ ['launch_id', '=', $launch_id ], ['status', '=', 0 ] ]);
            return $this->success();
        }else{
            return $this->success();
        }
    }
}