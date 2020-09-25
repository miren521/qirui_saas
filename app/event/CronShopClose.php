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
declare (strict_types = 1);

namespace app\event;

use app\model\shop\Shop;
use think\facade\Db;
use think\facade\Log;

/**
 * 店铺自动关闭(每日执行一次)
 */
class CronShopClose
{
    // 行为扩展的执行入口必须是run
    public function handle($param = [])
    {

        $shop_model = new Shop();
        $now_time = time();
        $condition = array(
            ["shop_status", "=", 1],
            ["expire_time", "exp", Db::raw('<= '.$now_time.' and expire_time != 0')],

        );
        $shop_list_result = $shop_model->getShopList($condition,"site_id");

        if(!empty($shop_list_result["data"])){
            $shop_list = $shop_list_result["data"];
            foreach($shop_list as $k => $v){
                $shop_model->shopClose($v["site_id"]);
            }
        }
    }

}