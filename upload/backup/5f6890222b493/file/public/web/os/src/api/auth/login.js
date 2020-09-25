import http from "../../utils/http"

/**
 * 登录
 */
export function login(params) {
    return http({
        url: "/api/login/login",
        data: params
    })
}

/**
 * 手机号登录
 * @param json params
 */
export function mobile_login(params) {
    return http({
        url: "/api/login/mobile",
        data: params
    })
}

/**
 * 获取短信动态码
 */
export function mobileCode(params) {
    return http({
        url: "/api/login/mobileCode",
        data: params
    })
}

/**
 * 重置密码
 */
export function rePass(params) {
    return http({
        url: "/api/findpassword/mobile",
        data: params
    })
}

/**
 * 下一步
 */
export function nextStep(params) {
    return http(
        {
            url: "/api/member/checkmobile",
            data: params
        },
        -1
    )
}

/**
 * 获取短信动态码
 */
export function smsCode(params) {
    return http({
        url: "/api/findpassword/mobilecode",
        data: params
    })
}

/**
 * 获取注册配置
 */
export function registerConfig(params) {
    return http({
        url: "/api/register/config",
        data: params
    })
}
