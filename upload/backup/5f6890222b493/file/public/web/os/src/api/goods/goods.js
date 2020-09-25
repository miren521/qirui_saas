import http from "../../utils/http"

/**
 * 商品列表
 * @param {object} params
 */
export function goodsSkuPage(params) {
    return http({
        url: "/api/goodssku/page",
        data: params
    })
}

export function goodsSkuList(params) {
    return http({
        url: "/api/goodssku/lists",
        data: params
    })
}

/**
 * 商品详情
 * @param {Object} params
 */
export function goodsSkuDetail(params) {
    return http({
        url: "/api/goodssku/detail",
        data: params
    })
}

/**
 * 商品信息
 * @param { Object } params
 */
export function goodsSkuInfo(params) {
    return http({
        url: "/api/goodssku/info",
        data: params
    })
}

/**
 * 商品信息
 * @param { Object } params
 */
export function goodsQrcode(params) {
    return http({
        url: "/api/goodssku/goodsqrcode",
        data: params
    })
}

/**
 * 获取满减信息
 * @param {Object} params
 */
export function manjian(params) {
    return http({
        url: "/manjian/api/manjian/info",
        data: params
    })
}

/**
 * 获取售后服务
 * @param {Object} params
 */
export function aftersale(params) {
    return http({
        url: "/api/goods/aftersale",
        data: params
    })
}

/**
 * 更新商品点击量
 * @param {Object} params
 */
export function modifyClicks(params) {
    return http({
        url: "/api/goods/modifyclicks",
        data: params
    })
}

/**
 * 添加商品足迹
 * @param {Object} params
 */
export function addGoodsbrowse(params) {
    return http({
        url: "/api/goodsbrowse/add",
        data: params
    })
}

/**
 * 商品推荐列表
 * @param {object} params
 */
export function goodsRecommend(params) {
    return http({
        url: "/api/goodssku/recommend",
        data: params
    })
}
/**
 * 品牌列表
 */
export function brandList(params) {
    return http({
        url: "/api/goodsbrand/page",
        data: params
    })
}