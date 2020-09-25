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


namespace app\api\controller;

use app\model\games\Record;

/**
 * 小游戏
 * @author Administrator
 *
 */
class Game extends BaseApi
{
	/**
	 * 会员中奖纪录分页列表信息
	 */
	public function recordPage()
	{
        $token = $this->checkToken();
        if ($token['code'] < 0) return $this->response($token);

		$page = isset($this->params['page']) ? $this->params['page'] : 1;
		$page_size = isset($this->params['page_size']) ? $this->params['page_size'] : PAGE_LIST_ROWS;
		$id = isset($this->params['id']) ? $this->params['id'] : 0;
		
	    $condition = [
            ['game_id', '=', $id],
            ['is_winning', '=', 1],
            ['member_id', '=', $this->member_id]
        ];
        $field = 'member_nick_name,points,is_winning,award_name,award_type,relate_id,relate_name,point,balance,create_time';
	    $record = new Record();
        $list = $record->getGamesDrawRecordPageList($condition, $page, $page_size, 'create_time desc', $field);
		return $this->response($list);
	}
}