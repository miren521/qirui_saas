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

namespace app\event;

use think\facade\Config;
/**
 * 初始化配置信息
 * @author Administrator
 *
 */
class InitConfig
{
    public function handle()
    {
        // 初始化常量
        $this->initConst();
        //初始化配置信息
        $this->initConfig();
       
       
    }

    /**
     * 初始化常量
     */
    private function initConst()
    {
        //加载版本信息
           include_once 'app/version.php';
        //加载基础化配置信息
        define('__ROOT__', str_replace([ '/index.php', '/install.php' ], '', request()->root(true)));
      
        define('__PUBLIC__', __ROOT__ . '/public');
        define('__UPLOAD__', 'upload');
     
        //插件目录名称
        define('ADDON_DIR_NAME', 'addon');
        //插件目录路径
        define('ADDON_PATH', 'addon/');
        //分页每页数量
        define('PAGE_LIST_ROWS', 10);
        //伪静态模式是否开启
        define('REWRITE_MODULE', true);

        // public目录绝对路径
        define('PUBLIC_PATH', dirname(dirname(dirname(__FILE__))) . '/public/');
        
        //兼容模式访问
        if (!REWRITE_MODULE) {
            define('ROOT_URL', request()->root(true) . '/?s=');
        }else{
            define('ROOT_URL', request()->root(true));
        }
        //检测网址访问
        $url = request()->url(true);
        if(strstr($url, 'call_user_func_array')|| strstr($url, 'invokefunction'))
        {
            die("非法请求");
        }
    
    }
    
    /**
     * 初始化配置信息
     */
    private function initConfig()
    {
        $view_array = [
            // 模板引擎类型使用Think
            'type'          => 'Think',
            // 默认模板渲染规则 1 解析为小写+下划线 2 全部转换小写 3 保持操作方法
            'auto_rule'     => 1,
            // 模板目录名
            'view_dir_name' => 'view',
            // 模板后缀
            'view_suffix'   => 'html',
            // 模板文件名分隔符
            'view_depr'     => DIRECTORY_SEPARATOR,
            // 模板引擎普通标签开始标记
            'tpl_begin'     => '{',
            // 模板引擎普通标签结束标记
            'tpl_end'       => '}',
            // 标签库标签开始标记
            'taglib_begin'  => '{',
            // 标签库标签结束标记
            'taglib_end'    => '}',
            'tpl_cache' => false,           //模板缓存，部署模式后改为true
            'tpl_replace_string' => [
                '__ROOT__' => __ROOT__,
                'ROOT_URL' => ROOT_URL,
                '__STATIC__' => __PUBLIC__ . '/static',
                'STATIC_EXT' => __PUBLIC__ . '/static/ext',
                'STATIC_CSS' => __PUBLIC__ . '/static/css',
                'STATIC_JS' => __PUBLIC__ . '/static/js',
                'STATIC_IMG' => __PUBLIC__ . '/static/img',
                'ADMIN_IMG' => __ROOT__ . '/app/admin/view/public/img',
                'ADMIN_CSS' => __ROOT__ . '/app/admin/view/public/css',
                'ADMIN_JS' => __ROOT__ . '/app/admin/view/public/js',
                'SHOP_IMG' => __ROOT__ . '/app/shop/view/public/img',
                'SHOP_CSS' => __ROOT__ . '/app/shop/view/public/css',
                'SHOP_JS' => __ROOT__ . '/app/shop/view/public/js',
                '__UPLOAD__' => __UPLOAD__,
            ]
        ];
        Config::set($view_array, 'view');

    }
    
    
}
