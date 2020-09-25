<?php

/**
 * This file is part of workerman.
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the MIT-LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @author walkor<walkor@workerman.net>
 * @copyright walkor<walkor@workerman.net>
 * @link http://www.workerman.net/
 * @license http://www.opensource.org/licenses/mit-license.php MIT License
 */

/**
 * 用于检测业务代码死循环或者长时间阻塞等问题
 * 如果发现业务卡死，可以将下面declare打开（去掉//注释），并执行php start.php reload
 * 然后观察一段时间workerman.log看是否有process_timeout异常
 */
//declare(ticks=1);

use \GatewayWorker\Lib\Gateway;
use Workerman\MySQL\Connection;

/**
 * 主逻辑
 * 主要是处理 onConnect onMessage onClose 三个方法
 * onConnect 和 onClose 如果不需要可以不用实现并删除
 */
class Events
{
    protected static $db;
    protected static $prefix;

    /**
     * 进行启动时操作
     *
     * @param mixed $businessWorker
     * @return void
     */
    public static function onWorkerStart($businessWorker)
    {
        // 数据库连接
        $config = [
            // 连接地址
            'host'   => '',
            // 端口
            'port'   => '',
            // 用户名
            'user'   => '',
            // 密码
            'passwd' => '',
            // 表前缀
            'prefix' => '',
            // 数据库名称
            'dbname' => ''
        ];
        self::$prefix = $config['prefix'];
        self::$db = new Connection($config['host'], $config['port'], $config['user'], $config['passwd'], $config['dbname']);
    }

    /**
     * 当客户端连接时触发
     * 如果业务不需此回调可以删除onConnect
     * @param int $client_id 连接id
     */
    public static function onConnect($client_id)
    {
        $message = json_encode(['type' => 'init', 'data' => ['client_id' => $client_id]]);
        // 向当前client_id发送数据
        Gateway::sendToClient($client_id, $message);
    }

    /**
     * 当客户端发来消息时触发
     * @param int $client_id 连接id
     * @param mixed $message 具体消息
     */
    public static function onMessage($client_id, $message)
    {
        // 向所有人发送
        // Gateway::sendToAll("$client_id said $message\r\n");
    }

    /**
     * 当用户断开连接时触发
     * @param int $client_id 连接id
     */
    public static function onClose($client_id)
    {
        if (isset($_SESSION['servicer_id'])) {
            // 客服离线，连接断开
            @self::$db->update(self::$prefix . 'servicer')->cols(['online' => 0, 'client_id' => ''])
                ->where("client_id = '$client_id' ")->query();
            $servicer_id = $_SESSION['servicer_id'];
            @self::$db->update(self::$prefix . 'servicer_member')->cols(['online' => 0, 'client_id' => ''])
                ->where("servicer_id = $servicer_id")->query();
        } else {
            // 用户离线，连接断开
            @self::$db->update(self::$prefix . 'servicer_member')->cols(['online' => 0, 'client_id' => ''])
                ->where("client_id = '$client_id' ")->query();
        }
    }
}
