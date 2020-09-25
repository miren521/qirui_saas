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

namespace addon\topic\model;

use app\model\BaseModel;
use app\model\system\Cron;
use think\facade\Cache;
use think\facade\Db;

/**
 * 专题活动
 */
class Topic extends BaseModel
{
	/**
	 * 添加专题活动
	 * @param unknown $data
	 */
	public function addTopic($data)
	{
		//时间段检测
		$topic_count = model('promotion_topic')->getCount([
            [ '', 'exp', Db::raw('not ( (`start_time` > '.$data['end_time'].' and `start_time` > '.$data['start_time'].' )  or (`end_time` < '.$data['start_time'].' and `end_time` < '.$data['end_time'].'))')]//todo  修正  所有的优惠都要一样
		]);
		if ($topic_count > 0) {
			return $this->error('', '专题时间段设置冲突');
		}
//		$topic_count = model('promotion_topic')->getCount([
//			[ 'start_time', '<=', $data['start_time'] ],
//			[ 'end_time', '>=', $data['end_time'] ],
//		]);
//		if ($topic_count > 0) {
//			return $this->error('', '专题时间段设置冲突');
//		}

        if (time() > $data['start_time'] && time() < $data['end_time']) {
            $data['status'] = 2;
        } else {
            $data['status'] = 1;
        }
		//添加数据
		$data['create_time'] = time();
		$topic_id = model('promotion_topic')->add($data);

		// 添加定时任务
        $cron = new Cron();
        if ($data['status'] == 2) {
            $cron->addCron(1, 0, "专题活动关闭", "CloseTopic", $data['end_time'], $topic_id);
        } else {
            $cron->addCron(1, 0, "专题活动开启", "OpenTopic", $data['start_time'], $topic_id);
            $cron->addCron(1, 0, "专题活动关闭", "CloseTopic", $data['end_time'], $topic_id);
        }

		Cache::clear("promotion_topic");
		return $this->success($topic_id);
	}
	
	/**
	 * 修改专题活动
	 * @param unknown $data
	 * @return multitype:string
	 */
	public function editTopic($data)
	{
		//时间段检测
		$topic_count = model('promotion_topic')->getCount([
            [ '', 'exp', Db::raw('not ( (`start_time` > '.$data['end_time'].' and `start_time` > '.$data['start_time'].' )  or (`end_time` < '.$data['start_time'].' and `end_time` < '.$data['end_time'].'))')],//todo  修正  所有的优惠都要一样
			[ 'topic_id', '<>', $data['topic_id'] ]
		]);
		
		if ($topic_count > 0) {
			return $this->error('', '专题时间段设置冲突');
		}
//		$topic_count = model('promotion_topic')->getCount([
//			[ 'start_time', '<=', $data['start_time'] ],
//			[ 'end_time', '>=', $data['end_time'] ],
//			[ 'topic_id', '<>', $data['topic_id'] ]
//		]);
//		if ($topic_count > 0) {
//			return $this->error('', '专题时间段设置冲突');
//		}

        if (time() > $data['start_time'] && time() < $data['end_time']) {
            $data['status'] = 2;
        } else {
            $data['status'] = 1;
        }
		//更新数据
		$res = model('promotion_topic')->update($data, [ [ 'topic_id', '=', $data['topic_id'] ] ]);
		$goods_data = [
			'start_time' => $data['start_time'],
			'end_time' => $data['end_time'],
		];
		model('promotion_topic_goods')->update($goods_data, [ [ 'topic_id', '=', $data['topic_id'] ] ]);

        // 添加定时任务
        $cron = new Cron();
        if ($data['status'] == 2) {
            //活动商品启动
            $this->cronOpenTopic($data['topic_id']);
            $cron->deleteCron([ [ 'event', '=', 'OpenTopic' ], [ 'relate_id', '=', $data['topic_id'] ] ]);
            $cron->deleteCron([ [ 'event', '=', 'CloseTopic' ], [ 'relate_id', '=', $data['topic_id'] ] ]);

            $cron->addCron(1, 0, "专题活动关闭", "CloseTopic", $data['end_time'], $data['topic_id']);
        } else {
            $cron->deleteCron([ [ 'event', '=', 'OpenTopic' ], [ 'relate_id', '=', $data['topic_id'] ] ]);
            $cron->deleteCron([ [ 'event', '=', 'CloseTopic' ], [ 'relate_id', '=', $data['topic_id'] ] ]);

            $cron->addCron(1, 0, "专题活动开启", "OpenTopic", $data['start_time'], $data['topic_id']);
            $cron->addCron(1, 0, "专题活动关闭", "CloseTopic", $data['end_time'], $data['topic_id']);
        }
		Cache::clear("promotion_topic");
		return $this->success($res);
	}
	
	/**
	 * 删除专题活动
	 * @param unknown $topic_id
	 */
	public function deleteTopic($topic_id)
	{
		$res = model('promotion_topic')->delete([ [ 'topic_id', '=', $topic_id ] ]);
		if ($res) {
			model('promotion_topic_goods')->delete([ [ 'topic_id', '=', $topic_id ] ]);
		}
		Cache::clear("promotion_topic");
		return $this->success($res);
	}
	
	/**
	 * 获取专题活动信息
	 * @param array $condition
	 * @param string $field
	 */
	public function getTopicInfo($condition, $field = '*')
	{
		$data = json_encode([ $condition, $field ]);
		$cache = Cache::get("promotion_topic_gettopicInfo_" . $data);
		if (!empty($cache)) {
			return $this->success($cache);
		}
		$res = model('promotion_topic')->getInfo($condition, $field);
		Cache::tag("promotion_topic")->set("promotion_topic_gettopicInfo_" . $data, $res);
		return $this->success($res);
	}
	
	/**
	 * 获取专题活动列表
	 * @param array $condition
	 * @param string $field
	 * @param string $order
	 * @param string $limit
	 */
	public function getTopicList($condition = [], $field = '*', $order = '', $limit = null)
	{
		$data = json_encode([ $condition, $field, $order, $limit ]);
		$cache = Cache::get("promotion_topic_gettopicList_" . $data);
		if (!empty($cache)) {
			return $this->success($cache);
		}
		$list = model('promotion_topic')->getList($condition, $field, $order, '', '', '', $limit);
		Cache::tag("promotion_topic")->set("promotion_topic_gettopicList_" . $data, $list);
		
		return $this->success($list);
	}
	
	/**
	 * 获取专题分页列表
	 * @param array $condition
	 * @param number $page
	 * @param string $page_size
	 * @param string $order
	 * @param string $field
	 */
	public function getTopicPageList($condition = [], $page = 1, $page_size = PAGE_LIST_ROWS, $order = 'modify_time desc,create_time desc', $field = '*')
	{
		$data = json_encode([ $condition, $field, $order, $page, $page_size ]);
		$cache = Cache::get("promotion_topic_getTopicPageList_" . $data);
		if (!empty($cache)) {
			return $this->success($cache);
		}
		$list = model('promotion_topic')->pageList($condition, $field, $order, $page, $page_size);
		Cache::tag("promotion_topic")->set("promotion_topic_getTopicPageList_" . $data, $list);
		return $this->success($list);
	}

    /**
     * 开启专题活动
     * @param $groupbuy_id
     * @return array|\multitype
     */
    public function cronOpenTopic($topic_id)
    {
        $topic_info = model('promotion_topic')->getInfo([ [ 'topic_id', '=', $topic_id ] ], 'start_time,status');
        if (!empty($topic_info)) {
            if ($topic_info['start_time'] <= time() && $topic_info['status'] == 1) {
                $res = model('promotion_topic')->update([ 'status' => 2 ], [ [ 'topic_id', '=', $topic_id ] ]);
                Cache::clear("promotion_topic");
                return $this->success($res);
            } else {
                return $this->error("", "专题活动已开启或者关闭");
            }

        } else {
            return $this->error("", "专题活动不存在");
        }

    }

    /**
     * 关闭专题活动
     * @param $groupbuy_id
     * @return array|\multitype
     */
    public function cronCloseTopic($topic_id)
    {
        $topic_info = model('promotion_topic')->getInfo([ [ 'topic_id', '=', $topic_id ] ], 'start_time,status');
        if (!empty($topic_info)) {
            if ($topic_info['status'] != 3) {
                $res = model('promotion_topic')->update([ 'status' => 3 ], [ [ 'topic_id', '=', $topic_id ] ]);
                Cache::clear("promotion_topic");
                return $this->success($res);
            } else {
                return $this->error("", "该活动已结束");
            }

        } else {
            return $this->error("", "专题活动不存在");
        }
    }
}