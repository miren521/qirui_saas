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



namespace addon\goodscircle\event;

/**
 * 应用安装
 */
class Install
{
	/**
	 * 执行安装
	 */
	public function handle()
	{
	    try{
	        execute_sql('addon/manjian/data/install.sql');
	        return success();
	    }catch (\Exception $e)
	    {
	        return error('', $e->getMessage());
	    }
	}
}