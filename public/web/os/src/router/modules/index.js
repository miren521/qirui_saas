import BasicLayout from "../../layout/basic"

const promotionRoutes = [
	//******************组合套餐模块（2）******************
	{
		path: "/promotion/combo-*",
		name: "combo",
		meta: {
			module: "combo"
		},
		component: () => import("@/views/promotion/combo/detail")
	},
	{
		path: "/promotion/combo_payment",
		name: "combo_payment",
		meta: {
			module: "combo",
			auth: true
		},
		component: () => import("@/views/promotion/combo/payment")
	},

	//******************秒杀模块（3）******************
	{
		path: "/promotion/seckill-*",
		name: "seckill_detail",
		meta: {
			module: "seckill",
			backgroundColor: "#fff"
		},
		component: () => import("@/views/promotion/seckill/detail")
	},
	{
		path: "/promotion/seckill",
		name: "seckill",
		meta: {
			module: "seckill",
			mainCss: {
				width: "100%"
			}
		},
		component: () => import("@/views/promotion/seckill/list")
	},
	{
		path: "/promotion/seckill_payment",
		name: "seckill_payment",
		meta: {
			module: "seckill",
			auth: true
		},
		component: () => import("@/views/promotion/seckill/payment")
	},

	//******************团购模块（3）******************
	{
		path: "/promotion/groupbuy-*",
		name: "groupbuy_detail",
		meta: {
			module: "groupbuy",
			backgroundColor: "#fff"
		},
		component: () => import("@/views/promotion/groupbuy/detail")
	},
	{
		path: "/promotion/groupbuy",
		name: "groupbuy",
		meta: {
			module: "groupbuy",
			mainCss: {
				width: "100%"
			}
		},
		component: () => import("@/views/promotion/groupbuy/list")
	},
	{
		path: "/promotion/groupbuy_payment",
		name: "groupbuy_payment",
		meta: {
			module: "groupbuy",
			auth: true
		},
		component: () => import("@/views/promotion/groupbuy/payment")
	},

	//******************专题活动模块（4）******************
	{
		path: "/promotion/topic-*",
		name: "topic_detail",
		meta: {
			module: "topic"
		},
		component: () => import("@/views/promotion/topic/detail")
	},
	{
		path: "/promotion/topic",
		name: "topic",
		meta: {
			module: "topic"
		},
		component: () => import("@/views/promotion/topic/list")
	},
	{
		path: "/promotion/topic-goods-*",
		name: "topic_goods_detail",
		meta: {
			module: "topic",
			backgroundColor: "#fff"
		},
		component: () => import("@/views/promotion/topic/goods_detail")
	},
	{
		path: "/promotion/topic_payment",
		name: "topic_payment",
		meta: {
			module: "topic",
			auth: true
		},
		component: () => import("@/views/promotion/topic/payment")
	}
]

const cmsRoutes = [{
		path: "/cms/notice",
		name: "notice",
		meta: {
			module: "notice",
			backgroundColor: "#fff"
		},
		component: () => import("@/views/cms/notice/list")
	},
	{
		path: "/cms/notice-*",
		name: "notice_detail",
		meta: {
			module: "notice",
			backgroundColor: "#fff"
		},
		component: () => import("@/views/cms/notice/detail")
	},
	{
		path: "/cms/help",
		name: "help",
		meta: {
			module: "help",
			backgroundColor: "#fff"
		},
		component: () => import("@/views/cms/help/list"),
		children: [{
			path: "/cms/help/listother-*",
			name: "list_other",
			meta: {
				module: "help",
				backgroundColor: "#fff"
			},
			component: () => import("@/views/cms/help/listother")
		}]
	},
	{
		path: "/cms/help-*",
		name: "help_detail",
		meta: {
			module: "help",
			backgroundColor: "#fff"
		},
		component: () => import("@/views/cms/help/detail")
	}
]

const goodsRoutes = [
	//******************商品模块（6）******************
	{
		path: "/brand",
		name: "brand",
		meta: {
			module: "goods",
			mainCss: {
				width: "100%"
			}
		},
		component: () => import("@/views/goods/brand")
	},
	{
		path: "/cart",
		name: "cart",
		meta: {
			module: "goods"
		},
		component: () => import("@/views/goods/cart")
	},
	{
		path: "/category",
		name: "category",
		meta: {
			module: "goods"
		},
		component: () => import("@/views/goods/category")
	},
	{
		path: "/coupon",
		name: "coupon",
		meta: {
			module: "goods"
		},
		component: () => import("@/views/goods/coupon")
	},
	{
		path: "/sku-*",
		name: "detail",
		meta: {
			module: "goods",
			backgroundColor: "#fff"
		},
		component: () => import("@/views/goods/detail")
	},
	{
		path: "/list",
		name: "list",
		meta: {
			module: "goods",
			title: '商品列表',
			backgroundColor: "#fff"
		},
		component: () => import("@/views/goods/list")
	},
	{
		path: "/street",
		name: "street",
		meta: {
			module: "shop",
			backgroundColor: "#fff",
			title: '店铺街'
		},
		component: () => import("@/views/shop/street")
	},
]

// 其他模块
export default {
	path: "/",
	component: BasicLayout,
	redirect: "/index",
	name: "Index",
	children: [{
			path: "/index*",
			name: "index",
			meta: {
				mainCss: {
					width: "100%"
				}
			},
			component: () => import("@/views/index/index")
		},
		{
			path: "/change_city",
			name: "change_city",
			meta: {
				module: "index"
			},
			component: () => import("@/views/index/change_city")
		},

		...goodsRoutes,
		...cmsRoutes,
		...promotionRoutes
	]
}
