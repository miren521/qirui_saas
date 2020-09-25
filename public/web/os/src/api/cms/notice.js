import http from "../../utils/http"

/**
 * 获取公告列表
 */
export function noticesList(params) {
    return http({
        url: "/api/notice/page",
        data: params
    })
}
/**
 * 获取公告详情
 */
export function noticeDetail(params) {
    return http({
        url: "/api/notice/info",
        data: params
    })
}
