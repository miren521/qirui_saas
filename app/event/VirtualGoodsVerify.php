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

use app\model\goods\VirtualGoods;
/**
 * 虚拟商品核销
 */
class VirtualGoodsVerify
{
    
	public function handle($data)
	{
        if($data['verify_type'] == 'virtualgoods')
        {
            $virtual_goods_model = new VirtualGoods();
            $res = $virtual_goods_model->verify($data["verify_code"]);
            return $res;
        }
        return '';
	}
	
}