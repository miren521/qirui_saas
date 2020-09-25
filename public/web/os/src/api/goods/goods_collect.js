import http from "../../utils/http"

/**
 * 是否关注该商品
 * @param {object} params
 */
export function isCollect(params) {
    return http({
        url: "/api/goodscollect/iscollect",
        data: params,
        forceLogin: true
    })
}

/**
 * 添加商品关注
 * @param {Object} params
 */
export function addCollect(params) {
    return http({
        url: "/api/goodscollect/add",
        data: params,
        forceLogin: true
    })
}

/**
 * 删除商品关注
 * @param {Object} params
 */
export function deleteCollect(params) {
    return http({
        url: "/api/goodscollect/delete",
        data: params,
        forceLogin: true
    })
}
