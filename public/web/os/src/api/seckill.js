import http from "../utils/http"

/**
 * 获取订单初始化数据
 * @param {object} params
 */
export function payment(params) {
    return http({
        url: "/seckill/api/ordercreate/payment",
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
        url: "/seckill/api/ordercreate/calculate",
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
        url: "/seckill/api/ordercreate/create",
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
        url: "/seckill/api/seckillgoods/page",
        data: params
    })
}

/**
 * 商品详情
 * @param {object} params
 */
export function goodsSkuDetail(params) {
    return http({
        url: "/seckill/api/seckillgoods/detail",
        data: params
    })
}

/**
 * 秒杀时间段
 * @param {object} params
 */
export function timeList(params) {
    return http({
        url: "/seckill/api/seckill/lists",
        data: params
    })
}
