import http from "../utils/http"

/**
 * 店铺列表
 * @param {object} params
 */
export function shopList(params) {
    return http({
        url: "/api/shop/page",
        data: params
    })
}

/**
 * 是否关注店铺
 * @param {object} params
 */
export function shopIsSubscribe(params) {
    return http({
        url: "/api/shopmember/issubscribe",
        data: params,
        forceLogin: true
    })
}

/**
 * 关注店铺
 * @param {object} params
 */
export function addShopSubscribe(params) {
    return http({
        url: "/api/shopmember/add",
        data: params,
        forceLogin: true
    })
}

/**
 * 取消关注店铺
 * @param {object} params
 */
export function deleteShopSubscribe(params) {
    return http({
        url: "/api/shopmember/delete",
        data: params,
        forceLogin: true
    })
}
