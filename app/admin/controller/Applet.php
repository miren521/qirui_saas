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


namespace app\admin\controller;


/**
 * 小程序管理 控制器
 */
class Applet extends BaseAdmin
{
	/**
	 * 概况
	 */
	public function index()
	{
        return $this->fetch('applet/index');
	}

}