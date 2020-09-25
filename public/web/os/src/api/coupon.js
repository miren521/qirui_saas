import http from "../utils/http"

/**
 * 优惠券类型列表
 * @param {object} params
 */
export function couponTypeList(params) {
    return http({
        url: "/coupon/api/coupon/typelists",
        data: params
    })
}

/**
 * 领取优惠券
 * @param {object} params
 */
export function couponReceive(params) {
    return http({
        url: "/coupon/api/coupon/receive",
        data: params,
        forceLogin: true
    })
}

/**
 * 领券中心优惠券列表
 */
export function couponList(params) {
    let url = ""
    if (params.activeName == "first") {
        url = "/coupon/api/coupon/typepagelists"
    } else {
        url = "/platformcoupon/api/platformcoupon/typepagelists"
    }
    return http({
        url: url,
        data: params
    })
}

/**
 * 领取优惠券店铺端
 */
export function receiveCoupon(params) {
    let url = ""
    if (params.activeName == "first") {
        url = "/coupon/api/coupon/receive"
    } else {
        url = "/platformcoupon/api/platformcoupon/receive"
    }
    return http({
        url: url,
        data: params,
        forceLogin: true
    })
}
