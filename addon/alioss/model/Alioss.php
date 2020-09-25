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

namespace addon\alioss\model;

use app\model\BaseModel;
// 引入Oss类
use \OSS\OssClient;
use \OSS\Core\OssException;
/**
 * 阿里云OSS上传
 */
class Alioss extends BaseModel
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
        $config_result = $config_model->getAliossConfig();
        $config = $config_result["data"];

        if($config["is_use"] == 1){
            $config = $config["value"];
            $access_key_id = $config["access_key_id"];
            $access_key_secret = $config["access_key_secret"];
            $bucket = $config["bucket"];
            $endpoint = $config["endpoint"];
            try{
                $ossClient = new OssClient($access_key_id, $access_key_secret, $endpoint);

                $result = $ossClient->putObject($bucket, $key, $data);
                $is_domain = $config['is_domain'] ?? 0;
                $path = $is_domain > 0 ? $config['domain']."/". $key : $result["info"]["url"];
                $data = array(
                    "path" => $path,
//                    "path" => $result["info"]["url"],
                    "domain" => $endpoint,
                    "bucket" => $bucket
                );
                return $this->success($data);
            } catch(OssException $e) {
                return $this->error($e->getMessage());
            }


        }
    }

    /**
     * 设置阿里云OSS参数配置
     * @param unknown $filePath  上传图片路径
     * @param unknown $key 上传到阿里云后保存的文件名
     */
    public function putFile($param){
        $file_path = $param["file_path"];
        $key = $param["key"];
        $config_model = new Config();
        $config_result = $config_model->getAliossConfig();
        $config = $config_result["data"];
        if($config["is_use"] == 1) {
            $config = $config["value"];
            $access_key_id = $config["access_key_id"];
            $access_key_secret = $config["access_key_secret"];
            $bucket = $config["bucket"];
            //要上传的空间
            $endpoint = $config["endpoint"];
            try{
                $ossClient = new OssClient($access_key_id, $access_key_secret, $endpoint);
                $result = $ossClient->uploadFile($bucket, $key, $file_path);

                $is_domain = $config['is_domain'] ?? 0;
                $path = $is_domain > 0 ? $config['domain']."/". $key : $result["info"]["url"];
                //返回图片的完整URL
                $data = array(
//                    "path" => $this->subEndpoint($endpoint, $bucket)."/". $key,
                    "path" => $path,
                    "domain" => $endpoint,
                    "bucket" => $bucket
                );
                return $this->success($data);
            } catch(OssException $e) {

                return $this->error($e->getMessage());
            }
        }
    }


    public function subEndpoint($endpoint, $bucket){
        if (strpos($endpoint, 'http://') === 0 ) {
            $temp = "http://";
        }else{
            $temp = "https://";
        }
        $temp_array = explode($temp, $endpoint);
        return $temp.$bucket.".".$temp_array[1];
    }

}