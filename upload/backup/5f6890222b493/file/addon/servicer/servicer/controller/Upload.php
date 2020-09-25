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
