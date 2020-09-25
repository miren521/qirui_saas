import http from "../utils/http"

/**
 * 楼层
 * @param {object} params
 */
export function floors(params) {
    return http({
        url: "/api/pc/floors",
        data: params
    })
}

/**
 * 获取热门搜索关键词
 */
export function apiHotSearchWords(params) {
    return http({
        url: "/api/pc/hotSearchWords",
        data: params
    })
}

/**
 * 获取默认搜索关键词
 */
export function apiDefaultSearchWords(params) {
    return http({
        url: "/api/pc/defaultSearchWords",
        data: params
    })
}
/**
 * 获取首页浮层
 */
export function floatLayer(params) {
    return http({
        url: "/api/pc/floatLayer",
        data: params
    })
}
