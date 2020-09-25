import http from "../../utils/http"

/**
 * 核销权限
 * @param {object} params
 */
export function checkisverifier(params) {
    return http({
        url: "/api/verify/checkisverifier",
        data: params,
        forceLogin: true
    })
}

/**
 * 核销
 * @param {object} params
 */
export function verifyInfo(params) {
    return http({
        url: "/api/verify/verifyInfo",
        data: params,
        forceLogin: true
    })
}

/**
 * 核销验证
 * @param {object} params
 */
export function verify(params) {
    return http({
        url: "/api/verify/verify",
        data: params,
        forceLogin: true
    })
}

/**
 * 核销类型
 * @param {object} params
 */
export function getVerifyType(params) {
    return http({
        url: "/api/verify/getVerifyType",
        data: params
    })
}

/**
 * 核销记录
 * @param {object} params
 */
export function verifyList(params) {
    return http({
        url: "/api/verify/lists",
        data: params,
        forceLogin: true
    })
}
