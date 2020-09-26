layui.define(["element", "jquery"], function (exports) {
    var element = layui.element,
        $ = layui.$;


    var lemoTab = {

        /**
         * 初始化tab
         * @param options
         */
        render: function (options) {
            options.filter = options.filter || null;
            options.multiModule = options.multiModule || false;
            options.urlHashLocation = options.urlHashLocation || false;
            options.listenSwichCallback = options.listenSwichCallback || function () {
            };
            lemoTab.listen(options);
            lemoTab.listenRoll();
            lemoTab.listenSwitch(options);
            lemoTab.listenHash(options.urlHashLocation);
        },

        /**
         * 新建tab窗口
         * @param tabId
         * @param href
         * @param title
         * @param addSession
         * @param isIframe
         */
        create: function (tabId, href, title, addSession, isIframe) {
            isIframe = isIframe || false;
            if (addSession === undefined || addSession === true) {
                var lemoTabInfo = JSON.parse(sessionStorage.getItem("lemoTabInfo"));
                if (lemoTabInfo == null) lemoTabInfo = {};
                lemoTabInfo[tabId] = {href: href, title: title};
                sessionStorage.setItem("lemoTabInfo", JSON.stringify(lemoTabInfo));
            }
            var ele = element;
            if (isIframe) ele = parent.layui.element;
            ele.tabAdd('lemoTab', {
                title: '<span class="lemo-tab-active"></span><span>' + title + '</span><i class="layui-icon layui-unselect layui-tab-close">ဆ</i>' //用于演示
                , content: '<iframe width="100%" height="100%" frameborder="no" border="0" marginwidth="0" marginheight="0"   src="' + href + '"></iframe>'
                , id: tabId
            });
        },

        /**
         * 刷新tab窗口
         */
        refresh: function () {

        },

        /**
         * 切换选项卡
         * @param tabId
         */
        change: function (tabId) {
            element.tabChange('lemoTab', tabId);
        },

        /**
         * 删除tab窗口
         * @param tabId
         * @param isParent
         */
        delete: function (tabId, isParent) {
            var lemoTabInfo = JSON.parse(sessionStorage.getItem("lemoTabInfo"));
            if (lemoTabInfo != null) {
                delete lemoTabInfo[tabId];
                sessionStorage.setItem("lemoTabInfo", JSON.stringify(lemoTabInfo))
            }

            // todo 未知BUG，不知道是不是layui问题，必须先删除元素
            $(".lemo-tab .layui-tab-title .layui-unselect.layui-tab-bar").remove();

            if (isParent === true) {
                parent.layui.element.tabDelete('lemoTab', tabId);
            } else {
                element.tabDelete('lemoTab', tabId);
            }
        },

        /**
         * 在iframe层关闭当前tab方法
         */
        deleteCurrentByIframe: function () {
            var ele = $(".lemo-tab .layui-tab-title li.layui-this", parent.document);
            if (ele.length > 0) {
                var layId = $(ele[0]).attr('lay-id');
                lemoTab.delete(layId, true);
            }
        },

        /**
         * 判断tab窗口
         */
        check: function (tabId, isIframe) {
            // 判断选项卡上是否有
            var checkTab = false;
            if (isIframe === undefined || isIframe === false) {
                $(".layui-tab-title li").each(function () {
                    var checkTabId = $(this).attr('lay-id');
                    if (checkTabId != null && checkTabId === tabId) {
                        checkTab = true;
                    }
                });
            } else {
                parent.layui.$(".layui-tab-title li").each(function () {
                    var checkTabId = $(this).attr('lay-id');
                    if (checkTabId != null && checkTabId === tabId) {
                        checkTab = true;
                    }
                });
            }
            if (checkTab === false) {
                return false;
            }

            // 判断sessionStorage是否有
            var lemoTabInfo = JSON.parse(sessionStorage.getItem("lemoTabInfo"));
            if (lemoTabInfo == null) {
                lemoTabInfo = {};
            }
            var check = lemoTabInfo[tabId];
            if (check === undefined || check === null) {
                return false;
            }
            return true;
        },

        /**
         * 监听
         * @param options
         */
        listen: function (options) {

            /**
             * 打开新窗口
             */
            $('body').on('click', '[lemo-href]', function () {
                var loading = layer.load(0, {shade: false, time: 2 * 1000});
                var tabId = $(this).attr('lemo-href'),
                    href = $(this).attr('lemo-href'),
                    title = $(this).text(),
                    target = $(this).attr('target');
                if (target === '_blank') {
                    layer.close(loading);
                    window.open(href, "_blank");
                    return false;
                }
                if (tabId === null || tabId === undefined) tabId = new Date().getTime();
                var checkTab = lemoTab.check(tabId);
                if (!checkTab) lemoTab.create(tabId, href, title, true);
                element.tabChange('lemoTab', tabId);
                layer.close(loading);
            });

            /**
             * 在iframe子菜单上打开新窗口
             */
            $('body').on('click', '[lemo-content-href]', function () {
                console.log('点击内容');
                var loading = parent.layer.load(0, {shade: false, time: 2 * 1000});
                var tabId = $(this).attr('lemo-content-href'),
                    href = $(this).attr('lemo-content-href'),
                    title = $(this).attr('data-title'),
                    target = $(this).attr('target');
                if (target === '_blank') {
                    parent.layer.close(loading);
                    window.open(href, "_blank");
                    return false;
                }
                if (tabId === null || tabId === undefined) tabId = new Date().getTime();
                var checkTab = lemoTab.check(tabId, true);
                if (!checkTab) lemoTab.create(tabId, href, title, true, true);
                parent.layui.element.tabChange('lemoTab', tabId);
                parent.layer.close(loading);
            });

            /**
             * 关闭选项卡
             **/
            $('body').on('click', '.lemo-tab .layui-tab-title .layui-tab-close', function () {
                var loading = layer.load(0, {shade: false, time: 2 * 1000});
                var $parent = $(this).parent();
                var tabId = $parent.attr('lay-id');
                if (tabId !== undefined || tabId !== null) {
                    lemoTab.delete(tabId);
                }
                layer.close(loading);
            });

            /**
             * 选项卡操作
             */
            $('body').on('click', '[lemo-tab-close]', function () {
                var loading = layer.load(0, {shade: false, time: 2 * 1000});
                var closeType = $(this).attr('lemo-tab-close');
                $(".lemo-tab .layui-tab-title li").each(function () {
                    var tabId = $(this).attr('lay-id');
                    var id = $(this).attr('id');
                    var isCurrent = $(this).hasClass('layui-this');
                    if (id !== 'lemoHomeTabId') {
                        if (closeType === 'all') {
                            lemoTab.delete(tabId);
                        } else {
                            if (closeType === 'current' && isCurrent) {
                                lemoTab.delete(tabId);
                            } else if (closeType === 'other' && !isCurrent) {
                                lemoTab.delete(tabId);
                            }
                        }
                    }
                });
                layer.close(loading);
            });


        },

        /**
         * 监听tab切换
         * @param options
         */
        listenSwitch: function (options) {
            options.filter = options.filter || null;
            options.multiModule = options.multiModule || false;
            options.urlHashLocation = options.urlHashLocation || false;
            options.listenSwichCallback = options.listenSwichCallback || function () {

            };
            element.on('tab(' + options.filter + ')', function (data) {
                var tabId = $(this).attr('lay-id');
                if (options.urlHashLocation) {
                    location.hash = '/' + tabId;
                }
                if (typeof options.listenSwichCallback === 'function') {
                    options.listenSwichCallback();
                }
                $("[lemo-href]").parent().removeClass('layui-this');
                if (options.multiModule) {
                    lemoTab.listenSwitchMultiModule(tabId);
                } else {
                    lemoTab.listenSwitchSingleModule(tabId);
                }
                lemoTab.rollPosition();
            });
        },

        /**
         * 监听hash变化
         * @param urlHashLocation
         * @returns {boolean}
         */
        listenHash: function (urlHashLocation) {
            urlHashLocation = urlHashLocation || false;
            if (!urlHashLocation) return false;
            var tabId = location.hash.replace(/^#\//, '');
            if (tabId === null || tabId === undefined) return false;
            $("[lemo-href]").each(function () {
                if ($(this).attr("lemo-href") === tabId) {
                    var title = $(this).text();
                    lemoTab.create(tabId, tabId, title, true);
                    element.tabChange('lemoTab', tabId);
                    return false;
                }
            });
        },

        /**
         * 监听滚动
         */
        listenRoll: function () {
            $(".lemo-tab-roll-left").click(function () {
                lemoTab.rollClick("left");
            });
            $(".lemo-tab-roll-right").click(function () {
                lemoTab.rollClick("right");
            });
        },

        /**
         * 单模块切换
         * @param tabId
         */
        listenSwitchSingleModule: function (tabId) {
            $("[lemo-href]").each(function () {
                if ($(this).attr("lemo-href") === tabId) {
                    // 自动展开菜单栏
                    var addMenuClass = function ($element, type) {
                        if (type === 1) {
                            $element.addClass('layui-this');
                            if ($element.hasClass('layui-nav-item') && $element.hasClass('layui-this') === false) {
                                addMenuClass($element.parent().parent(), 2);
                            }
                        } else {
                            $element.addClass('layui-nav-itemed');
                            if ($element.hasClass('layui-nav-item') && $element.hasClass('layui-nav-itemed') === false) {
                                addMenuClass($element.parent().parent(), 2);
                            }
                        }
                    };
                    addMenuClass($(this).parent(), 1);
                    return false;
                }
            });
        },

        /**
         * 多模块切换
         * @param tabId
         */
        listenSwitchMultiModule: function (tabId) {
            $("[lemo-href]").each(function () {
                if ($(this).attr("lemo-href") === tabId) {

                    // 自动展开菜单栏
                    var addMenuClass = function ($element, type) {
                        if (type === 1) {
                            $element.addClass('layui-this');
                            if ($element.hasClass('layui-nav-item') && $element.hasClass('layui-this')) {
                                var moduleId = $element.parent().attr('id');
                                $(".lemo-header-menu li").attr('class', 'layui-nav-item');
                                $("#" + moduleId + "HeaderId").addClass("layui-this");
                                $(".lemo-menu-left .layui-nav.layui-nav-tree").attr('class', 'layui-nav layui-nav-tree layui-hide');
                                $("#" + moduleId).attr('class', 'layui-nav layui-nav-tree layui-this');
                            } else {
                                addMenuClass($element.parent().parent(), 2);
                            }
                        } else {
                            $element.addClass('layui-nav-itemed');
                            if ($element.hasClass('layui-nav-item') && $element.hasClass('layui-nav-itemed')) {
                                var moduleId = $element.parent().attr('id');
                                $(".lemo-header-menu li").attr('class', 'layui-nav-item');
                                $("#" + moduleId + "HeaderId").addClass("layui-this");
                                $(".lemo-menu-left .layui-nav.layui-nav-tree").attr('class', 'layui-nav layui-nav-tree layui-hide');
                                $("#" + moduleId).attr('class', 'layui-nav layui-nav-tree layui-this');
                            } else {
                                addMenuClass($element.parent().parent(), 2);
                            }
                        }
                    };
                    addMenuClass($(this).parent(), 1);
                    return false;
                }
            });
        },

        /**
         * 自动定位
         */
        rollPosition: function () {
            var $tabTitle = $('.lemo-tab  .layui-tab-title');
            var autoLeft = 0;
            $tabTitle.children("li").each(function () {
                if ($(this).hasClass('layui-this')) {
                    return false;
                } else {
                    autoLeft += $(this).outerWidth();
                }
            });
            $tabTitle.animate({
                scrollLeft: autoLeft - $tabTitle.width() / 3
            }, 200);
        },

        /**
         * 点击滚动
         * @param direction
         */
        rollClick: function (direction) {
            var $tabTitle = $('.lemo-tab  .layui-tab-title');
            var left = $tabTitle.scrollLeft();
            if ('left' === direction) {
                $tabTitle.animate({
                    scrollLeft: left - 450
                }, 200);
            } else {
                $tabTitle.animate({
                    scrollLeft: left + 450
                }, 200);
            }
        }

    };

    exports("lemoTab", lemoTab);
});