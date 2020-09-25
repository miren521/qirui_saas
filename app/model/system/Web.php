<?php
// +---------------------------------------------------------------------+
// | NiuCloud | [ WE CAN DO IT JUST NiuCloud ]                |
// +---------------------------------------------------------------------+
// | Copy right 2019-2029 www.niucloud.com                          |
// +---------------------------------------------------------------------+
// | Author | NiuCloud <niucloud@outlook.com>                       |
// +---------------------------------------------------------------------+
// | Repository | https://github.com/niucloud/framework.git          |
// +---------------------------------------------------------------------+

namespace app\model\system;

use app\model\BaseModel;
use extend\api\HttpClient;
use think\facade\Cache;

class Web extends BaseModel
{
    private $url = "https://www.niushop.com";

    /**
     * 官网资讯
     */
    public function news()
    {
        $cache = Cache::get("new_day");
        if(!empty($cache)) return $cache;

        $result = $this->doPost('/api/article/companynews', []);
        $res = json_decode($result, true);
        if($res["code"] >= 0){
            Cache::set("new_day", $res, 86400);
        }
        return $res;
    }


    /**
     * post 服务器请求
     */
    private function doPost($post_url, $post_data)
    {
        $httpClient             = new HttpClient();
        $res                    = $httpClient->post($this->url . $post_url, $post_data);
        return $res;
    }
}
