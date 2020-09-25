import Vue from "vue"
import router from "./router"
import store from "./store"
import VueMeta from "vue-meta"
import Element from "element-ui"
import scroll from 'vue-seamless-scroll'

import "element-ui/lib/theme-chalk/index.css"
import "./assets/styles/main.scss"
import "./assets/styles/element-variables.scss"

import VideoPlayer from "vue-video-player"
import "video.js/dist/video-js.css"

import App from "./App.vue"

import Util from "./utils/util"
import Lang from "./utils/lang.js"
import Config from "./utils/config.js"

Vue.prototype.$store = store //挂在vue
Vue.config.productionTip = false

// 常用工具函数
Vue.prototype.$util = Util
Vue.prototype.$img = Util.img
Vue.prototype.$timeStampTurnTime = Util.timeStampTurnTime
Vue.prototype.$copy = Util.copy
window.util = Util
window.img = Util.img
window.timeStampTurnTime = Util.timeStampTurnTime
window.copy = Util.copy

// 语言包
Vue.prototype.$langConfig = Lang //语言包对象
Vue.prototype.$lang = Lang.lang //解析语言包
window.langConfig = Lang
window.lang = Lang.lang

Vue.prototype.$config = Config

Vue.use(VideoPlayer)
Vue.use(VueMeta)
Vue.use(Element)
Vue.use(scroll)

window.vue = new Vue({
    router,
    store,
    metaInfo() {
        return {
            title: store.getters.siteInfo.title || "NiuShop",
            meta: [
                { name: "title", content: store.getters.siteInfo.title || "NiuShop" },
                { name: "desc", content: store.getters.siteInfo.desc || "" },
                { name: "keywords", content: store.getters.siteInfo.keywords || "" }
            ]
        }
    },
    render: h => h(App)
}).$mount("#app")
