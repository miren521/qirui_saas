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



namespace addon\live\event;

use app\model\system\Cron;

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
            $cron_model = new Cron();
            $execute_time = strtotime(date("Y-m-d 00:00:00", strtotime('+1 day')));
            $item_result = $cron_model->addCron(2, 1, '轮询更新同步直播间', 'LiveRoomStatus', $execute_time, 0, 1);
            if($item_result['code'] < 0)
                return $item_result;

            $item_result = $cron_model->addCron(2, 1, '轮询更新同步直播商品', 'LiveGoodsStatus', $execute_time, 0, 1);
            if($item_result['code'] < 0)
                return $item_result;

	        return success();
	    }catch (\Exception $e)
	    {
	        return error('', $e->getMessage());
	    }
	}
}