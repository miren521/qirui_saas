import Vue from "vue"
import VueRouter from "vue-router"
import Lang from "../utils/lang.js"
import NProgress from "nprogress"
import "nprogress/nprogress.css"
import indexRoutes from "./modules/index"
import memberRoutes from "./modules/member"
import shopRoutes from "./modules/shop"
import authRoutes from "./modules/auth"
import { getToken } from "../utils/auth"
import store from "../store"

// 屏蔽跳转到本身时的报错
const originalPush = VueRouter.prototype.push
VueRouter.prototype.push = function push(location) {
    if (location && typeof location == "string" && location.indexOf("http") != -1) return window.location.open(location, "_self")

    return originalPush.call(this, location).catch(err => err)
}

const originalResolve = VueRouter.prototype.resolve
VueRouter.prototype.pushToTab = function pushToTab(location) {
	if(!location) return;
    if (location && typeof location == "string" && location.indexOf("http") != -1) return window.open(location, "_blank")

    const { href } = originalResolve.call(this, location)
    window.open(href, "_blank")
}

Vue.use(VueRouter)

/**
 * meta参数解析
 * module: 所属模块，目前用于寻找语言包
 * module: 菜单所属模块
 * menu: 所属菜单，用于判断三级菜单是否显示高亮，如菜单列表、添加菜单、编辑菜单都是'menu'，用户列表、添加用户、编辑用户都是'user'，如此类推
 */
const mainRouters = [
    indexRoutes,
    authRoutes,
    memberRoutes,
    shopRoutes,
    {
        path: "/closed",
        name: "closed",
        meta: {
            module: "index"
        },
        component: () => import("@/views/index/closed.vue")
    },
    {
        path: "*",
        name: "error",
        meta: {
            module: "index"
        },
        component: () => import("@/views/index/error.vue")
    }
]

const router = new VueRouter({
	mode:'history',
	base : '/web/',
    routes: mainRouters
})

// 路由守卫，控制访问权限
router.beforeEach((to, from, next) => {
    if (store.getters.siteInfo && !store.getters.siteInfo.web_status && to.path != "/closed") {
        return next("/closed")
    }
	
    if (to.meta.auth) {
        const token = getToken()
        if (!token) {
            return next(`/login?redirect=${encodeURIComponent(to.fullPath)}`)
        }
    }

    window.document.body.style.backgroundColor = to.meta.backgroundColor || ""

    NProgress.start()
    next()
})

router.afterEach((to, from) => {
    const title = Lang.getLangField("title", to)
    const metaTitle = store.getters.siteInfo.title || "NiuCloud NiuShop"
    window.document.title = `${title} - ${metaTitle}`
    setTimeout(() => {
        if (document.getElementsByClassName("el-main").length) {
            if (to.meta.mainCss) {
                for (let field in to.meta.mainCss) {
                    document.getElementsByClassName("el-main")[0].style[field] = to.meta.mainCss[field]
                }
            } else {
                document.getElementsByClassName("el-main")[0].style = ""
            }
        }
    }, 100)
    NProgress.done()
})

export default router
