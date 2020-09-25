import http from "../../utils/http"
/**
 * 我的店铺收藏
 */
export function shopCollect(params) {
    return http({
        url: "/api/shopmember/membershoppages",
        data: params,
        forceLogin: true
    })
}
/**
 * 我的商品收藏
 */
export function goodsCollect(params) {
    return http({
        url: "/api/goodscollect/page",
        data: params,
        forceLogin: true
    })
}
/**
 * 取消商品收藏
 */
export function deleteGoods(params) {
    return http({
        url: "/api/goodscollect/delete",
        data: params,
        forceLogin: true
    })
}
/**
 * 取消店铺收藏
 */
export function deleteShop(params) {
    return http({
        url: "/api/shopmember/delete",
        data: params,
        forceLogin: true
    })
}