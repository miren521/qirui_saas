layui.define(["element", "jquery"], function (exports) {
    var element = layui.element,
        $ = layui.$,
        layer = layui.layer;

    var lemoMenu = {

        /**
         * 菜单初始化
         * @param options.menuList   菜单数据信息
         * @param options.multiModule 是否开启多模块
         * @param options.menuChildOpen 是否展开子菜单
         */
        render: function (options) {
            options.menuList = options.menuList || [];
            options.multiModule = options.multiModule || false;
            options.menuChildOpen = options.menuChildOpen || false;
            if (options.multiModule) {
                lemoMenu.renderMultiModule(options.menuList, options.menuChildOpen);
            } else {
                lemoMenu.renderSingleModule(options.menuList, options.menuChildOpen);
            }
            lemoMenu.listen();
        },

        /**
         * 单模块
         * @param menuList 菜单数据
         * @param menuChildOpen 是否默认展开
         */
        renderSingleModule: function (menuList, menuChildOpen) {
            menuList = menuList || [];
            var leftMenuHtml = '',
                childOpenClass = '',
                leftMenuCheckDefault = 'layui-this';

            if (menuChildOpen) childOpenClass = ' layui-nav-itemed';

            leftMenuHtml += '<ul class="layui-nav layui-nav-tree layui-left-nav-tree ' + leftMenuCheckDefault + '" >\n';
            $.each(menuList, function (index, menu) {
                leftMenuHtml += '<li class="layui-nav-item ' + childOpenClass + '">\n';
                if (menu.child != undefined && menu.child != []) {
                    leftMenuHtml += '<a href="javascript:;" class="layui-menu-tips" ><i class="' + menu.icon + '"></i><span class="layui-left-nav"> ' + menu.title + '</span> </a>';
                    var buildChildHtml = function (html, child) {
                        html += '<dl class="layui-nav-child">\n';
                        $.each(child, function (childIndex, childMenu) {
                            html += '<dd class="' + childOpenClass + '">\n';
                            if (childMenu.child != undefined && childMenu.child != []) {
                                html += '<a href="javascript:;" class="layui-menu-tips" ><i class="' + childMenu.icon + '"></i><span class="layui-left-nav"> ' + childMenu.title + '</span></a>';
                                html = buildChildHtml(html, childMenu.child);
                            } else {
                                html += '<a href="javascript:;" class="layui-menu-tips"  lemo-href="' + childMenu.href + '" target="' + childMenu.target + '"><i class="' + childMenu.icon + '"></i><span class="layui-left-nav"> ' + childMenu.title + '</span></a>\n';
                            }
                            html += '</dd>\n';
                        });
                        html += '</dl>\n';
                        return html;
                    };
                    leftMenuHtml = buildChildHtml(leftMenuHtml, menu.child);
                } else {
                    leftMenuHtml += '<a href="javascript:;" class="layui-menu-tips"  lemo-href="' + menu.href + '" target="' + menu.target + '"><i class="' + menu.icon + '"></i><span class="layui-left-nav"> ' + menu.title + '</span></a>\n';
                }
                leftMenuHtml += '</li>\n';
            });
            leftMenuHtml += '</ul>\n';

            $('.lemo-header-menu').remove();
            $('.lemo-menu-left').html(leftMenuHtml);

            element.init();
        },

        /**
         * 多模块
         * @param menuList 菜单数据
         * @param menuChildOpen 是否默认展开
         */
        renderMultiModule: function (menuList, menuChildOpen) {
            menuList = menuList || [];
            var headerMenuHtml = '',
                headerMobileMenuHtml = '',
                leftMenuHtml = '',
                childOpenClass = '',
                headerMenuCheckDefault = 'layui-this',
                leftMenuCheckDefault = 'layui-this';

            if (menuChildOpen) childOpenClass = ' layui-nav-itemed';

            $.each(menuList, function (key, val) {
                key = 'multi_module_' + key;
                headerMenuHtml += '<li class="layui-nav-item ' + headerMenuCheckDefault + '" id="' + key + 'HeaderId" data-menu="' + key + '"> <a href="javascript:;"><i class="' + val.icon + '"></i> ' + val.title + '</a> </li>\n';
                headerMobileMenuHtml += '<dd><a href="javascript:;" id="' + key + 'HeaderId" data-menu="' + key + '"><i class="' + val.icon + '"></i> ' + val.title + '</a></dd>\n';
                leftMenuHtml += '<ul class="layui-nav layui-nav-tree layui-left-nav-tree ' + leftMenuCheckDefault + '" id="' + key + '">\n';
                var menuList = val.child;
                $.each(menuList, function (index, menu) {
                    leftMenuHtml += '<li class="layui-nav-item ' + childOpenClass + '">\n';
                    if (menu.child.length!==0) {
                        leftMenuHtml += '<a href="javascript:;" class="layui-menu-tips" ><i class="' + menu.icon + '"></i><span class="layui-left-nav"> ' + menu.title + '</span> </a>';
                        var buildChildHtml = function (html, child) {
                            html += '<dl class="layui-nav-child">\n';
                            $.each(child, function (childIndex, childMenu) {
                                html += '<dd class="' + childOpenClass + '">\n';
                                if (childMenu.child.length !==0) {
                                    html += '<a href="javascript:;" class="layui-menu-tips" ><i class="' + childMenu.icon + '"></i><span class="layui-left-nav"> ' + childMenu.title + '</span></a>';
                                    html = buildChildHtml(html, childMenu.child);
                                } else {
                                    childMenu.target = childMenu.target?childMenu.target:'_self'
                                    html += '<a href="javascript:;" class="layui-menu-tips"  lemo-href="' + childMenu.href + '" target="' + childMenu.target + '"><i class="' + childMenu.icon + '"></i><span class="layui-left-nav"> ' + childMenu.title + '</span></a>\n';
                                }
                                html += '</dd>\n';
                            });
                            html += '</dl>\n';
                            return html;
                        };
                        leftMenuHtml = buildChildHtml(leftMenuHtml, menu.child);
                    } else {
                        menu.target = menu.target?menu.target:'_self'
                        leftMenuHtml += '<a href="javascript:;" class="layui-menu-tips"  lemo-href="' + menu.href + '" target="' + menu.target + '"><i class="' + menu.icon + '"></i><span class="layui-left-nav"> ' + menu.title + '</span></a>\n';
                    }
                    leftMenuHtml += '</li>\n';
                });
                leftMenuHtml += '</ul>\n';
                headerMenuCheckDefault = '';
                leftMenuCheckDefault = 'layui-hide';
            });
            $('.lemo-menu-header-pc').html(headerMenuHtml); //电脑
            $('.lemo-menu-header-mobile').html(headerMobileMenuHtml); //手机
            $('.lemo-menu-left').html(leftMenuHtml);
            element.init();
        },

        /**
         * 监听
         */
        listen: function () {

            /**
             * 菜单模块切换
             */
            $('body').on('click', '[data-menu]', function () {
                var loading = layer.load(0, {shade: false, time: 2 * 1000});
                var menuId = $(this).attr('data-menu');
                // header
                $(".lemo-header-menu .layui-nav-item.layui-this").removeClass('layui-this');
                $(this).addClass('layui-this');
                // left
                $(".lemo-menu-left .layui-nav.layui-nav-tree.layui-this").addClass('layui-hide');
                $(".lemo-menu-left .layui-nav.layui-nav-tree.layui-this.layui-hide").removeClass('layui-this');
                $("#" + menuId).removeClass('layui-hide');
                $("#" + menuId).addClass('layui-this');
                layer.close(loading);
            });

            /**
             * 菜单缩放
             */
            $('body').on('click', '[data-side-fold],.lemo-site-mobile', function () {
                var loading = layer.load(0, {shade: false, time: 2 * 1000});
                var isShow = $('.lemo-tool [data-side-fold]').attr('data-side-fold');
                if (isShow == 1) { // 缩放
                    $('.lemo-tool [data-side-fold]').attr('data-side-fold', 0);
                    $('.lemo-tool [data-side-fold]').attr('class', 'fa fa-indent');
                    $('.layui-layout-body').attr('class', 'layui-layout-body lemo-mini');
                } else { // 正常
                    $('.lemo-tool [data-side-fold]').attr('data-side-fold', 1);
                    $('.lemo-tool [data-side-fold]').attr('class', 'fa fa-outdent');
                    $('.layui-layout-body').attr('class', 'layui-layout-body lemo-all');
                }
                element.init();
                layer.close(loading);
            });

            /**
             * 手机端点开模块
             */
            $('body').on('click', '.lemo-header-menu.mobile dd', function () {
                var loading = layer.load(0, {shade: false, time: 2 * 1000});
                $('.lemo-tool [data-side-fold]').attr('data-side-fold', 0);
                $('.lemo-tool [data-side-fold]').attr('class', 'fa fa-indent');
                $('.layui-layout-body').attr('class', 'layui-layout-body lemo-mini');
                $('.lemo-logo').trigger("click");
                element.init();
                layer.close(loading);
            });
        },

    };


    exports("lemoMenu", lemoMenu);
});