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


namespace addon\fenxiao\model;

use app\model\BaseModel;
use extend\Poster as PosterExtend;

/**
 * 海报生成类
 */
class Poster extends BaseModel
{
	/**
	 * 分销海报
	 */
	public function distribution($app_type, $page, $qrcode_param)
	{
		try {
			$qrcode_info = $this->getQrcode($app_type, $page, $qrcode_param);
			if ($qrcode_info['code'] < 0) return $qrcode_info;
			
			$member_info = $this->getMemberInfo($qrcode_param['source_member']);
			if (empty($member_info)) return $this->error('未获取到会员信息');
			
			$poster = new PosterExtend(740, 1250);
			$option = [
				[
					'action' => 'imageCopy', // 背景图
					'data' => [
						'upload/poster/bg/fenxiao_1.png',
						0,
						0,
						740,
						1250
					]
				],
				[
					'action' => 'imageCopy', // 写入二维码
					'data' => [
						$qrcode_info['data']['path'],
						505,
						980,
						205,
						205
					]
				],
				[
					'action' => 'imageCircularCopy', // 写入用户头像
					'data' => [
						!empty($member_info['headimg']) ? $member_info['headimg'] : 'upload/uniapp/default_headimg.png',
						82,
						852,
						112,
						112
					]
				],
				[
					'action' => 'imageText', // 写入分享人昵称
					'data' => [
						$member_info['nickname'],
						22,
						[ 255, 129, 61 ],
						40,
						1030,
						440,
						1,
						true
					]
				],
				[
					'action' => 'imageText', // 写入分享语
					'data' => [
						'邀您一起分享赚佣金',
						18,
						[ 141, 141, 141 ],
						40,
						1070,
						440,
						1
					]
				]
			];
			
			$option_res = $poster->create($option);
			if (is_array($option_res)) return $option_res;
			
			$res = $option_res->jpeg('upload/poster/distribution', 'distribution_' . $qrcode_param['source_member'] . '_' . $app_type);
			return $res;
		} catch (\Exception $e) {
			return $this->error($e->getMessage() . $e->getFile() . $e->getLine());
		}
	}
	
	/**
	 * 获取用户信息
	 * @param unknown $member_id
	 */
	private function getMemberInfo($member_id)
	{
		$info = model('member')->getInfo([ 'member_id' => $member_id ], 'nickname,headimg');
		return $info;
	}
	
	/**
	 * 获取二维码
	 * @param unknown $app_type 请求类型
	 * @param unknown $page uniapp页面路径
	 * @param unknown $qrcode_param 二维码携带参数
	 * @param string $promotion_type 活动类型 null为无活动
	 */
	private function getQrcode($app_type, $page, $qrcode_param)
	{
		$res = event('Qrcode', [
			'app_type' => $app_type,
			'type' => 'get',
			'data' => $qrcode_param,
			'page' => $page,
			'qrcode_path' => 'upload/qrcode/distribution',
			'qrcode_name' => 'distribution' . '_' . $qrcode_param['source_member'],
		], true);
		return $res;
	}
}