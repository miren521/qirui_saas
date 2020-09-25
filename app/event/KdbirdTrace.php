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
 * 初始化配置信息
 * @author Administrator
 *
 */
class KdbirdTrace
{
    public function handle($data)
    {
        $kdbird_model = new Kdbird();
        $express_no_data = $data["express_no_data"];
        $express_no = $express_no_data["express_no_kdniao"];
        $mobile = $data["mobile"] ?? '';
        $result = $kdbird_model->trace($data["code"], $express_no, $mobile);
        return $result;
    }
}
