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


namespace app\model\games;


use app\model\BaseModel;

/**
 * 抽奖记录
 */
class Record extends BaseModel
{

    /**
     * 获取抽奖记录列表
     * @param array $condition
     * @param string $field
     * @param string $order
     * @param null $limit
     * @return array
     */
	public function getGamesDrawRecordList($condition = [], $field = '*', $order = '', $limit = null)
	{
		$list = model('promotion_games_draw_record')->getList($condition, $field, $order, '', '', '', $limit);
		return $this->success($list);
	}

    /**
     * 获取抽奖记录分页列表
     * @param array $condition
     * @param int $page
     * @param int $page_size
     * @param string $order
     * @param string $field
     * @return array
     */
	public function getGamesDrawRecordPageList($condition = [], $page = 1, $page_size = PAGE_LIST_ROWS, $order = '', $field = '*')
	{
		$list = model('promotion_games_draw_record')->pageList($condition, $field, $order, $page, $page_size);
		return $this->success($list);
	}



}