import {
	mapGetters
} from "vuex"
import {
	cartList
} from "@/api/goods/cart"
export default {
	data: () => {
		return {
			cartList: [], // 购物车
			checkAll: false,
			totalPrice: "0.00",
			totalCount: 0,
			invalidGoods: [], // 失效商品集合
			loading: true,
			modifyNum: 1, // 防止数量跳动
		}
	},
	created() {
		this.getCartList()
	},
	computed: {
		...mapGetters(["defaultGoodsImage"])
	},
	methods: {
		// 获取购物车数据
		getCartList() {
			cartList({})
				.then(res => {
					if (res.code >= 0 && res.data.length) {
						this.handleCartList(res.data)
					}
					this.loading = false
				})
				.catch(res => {
					this.loading = false
				})
		},
		// 处理购物车数据结构
		handleCartList(data) {
			this.invalidGoods = []
			this.cartList = []
			var temp = {}
			data.forEach((item, index) => {
				if (item.goods_state == 1 && item.verify_state == 1) {
					item.checked = true
					if (temp["site_" + item.site_id] != undefined) {
						temp["site_" + item.site_id].cartList.push(item)
					} else {
						temp["site_" + item.site_id] = {
							siteId: item.site_id,
							siteName: item.site_name,
							checked: true,
							cartList: [item]
						}
					}
				} else {
					this.invalidGoods.push(item)
				}
			})

			Object.keys(temp).forEach(key => {
				this.cartList.push(temp[key])
			})
			this.calculationTotalPrice()
			this.cartList.forEach(v => {
				v.cartList.forEach(k => {
					if (k.sku_spec_format) {
						k.sku_spec_format = JSON.parse(k.sku_spec_format)
					} else {
						k.sku_spec_format = []
					}
				})
			})
		},
		// 单选
		singleElection(siteIndex, index) {
			this.calculationTotalPrice()
		},
		// 店铺全选
		siteAllElection(index) {
			this.cartList[index].cartList.forEach(item => {
				item.checked = this.cartList[index].checked
			})
			this.calculationTotalPrice()
		},
		// 全选
		allElection() {
			if (this.cartList.length) {
				this.cartList.forEach(siteItem => {
					siteItem.checked = this.checkAll
					siteItem.cartList.forEach(item => {
						item.checked = this.checkAll
					})
				})
			}
			this.calculationTotalPrice()
		},
		// 计算购物车总价
		calculationTotalPrice() {
			if (this.cartList.length) {
				let totalPrice = 0,
					totalCount = 0,
					siteAllElectionCount = 0

				this.cartList.forEach(siteItem => {
					let siteGoodsCount = 0
					siteItem.cartList.forEach(item => {
						if (item.checked) {
							siteGoodsCount += 1
							totalCount += 1
							totalPrice += item.discount_price * item.num
						}
					})
					if (siteItem.cartList.length == siteGoodsCount) {
						siteItem.checked = true
						siteAllElectionCount += 1
					} else {
						siteItem.checked = false
					}
				})
				this.totalPrice = totalPrice.toFixed(2)
				this.totalCount = totalCount
				this.checkAll = this.cartList.length == siteAllElectionCount
			} else {
				this.totalPrice = "0.00"
				this.totalCount = 0
			}
			this.modifyNum = 1;
		},
		// 删除单个
		deleteCart(siteIndex, cartIndex) {
			this.$confirm("确定要删除该商品吗？", "提示信息", {
				confirmButtonText: "确定",
				cancelButtonText: "取消",
				type: "warning"
			}).then(() => {
				this.$store
					.dispatch("cart/delete_cart", {
						cart_id: this.cartList[siteIndex].cartList[cartIndex].cart_id.toString()
					})
					.then(res => {
						if (res.code >= 0) {
							this.cartList[siteIndex].cartList.splice(cartIndex, 1)
							if (this.cartList[siteIndex].cartList.length == 0) this.cartList.splice(siteIndex, 1)
							this.calculationTotalPrice()
							this.$message({
								type: "success",
								message: "删除成功"
							})
						} else {
							this.$message({
								message: res.message,
								type: "warning"
							})
						}
					})
					.catch(err => {
						this.$message.error(err.message)
					})
			})
		},
		// 删除选择的购物车
		deleteCartSelected() {
			var cartIds = []
			var selectedItem = []
			this.cartList.forEach((siteItem, siteIndex) => {
				siteItem.cartList.forEach((item, cartIndex) => {
					if (item.checked) {
						cartIds.push(item.cart_id)
						selectedItem.push({
							siteIndex: siteIndex,
							cartIndex: cartIndex,
							siteId: siteItem.siteId,
							cartId: item.cart_id
						})
					}
				})
			})

			if (cartIds.length == 0) {
				this.$message({
					message: "请选择要删除的商品",
					type: "warning"
				})
				return
			}

			this.$confirm("确定要删除选择的商品吗？", "提示信息", {
				confirmButtonText: "确定",
				cancelButtonText: "取消",
				type: "warning"
			}).then(() => {
				this.$store
					.dispatch("cart/delete_cart", {
						cart_id: cartIds.toString()
					})
					.then(res => {
						if (res.code >= 0) {
							selectedItem.forEach(selectedItem => {
								this.cartList.forEach((siteItem, siteIndex) => {
									siteItem.cartList.forEach((item, cartIndex) => {
										if (selectedItem.cartId == item.cart_id) {
											siteItem.cartList.splice(cartIndex, 1)
										}
										if (siteItem.cartList.length == 0) {
											this.cartList.splice(siteIndex, 1)
										}
									})
								})
							})
							this.calculationTotalPrice()
							this.$message({
								type: "success",
								message: "删除成功"
							})
						} else {
							this.$message({
								message: res.message,
								type: "warning"
							})
						}
					})
					.catch(err => {
						this.$message.error(err.message)
					})
			})
		},
		// 清空失效商品
		clearInvalidGoods() {
			this.$confirm("确认要清空这些商品吗？", "提示信息", {
				confirmButtonText: "确定",
				cancelButtonText: "取消",
				type: "warning"
			}).then(() => {
				var cartIds = []
				this.invalidGoods.forEach(goodsItem => {
					cartIds.push(goodsItem.cart_id)
				})
				if (cartIds.length) {
					this.$store
						.dispatch("cart/delete_cart", {
							cart_id: cartIds.toString()
						})
						.then(res => {
							if (res.code >= 0) {
								this.invalidGoods = []
								this.$message({
									type: "success",
									message: "删除成功"
								})
							} else {
								this.$message({
									message: res.message,
									type: "warning"
								})
							}
						})
						.catch(err => {
							this.$message.error(err.message)
						})
				}
			})
		},
		// 变更购物车数量
		cartNumChange(num, params) {
			if (num < 1) num = 1;
			// 防止数量跳动
			this.modifyNum = 0;
			this.$store
				.dispatch("cart/edit_cart_num", {
					num,
					cart_id: this.cartList[params.siteIndex].cartList[params.cartIndex].cart_id
				})
				.then(res => {
					if (res.code >= 0) {
						this.cartList[params.siteIndex].cartList[params.cartIndex].num = num;
						this.calculationTotalPrice();
					} else {
						this.$message({
							message: res.message,
							type: "warning"
						});
						this.modifyNum = 1;
					}
				})
				.catch(err => {
					this.$message.error(err.message);
					this.modifyNum = 1;
				})
		},
		// 结算
		settlement() {
			if (this.totalCount > 0) {
				let cart_ids = []
				this.cartList.forEach(siteItem => {
					siteItem.cartList.forEach(item => {
						if (item.checked) {
							cart_ids.push(item.cart_id)
						}
					})
				})

				var data = {
					cart_ids: cart_ids.toString()
				}
				this.$store.dispatch("order/setOrderCreateData", data)
				this.$router.push({
					path: "/payment"
				})
			}
		},
		imageError(siteIndex, cartIndex) {
			this.cartList[siteIndex].cartList[cartIndex].sku_image = this.defaultGoodsImage
		},
		imageErrorInvalid(index) {
			this.invalidGoods[index].sku_image = this.defaultGoodsImage
		}
	}
}
