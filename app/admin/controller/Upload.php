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


namespace app\admin\controller;

use app\model\upload\Upload as UploadModel;
use app\model\upload\Config as ConfigModel;

/**
 * 上传 控制器
 */
class Upload extends BaseAdmin
{
	/**
	 *  上传配置
	 */
	public function config()
	{
		$config_model = new ConfigModel();
		if (request()->isAjax()) {
			//基础上传
			$max_filesize = input("max_filesize", "10240");//允许上传大小
			$image_allow_ext = trim(input("image_allow_ext", ""));//图片允许扩展名
			$image_allow_mime = trim(input("image_allow_mime", ""));//图片允许Mime类型
			
			/*************************************************************************** 缩略图 *******************************************************************/
			$thumb_position = input("thumb_position", 'top-left');//自定义裁剪的位置。默认是 '中心'  (相对于当前图像)top-left(默认)top top-right left center right bottom-left bottom bottom-right
			$thumb_big_width = input("thumb_big_width", 400);//缩略大图 宽
			$thumb_big_height = input("thumb_big_height", 400);//缩略大图 高
			$thumb_mid_width = input("thumb_mid_width", 200);//缩略中图 宽
			$thumb_mid_height = input("thumb_mid_height", 200);//缩略中图 高
			$thumb_small_width = input("thumb_small_width", 100);//缩略小图 宽
			$thumb_small_height = input("thumb_small_height", 100);//缩略小图 高
			/*************************************************************************** 水印 *******************************************************************/
			$is_watermark = input("is_watermark", 0);//是否开启水印
			$watermark_type = input("watermark_type", '1');//水印类型  1图片 2文字
			$watermark_source = input("watermark_source", '');//水印图片来源
			$watermark_position = input("watermark_position", 'top-left');//水印图片位置(相对于当前图像)top-left(默认)top top-right left center right bottom-left bottom bottom-right
			$watermark_x = input("watermark_x", 0);//水印图片横坐标偏移量
			$watermark_y = input("watermark_y", 0);//水印图片纵坐标偏移量
			$watermark_opacity = input("watermark_opacity", '0');//水印图片透明度
			$watermark_rotate = input("watermark_rotate", '0');//水印图片倾斜度
			
			$watermark_text = input("watermark_text", '');//水印文字
			$watermark_text_file = input("watermark_text_file", '');//水印文字 字体文件。设置True Type Font文件的路径，或者GD库内部字体之一的1到5之间的整数值。 默认值：1
			$watermark_text_size = input("watermark_text_size", '12');//水印文字 字体大小。字体大小仅在设置字体文件时可用，否则将被忽略。 默认值：12
			$watermark_text_color = input("watermark_text_color", '#000000');//水印文字 字体颜色
			$watermark_text_align = input("watermark_text_align", 'left');//水印文字水平对齐方式 水平对齐方式：left,right,center。默认left
			$watermark_text_valign = input("watermark_text_valign", 'bottom');//水印文字垂直对齐方式  垂直对齐方式：top,bottom,middle。默认bottom
			$watermark_text_angle = input("watermark_text_angle", '0');//文本旋转角度。文本将围绕垂直和水平对齐点逆时针旋转。 旋转仅在设置字体文件时可用，否则将被忽略
			
			$data = array(
				//上传相关配置
				"upload" => array(
					"max_filesize" => $max_filesize,//最大上传限制,
					"image_allow_ext" => $image_allow_ext,
					"image_allow_mime" => $image_allow_mime,
				),
				//缩略图相关配置
				"thumb" => array(
					"thumb_position" => $thumb_position,
					"thumb_big_width" => $thumb_big_width,
					"thumb_big_height" => $thumb_big_height,
					"thumb_mid_width" => $thumb_mid_width,
					"thumb_mid_height" => $thumb_mid_height,
					"thumb_small_width" => $thumb_small_width,
					"thumb_small_height" => $thumb_small_height,
				),
				//水印相关配置
				"water" => array(
					"is_watermark" => $is_watermark,
					"watermark_type" => $watermark_type,
					"watermark_source" => $watermark_source,
					"watermark_position" => $watermark_position,
					"watermark_x" => $watermark_x,
					"watermark_y" => $watermark_y,
					"watermark_opacity" => $watermark_opacity,
					"watermark_rotate" => $watermark_rotate,
					"watermark_text" => $watermark_text,
					"watermark_text_file" => $watermark_text_file,
					"watermark_text_size" => $watermark_text_size,
					"watermark_text_color" => $watermark_text_color,
					"watermark_text_align" => $watermark_text_align,
					"watermark_text_valign" => $watermark_text_valign,
					"watermark_text_angle" => $watermark_text_angle,
				),
			);
			$this->addLog("修改上传配置");
			$result = $config_model->setUploadConfig($data);
			return $result;
		} else {
			$this->forthMenu();
			$config_result = $config_model->getUploadConfig();
			$config = $config_result["data"];
			$this->assign("config", $config);
			$position = array(
				"top-left" => "上左",
				"top" => "上中",
				"top-right" => "上右",
				"left" => "左",
				"center" => "中",
				"right" => "右",
				"bottom-left" => "下左",
				"bottom" => "下中",
				"bottom-right" => "下右",
			);//位置
			$this->assign("position", $position);
			return $this->fetch('upload/config');
		}
		
	}
	
	/**
	 * 上传(不存入相册)
	 * @return \app\model\upload\Ambigous|\multitype
	 */
	public function upload()
	{
		$upload_model = new UploadModel();
		$thumb_type = input("thumb", "");
		$name = input("name", "");
		$param = array(
			"thumb_type" => "",
			"name" => "file"
		);
		$result = $upload_model->setPath("common/images/" . date("Ymd") . '/')->image($param);
		return $result;
	}
	
	/**
	 * 上传 存入相册
	 * @return \multitype
	 */
	public function uploadToAlbum()
	{
		$upload_model = new UploadModel();
		$thumb_type = input("thumb", "");
		$name = input("name", "");
		$param = array(
			"thumb_type" => "",
			"name" => "file"
		);
		$result = $upload_model->setPath("common/images/" . date("Ymd") . '/')->imageToAlbum($param);
		return $result;
	}
	
	/**
	 * 云上传方式
	 */
	public function oss()
	{
		if (request()->isAjax()) {
			$config_model = new ConfigModel();
			$list = event('OssType', []);
			return $config_model->success($list);
		} else {
			$this->forthMenu();
			return $this->fetch("upload/oss");
		}
	}

    /**文件上传
     * @return array|\multitype
     */
	public function file(){
        $upload_model = new UploadModel();
        $name = input("name", "");
        $param = array(
            "name" => "file"
        );
        $result = $upload_model->setPath("common/files/" . date("Ymd") . '/')->file($param);
        return $result;
    }
}