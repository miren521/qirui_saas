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

use app\model\express\Kdbird;

/**
 * 关闭物流查询
 * @author Administrator
 *
 */
class CloseKdbirdTrace
{
    public function handle($param = [])
    {
        $kdbird_model = new Kdbird();
        $result = $kdbird_model->modifyStatus(0);
        return $result;
    }
}
