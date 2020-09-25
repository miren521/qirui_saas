import http from "../../utils/http"

/**
 * 获取会员信息【进入首页调用了，不能控制登录】
 */
export function memberDetail(params) {
    return http({
        url: "/api/member/detail",
        data: params
    })
}

/**
 * 获取地址列表
 */
export function addressList(params) {
    return http({
        url: "/api/memberaddress/page",
        data: params,
        forceLogin: true
    })
}

/**
 * 设为默认
 */
export function setDefault(params) {
    return http({
        url: "/api/memberaddress/setdefault",
        data: params,
        forceLogin: true
    })
}

/**
 * 删除地址
 */
export function deleteAddress(params) {
    return http({
        url: "/api/memberaddress/delete",
        data: params,
        forceLogin: true
    })
}

/**
 * 地址信息
 */
export function addressInfo(params) {
    return http({
        url: "/api/memberaddress/info",
        data: params,
        forceLogin: true
    })
}

/**
 * 添加地址
 */
export function saveAddress(params) {
    return http({
        url: "/api/memberaddress/" + params.url,
        data: params,
        forceLogin: true
    })
}

/**
 * 获取优惠券列表
 */
export function couponList(params) {
    let url = ""
    if (params.couponsource == "1") {
        url = "/coupon/api/coupon/memberpage"
    } else {
        url = "/platformcoupon/api/platformcoupon/memberpage"
    }
    return http({
        url: url,
        data: params,
        forceLogin: true
    })
}

/**
 * 我的足迹
 */
export function footPrint(params) {
    return http({
        url: "/api/goodsbrowse/page",
        data: params,
        forceLogin: true
    })
}

/**
 * 删除时间线
 */
export function delFootprint(params) {
    return http({
        url: "/api/goodsbrowse/delete",
        data: params,
        forceLogin: true
    })
}

/**
 * 账户列表
 */
export function accountList(params) {
	return http({
		url: "/api/memberbankaccount/page",
		data: params,
	})
}

/**
 * 设置默认账户
 */
export function accountDefault(params) {
	return http({
		url: "/api/memberbankaccount/setdefault",
		data: params,
	})
}

/**
 * 删除账户
 */
export function delAccount(params) {
	return http({
		url: "/api/memberbankaccount/delete",
		data: params,
	})
}

/**
 * 获取转账方式
 */
export function transferType(params) {
	return http({
		url: "/api/memberwithdraw/transferType",
		data: params,
	})
}

/**
 * 获取账户详情
 */
export function accountDetail(params) {
	return http({
		url: "/api/memberbankaccount/info",
		data: params,
	})
}

/**
 * 保存
 */
export function saveAccount(params) {
	return http({
		url: "/api/memberbankaccount/" + params.url,
		data: params,
	})
}