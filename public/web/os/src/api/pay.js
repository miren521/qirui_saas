import http from "../utils/http"

/**
 * 支付详情
 * @param {object} params
 */
export function getPayInfo(params) {
    return http({
        url: "/api/pay/info",
        data: params,
        forceLogin: true
    })
}

/**
 * 支付方式
 * @param {object} params
 */
export function getPayType(params) {
    return http({
        url: "/api/pay/type",
        data: params,
        forceLogin: true
    })
}

/**
 * 支付状态
 * @param {object} params
 */
export function checkPayStatus(params) {
    return http({
        url: "/api/pay/status",
        data: params,
        forceLogin: true
    })
}

/**
 * 支付状态
 * @param {object} params
 */
export function pay(params) {
    return http({
        url: "/api/pay/pay",
        data: params,
        forceLogin: true
    })
}
