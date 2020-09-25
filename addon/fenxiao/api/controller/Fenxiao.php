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


namespace addon\fenxiao\api\controller;

use addon\fenxiao\model\Fenxiao as FenxiaoModel;
use addon\fenxiao\model\FenxiaoLevel;
use addon\fenxiao\model\FenxiaoOrder;
use addon\fenxiao\model\Poster;
use app\api\controller\BaseApi;
use app\model\member\Member;
use Carbon\Carbon;

/**
 * 分销相关信息
 */
class Fenxiao extends BaseApi
{
	/**
	 * 获取分销商信息
	 */
    public function detail()
    {
        $token = $this->checkToken();
        if ($token[ 'code' ] < 0) return $this->response($token);

        $condition = [
            [ 'f.member_id', '=', $this->member_id ]
        ];

        $model = new FenxiaoModel();
        $info = $model->getFenxiaoDetailInfo($condition);

        if (empty($info[ 'data' ])) {
            $res = $model->autoBecomeFenxiao($this->member_id);
            if (isset($res[ 'code' ]) && $res[ 'code' ] >= 0) {
                $info = $model->getFenxiaoDetailInfo($condition);
            }
        } else {
            $member = new Member();
            $info[ 'data' ][ 'one_child_num' ] = $member->getMemberCount([ [ 'fenxiao_id', '=', $info[ 'data' ][ 'fenxiao_id' ] ], [ 'is_fenxiao', '=', 0 ] ])[ 'data' ];

            $condition_result = $model->geFenxiaoLastLevel($this->member_id);
            $info[ 'data' ][ 'condition' ] = $condition_result[ 'data' ];
        }

        return $this->response($info);
    }
	
	/**
	 * 获取推荐人分销商信息
	 */
	public function sourceInfo()
	{
		$token = $this->checkToken();
		if ($token['code'] < 0) return $this->response($token);
		
		$member = new Member();
		$member_info = $member->getMemberInfo([ ['member_id', '=', $this->member_id] ], 'fenxiao_id');
		$fenxiao_id = $member_info['data']['fenxiao_id'] ?? 0; 
		
		if (empty($fenxiao_id)) {
			return $this->response($this->error('', 'REQUEST_SOURCE_MEMBER'));
		}
		$condition = [
			[ 'fenxiao_id', '=', $fenxiao_id ]
		];
		
		$model = new FenxiaoModel();
		$info = $model->getFenxiaoInfo($condition, 'fenxiao_name');
		
		return $this->response($info);
	}
	
	/**
	 * 分销海报
	 * @return \app\api\controller\false|string
	 */
	public function poster()
	{
		$token = $this->checkToken();
		if ($token['code'] < 0) return $this->response($token);
		
		$qrcode_param = isset($this->params['qrcode_param']) ? $this->params['qrcode_param'] : '';//二维码
		if (empty($qrcode_param)) {
			return $this->response($this->error('', 'REQUEST_QRCODE_PARAM'));
		}
		
		$qrcode_param = json_decode($qrcode_param, true);
		$qrcode_param['source_member'] = $this->member_id;
		
		$poster = new Poster();
		$res = $poster->distribution($this->params['app_type'], $this->params['page'], $qrcode_param);
		
		return $this->response($res);
	}
	
	/**
	 * 分销商等级信息
	 */
	public function level()
	{
		$token = $this->checkToken();
		if ($token['code'] < 0) return $this->response($token);
		
		$level = $this->params['level'] ?? 0;
		
		$condition = [
			[ 'level_id', '=', $level ]
		];
		$model = new FenxiaoLevel();
		$info = $model->getLevelInfo($condition);
		
		return $this->response($info);
	}
	
	/**
	 * 分销商我的团队
	 */
	public function team(){
		$token = $this->checkToken();
		if ($token['code'] < 0) return $this->response($token);
		
		$page = $this->params['page'] ?? 1;
		$page_size = $this->params['page_size'] ?? PAGE_LIST_ROWS;
		$level = $this->params['level'] ?? 1;

		$model = new FenxiaoModel();
		$fenxiao_info = $model->getFenxiaoInfo([[ 'member_id', '=', $this->member_id ]], 'fenxiao_id');
		if (empty($fenxiao_info['data'])) return $this->response($this->error('', 'MEMBER_NOT_IS_FENXIAO'));
		
		$list = $model->getFenxiaoTeam($level, $fenxiao_info['data']['fenxiao_id'], $page, $page_size);
		
		return $this->response($list);
	}
	
	/**
	 * 查询我的团队的数量
	 */
	public function teamNum(){
		$token = $this->checkToken();
		if ($token['code'] < 0) return $this->response($token);
		
		$model = new FenxiaoModel();
		$fenxiao_info = $model->getFenxiaoInfo([[ 'member_id', '=', $this->member_id ]], 'fenxiao_id');
		if (empty($fenxiao_info['data'])) return $this->response($this->error('', 'MEMBER_NOT_IS_FENXIAO'));
	
		$data = $model->getFenxiaoTeamNum($fenxiao_info['data']['fenxiao_id']);
		return $this->response($data);
	}
}