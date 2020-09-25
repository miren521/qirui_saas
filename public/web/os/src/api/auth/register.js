import http from "../../utils/http"

/**
 * 获取注册协议
 */
export function getRegisiterAggrement(params) {
    return http({
        url: "/api/register/aggrement",
        data: params
    })
}

/**
 * 注册
 */
export function register(params) {
    return http({
        url: "/api/register/username",
        data: params
    })
}

/**
 * 注册配置
 */
export function registerConfig(params) {
    return http({
        url: "/api/register/config",
        data: params
    })
}
