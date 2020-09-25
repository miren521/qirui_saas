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

namespace addon\alipay\event;

use addon\alipay\model\Pay;

class PayTransfer
{
    public function handle(array $params)
    {
        if ($params['transfer_type'] == 'alipay') {
            $pay = new Pay();
            $res = $pay->payTransfer($params);
            return $res;
        }
    }
}