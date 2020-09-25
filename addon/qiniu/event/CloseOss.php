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

namespace addon\qiniu\event;

use addon\qiniu\model\Config;

/**
 * 关闭云上传
 */
class CloseOss
{
        public function handle()
        {
            $config_model = new Config();
            $result = $config_model->modifyConfigIsUse(0);
            return $result;
        }
}