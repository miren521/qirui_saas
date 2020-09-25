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


namespace app\shopapi\controller;

use app\model\upload\Upload as UploadModel;

/**
 * 上传管理
 * @author Administrator
 *
 */
class Upload extends BaseApi
{
	
	/**
	 * 头像上传
	 */
	public function image()
	{
		$upload_model = new UploadModel(0);
		$param = array(
			"thumb_type" => "",
			"name" => "file"
		);
        $path = $this->site_id > 0 ? "common/images/".date("Ymd"). '/' : "common/images/".date("Ymd"). '/';
		$result = $upload_model->setPath($path)->image($param);
		return $this->response($result);
	}

	/**
	 * 上传 存入相册
	 */
	public function album()
	{
        $token = $this->checkToken();
        if ($token['code'] < 0) return $this->response($token);

        $upload_model  = new UploadModel($token['data']['site_id']);
        $album_id = input("album_id", 0);

        $param = array(
            "thumb_type" => ["big","mid","small"],
            "name" => "file",
            "album_id" => $album_id
        );
        $result = $upload_model->setPath("common/images/".date("Ymd"). '/')->imageToAlbum($param);

		return $this->response($result);
	}
	
}