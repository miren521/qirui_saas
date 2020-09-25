import http from "../../utils/http"

/**
 * 退款数据
 * @param {object} params
 */
export function refundData(params) {
    return http({
        url: "/api/orderrefund/refundData",
        data: params,
        forceLogin: true
    })
}
/**
 * 退款
 * @param {object} params
 */
export function refund(params) {
    return http({
        url: "/api/orderrefund/refund",
        data: params,
        forceLogin: true
    })
}
/**
 * 退款详情
 * @param {object} params
 */
export function detail(params) {
    return http({
        url: "/api/orderrefund/detail",
        data: params,
        forceLogin: true
    })
}

/**
 * 退货物流
 * @param {object} params
 */
export function delivery(params) {
    return http({
        url: "/api/orderrefund/delivery",
        data: params,
        forceLogin: true
    })
}

/**
 * 撤销维权
 * @param {object} params
 */
export function cancleRefund(params) {
    return http({
        url: "/api/orderrefund/cancel",
        data: params,
        forceLogin: true
    })
}

/**
 * 平台维权数据
 * @param {object} params
 */
export function complainData(params) {
    return http({
        url: "/api/ordercomplain/detail",
        data: params,
        forceLogin: true
    })
}

/**
 * 平台维权申请
 * @param {object} params
 */
export function complain(params) {
    return http({
        url: "/api/ordercomplain/complain",
        data: params,
        forceLogin: true
    })
}

/**
 * 平台维权撤销
 * @param {object} params
 */
export function complainCancel(params) {
    return http({
        url: "/api/ordercomplain/cancel",
        data: params,
        forceLogin: true
    })
}

/**
 * 退款列表
 * @param {object} params
 */
export function refundList(params) {
    return http({
        url: "/api/orderrefund/lists",
        data: params,
        forceLogin: true
    })
}
