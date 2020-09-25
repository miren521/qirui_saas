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

declare ( strict_types = 1 );

namespace addon\bargain\event;

use app\model\web\Adv as AdvModel;
use app\model\web\AdvPosition;

/**
 * 广告
 * @author Administrator
 *
 */
class InitAdv
{
	public function handle($data)
	{
		$adv_position_model = new AdvPosition();
		$res = $adv_position_model->addAdvPosition([
			'ap_name' => '砍价专区',
			'keyword' => 'NS_BARGAIN',
			'ap_intro' => '',
			'ap_height' => '400',
			'ap_width' => '1920',
			'default_content' => '',
			'ap_background_color' => '#FFFFFF',
			'type' => 2,
		]);

		$adv_model = new AdvModel();
		$adv_model->addAdv([
			'ap_id' => $res[ 'data' ],
			'adv_title' => '砍价专区',
			'adv_url' => '',
			'adv_image' => 'upload/default/bargain/gg_1.png',
			'slide_sort' => 0,
			'price' => 0,
			'background' => '#FFFFFF'
		]);

	}
}
