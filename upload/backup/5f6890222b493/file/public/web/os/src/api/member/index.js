import http from "../../utils/http"
/**
 * 获取会员基础信息
 */
export function memberInfo(params) {
    return http({
        url: "/api/member/info",
        data: params,
        forceLogin: true
    })
}

/**
 * 订单数量
 */
export function orderNum(params) {
    return http({
        url: "/api/order/num",
        data: params,
        forceLogin: true
    })
}

/**
 * 优惠券数量
 */
export function couponNum(params) {
    return http({
        url: "/coupon/api/coupon/num",
        data: params,
        forceLogin: true
    })
}

/**
 * 我的足迹
 */
export function footprint(params) {
    return http({
        url: "/api/goodsbrowse/page",
        data: params,
        forceLogin: true
    })
}
/**
 * 会员等级列表
 */
export function levelList(params) {
    return http({
        url: "/api/memberlevel/lists",
        data: params,
        forceLogin: true
    })
}