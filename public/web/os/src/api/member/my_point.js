import http from "../../utils/http"
/**
 * 获取积分基础信息
 */
export function pointInfo(params) {
    return http({
        url: "/api/memberaccount/info",
        data: params,
        forceLogin: true
    })
}
/**
 * 获取积分列表
 */
export function pointList(params) {
    return http({
        url: "/api/memberaccount/page",
        data: params,
        forceLogin: true
    })
}
