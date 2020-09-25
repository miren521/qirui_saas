<?php
// +---------------------------------------------------------------------+
// | NiuCloud | [ WE CAN DO IT JUST NiuCloud ]                |
// +---------------------------------------------------------------------+
// | Copy right 2019-2029 www.niucloud.com                          |
// +---------------------------------------------------------------------+
// | Author | NiuCloud <niucloud@outlook.com>                       |
// +---------------------------------------------------------------------+
// | Repository | https://github.com/niucloud/framework.git          |
// +---------------------------------------------------------------------+
declare (strict_types = 1);

namespace addon\topic\event;

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
/* 	    try{
	        execute_sql('addon/seckill/data/install.sql');
	        return success();
	    }catch (\Exception $e)
	    {
	        return error('', $e->getMessage());
	    } */
	    return success();
	}
}