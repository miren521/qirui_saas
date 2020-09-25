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


namespace addon\servicer\servicer\controller;

use app\model\upload\Upload as UploadModel;

/**
 * 图片上传
 * Class Verify
 * @package app\shop\controller
 */
class Upload extends BaseServicer
{
    /**
     * 视频上传
     * @return \multitype
     */
    public function video()
    {
        $upload_model = new UploadModel($this->site_id);
        $name = input("name", "");
        $param = ["name" => "file"];
        $result = $upload_model->setPath("chat_img/" . date("Ymd") . '/')->image($param);
        return $result;
    }

    /**
     * 上传(不存入相册)
     * @return array|bool|mixed|\multitype|string
     */
    public function upload()
    {
        $upload_model = new UploadModel();
        $param = ["thumb_type" => "", "name" => "file"];
        $result = $upload_model->setPath("chat_img/" . date("Ymd") . '/')->image($param);
        return $result;
    }
}
