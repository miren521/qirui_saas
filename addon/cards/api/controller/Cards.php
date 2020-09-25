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


namespace addon\cards\api\controller;

use app\api\controller\BaseApi;
use app\model\games\Games;
use app\model\games\Record;

/**
 * 刮刮卡
 */
class Cards extends BaseApi
{

    public function __construct()
    {
        parent::__construct();

        if (request()->isOptions()) exit;
        //获取参数
        $this->params = input();
    }

	/**
	 * 基础信息
	 */
	public function info()
	{
	    $game_id = $this->params['id'] ?? 0;
        $site_id = isset($this->params[ 'site_id' ]) ? $this->params[ 'site_id' ] : 0;

        $game = new Games();
        $info = $game->getGamesInfo([ ['game_id', '=', $game_id], ['site_id', '=', $site_id ], ['game_type', '=', 'cards'] ], 'game_id,game_name,points,start_time,end_time,status,remark,no_winning_desc,no_winning_img,is_show_winner,level_id,level_name,join_type,join_frequency');
        if (!empty($info['data'])) {
            // 中奖名单
            if ($info['data']['is_show_winner']) {
                $record = new Record();
                $record_data = $record->getGamesDrawRecordPageList([ ['game_id', '=', $game_id], ['is_winning', '=', 1] ], 1, 10, 'create_time desc', 'member_nick_name,award_name,create_time');
                $info['data']['draw_record'] = $record_data['data']['list'];
            }
            // 剩余次数
            $token = $this->checkToken();
            $info['data']['surplus_num'] = 0;
            if ($info['data']['join_frequency'] && $token['code'] == 0) {
                $surplus_num = $game->getMemberSurplusNum($game_id, $this->member_id, $site_id);
                $info['data']['surplus_num'] = $surplus_num['data'];
            }
        }
        return $this->response($info);
    }

    /**
     * 抽奖
     * @return false|string
     */
    public function lottery()
    {
        $token = $this->checkToken();
        if ($token['code'] < 0) return $this->response($token);

        $site_id = isset($this->params[ 'site_id' ]) ? $this->params[ 'site_id' ] : 0;
        $game_id = $this->params['id'];

        $game = new Games();
        $res = $game->lottery($game_id, $this->member_id, $site_id);
        return $this->response($res);
    }
}