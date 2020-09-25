window.rootPath = (function (src) {
    src = document.scripts[document.scripts.length - 1].src;
    return src.substring(0, src.lastIndexOf("/") + 1);
})();
layui.config({
    base: rootPath + "lay-module/",
    version: true
}).extend({
    lemoAdmin: "lemo/lemoAdmin", // layuilemo后台扩展
    lemoMenu: "lemo/lemoMenu", // layuilemo菜单扩展
    lemoTab: "lemo/lemoTab", // layuilemo tab扩展
    lemoTheme: "lemo/lemoTheme", // layuilemo 主题扩展

});