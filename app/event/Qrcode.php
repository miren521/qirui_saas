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

declare (strict_types = 1);

namespace app\event;

use app\model\system\Qrcode as QrcodeModel;
/**
 * 生成二维码
 * @author Administrator
 *
 */
class Qrcode
{
    public function handle($param)
    {
        if (in_array($param["app_type"], ['h5','pc', 'all', 'wechat'])) {
            if ($param["app_type"] == 'all') $param["app_type"] = 'h5';
            $qrcode = new QrcodeModel();
            $res = $qrcode->createQrcode($param);
    	    return $res;
	    }
    }
    
}
