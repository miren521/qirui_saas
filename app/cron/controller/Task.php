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


namespace app\cron\controller;

use app\Controller;
use app\model\system\Cron;
use Exception;
use think\facade\Log;
use think\facade\Cache;

/**
 * 计划任务
 * @author Administrator
 */
class Task extends Controller
{
    /**
     * 执行计划任务(单独计划任务)
     * @throws Exception
     */
    public function execute()
    {
        ignore_user_abort(true);
        set_time_limit(0);
        //设置计划任务标识
        $last_time = Cache::get("cron_last_load_time");
        if ($last_time == false) {
            $last_time = 0;
        }

        Log::write("检测事件执行" . date("Y-m-d H:i:s", time()));
        $time = time();
        if (($time - $last_time) < 20) {
            Log::write("防止多次执行");
            exit();//跳出
        }
        Cache::set("cron_last_load_time", time());
        $cron_model = new Cron();
        $cron_model->execute();
        sleep(60);
        $url = url('cron/task/execute');
        http($url, 1);
        exit();
    }

    /**
     * php自动执行事件
     * @throws Exception
     */
    public function phpCron()
    {
        $url = url('cron/task/execute');
        http($url, 1);
    }
}
