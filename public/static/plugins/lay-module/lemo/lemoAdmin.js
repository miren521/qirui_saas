layui.define(["jquery", "lemoMenu", "lemoTab", "lemoTheme"], function (exports) {
    var $ = layui.$,
        layer = layui.layer,
        lemoMenu = layui.lemoMenu,
        lemoTheme = layui.lemoTheme,
        lemoTab = layui.lemoTab;

    if (!/http(s*):\/\//.test(location.href)) {
        var tips = "请先将项目部署至web容器（Apache/Tomcat/Nginx/IIS/等），否则部分数据将无法显示";
        return layer.alert(tips);
    }

    var lemoAdmin = {
        /**
         * 后台框架初始化
         * @param options.iniUrl   后台初始化接口地址
         * @param options.clearUrl   后台清理缓存接口
         * @param options.urlHashLocation URL地址hash定位
         * @param options.bgColorDefault 默认皮肤
         * @param options.multiModule 是否开启多模块
         * @param options.menuChildOpen 是否展开子菜单
         */
        render: function (options) {
            options.iniUrl = options.iniUrl || null;
            options.clearUrl = options.clearUrl || null;
            options.urlHashLocation = options.urlHashLocation || false;
            options.bgColorDefault = options.bgColorDefault || 0;
            options.multiModule = options.multiModule || false;
            options.menuChildOpen = options.menuChildOpen || false;
            var loading = layer.load(0, {shade: false, time: 2 * 1000});
            $.getJSON(options.iniUrl, function (data) {
                if (data == null) {
                    lemoAdmin.error('暂无菜单信息')
                } else {

                    lemoAdmin.renderLogo(data.logoInfo);
                    lemoAdmin.renderHome(data.homeInfo);
                    lemoAdmin.listen();
                    lemoMenu.render({
                        menuList: data.menuInfo,
                        multiModule: options.multiModule,
                        menuChildOpen: options.menuChildOpen
                    });
                    lemoTab.render({
                        filter: 'lemoTab',
                        urlHashLocation: options.urlHashLocation,
                        multiModule: options.multiModule,
                        menuChildOpen: options.menuChildOpen,
                        listenSwichCallback: function () {
                            lemoAdmin.renderDevice();
                        }
                    });
                    lemoTheme.render({
                        bgColorDefault: options.bgColorDefault
                    });
                }
            }).fail(function () {
                lemoAdmin.error('菜单接口有误');
            });
            layer.close(loading)
        },

        /**
         * 初始化logo
         * @param data
         */
        renderLogo: function (data) {
            var html = '<a href="' + data.href + '"><img src="' + data.image + '" alt="logo"><h1>' + data.title + '</h1></a>';
            $('.lemo-logo').html(html);
        },

        /**
         * 初始化首页
         * @param data
         */
        renderHome: function (data) {
            sessionStorage.setItem('lemoHomeHref', data.href);
            $('#lemoHomeTabId').html('<span class="lemo-tab-active"></span><span class="disable-close">' + data.title + '</span><i class="layui-icon layui-unselect layui-tab-close">ဆ</i>');
            $('#lemoHomeTabId').attr('lay-id', data.href);
            $('#lemoHomeTabIframe').html('<iframe width="100%" height="100%" frameborder="no" border="0" marginwidth="0" marginheight="0"  src="' + data.href + '"></iframe>');
        },

        /**
         * 进入全屏
         */
        fullScreen: function () {
            var el = document.documentElement;
            var rfs = el.requestFullScreen || el.webkitRequestFullScreen;
            if (typeof rfs != "undefined" && rfs) {
                rfs.call(el);
            } else if (typeof window.ActiveXObject != "undefined") {
                var wscript = new ActiveXObject("WScript.Shell");
                if (wscript != null) {
                    wscript.SendKeys("{F11}");
                }
            } else if (el.msRequestFullscreen) {
                el.msRequestFullscreen();
            } else if (el.oRequestFullscreen) {
                el.oRequestFullscreen();
            } else {
                lemoAdmin.error('浏览器不支持全屏调用！');
            }
        },

        /**
         * 退出全屏
         */
        exitFullScreen: function () {
            var el = document;
            var cfs = el.cancelFullScreen || el.webkitCancelFullScreen || el.exitFullScreen;
            if (typeof cfs != "undefined" && cfs) {
                cfs.call(el);
            } else if (typeof window.ActiveXObject != "undefined") {
                var wscript = new ActiveXObject("WScript.Shell");
                if (wscript != null) {
                    wscript.SendKeys("{F11}");
                }
            } else if (el.msExitFullscreen) {
                el.msExitFullscreen();
            } else if (el.oRequestFullscreen) {
                el.oCancelFullScreen();
            } else {
                lemoAdmin.error('浏览器不支持全屏调用！');
            }
        },

        /**
         * 初始化设备端
         */
        renderDevice: function () {
            if (lemoAdmin.checkMobile()) {
                $('.lemo-tool i').attr('data-side-fold', 1);
                $('.lemo-tool i').attr('class', 'fa fa-outdent');
                $('.layui-layout-body').attr('class', 'layui-layout-body lemo-all');
            }
        },

        /**
         * 成功
         * @param title
         * @returns {*}
         */
        success: function (title) {
            return layer.msg(title, {icon: 1, shade: this.shade, scrollbar: false, time: 2000, shadeClose: true});
        },

        /**
         * 失败
         * @param title
         * @returns {*}
         */
        error: function (title) {
            return layer.msg(title, {icon: 2, shade: this.shade, scrollbar: false, time: 3000, shadeClose: true});
        },

        /**
         * 判断是否为手机
         * @returns {boolean}
         */
        checkMobile: function () {
            var ua = navigator.userAgent.toLocaleLowerCase();
            var pf = navigator.platform.toLocaleLowerCase();
            var isAndroid = (/android/i).test(ua) || ((/iPhone|iPod|iPad/i).test(ua) && (/linux/i).test(pf))
                || (/ucweb.*linux/i.test(ua));
            var isIOS = (/iPhone|iPod|iPad/i).test(ua) && !isAndroid;
            var isWinPhone = (/Windows Phone|ZuneWP7/i).test(ua);
            var clientWidth = document.documentElement.clientWidth;
            if (!isAndroid && !isIOS && !isWinPhone && clientWidth > 768) {
                return false;
            } else {
                return true;
            }
        },

        /**
         * 监听
         */
        listen: function () {

            /**
             * 清理
             */
            $('body').on('click', '[data-clear]', function () {
                var loading = layer.load(0, {shade: false, time: 2 * 1000});
                sessionStorage.clear();
                // 判断是否清理服务端
                var clearUrl = $(this).attr('data-href');
                if (clearUrl != undefined && clearUrl != '' && clearUrl != null) {
                    $.getJSON(clearUrl, function (data, status) {
                        layer.close(loading);
                        if (data.code < 1) {
                            return lemoAdmin.error(data.msg);
                        } else {
                            return lemoAdmin.success(data.msg);
                        }
                    }).fail(function () {
                        layer.close(loading);
                        return lemoAdmin.error('清理缓存接口有误');
                    });
                } else {
                    layer.close(loading);
                    return lemoAdmin.success('清除缓存成功');
                }
            });

            /**
             * 刷新
             */
            $('body').on('click', '[data-refresh]', function () {
                var clearUrl = $(this).attr('data-href');
                $.post(clearUrl, function (res) {
                    if (res.code == 0) {
                        lemoAdmin.error('刷新失败');
                    } else {
                        $(".layui-tab-item.layui-show").find("iframe")[0].contentWindow.location.reload();
                        lemoAdmin.success('刷新成功');


                    }
                }).fail(function () {
                    lemoAdmin.error('刷新失败');
                });


            });
            /**
             * 监听提示信息
             */
            $("body").on("mouseenter", ".layui-menu-tips", function () {
                if (lemoAdmin.checkMobile()) {
                    return false;
                }
                var classInfo = $(this).attr('class'),
                    tips = $(this).children('span').text(),
                    isShow = $('.lemo-tool i').attr('data-side-fold');
                if (isShow == 0) {
                    openTips = layer.tips(tips, $(this), {tips: [2, '#2f4056'], time: 30000});
                }
            });
            $("body").on("mouseleave", ".layui-menu-tips", function () {
                if (lemoAdmin.checkMobile()) {
                    return false;
                }
                var isShow = $('.lemo-tool i').attr('data-side-fold');
                if (isShow == 0) {
                    try {
                        layer.close(openTips);
                    } catch (e) {
                        // console.log(e.message);
                    }
                }
            });
            /**
             * 全屏
             */
            $('body').on('click', '[data-check-screen]', function () {
                var check = $(this).attr('data-check-screen');
                if (check == 'full') {
                    lemoAdmin.fullScreen();
                    $(this).attr('data-check-screen', 'exit');
                    $(this).html('<i class="fa fa-compress"></i>');
                } else {
                    lemoAdmin.exitFullScreen();
                    $(this).attr('data-check-screen', 'full');
                    $(this).html('<i class="fa fa-arrows-alt"></i>');
                }
            });


            /**
             * 点击遮罩层
             */
            $('body').on('click', '.lemo-make', function () {
                lemoAdmin.renderDevice();
            });
            // 语言切换
            $('body').on('click', '.lang',function () {
                var url = '/index.php/admin/system/enlang';
                var lang = 'zh-cn';
                if($(this).hasClass('en')){
                    lang = 'en-us';
                }
                $.get(url,{lang:lang}, function (res) {
                    if (res.code <= 0) {
                        lemoAdmin.error(res.msg);
                    } else {
                        layer.msg(res.msg, function () {
                            location.reload();
                        });

                    }
                }).fail(function () {
                    lemoAdmin.error('菜单接口有误');
                });

            });
            // 退出
            $('body').on('click', '.login-out', function () {
                var url = '/index.php/admin/index/logout';
                $.post(url, function (res) {
                    if (res.code == 0) {
                        lemoAdmin.error(res.msg);
                    } else {
                        layer.msg(res.msg, function () {
                            window.location = '/index.php/admin/login/index';
                        });
                    }
                }).fail(function () {
                    lemoAdmin.error('退出失败');
                });

            });

        }
    };


    exports("lemoAdmin", lemoAdmin);
});