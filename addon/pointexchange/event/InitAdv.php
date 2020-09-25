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

namespace addon\pointexchange\event;

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
			'ap_name' => '积分商城',
			'keyword' => 'NS_POINT',
			'ap_intro' => '',
			'ap_height' => '400',
			'ap_width' => '1920',
			'default_content' => '',
			'ap_background_color' => '#FFFFFF',
			'type' => 2,
		]);

	}
}
