import http from "../../utils/http"

/**
 * 获取订单初始化数据
 * @param {object} params
 */
export function payment(params) {
    return http({
        url: "/api/ordercreate/payment",
        data: params,
        forceLogin: true
    })
}

/**
 * 获取订单初始化数据
 * @param {object} params
 */
export function calculate(params) {
    return http({
        url: "/api/ordercreate/calculate",
        data: params,
        forceLogin: true
    })
}

/**
 * 订单创建
 * @param {object} params
 */
export function orderCreate(params) {
    return http({
        url: "/api/ordercreate/create",
        data: params,
        forceLogin: true
    })
}

/**
 * 验证支付密码
 * @param {object} params
 */
export function checkpaypassword(params) {
    return http({
        url: "/api/member/checkpaypassword",
        data: params,
        forceLogin: true
    })
}
