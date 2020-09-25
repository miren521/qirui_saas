import http from "../../utils/http"

/**
 * 订单列表
 * @param {object} params
 */
export function apiOrderList(params) {
    return http({
        url: "/api/order/lists",
        data: params,
        forceLogin: true
    })
}

/**
 * 订单支付
 * @param {object} params
 */
export function apiOrderPay(params) {
    return http({
        url: "/api/order/pay",
        data: params,
        forceLogin: true
    })
}

/**
 * 订单关闭
 * @param {object} params
 */
export function apiOrderClose(params) {
    return http({
        url: "/api/order/close",
        data: params,
        forceLogin: true
    })
}

/**
 * 订单收货(收到所有货物)
 * @param {object} params
 */
export function apiOrderTakedelivery(params) {
    return http({
        url: "/api/order/takedelivery",
        data: params,
        forceLogin: true
    })
}

/**
 * 订单详情
 * @param {object} params
 */
export function apiOrderDetail(params) {
    return http({
        url: "/api/order/detail",
        data: params,
        forceLogin: true
    })
}

/**
 * 订单包裹信息
 * @param {object} params
 */
export function apiOrderPackageInfo(params) {
    return http({
        url: "/api/order/package",
        data: params,
        forceLogin: true
    })
}

/**
 * 订单评价获取订单信息
 */
export function orderInfo(params) {
    return http({
        url: "/api/order/evluateinfo",
        data: params,
        forceLogin: true
    })
}

/**
 * 提交评价
 */
export function save(params) {
    var url = ""
    if (params.isEvaluate) {
        url = "/api/goodsevaluate/again"
    } else {
        url = "/api/goodsevaluate/add"
    }

    return http({
        url: url,
        data: params,
        forceLogin: true
    })
}
