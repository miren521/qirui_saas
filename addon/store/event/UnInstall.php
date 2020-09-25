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

namespace addon\store\event;

use addon\store\model\Menu;
/**
 * 应用卸载
 */
class UnInstall
{
	/**
	 * 执行卸载
	 */
	public function handle()
	{
	    $menu = new Menu();
	    $res = $menu->uninstall();
        return $res;
	}
}