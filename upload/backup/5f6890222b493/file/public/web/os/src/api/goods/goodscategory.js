import http from "../../utils/http"

/**
 * 获取商品分类树结构
 */
export function tree(params) {
    return http({
        url: "/api/goodscategory/tree",
        data: params
    })
}

/**
 * 获取商品品牌信息
 * @param {Object} params 参数
 */
export function relevanceinfo(params) {
    return http({
        url: "/api/goodscategory/relevanceinfo",
        data: params
    })
}

/**
 * 获取商品分类信息
 * @param {Object} params 参数 category_id:1
 */
export function goodsCategoryInfo(params) {
    return http({
        url: "/api/goodscategory/info",
        data: params
    })
}
