import MemberLayout from "../../layout/member"

// 会员详情相关路由
const memberDetailRoutes = [{
	path: "index",
	name: "member",
	meta: {
		module: "member",
		auth: true,
	},
	component: () => import("@/views/member/index")
},
{
	path: "security",
	name: "security",
	meta: {
		module: "member",
		auth: true
	},
	component: () => import("@/views/member/security")
},
{
	path: "account",
	name: "account",
	meta: {
		module: "member",
		auth: true
	},
	component: () => import("@/views/member/account")
},
{
	path: "delivery_address",
	name: "delivery_address",
	meta: {
		module: "member",
		auth: true
	},
	component: () => import("@/views/member/delivery_address")
},
{
	path: "address_edit",
	name: "address_edit",
	meta: {
		module: "member",
		auth: true,
		// parentRouter: "delivery_address" // 记录上级路由，用于会员中心选择左侧菜单
	},
	component: () => import("@/views/member/address_edit")
},
{
	path: "collection",
	name: "collection",
	meta: {
		module: "member",
		auth: true
	},
	component: () => import("@/views/member/collection")
},
{
	path: "my_coupon",
	name: "my_coupon",
	meta: {
		module: "member",
		auth: true
	},
	component: () => import("@/views/member/coupon")
},
{
	path: "footprint",
	name: "footprint",
	meta: {
		module: "member",
		auth: true
	},
	component: () => import("@/views/member/footprint")
},
{
	path: "info",
	name: "info",
	meta: {
		module: "member",
		auth: true
	},
	component: () => import("@/views/member/info")
},
{
	path: "level",
	name: "level",
	meta: {
		module: "member",
		auth: true
	},
	component: () => import("@/views/member/level")
},
{
	path: "signin",
	name: "signin",
	meta: {
		module: "member",
		auth: true
	},
	component: () => import("@/views/member/signin")
},
{
	path: "order_list",
	name: "order_list",
	meta: {
		module: "order",
		auth: true
	},
	component: () => import("@/views/order/list")
},
{
	path: "my_point",
	name: "my_point",
	meta: {
		module: "member",
		auth: true
	},
	component: () => import("@/views/member/my_point")
},
{
	path: "activist",
	name: "activist",
	meta: {
		module: "member",
		auth: true
	},
	component: () => import("@/views/order/activist")
},
{
	path: "refund_detail",
	name: "refund_detail",
	meta: {
		module: "order",
		auth: true,
		// parentRouter: "activist" // 记录上级路由，用于会员中心选择左侧菜单
	},
	component: () => import("@/views/order/refund_detail")
},
{
	path: "refund",
	name: "refund",
	meta: {
		module: "order_list",
		auth: true,
		// parentRouter: "order_list" // 记录上级路由，用于会员中心选择左侧菜单
	},
	component: () => import("@/views/order/refund")
},
{
	path: "complain",
	name: "complain",
	meta: {
		module: "order",
		auth: true,
		parentRouter: "activist" // 记录上级路由，用于会员中心选择左侧菜单
	},
	component: () => import("@/views/order/complain")
},
{
	path: "order_detail",
	name: "order_detail",
	meta: {
		module: "order",
		auth: true,
		// parentRouter: "order_list" // 记录上级路由，用于会员中心选择左侧菜单
	},
	component: () => import("@/views/order/detail")
},
{
	path: "order_detail_local_delivery",
	name: "order_detail_local_delivery",
	meta: {
		module: "order",
		auth: true,
		// parentRouter: "order_list" // 记录上级路由，用于会员中心选择左侧菜单
	},
	component: () => import("@/views/order/detail_local_delivery")
},
{
	path: "order_detail_pickup",
	name: "order_detail_pickup",
	meta: {
		module: "order",
		auth: true,
		// parentRouter: "order_list" // 记录上级路由，用于会员中心选择左侧菜单
	},
	component: () => import("@/views/order/detail_pickup")
},
{
	path: "order_detail_virtual",
	name: "order_detail_virtual",
	meta: {
		module: "order",
		auth: true,
		// parentRouter: "order_list" // 记录上级路由，用于会员中心选择左侧菜单
	},
	component: () => import("@/views/order/detail_virtual")
},
{
	path: "logistics",
	name: "logistics",
	meta: {
		module: "order",
		auth: true,
		// parentRouter: "order_list" // 记录上级路由，用于会员中心选择左侧菜单
	},
	component: () => import("@/views/order/logistics")
},
{
	path: "verification",
	name: "verification",
	meta: {
		module: "order",
		auth: true
	},
	component: () => import("@/views/order/verification")
},
{
	path: "verification_list",
	name: "verification_list",
	meta: {
		module: "order",
		auth: true
	},
	component: () => import("@/views/order/verification_list")
},
{
	path: "verification_detail",
	name: "verification_detail",
	meta: {
		module: "order",
		auth: true,
		parentRouter: "verification_list" // 记录上级路由，用于会员中心选择左侧菜单
	},
	component: () => import("@/views/order/verification_detail")
},
{
	path: "account_list",
	name: "account_list",
	meta: {
		module: "member",
		auth: true
	},
	component: () => import("../../views/member/account_list")
},
{
	path: "account_edit",
	name: "account_edit",
	meta: {
		module: "member",
		auth: true,
		parentRouter: "account_list" // 记录上级路由，用于会员中心选择左侧菜单
	},
	component: () => import("../../views/member/account_edit")
},
{
	path: "apply_withdrawal",
	name: "apply_withdrawal",
	meta: {
		module: "member",
		auth: true,
		parentRouter: "account" // 记录上级路由，用于会员中心选择左侧菜单
	},
	component: () => import("../../views/member/apply_withdrawal")
},
{
	path: "withdrawal",
	name: "withdrawal",
	meta: {
		module: "member",
		auth: true
	},
	component: () => import("@/views/member/withdrawal")
},
{
	path: "withdrawal_detail",
	name: "withdrawal_detail",
	meta: {
		module: "member",
		auth: true,
		parentRouter: "withdrawal" // 记录上级路由，用于会员中心选择左侧菜单
	},
	component: () => import("@/views/member/withdrawal_detail")
},
{
	path: "recharge_list",
	name: "recharge_list",
	meta: {
		module: "member",
		auth: true,
		parentRouter: "account" // 记录上级路由，用于会员中心选择左侧菜单
	},
	component: () => import("@/views/member/recharge_list")
},
{
	path: "recharge_detail",
	name: "recharge_detail",
	meta: {
		module: "member",
		auth: true,
		parentRouter: "account" // 记录上级路由，用于会员中心选择左侧菜单
	},
	component: () => import("@/views/member/recharge_detail")
},
{
	path: "recharge_order",
	name: "recharge_order",
	meta: {
		module: "member",
		auth: true,
		parentRouter: "account" // 记录上级路由，用于会员中心选择左侧菜单
	},
	component: () => import("@/views/member/recharge_order")
}
]

// 会员订单相关路由
const memberOrderRoutes = [{
	path: "/evaluate",
	name: "evaluate",
	meta: {
		module: "order",
		auth: true
	},
	component: () => import("@/views/order/evaluate")
},
{
	path: "/payment",
	name: "payment",
	meta: {
		module: "order",
		auth: true
	},
	component: () => import("@/views/order/payment")
},
{
	path: "/pay",
	name: "pay",
	meta: {
		module: "pay",
		auth: true
	},
	component: () => import("@/views/pay/index")
},
{
	path: "/result",
	name: "result",
	meta: {
		module: "pay",
		auth: true
	},
	component: () => import("@/views/pay/result")
}
]

// 会员模块
export default {
	path: "/member",
	component: MemberLayout,
	redirect: "index",
	alwaysShow: true,
	name: "MemberIndex",
	children: [{
		path: "/member",
		name: "home",
		redirect: "index",
		children: memberDetailRoutes,
		component: () => import("@/views/member/home")
	},

	...memberOrderRoutes
	]
}
