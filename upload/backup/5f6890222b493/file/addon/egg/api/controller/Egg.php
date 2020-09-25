<?php
/**
 * Niushop商城系统 - 团队十年电商经验汇集巨献!
 * =========================================================
 * Copy right 2019-2029 山西牛酷信息科技有限公司, 保留所有权利。
 * ----------------------------------------------
 * 官方网址: https://www.niushop.com
 * 这不是一个自由软件！您只能在不用于商业目的的前提下对程序代码进行修改和使用。
 * 任何企业和个人不允许对程序代码以任何形式任何目的再发布。
 * =========================================================
 */

namespace addon\egg\api\controller;

use app\api\controller\BaseApi;
use app\model\games\Games;
use app\model\games\Record;

/**
 * 砸金蛋
 */
class Egg extends BaseApi
{

	/**
	 * 基础信息
	 */
	public function info()
	{
        $site_id = isset($this->params[ 'site_id' ]) ? $this->params[ 'site_id' ] : 0;
	    $game_id = $this->params['id'] ?? 0;
        $game = new Games();
        $info = $game->getGamesInfo([ ['game_id', '=', $game_id], ['site_id', '=', $site_id ], ['game_type', '=', 'egg'] ], 'game_id,game_name,points,start_time,end_time,status,remark,no_winning_desc,no_winning_img,is_show_winner,level_id,level_name,join_type,join_frequency');
        if (!empty($info['data'])) {
            // 奖项
            $game_ward = $game->getGameAward([ ['game_id', '=', $game_id] ], 'award_id,award_name,award_img');
            $game_ward = $game_ward['data'];
            $info['data']['award'] = $game_ward;
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
    public function lottery(){
        $token = $this->checkToken();
        if ($token['code'] < 0) return $this->response($token);
        $site_id = isset($this->params[ 'site_id' ]) ? $this->params[ 'site_id' ] : 0;
        $game_id = $this->params['id'];

        $game = new Games();
        $res = $game->lottery($game_id, $this->member_id, $site_id);
        return $this->response($res);
    }
}