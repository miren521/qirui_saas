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

namespace addon\wechatpay\event;

use addon\wechatpay\model\Pay;
class PayTransfer
{
    public function handle(array $params){
        if ($params['transfer_type'] == 'wechatpay') {
            $is_weapp = $params['is_weapp'] ?? 0;
            $pay = new Pay($is_weapp);
            $res = $pay->transfer($params);
            return $res;
        }
    }
}