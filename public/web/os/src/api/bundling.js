import http from "../utils/http"

/**
 * 组合套餐列表
 * @param {object} params
 */
export function bundlingList(params) {
    return http({
        url: "/bundling/api/bundling/lists",
        data: params
    })
}
