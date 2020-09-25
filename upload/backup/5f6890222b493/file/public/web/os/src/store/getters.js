const getters = {
	// 用户TOKRN
	token: state => state.member.token,
	lang: state => state.app.lang,
	city: state => state.app.city,
	locationRegion: state => state.app.locationRegion,
	// 自动登录时长
	autoLoginRange: state => state.member.autoLoginRange,
	wapQrcode: state => state.site.siteQrCode,
	// 会员详情
	member: state => state.member.member,

	// 会员自动登录时长
	autoLoginRange: state => state.member.autoLoginRange,

	copyRight: state => state.site.copyRight,
	siteInfo: state => state.site.siteInfo,
	addonIsExit: state => state.site.addons,

	// 购物车商品总数
	cartCount: state => state.cart.cartCount,

	defaultGoodsImage: state => state.site.defaultFiles.default_goods_img,
	defaultHeadImage: state => state.site.defaultFiles.default_headimg,
	defaultShopImage: state => state.site.defaultFiles.default_shop_img,

	// 普通待付款订单
	orderCreateGoodsData: state => state.order.orderCreateGoodsData,

	//团购待付款订单
	groupbuyOrderCreateData: state => state.order.groupbuyOrderCreateData,

	//秒杀待付款订单
	seckillOrderCreateData: state => state.order.seckillOrderCreateData,

	//组合套餐待付款订单
	comboOrderCreateData: state => state.order.comboOrderCreateData
}

export default getters;
