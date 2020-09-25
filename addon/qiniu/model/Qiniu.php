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

namespace addon\qiniu\model;

use app\model\BaseModel;
// 引入鉴权类
use \Qiniu\Auth;
// 引入上传类
use \Qiniu\Storage\UploadManager;

/**
 * 七牛云上传
 */
class Qiniu extends BaseModel
{

    /**
     * 字节组上传
     * @param $data
     * @param $key
     * @return array
     */
    public function put($param){
        $data = $param["data"];
        $key = $param["key"];
        $config_model = new Config();
        $config_result = $config_model->getQiniuConfig();
        $config = $config_result["data"];

        if($config["is_use"] == 1){
            $config = $config["value"];
            $accessKey = $config["access_key"];
            $secretKey = $config["secret_key"];
            $bucket = $config["bucket"];
            $auth = new Auth($accessKey, $secretKey);
            $token = $auth->uploadToken($bucket);
            $uploadMgr = new UploadManager();
            //----------------------------------------upload demo1 ----------------------------------------
            // 上传字符串到七牛
            list($ret, $err) = $uploadMgr->put($token, $key, $data);
            var_dump($ret);
            var_dump($err);
            exit();
            if ($err !== null) {
                return $this->error($err->getResponse()->error);
            } else {
                //返回图片的完整URL
                $domain = $config["domain"];//自定义域名
                $data = array(
                    "path" => $domain."/". $key,
                    "domain" => $domain,
                    "bucket" => $bucket
                );
                return $this->success($data);
            }
        }
    }

    /**
     * 设置七牛参数配置
     * @param unknown $filePath  上传图片路径
     * @param unknown $key 上传到七牛后保存的文件名
     */
    public function putFile($param){
        $file_path = $param["file_path"];
        $key = $param["key"];
        $config_model = new Config();
        $config_result = $config_model->getQiniuConfig();
        $config = $config_result["data"];
        if($config["is_use"] == 1) {
            $config = $config["value"];
            $accessKey = $config["access_key"];
            $secretKey = $config["secret_key"];
            $bucket = $config["bucket"];
            $auth = new Auth($accessKey, $secretKey);
            //要上传的空间
            $domain = $config["domain"];
            $token = $auth->uploadToken($bucket);
            // 初始化 UploadManager 对象并进行文件的上传
            $uploadMgr = new UploadManager();
            // 调用 UploadManager 的 putFile 方法进行文件的上传
            list($ret, $err) = $uploadMgr->putFile($token, $key, $file_path);
            if ($err !== null) {
                return $this->error($err->getResponse()->error);
            } else {
                //返回图片的完整URL
                $domain = $config["domain"];//自定义域名
                $data = array(
                    "path" => $domain."/". $key,
                    "domain" => $domain,
                    "bucket" => $bucket
                );
                return $this->success($data);
            }
        }
    }

}