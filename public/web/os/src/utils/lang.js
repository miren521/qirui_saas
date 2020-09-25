const langList = ["zh-cn", "en-us"]

var locale = "zh-cn" //设置语言，uni.getStorageSync('lang') ||

export default {
    langList: ["zh-cn", "en-us"],
    /**
     * 解析多语言
     * @param {Object} field 字段
     * @param {Object} route 路由对象
     */
    lang(field, route) {
        let name = "",
            module = ""
        if (route) {
            name = route.name
            module = route.meta.module
            if (route.path == "/" || route.name == "index") {
                name = "index"
                module = "index"
            }
        } else {
            name = vue.$route.name
            module = vue.$route.meta.module
        }

        if (!name || !module) return
		
        var value = ""
        try {
            // 公共语言包
            var lang = require("../lang/" + locale + "/common.js").lang

            // 当前页面语言包
            let currentViewLang = require("../lang/" + locale + "/" + module + "/" + name + ".js").lang

            Object.assign(lang, currentViewLang)

            var arr = field.split(".")
            if (arr.length > 1) {
                for (let i in arr) {
                    var next = parseInt(i) + 1
                    if (next < arr.length) {
                        value = lang[arr[i]][arr[next]]
                    }
                }
            } else {
                value = lang[field]
            }
        } catch (e) {
            value = field
        }

        if (arguments.length > 1) {
            //有参数,需要替换
            for (var i = 1; i < arguments.length; i++) {
                value = value.replace("{" + (i - 1) + "}", arguments[i])
            }
        }

        if (value == undefined || (value == "title" && field == "title")) value = "" // field
        return value
    },
    //切换语言
    change(value) {
        if (!vue) return

        vue.$store.commit("lang", value)
        locale = vue.$store.state.lang //设置语言
        this.refresh()
    },
    //刷新标题
    refresh(route) {
        window.document.title = this.lang("title", route)
    },
    // 获取语言包列表
    list() {
        var list = []
        try {
            //公共语言包
            for (var i = 0; i < langList.length; i++) {
                let item = require("../lang/" + langList[i] + "/common.js").lang
                list.push({
                    name: item.common.name,
                    value: langList[i]
                })
            }
        } catch (e) {
            // "没有找到语言包:", '../../lang/' + locale + '/common.js'
        }
        return list
    },
    // 获取字段语言展示
    getLangField(field, route) {
        return this.lang(field, route) || ""
    }
}
