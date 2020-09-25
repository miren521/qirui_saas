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


namespace app\model\upload;

use app\model\system\Config as ConfigModel;
use app\model\BaseModel;

/**
 * 上传设置
 */
class Config extends BaseModel
{
	/**
	 * 上传配置
	 * array $data
	 */
	public function setUploadConfig($data)
	{
		$config = new ConfigModel();
		$res = $config->setConfig($data, '上传设置', 1, [ [ 'site_id', '=', 0 ], [ 'app_module', '=', 'admin' ], [ 'config_key', '=', 'UPLOAD_CONFIG' ] ]);
		return $res;
	}

	/**
	 * 查询上传配置
	 */
	public function getUploadConfig()
	{
		$config = new ConfigModel();
		$res = $config->getConfig([ [ 'site_id', '=', 0 ], [ 'app_module', '=', 'admin' ], [ 'config_key', '=', 'UPLOAD_CONFIG' ] ]);
		if (empty($res[ 'data' ][ 'value' ])) {
			$res[ 'data' ][ 'value' ] =
				[
					"upload" => [
						"max_filesize" => "0",
						"image_allow_ext" => "",
						"image_allow_mime" => ""
					],
					"thumb" => [
						"thumb_position" => "top",
						"thumb_big_width" => "700",
						"thumb_big_height" => "700",
						"thumb_mid_width" => "400",
						"thumb_mid_height" => "400",
						"thumb_small_width" => "100",
						"thumb_small_height" => "100"
					],
					"water" => [
						"is_watermark" => "0",
						"watermark_type" => "1",
						"watermark_source" => "",
						"watermark_position" => "top-left",
						"watermark_x" => "",
						"watermark_y" => "",
						"watermark_opacity" => "",
						"watermark_rotate" => "",
						"watermark_text" => "",
						"watermark_text_file" => "",
						"watermark_text_size" => "",
						"watermark_text_color" => "",
						"watermark_text_align" => "left",
						"watermark_text_valign" => "top",
						"watermark_text_angle" => ""
					]
				];
		}
		return $res;
	}
}