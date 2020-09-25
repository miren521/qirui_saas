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
 * 关闭物流查询
 * @author Administrator
 *
 */
class CloseKd100Trace
{
    public function handle($param = [])
    {
        $kd100_model = new Kd100();
        $result = $kd100_model->modifyStatus(0);
        return $result;

    }

    
}
