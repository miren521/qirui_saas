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

use app\model\express\Kd100;

/**
 * 初始化配置信息
 * @author Administrator
 *
 */
class Kd100Trace
{
    public function handle($data)
    {
        $kd100_model = new Kd100();
        $express_no_data = $data["express_no_data"];
        $express_no = $express_no_data["express_no_kd100"];
        $result = $kd100_model->trace($data["code"], $express_no);
        return $result;

    }

    
}
