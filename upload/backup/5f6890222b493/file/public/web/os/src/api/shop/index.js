import http from "../../utils/http"
/**
 * 获取店铺基础信息
 */
export function shopInfo(params) {
    return http({
        url: "/api/shop/info",
        data: params
    })
}
/**
 * 获取店铺是否被关注
 */
export function isFollow(params) {
    return http({
        url: "/api/shopmember/issubscribe",
        data: params
    })
}
/**
 * 取消关注
 */
export function unFollow(params) {
    return http({
        url: "/api/shopmember/delete",
        data: params,
        forceLogin: true
    })
}
/**
 * 店铺关注
 */
export function follow(params) {
    return http({
        url: "/api/shopmember/add",
        data: params,
        forceLogin: true
    })
}
/**
 * 获取店铺分类树结构
 */
export function tree(params) {
    return http({
        url: "/api/shopgoodscategory/tree",
        data: params
    })
}
