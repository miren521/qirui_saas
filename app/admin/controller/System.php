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

use app\model\system\Menu;
use app\model\system\Addon;
use app\model\system\Database;
use app\model\system\Upgrade;
use extend\database\Database as dbdatabase;
use app\model\web\Config as ConfigModel;
use app\model\system\Upgrade as UpgradeModel;
use app\model\shop\ShopGroup as ShopGroupModel;
use think\facade\Cache;
use think\facade\Db;

/**
 * 首页 控制器
 */
class System extends BaseAdmin
{
    /**
     * 缓存设置
     */
    public function cache()
    {

        if (request()->isAjax()) {
            $type = input("key", '');
            $msg = '缓存更新成功';
            switch ( $type ) {
                case 'all':
                    // 清除缓存
                case 'content':
                    Cache::clear();
                    if ($type == 'content') {
                        $msg = '数据缓存清除成功';
                        break;
                    }
                // 数据表缓存清除
                case 'data_table_cache':
                    if (is_dir('runtime/schema')) {
                        rmdirs("schema");
                    }
                    if ($type == 'data_table_cache') {
                        $msg = '数据表缓存清除成功';
                        break;
                    }
                // 模板缓存清除
                case 'template_cache':
                    if (is_dir('runtime/temp')) {
                        rmdirs("temp");
                    }
                    if ($type == 'template_cache') {
                        $msg = '模板缓存清除成功';
                        break;
                    }
            }
            return success(0, $msg, '');
        } else {
            $config_model = new ConfigModel();
            $cache_list = $config_model->getCacheList();

            $this->assign("cache_list", $cache_list);
            return $this->fetch('system/cache');
        }
    }

    /**
     * 插件管理
     */
    public function addon()
    {
        $addon = new Addon();
        if (request()->isAjax()) {
            $addon_name = input("addon_name");
            $tag = input("tag", "install");
            if ($tag == 'install') {
                $res = $addon->install($addon_name);
                return $res;
            } else {
                $res = $addon->uninstall($addon_name);
                return $res;
            }
        }
        $uninstall = $addon->getUninstallAddonList();
        $install = $addon->getAddonList();
        $this->assign("addons", $install[ 'data' ]);
        $this->assign("uninstall", $uninstall[ 'data' ]);
        return $this->fetch('system/addon');
    }

    /**
     * 数据库管理
     */
    public function database()
    {
        $database = new Database();
        $table = $database->getDatabaseList();
        $this->assign('list', $table);
        $this->forthMenu();
        return $this->fetch('system/database');
    }

    /**
     * 数据库还原页面展示
     */
    public function importlist()
    {
        $database = new Database();

        $path = $database->backup_path;
        if (!is_dir($path)) {
            $mode = intval('0777', 8);
            mkdir($path, $mode, true);
        }

        $flag = \FilesystemIterator::KEY_AS_FILENAME;
        $glob = new \FilesystemIterator($path, $flag);
        $list = array ();

        foreach ($glob as $name => $file) {

            if (preg_match('/^\d{8,8}-\d{6,6}-\d+\.sql(?:\.gz)?$/', $name)) {

                $name = sscanf($name, '%4s%2s%2s-%2s%2s%2s-%d');
                $date = "{$name[0]}-{$name[1]}-{$name[2]}";
                $time = "{$name[3]}:{$name[4]}:{$name[5]}";
                $part = $name[ 6 ];

                if (isset($list[ "{$date} {$time}" ])) {
                    $info = $list[ "{$date} {$time}" ];
                    $info[ 'part' ] = max($info[ 'part' ], $part);
                    $info[ 'size' ] = $info[ 'size' ] + $file->getSize();
                    $info[ 'size' ] = $database->format_bytes($info[ 'size' ]);
                } else {
                    $info[ 'part' ] = $part;
                    $info[ 'size' ] = $file->getSize();
                    $info[ 'size' ] = $database->format_bytes($info[ 'size' ]);
                }

                $info[ 'name' ] = date('Ymd-His', strtotime("{$date} {$time}"));;
                $extension = strtoupper(pathinfo($file->getFilename(), PATHINFO_EXTENSION));
                $info[ 'compress' ] = ( $extension === 'SQL' ) ? '-' : $extension;
                $info[ 'time' ] = strtotime("{$date} {$time}");

                $list[] = $info;
            }
        }

        if (!empty($list)) {
            $list = $database->my_array_multisort($list, "time");
        }
        $this->assign('list', $list);
        $this->forthMenu();
        return $this->fetch('system/importlist');

    }

    /**
     * 还原数据库
     */
    public function importData()
    {

        $time = request()->post('time', '');
        $part = request()->post('part', 0);
        $start = request()->post('start', 0);

        $database = new Database();
        if (is_numeric($time) && ( is_null($part) || empty($part) ) && ( is_null($start) || empty($start) )) { // 初始化
            // 获取备份文件信息
            $name = date('Ymd-His', $time) . '-*.sql*';
            $path = realpath($database->backup_path) . DIRECTORY_SEPARATOR . $name;
            $files = glob($path);
            $list = array ();
            foreach ($files as $name) {
                $basename = basename($name);
                $match = sscanf($basename, '%4s%2s%2s-%2s%2s%2s-%d');
                $gz = preg_match('/^\d{8,8}-\d{6,6}-\d+\.sql.gz$/', $basename);
                $list[ $match[ 6 ] ] = array (
                    $match[ 6 ],
                    $name,
                    $gz
                );
            }
            ksort($list);
            // 检测文件正确性
            $last = end($list);
            if (count($list) === $last[ 0 ]) {
                session('backup_list', $list); // 缓存备份列表
                $return_data = [
                    'code' => 1,
                    'message' => '初始化完成',
                    'data' => [ 'part' => 1, 'start' => 0 ]
                ];
                return $return_data;
            } else {
                $return_data = [
                    'code' => -1,
                    'message' => '备份文件可能已经损坏，请检查！',
                ];
                return $return_data;
            }
        } elseif (is_numeric($part) && is_numeric($start)) {
            $list = session('backup_list');
            $db = new dbdatabase($list[ $part ], array (
                'path' => realpath($database->backup_path) . DIRECTORY_SEPARATOR,
                'compress' => $list[ $part ][ 2 ]
            ));

            $start = $db->import($start);
            if ($start === false) {
                $return_data = [
                    'code' => -1,
                    'message' => '还原数据出错！',
                ];
                return $return_data;
            } elseif ($start === 0) { // 下一卷
                if (isset($list[ ++$part ])) {
                    $data = array (
                        'part' => $part,
                        'start' => 0
                    );
                    $return_data = [
                        'code' => -1,
                        'message' => "正在还原...#{$part}",
                        'data' => $data
                    ];
                    return $return_data;
                } else {
                    session('backup_list', null);
                    $return_data = [
                        'code' => -1,
                        'message' => "还原完成！",
                    ];
                    return $return_data;
                }
            } else {
                $data = array (
                    'part' => $part,
                    'start' => $start[ 0 ]
                );
                if ($start[ 1 ]) {
                    $rate = floor(100 * ( $start[ 0 ] / $start[ 1 ] ));

                    $return_data = [
                        'code' => 1,
                        'message' => "正在还原...#{$part} ({$rate}%)",
                    ];
                    return $return_data;
                } else {
                    $data[ 'gz' ] = 1;
                    $return_data = [
                        'code' => 1,
                        'message' => "正在还原...#{$part}",
                        'data' => $data
                    ];
                    return $return_data;
                }
            }
        } else {
            $return_data = [
                'code' => -1,
                'message' => "参数有误",
            ];
            return $return_data;
        }
    }

    /**
     * 数据表修复
     */
    public function tablerepair()
    {
        if (request()->isAjax()) {
            $table_str = input('tables', '');
            $database = new Database();
            $res = $database->repair($table_str);
            return $res;
        }
    }


    /**
     * 数据表备份
     */
    public function backup()
    {
        $database = new Database();
        $tables = input('tables', []);
        $id = input('id', '');
        $start = input('start', '');

        if (!empty($tables) && is_array($tables)) { // 初始化
            // 读取备份配置
            $config = array (
                'path' => $database->backup_path . DIRECTORY_SEPARATOR,
                'part' => 20971520,
                'compress' => 1,
                'level' => 9
            );
            // 检查是否有正在执行的任务
            $lock = "{$config['path']}backup.lock";
            if (is_file($lock)) {
                return error(-1, '检测到有一个备份任务正在执行，请稍后再试！');
            } else {
                $mode = intval('0777', 8);
                if (!file_exists($config[ 'path' ]) || !is_dir($config[ 'path' ]))
                    mkdir($config[ 'path' ], $mode, true); // 创建锁文件

                file_put_contents($lock, date('Ymd-His', time()));
            }
            // 自动创建备份文件夹
            // 检查备份目录是否可写
            is_writeable($config[ 'path' ]) || exit('backup_not_exist_success');
            session('backup_config', $config);
            // 生成备份文件信息
            $file = array (
                'name' => date('Ymd-His', time()),
                'part' => 1
            );

            session('backup_file', $file);

            // 缓存要备份的表
            session('backup_tables', $tables);

            $dbdatabase = new dbdatabase($file, $config);
            if (false !== $dbdatabase->create()) {

                $data = array ();
                $data[ 'status' ] = 1;
                $data[ 'message' ] = '初始化成功';
                $data[ 'tables' ] = $tables;
                $data[ 'tab' ] = array (
                    'id' => 0,
                    'start' => 0
                );
                return $data;
            } else {
                return error(-1, '初始化失败，备份文件创建失败！');
            }
        } elseif (is_numeric($id) && is_numeric($start)) { // 备份数据
            $tables = session('backup_tables');
            // 备份指定表
            $dbdatabase = new dbdatabase(session('backup_file'), session('backup_config'));
            $start = $dbdatabase->backup($tables[ $id ], $start);
            if (false === $start) { // 出错
                return error(-1, '备份出错！');
            } elseif (0 === $start) { // 下一表
                if (isset($tables[ ++$id ])) {
                    $tab = array (
                        'id' => $id,
                        'table' => $tables[ $id ],
                        'start' => 0
                    );
                    $data = array ();
                    $data[ 'rate' ] = 100;
                    $data[ 'status' ] = 1;
                    $data[ 'message' ] = '备份完成！';
                    $data[ 'tab' ] = $tab;
                    return $data;
                } else { // 备份完成，清空缓存
                    unlink($database->backup_path . DIRECTORY_SEPARATOR . 'backup.lock');
                    session('backup_tables', null);
                    session('backup_file', null);
                    session('backup_config', null);
                    return success(1);
                }
            } else {
                $tab = array (
                    'id' => $id,
                    'table' => $tables[ $id ],
                    'start' => $start[ 0 ]
                );
                $rate = floor(100 * ( $start[ 0 ] / $start[ 1 ] ));
                $data = array ();
                $data[ 'status' ] = 1;
                $data[ 'rate' ] = $rate;
                $data[ 'message' ] = "正在备份...({$rate}%)";
                $data[ 'tab' ] = $tab;
                return $data;
            }
        } else { // 出错
            return error(-1, '参数有误!');
        }
    }

    /**
     * 删除备份文件
     */
    public function deleteData()
    {
        $name_time = input('time', '');
        if ($name_time) {
            $database = new Database();
            $name = date('Ymd-His', $name_time) . '-*.sql*';
            $path = realpath($database->backup_path) . DIRECTORY_SEPARATOR . $name;
            array_map("unlink", glob($path));
            if (count(glob($path))) {
                return error(-1, "备份文件删除失败，请检查权限！");
            } else {
                return success(1, "备份文件删除成功！");
            }
        } else {
            return error(-1, "参数有误！");
        }
    }

    public function auth()
    {
        $this->forthMenu();

        $upgrade_model = new UpgradeModel();
        $auth_info = $upgrade_model->authInfo();
        
        $this->assign('auth_info', $auth_info);

        //系统信息 获取自配置文件
        $app_info = config('info');
        $this->assign('app_info', $app_info);

        return $this->fetch('system/auth');
    }

    /**
     * 系统升级
     */
    public function upgrade()
    {
        if (request()->isAjax()) {
            $upgrade_model = new UpgradeModel();
            $res = $upgrade_model->getUpgradeVersion();
            if (empty($res)) {
                return success(0, '操作失败');
            }
            if ($res[ 'code' ] != 0) return $res;
            $errors = $this->checkSystemUpgradeRight($res[ 'data' ]);
            session('system_upgrade_info_ready', $res[ 'data' ]);
            return success(0, '操作成功', [
                'system_upgrade_info_ready' => $res[ 'data' ],
                'right_check' => $errors,
            ]);
        }
        $this->forthMenu();
        return $this->fetch('system/upgrade');
    }

    /**
     * 检测系统升级权限
     */
    protected function checkSystemUpgradeRight($system_upgrade_info_ready)
    {
        $errors = [];

        //检测下载文件目录权限
        $download_root = "upload/upgrade";
        $download_root = $this->getRealPath($download_root);
        if (!is_writeable($download_root)) {
            $errors[] = [
                'path' => $download_root,
                'type' => 'dir',
                'type_name' => '文件夹',
            ];
        }

        //检测备份文件目录权限
        $backup_root = "upload/backup";
        $backup_root = $this->getRealPath($backup_root);
        if (!is_writeable($backup_root)) {
            $errors[] = [
                'path' => $backup_root,
                'type' => 'dir',
                'type_name' => '文件夹',
            ];
        }

        foreach ($system_upgrade_info_ready as $info) {
            //判断文件夹是否可写
            if ($info[ 'type' ] == 'addon' && $info[ 'action' ] == 'install') {
                $addon_path = "addon";
                if (!is_writeable($addon_path)) {
                    $errors[] = [
                        'path' => $addon_path,
                        'type' => 'dir',
                        'type_name' => '文件夹',
                    ];
                }
            }
            //遍历文件 检测权限
            if (is_array($info[ 'files' ])) {
                foreach ($info[ 'files' ] as $val) {
                    $file_path = '';
                    if ($info[ 'action' ] == 'upgrade') {
                        if ($info[ 'type' ] == 'system') {
                            $file_path = $val[ 'file_path' ];
                        } else {
                            $file_path = "addon/{$val['file_path']}";
                        }
                    }
                    if ($info[ 'action' ] == 'download' && $info[ 'type' ] == 'client') {
                        $file_path = "public/{$val['file_path']}";
                    }
                    if ($file_path) {
                        if (file_exists($file_path)) {
                            if (!is_writeable($file_path)) {
                                $errors[] = [
                                    'path' => $file_path,
                                    'type' => 'file',
                                    'type_name' => '文件',
                                ];
                            }
                        } else {
                            $dir_path = dirname($file_path);
                            $dir_path = $this->getRealPath($dir_path);
                            if (!is_writeable($dir_path)) {
                                $errors[] = [
                                    'path' => $dir_path,
                                    'type' => 'dir',
                                    'type_name' => '文件夹',
                                ];
                            }
                        }
                    }
                }
            }
        }
        return $errors;
    }

    /**
     * 获取真实存在的目录 检测权限使用
     * @param $path
     * @return false|string
     */
    protected function getRealPath($path)
    {
        while (!is_dir($path) && strrpos($path, "/")) {
            $path = substr($path, 0, strrpos($path, "/"));
        }
        return $path;
    }

    /**
     * 升级操作页面
     */
    public function upgradeAction()
    {
        if (request()->isAjax()) {
            $system_upgrade_info_ready = session('system_upgrade_info_ready');
            //将系统和插件的文件及sql都整合到一起
            $files = [];
            $sqls = '';
            $upgrade_no = uniqid();

            //合并文件和sql
            foreach ($system_upgrade_info_ready as $info) {
                foreach ($info[ 'files' ] as $val) {
                    $val[ 'type' ] = $info[ 'type' ];
                    $val[ 'code' ] = $info[ 'code' ];
                    $files[] = $val;
                }
                if (isset($info[ 'sqls' ]) && !empty($info[ 'sqls' ])) {
                    $sqls .= "\n";//防止脚本没有换行导致sql解析完成后将多条sql一起执行,导致出错
                    $sqls .= $info[ 'sqls' ];
                }
            }

            $system_upgrade_info = [
                'files' => $files,
                'sqls' => $sqls,
                'upgrade_no' => $upgrade_no,
            ];

            session('system_upgrade_info', $system_upgrade_info);
            return success(0, '操作成功', session('system_upgrade_info'));
        } else {
            $system_upgrade_info_ready = session('system_upgrade_info_ready');
            if (empty($system_upgrade_info_ready)) {
                $this->error('没有可以升级的内容');
            }
            return $this->fetch('system/upgrade_action');
        }
    }

    /**
     * 升级操作--备份原文件
     * @return array
     */
    public function backupFile()
    {
        $system_upgrade_info = session('system_upgrade_info');
        $upgrade_no = $system_upgrade_info[ 'upgrade_no' ];
        //备份文件的根目录
        $backup_root = "upload/backup/{$upgrade_no}/file";
        if (!is_dir($backup_root)) {
            dir_mkdir($backup_root);
        }
        try {
            if (!empty($system_upgrade_info)) {
                foreach ($system_upgrade_info[ 'files' ] as $k => $v) {
                    $type = $v[ 'type' ];
                    if ($type == 'system') {
                        //如果是系统升级 备份的文件是和根目录比对 下载文件类似 b2c_saas/index.php 要把前面的b2c_saas去掉
                        $file_path = $v[ 'file_path' ];
                    } else {
                        //如果是插件升级 备份的文件是和插件目录比对 下载文件类似 test/index.php 需要补充前缀addon
                        $file_path = "addon/{$v['file_path']}";
                    }
                    if (preg_match('/[\x{4e00}-\x{9fa5}]/u', $file_path) > 0) {
                        $file_path = iconv('utf-8', 'gbk', $file_path);
                    }
                    if (file_exists($file_path)) {
                        $dest_file_path = "{$backup_root}/{$file_path}";
                        $dest_dir_path = substr($dest_file_path, 0, strrpos($dest_file_path, '/'));
                        //要先创建文件夹才可以执行copy操作
                        if (!is_dir($dest_dir_path)) {
                            dir_mkdir($dest_dir_path);
                        }
                        copy($file_path, $dest_file_path);
                    }
                }
                //备份客户端
                $client_type_arr = [ 'web' ];
                foreach ($client_type_arr as $client_type) {
                    $client_path = "{$backup_root}/public/{$client_type}";
                    if (!is_dir($client_path)) {
                        dir_mkdir($client_path);
                    }
                    dir_copy("public/{$client_type}", $client_path);
                }
            }
            return success();
        } catch (\Exception $e) {
            return error(-1, [], $e->getMessage());
        }
    }

    /**
     * 升级操作---备份数据库
     */
    public function backupSql()
    {
        try {
            $system_upgrade_info = session('system_upgrade_info');
            $upgrade_no = $system_upgrade_info[ 'upgrade_no' ];

            $database = new Database();
            ini_set('memory_limit', '500M');
            $size = 300;
            $volumn = 1024 * 1024 * 2;
            $dump = '';
            $last_table = input('last_table', '');
            $series = max(1, input('series', 1));
            if (empty($last_table)) {
                $catch = true;
            } else {
                $catch = false;
            }
            $back_sql_root = "upload/backup/{$upgrade_no}/sql";
            if (!is_dir($back_sql_root)) {
                dir_mkdir($back_sql_root);
            }
            $tables = $database->getDatabaseList();
            if (empty($tables)) {
                return success();
            }
            foreach ($tables as $table) {
                $table = array_shift($table);
                if (!empty($last_table) && $table == $last_table) {
                    $catch = true;
                }
                if (!$catch) {
                    continue;
                }
                if (!empty($dump)) {
                    $dump .= "\n\n";
                }
                if ($table != $last_table) {
                    $row = $database->getTableSchemas($table);
                    $dump .= $row;
                }
                $index = 0;
                if (!empty(input('index'))) {
                    $index = input('index');
                }
                //枚举所有表的INSERT语句
                while (true) {
                    $start = $index * $size;
                    $result = $database->getTableInsertSql($table, $start, $size);
                    if (!empty($result)) {
                        $dump .= $result[ 'data' ];
                        if (strlen($dump) > $volumn) {
                            $bakfile = "{$back_sql_root}/backup-{$series}.sql";
                            $dump .= "\n\n";
                            file_put_contents($bakfile, $dump);
                            ++$series;
                            ++$index;
                            $current = array (
                                'is_backup_end' => 0,
                                'last_table' => $table,
                                'index' => $index,
                                'series' => $series,
                            );
                            $current_series = $series - 1;
                            return success(0, '正在导出数据, 请不要关闭浏览器, 当前第 ' . $current_series . ' 卷.', $current);
                        }
                    }
                    if (empty($result) || count($result[ 'result' ]) < $size) {
                        break;
                    }
                    ++$index;
                }
            }
            $back_file = "{$back_sql_root}/backup-{$series}.sql";
            $dump .= "\n\n----MySQL Dump End";
            file_put_contents($back_file, $dump);
            return success(0, '数据库备份完成', [ 'is_backup_end' => 1 ]);
        } catch (\Exception $e) {
            return error(-1, $e->getMessage());
        }
    }

    /**
     * 升级操作---下载文件
     */
    public function download()
    {
        ini_set("memory_limit", "-1");
        set_time_limit(300);

        $action_type = input('action_type', 'upgrade');
        $system_upgrade_info = session('system_upgrade_info');
        $download_file_index = input('download_file_index', 0);
        $file_path = $system_upgrade_info[ 'files' ][ $download_file_index ][ 'file_path' ];
        $token = $system_upgrade_info[ 'files' ][ $download_file_index ][ 'token' ];
        $type = $system_upgrade_info[ 'files' ][ $download_file_index ][ 'type' ];
        $code = $system_upgrade_info[ 'files' ][ $download_file_index ][ 'code' ];
        $upgrade_no = $system_upgrade_info[ 'upgrade_no' ];

        if ($type == 'system') {
            $download_root = "upload/upgrade/{$upgrade_no}";//框架
        } else if ($type == 'client') {
            $download_root = "upload/upgrade/{$upgrade_no}";//客户端
        } else if ($type == 'addon') {
            $download_root = "upload/upgrade/{$upgrade_no}/addon";//插件
        }

        try {
            $up_model = new UpgradeModel();
            $data = array (
                'file' => $file_path,
                "token" => $token
            );

            $info = $up_model->download($data);//异步下载更新文件

            //下载文件失败
            if ($info[ "code" ] < 0) {
                return json($info);
            }

            $dir_path = dirname($file_path);
            $dir_make = dir_mkdir($download_root . '/' . $dir_path);
            if ($dir_make) {
                if ($action_type == 'download' && $download_file_index == 0 && !empty($system_upgrade_info[ 'sqls' ])) {
                    $sqls = str_replace("{{prefix}}", config("database.connections.mysql.prefix"), $system_upgrade_info[ 'sqls' ]);
                    file_put_contents("upload/upgrade/{$upgrade_no}/upgrade.sql", $sqls);
                }
                if (!empty($info)) {
                    $temp_path = $download_root . '/' . $file_path;
                    if (preg_match('/[\x{4e00}-\x{9fa5}]/u', $temp_path) > 0) {
                        $temp_path = iconv('utf-8', 'gbk', $temp_path);
                    }
                    file_put_contents($temp_path, base64_decode($info[ 'data' ]));
                    return json([ 'code' => 0, 'message' => $file_path, 'download_root' => "upload/upgrade/{$upgrade_no}/" ]);
                } else {
                    return json([ 'code' => -1, 'message' => '升级文件不存在' ]);
                }
            } else {
                return json([ 'code' => -1, 'message' => '文件读写权限不足' ]);
            }
        } catch (\Exception $e) {
            return json([ 'code' => -1, 'message' => $e->getMessage() ]);
        }
    }

    /**
     * 升级操作---更新文件覆盖
     */
    public function executeFile()
    {
        $system_upgrade_info = session('system_upgrade_info');
        $upgrade_no = $system_upgrade_info[ 'upgrade_no' ];
        try {
            //下载目录和要覆盖的目录
            $download_root = "upload/upgrade/{$upgrade_no}";
            $to_path = './';
            //文件替换
            dir_copy($download_root, $to_path);

            return json([ 'code' => 0, 'message' => '操作成功' ]);
        } catch (\Exception $e) {
            //升级失败
            $upgrade_model = new UpgradeModel();
            $upgrade_model->editUpgradeLog([ 'status' => 2, 'error_message' => $e->getMessage() ], [ 'upgrade_no' => $upgrade_no ]);
            return json([ 'code' => -1, 'message' => $e->getMessage() ]);
        }
    }

    /**
     * 更新操作---sql执行
     */
    public function executeSql()
    {
        $system_upgrade_info = session('system_upgrade_info');
        $sqls = $system_upgrade_info[ 'sqls' ];
        $upgrade_no = $system_upgrade_info[ 'upgrade_no' ];

        try {
            if (!empty($sqls)) {
                $sqls = str_replace("{{prefix}}", config("database.connections.mysql.prefix"), $sqls);
                file_put_contents("upload/upgrade/{$upgrade_no}/upgrade.sql", $sqls);

                //执行sql
                $sql_arr = parse_sql($sqls);
                foreach ($sql_arr as $k => $v) {
                    $v = trim($v);
                    if (!empty($v) && $v != "") {
                        Db::execute($v);
                    }
                }
                return json(success());
            } else {
                return json(success());
            }
        } catch (\Exception $e) {
            //升级失败
            $upgrade_model = new UpgradeModel();
            $upgrade_model->editUpgradeLog([ 'status' => 2, 'error_message' => $e->getMessage() ], [ 'upgrade_no' => $upgrade_no ]);
            return json(error(-1, $e->getMessage()));
        }
    }

    /**
     * 升级开始
     * @return array
     */
    public function upgradeStart()
    {
        $system_upgrade_info_ready = session('system_upgrade_info_ready');
        $system_upgrade_info = session('system_upgrade_info');
        $upgrade_no = $system_upgrade_info[ 'upgrade_no' ];

        // 添加升级日志
        $version_info = [];
        foreach ($system_upgrade_info_ready as $key => $val) {
            $version_info[] = [
                'action' => $val[ 'action' ],
                'action_name' => $val[ 'action_name' ],
                'type' => $val[ 'type' ],
                'type_name' => $val[ 'type_name' ],
                'current_version_name' => $val[ 'current_version_name' ],
                'latest_version_name' => $val[ 'latest_version_name' ],
                'scripts' => $val[ 'scripts' ],
                'goods_name' => $val[ 'goods_name' ],
            ];
        }

        $data = [
            'upgrade_no' => $upgrade_no,
            'upgrade_time' => time(),
            'backup_root' => "upload/backup/{$upgrade_no}",
            'download_root' => "upload/download_root/{$upgrade_no}",
            'version_info' => json_encode($version_info),
            'status' => 0
        ];

        $upgrade_model = new UpgradeModel();
        $res = $upgrade_model->addUpgradeLog($data);

        return $res;
    }

    /**
     * 升级完成
     */
    public function upgradeEnd()
    {
        $system_upgrade_info_ready = session('system_upgrade_info_ready');
        $system_upgrade_info = session('system_upgrade_info');
        $upgrade_no = $system_upgrade_info[ 'upgrade_no' ];

        $upgrade_model = new UpgradeModel();
        try {
            //更新系统菜单
            $menu = new Menu();
            $menu->refreshMenu('shop', '');
            $menu->refreshMenu('admin', '');

            //修改插件信息
            $addon_model = new Addon();
            $addon_model->refreshDiyView('');
            if(!empty($system_upgrade_info_ready)){
                foreach ($system_upgrade_info_ready as $key => $val) {
                    if ($val[ 'type' ] == 'addon') {
                        if ($val[ 'action' ] == 'upgrade') {
                            $addon_model->uninstall($val[ 'code' ]);
                            $addon_model->install($val[ 'code' ]);
                        } else {
                            $addon_model->install($val[ 'code' ]);
                        }
                    }
                }
            }
            //升级成功
            $upgrade_model->editUpgradeLog([ 'status' => 1 ], [ 'upgrade_no' => $upgrade_no ]);



            //清空session数据
            session('system_upgrade_info_ready', null);
            session('system_upgrade_info', null);

            return json(success());
        } catch (\Exception $e) {
            //升级失败
            $upgrade_model->editUpgradeLog([ 'status' => 2, 'error_message' => $e->getMessage() ], [ 'upgrade_no' => $upgrade_no ]);
            return json(error(-1, $e->getMessage()));
        }
    }

    /**
     * 执行恢复
     * @return \think\response\Json
     */
    public function executeRecovery()
    {
        $system_upgrade_info_ready = session('system_upgrade_info_ready');
        $system_upgrade_info = session('system_upgrade_info');
        $upgrade_no = $system_upgrade_info[ 'upgrade_no' ];
        try {
            $upgrade_model = new UpgradeModel();
            $log_info = $upgrade_model->getUpgradeLogInfo([ 'upgrade_no' => $upgrade_no ]);
            if (empty($log_info)) {
                return json([ 'code' => -1, '回滚信息有误' ]);
            }
            $backup_file_path = "{$log_info['backup_root']}/file/";
            $backup_sql_path = "{$log_info['backup_root']}/sql/";

            //回滚备份的文件
            if (dir_is_empty($backup_file_path)) {
                return json([ 'code' => -1, '没有可回滚的备份文件!' ]);
            }
            dir_copy($backup_file_path, './');

            //回滚执行的sql语句
            $flag = \FilesystemIterator::KEY_AS_FILENAME;
            $glob = new \FilesystemIterator($backup_sql_path, $flag);
            foreach ($glob as $name => $sql) {
                $sql_path = $backup_sql_path . '/' . $name;
                $sql = file_get_contents($sql_path);
                //执行sql
                $sql_arr = parse_sql($sql);
                foreach ($sql_arr as $k => $v) {
                    $v = trim($v);
                    if (!empty($v) && $v != "") {
                        Db::execute($v);
                    }
                }
            }

            //删除已安装的插件
            foreach ($system_upgrade_info_ready as $val) {
                if ($val[ 'action' ] == 'install' && $val[ 'type' ] == 'addon') {
                    $addon_dir_path = "addon/{$val['code']}";
                    if (is_dir($addon_dir_path)) {
                        deleteDir($addon_dir_path);
                        @rmdir($addon_dir_path);
                    }
                }
            }
            return json([ 'code' => 0, 'message' => '备份回滚成功!' ]);
        } catch (\Exception $e) {
            return json([ 'code' => -1, 'message' => $e->getMessage() ]);
        }
    }

    /**
     * 刷新菜单 测试
     */
    public function refresh()
    {
        $menu = new Menu();
        $res = $menu->refreshMenu('admin', '');
        var_dump($res);
    }

    /**
     * 刷新自定义模板 测试
     */
    public function refreshDiy()
    {
        $addon = new Addon();
        $res = $addon->refreshDiyView('');
        var_dump($res);

    }

}
