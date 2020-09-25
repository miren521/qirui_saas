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

namespace addon\alioss\event;

use addon\alioss\model\Alioss;

/**
 * 云上传方式
 */
class Put
{
	/**
	 * 短信发送方式方式及配置
	 */
	public function handle($param)
	{
	    $qiniu_model = new Alioss();
        $result = $qiniu_model->putFile($param);
        return $result;
	}
}