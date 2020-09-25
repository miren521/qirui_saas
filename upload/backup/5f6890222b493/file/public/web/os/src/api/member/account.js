import http from "../../utils/http"
/**
 * 获取余额基础信息
 */
export function balance(params) {
    return http({
        url: "/api/memberaccount/info",
        data: params,
        forceLogin: true
    })
}
/**
 * 获取提现配置
 */
export function withdrawConfig(params) {
    return http({
        url: "/api/memberwithdraw/config",
        data: params,
        forceLogin: true
    })
}
/**
 * 获取余额明细
 */
export function balanceDetail(params) {
    return http({
        url: "/api/memberaccount/page",
        data: params,
        forceLogin: true
    })
}

/**
 * 获取提现信息
 */
export function withdrawInfo(params) {
	return http({
		url: "/api/memberwithdraw/info",
		data: params,
	})
}

/**
 * 获取银行账号信息
 */
export function accountInfo(params) {
	return http({
		url: "/api/memberbankaccount/defaultinfo",
		data: params,
	})
}

/**
 * 提现
 */
export function withdraw(params) {
	return http({
		url: "/api/memberwithdraw/apply",
		data: params,
	})
}

/**
 * 提现记录
 */
export function withdrawList(params) {
	return http({
		url: "/api/memberwithdraw/page",
		data: params,
	})
}

/**
 * 提现详情
 */
export function withdrawDetail(params) {
	return http({
		url: "/api/memberwithdraw/detail",
		data: params,
	})
}

/**
 * 充值套餐列表
 */
export function rechargeList(params) {
	return http({
		url: "/memberrecharge/api/memberrecharge/page",
		data: params,
	})
}

/**
 * 充值套餐详情
 */
export function rechargeDetail(params) {
	return http({
		url: "/memberrecharge/api/memberrecharge/info",
		data: params,
	})
}

/**
 * 充值
 */
export function recharge(params) {
	return http({
		url: "/memberrecharge/api/ordercreate/create",
		data: params,
	})
}

/**
 * 充值记录
 */
export function rechargeOrder(params) {
	return http({
		url: "/memberrecharge/api/order/page",
		data: params,
	})
}