import http from "../../utils/http"

/**
 * 获取帮助列表
 */
export function helpList(params) {
    return http({
        url: "/api/helpclass/lists",
        data: params
    })
}
/**
 * 获取帮助详情
 */
export function helpDetail(params) {
    return http({
        url: "/api/help/info",
        data: params
    })
}
/**
 * 获取帮助详情
 */
export function helpOther(params) {
    return http({
        url: "/api/help/page",
        data: params
    })
}
