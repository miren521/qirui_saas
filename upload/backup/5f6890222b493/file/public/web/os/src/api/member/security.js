import http from "../../utils/http"

/**
 * 修改密码
 * @param {object} params
 */
export function passWord(params) {
    return http({
        url: "/api/member/modifypassword",
        data: params,
        forceLogin: true
    })
}
/**
 * 发送短信动态码
 * @param {object} params
 */
export function tellCode(params) {
    return http({
        url: "/api/member/bindmobliecode",
        data: params,
        forceLogin: true
    })
}
/**
 * 绑定手机号
 * @param {object} params
 */
export function tell(params) {
    return http({
        url: "/api/member/modifymobile",
        data: params,
        forceLogin: true
    })
}
/**
 * 检测邮箱是否存在
 * @param {object} params
 */
export function checkEmail(params) {
    return http({
        url: "/api/member/checkemail",
        data: params,
        forceLogin: true
    })
}
/**
 * 发送邮箱动态码
 * @param {object} params
 */
export function emailCode(params) {
    return http({
        url: "/api/member/bingemailcode",
        data: params,
        forceLogin: true
    })
}
/**
 * 绑定邮箱
 * @param {object} params
 */
export function email(params) {
    return http({
        url: "/api/member/modifyemail",
        data: params,
        forceLogin: true
    })
}
/**
 * 验证码验证
 * @param {object} params
 */
export function verifypaypwdcode(params) {
    return http({
        url: "/api/member/verifypaypwdcode",
        data: params,
        forceLogin: true
    })
}
/**
 * 获取之前密码
 * @param {object} params
 */
export function modifypaypassword(params) {
    return http({
        url: "/api/member/modifypaypassword",
        data: params,
        forceLogin: true
    })
}
/**
 * 手机动态码
 * @param {object} params
 */
export function paypwdcode(params) {
    return http({
        url: "/api/member/paypwdcode",
        data: params,
        forceLogin: true
    })
}
/**
 * 手机密码动态码
 * @param {object} params
 */
export function pwdmobliecode(params) {
    return http({
        url: '/api/member/pwdmobliecode',
        data: params,
        forceLogin: true
    })
}

