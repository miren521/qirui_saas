import http from "../utils/http"

/**
 * 获取订单初始化数据
 * @param {object} params
 */
export function payment(params) {
    return http({
        url: "/groupbuy/api/ordercreate/payment",
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
        url: "/groupbuy/api/ordercreate/calculate",
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
        url: "/groupbuy/api/ordercreate/create",
        data: params,
        forceLogin: true
    })
}

/**
 * 商品列表
 * @param {object} params
 */
export function goodsPage(params) {
    return http({
        url: "/groupbuy/api/goods/page",
        data: params,
        forceLogin: true
    })
}

/**
 * 商品详情
 * @param {object} params
 */
export function goodsSkuDetail(params) {
    return http({
        url: "/groupbuy/api/goods/detail",
        data: params,
        forceLogin: true
    })
}

/**
 * 商品信息
 * @param {object} params
 */
export function goodsSkuInfo(params) {
    return http({
        url: "/groupbuy/api/goods/info",
        data: params,
        forceLogin: true
    })
}
