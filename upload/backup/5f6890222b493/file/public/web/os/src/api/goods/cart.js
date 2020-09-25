import http from "../../utils/http"

/**
 * 加入购物车
 * @param {array} params
 */
export function addToCart(params) {
    return http({
        data: params,
        url: "/api/cart/add",
        forceLogin: true
    })
}

/**
 * 购物车列表
 * @param {array} params
 */
export function cartList(params) {
    return http({
        data: params,
        url: "/api/cart/lists"
    })
}

/**
 * 删除购物车
 * @param {array} params
 */
export function deleteCart(params) {
    return http({
        data: params,
        url: "/api/cart/delete",
        forceLogin: true
    })
}

/**
 * 修改购物车数量
 * @param {array} params
 */
export function editCartNum(params) {
    return http({
        data: params,
        url: "/api/cart/edit",
        forceLogin: true
    })
}

/**
 * 获取购物车数量
 * @param {array} params
 */
export function getCartCount(params) {
    return http({
        data: params,
        url: "/api/cart/count"
    })
}
