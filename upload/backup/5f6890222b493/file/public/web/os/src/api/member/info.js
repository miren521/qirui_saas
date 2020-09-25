import http from "../../utils/http"

/**
 * 获取用户信息
 * @param {object} params
 */
export function info(params) {
    return http({
        url: "/api/member/info",
        data: params,
        forceLogin: true
    })
}
/**
 * 修改昵称
 * @param {object} params
 */
export function nickName(params) {
    return http({
        url: "/api/member/modifynickname",
        data: params,
        forceLogin: true
    })
}
/**
 * 修改头像
 * @param {object} params
 */
export function headImg(params) {
    return http({
        url: "/api/member/modifyheadimg",
        data: params,
        forceLogin: true
    })
}
