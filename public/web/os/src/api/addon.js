import http from "../utils/http"

/**
 * 插件是否存在
 * @param {object} params
 */
export function addonisexit(params) {
    return http({
        url: "/api/addon/addonisexit",
        data: params
    })
}
