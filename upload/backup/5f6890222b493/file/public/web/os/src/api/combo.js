import http from "../utils/http"

/**
 * 获取订单初始化数据
 * @param {object} params
 */
export function payment(params) {
    return http({
        url: "/bundling/api/ordercreate/payment",
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
        url: "/bundling/api/ordercreate/calculate",
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
        url: "/bundling/api/ordercreate/create",
        data: params,
        forceLogin: true
    })
}

/**
 * 获取套餐列表
 * @param {object} params
 */
export function detail(params) {
    return http({
        url: "/bundling/api/bundling/detail",
        data: params
    })
}
