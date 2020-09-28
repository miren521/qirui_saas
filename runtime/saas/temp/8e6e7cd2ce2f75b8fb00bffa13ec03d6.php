<?php /*a:3:{s:63:"E:\mi\company\SaaS\code\back-end\app\saas\view\index\index.html";i:1601084737;s:65:"E:\mi\company\SaaS\code\back-end\app\saas\view\common\header.html";i:1601084077;s:65:"E:\mi\company\SaaS\code\back-end\app\saas\view\common\footer.html";i:1601084092;}*/ ?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title><?php echo config('admin.sys_name'); ?>后台管理</title>
    <meta name="renderer" content="webkit">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    <meta name="apple-mobile-web-app-status-bar-style" content="black">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="format-detection" content="telephone=no">
    <link rel="stylesheet" href="http://saas.com/public/static/plugins/layui/css/layui.css" media="all" />
    <link rel="stylesheet" href="http://saas.com/public/static/admin/css/main.css?v=<?php echo time(); ?>" media="all">
    <link rel="stylesheet" href="http://saas.com/public/static/plugins/font-awesome-4.7.0/css/font-awesome.min.css" media="all">
    <!--[if lt IE 9]>
    <script src="https://cdn.staticfile.org/html5shiv/r29/html5.min.js"></script>
    <script src="https://cdn.staticfile.org/respond.js/1.4.2/respond.min.js"></script>
    <![endif]-->
    <style id="lemo-bg-color">
    </style>
</head>
<link rel="stylesheet" href="http://saas.com/public/static/admin/css/common.css" media="all">
<body class="layui-layout-body lemo-all">
<div class="layui-layout layui-layout-admin">

    <div class="layui-header header">
        <div class="layui-logo lemo-logo"></div>

        <div class="lemo-header-content">
            <a>
                <div class="lemo-tool"><i title="展开" class="fa fa-outdent" data-side-fold="1"></i></div>
            </a>
            <!--电脑端头部菜单-->
            <ul class="layui-nav layui-layout-left lemo-header-menu mobile layui-hide-xs lemo-menu-header-pc">
            </ul>

            <!--手机端头部菜单-->
            <ul class="layui-nav layui-layout-left lemo-header-menu mobile layui-hide-sm">
                <li class="layui-nav-item">
                    <a href="javascript:;"><i class="fa fa-list-ul"></i> 选择模块</a>
                    <dl class="layui-nav-child lemo-menu-header-mobile">
                    </dl>
                </li>
            </ul>

            <ul class="layui-nav layui-layout-right">

                <li class="layui-nav-item" lay-unselect>
                    <a href="javascript:;" data-refresh="刷新" data-href="<?php echo url('clearData'); ?>"><i
                            class="fa fa-refresh"></i></a>
                </li>
                <li class="layui-nav-item" lay-unselect>
                    <a href="javascript:;" data-clear="清理" data-href="<?php echo url('clearData'); ?>" class="lemo-clear"><i
                            class="fa fa-trash-o"></i></a>
                </li>
                <li class="layui-nav-item mobile layui-hide-xs" lay-unselect>
                    <a href="javascript:;" data-check-screen="full"><i class="fa fa-arrows-alt"></i></a>
                </li>
                <li class="layui-nav-item lemo-setting">
                    <a href="javascript:;"><?php echo session('saas.user_info.username'); ?></a>
                    <dl class="layui-nav-child">
                        <dd>
                            <a href="javascript:;" lemo-content-href="<?php echo url('sys.auth/adminEdit'); ?>" data-title="基本资料"
                               data-icon="fa fa-gears">基本资料</a>
                        </dd>
                        <dd>
                            <a href="javascript:;" lemo-content-href="<?php echo url('password'); ?>" data-title="修改密码"
                               data-icon="fa fa-gears">修改密码</a>
                        </dd>
                        <dd>
                            <a href="javascript:;" class="login-out">退出登录</a>
                        </dd>
                    </dl>
                </li>
                <li class="layui-nav-item lemo-setting">
                    <a href="javascript:;"><?php echo lang('lang'); ?></a>
                    <dl class="layui-nav-child">
                        <dd>
                            <a href="javascript:;" class="lang zh" data-icon="fa fa-gears">中文</a>
                        </dd>
                        <dd>
                            <a href="javascript:;" class="lang en" data-icon="fa fa-gears">English</a>
                        </dd>

                    </dl>
                </li>
                <li class="layui-nav-item lemo-select-bgcolor mobile layui-hide-xs" lay-unselect>
                    <a href="javascript:;" data-bgcolor="配色方案"><i class="fa fa-ellipsis-v"></i></a>
                </li>
            </ul>
        </div>
    </div>

    <!--无限极左侧菜单-->
    <div class="layui-side layui-bg-black lemo-menu-left">
    </div>

    <!--遮罩层-->
    <div class="lemo-make"></div>

    <!-- 移动导航 -->
    <div class="lemo-site-mobile"><i class="layui-icon"></i></div>

    <div class="layui-body">

        <div class="lemo-tab layui-tab-rollTool layui-tab" lay-filter="lemoTab" lay-allowclose="true">
            <ul class="layui-tab-title">
                <li class="layui-this" id="lemoHomeTabId" lay-id=""></li>
            </ul>
            <div class="layui-tab-control">
                <li class="lemo-tab-roll-left layui-icon layui-icon-left"></li>
                <li class="lemo-tab-roll-right layui-icon layui-icon-right"></li>
                <li class="layui-tab-tool layui-icon layui-icon-down">
                    <ul class="layui-nav close-box">
                        <li class="layui-nav-item">
                            <a href="javascript:;"><span class="layui-nav-more"></span></a>
                            <dl class="layui-nav-child">
                                <dd><a href="javascript:;" lemo-tab-close="current">关 闭 当 前</a></dd>
                                <dd><a href="javascript:;" lemo-tab-close="other">关 闭 其 他</a></dd>
                                <dd><a href="javascript:;" lemo-tab-close="all">关 闭 全 部</a></dd>
                            </dl>
                        </li>
                    </ul>
                </li>
            </div>
            <div class="layui-tab-content">
                <div id="lemoHomeTabIframe" class="layui-tab-item layui-show"></div>
            </div>
        </div>

    </div>

</div>


<script src="http://saas.com/public/static/plugins/layui/layui.js" charset="utf-8"></script>
<!--<script>-->
<!--    layui.config({-->
<!--        base: "/static/admin/js/",-->
<!--        version: true-->
<!--    }).extend({-->
<!--        Admin: 'Admin'-->
<!--    }).use(['Admin'], function () {-->
<!--        Admin = layui.Admin;-->
<!--    });-->
<!--</script>-->

<script src="http://saas.com/public/static/plugins/lay-config.js?v=1.0.4" charset="utf-8"></script>
<script>

    layui.use(['jquery', 'layer', 'lemoAdmin'], function () {
        var $ = layui.jquery,
            layer = layui.layer,
            lemoAdmin = layui.lemoAdmin;

        var options = {
            iniUrl: "<?php echo url('saas/index/menus'); ?>",    // 初始化接口
            clearUrl: "<?php echo url('clearData'); ?>", // 缓存清理接口
            urlHashLocation: true,      // 是否打开hash定位
            bgColorDefault: 0,          // 主题默认配置
            multiModule: true,          // 是否开启多模块
            menuChildOpen: false,       // 是否默认展开菜单
        };
        lemoAdmin.render(options);

    });
</script>
</body>

