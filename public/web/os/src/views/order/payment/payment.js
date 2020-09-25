import {
	checkpaypassword,
	payment,
	calculate,
	orderCreate
} from "@/api/order/payment"
import {
	addressList,
	saveAddress,
	setDefault,
	deleteAddress,
	addressInfo
} from "@/api/member/member"
import {
	getArea
} from "@/api/address"
import {
	mapGetters
} from "vuex"

export default {
	name: "payment",
	components: {},
	data: () => {
		var checkMobile = (rule, value, callback) => {
			if (value === "") {
				callback(new Error("请输入手机号"))
			} else if (!/^1[3|4|5|6|7|8|9][0-9]{9}$/.test(value)) {
				callback(new Error("手机号格式错误"))
			} else {
				callback()
			}
		}

		return {
			dialogVisible: false,
			memberAddress: [], //收货地址列表
			addressId: 0, //收货地址
			addressForm: {
				id: 0,
				name: "",
				mobile: "",
				telephone: "",
				province_id: "",
				city_id: "",
				district_id: "",
				community_id: "",
				address: "",
				full_address: "",
				is_default: "",
				longitude: "",
				latitude: ""
			},
			pickerValueArray: {},
			cityArr: {},
			districtArr: {},
			addressRules: {
				name: [{
						required: true,
						message: "请输入收货人",
						trigger: "blur"
					},
					{
						min: 1,
						max: 20,
						message: "长度在 1 到 20 个字符",
						trigger: "blur"
					}
				],
				mobile: [{
					required: true,
					validator: checkMobile,
					trigger: "change"
				}],
				province: [{
					required: true,
					message: "请选择省",
					trigger: "change"
				}],
				city: [{
					required: true,
					message: "请选择市",
					trigger: "change"
				}],
				district: [{
					required: true,
					message: "请选择区/县",
					trigger: "change"
				}],
				address: [{
					required: true,
					message: "请输入详细地址",
					trigger: "change"
				}]
			},
			isSend: false,
			orderCreateData: {
				is_balance: 0,
				pay_password: "",
				platform_coupon_id: 0,
				buyer_message: {},
			},
			orderPaymentData: {
				goods_money: 0,
				pay_money: 0,
				member_account: {
					balance: 0,
					is_pay_password: 0
				},
				platform_coupon_list: []
			},
			dialogCoupon: false,
			siteCoupon: {
				site_id: 0,
				data: []
			},
			siteDelivery: {
				site_id: 0,
				data: []
			},
			dialogStore: false,
			promotionInfo: false,
			storeList: {},
			sitePromotion: [],
			isSub: false,
			dialogpay: false,
			password: "",
			fullscreenLoading: true,
			deliveryTime: false,
			timeTip: "选择配送时间",
			time: null,
			addressShow: false,
			couponRadio: 0,
			storeRadio: 0,
			dialogPlatcoupon: false,
			platformCouponRadio: 0,
		}
	},
	computed: {
		...mapGetters(["orderCreateGoodsData", "defaultGoodsImage", "city"])
	},
	mounted() {},
	created() {
		this.getMemberAddress()
		this.getOrderPaymentData()
	},

	filters: {
		/**
		 * 金额格式化输出
		 * @param {Object} money
		 */
		moneyFormat(money) {
			return parseFloat(money).toFixed(2)
		},
		/**
		 * 店铺优惠摘取
		 */
		promotion(data) {
			let promotion = ""
			if (data) {
				Object.keys(data).forEach(key => {
					promotion += data[key].content + "　"
				})
			}
			return promotion
		}
	},
	methods: {
		//获取收货地址
		getMemberAddress() {
			addressList({
					page_size: 0
				})
				.then(res => {
					const {
						code,
						message,
						data
					} = res
					if (data && data.list) {
						let that = this
						this.memberAddress = data.list
						data.list.forEach(function(e) {
							if (e.is_default == 1) {
								that.addressId = e.id
							}
						})
					}
				})
				.catch(err => {
					const {
						code,
						message,
						data
					} = err
					this.$message.error(message)
				})
		},

		//设置会员收货地址
		setMemberAddress(params) {
			this.addressId = params
			setDefault({
					id: params
				})
				.then(res => {
					const {
						code,
						message,
						data
					} = res
					this.orderCalculate()
				})
				.catch(err => {
					const {
						code,
						message,
						data
					} = err
					this.$message.error(message)
				})
		},

		//删除会员收货地址
		deleteMemberAddress(params) {
			deleteAddress({
					id: params
				})
				.then(res => {
					const {
						code,
						message,
						data
					} = res
					if (data) {
						this.$message({
							message: message,
							type: "success"
						})
						this.getMemberAddress()
					} else {
						this.$message({
							message: message,
							type: "warning"
						})
					}
				})
				.catch(err => {
					this.$message.error(err.message)
				})
		},

		//打开添加收货地址弹出层
		addAddressShow() {
			this.dialogVisible = true
			this.addressForm.id = 0
			this.addressForm.name = ""
			this.addressForm.mobile = ""
			this.addressForm.telephone = ""
			this.addressForm.province_id = ""
			this.addressForm.city_id = ""
			this.addressForm.district_id = ""
			this.addressForm.community_id = ""
			this.addressForm.address = ""
			this.addressForm.full_address = ""
			this.addressForm.is_default = ""
			this.addressForm.longitude = ""
			this.addressForm.latitude = ""
			// this.$nextTick(() => {
			// 	this.$refs.form.resetFields();
			// });
			this.cityArr = {}
			this.districtArr = {}
			this.getAddress(0)
		},

		//获取地址
		getAddress(type) {
			let pid = 0
			let that = this
			switch (type) {
				case 0:
					//加载省
					pid = 0
					break
				case 1:
					//加载市
					pid = this.addressForm.province_id
					that.cityArr = {}
					that.districtArr = {}
					this.addressForm.city_id = ""
					this.addressForm.district_id = ""
					break
				case 2:
					//加载区县
					pid = this.addressForm.city_id
					that.districtArr = {}
					this.addressForm.district_id = ""
					break
			}

			getArea({
					pid: pid
				})
				.then(res => {
					const {
						code,
						message,
						data
					} = res
					if (data) {
						switch (type) {
							case 0:
								that.pickerValueArray = data
								break
							case 1:
								//加载市
								that.cityArr = data
								break
							case 2:
								//加载区县
								that.districtArr = data
								break
						}
					}
				})
				.catch(err => {
					const {
						code,
						message,
						data
					} = err
					this.$message.error(message)
				})
		},

		//编辑地址 初始化
		initAddress(type) {
			let pid = 0
			let that = this
			switch (type) {
				case 0:
					//加载省
					pid = 0
					break
				case 1:
					//加载市
					pid = this.addressForm.province_id
					that.cityArr = {}
					that.districtArr = {}
					break
				case 2:
					//加载区县
					pid = this.addressForm.city_id
					that.districtArr = {}
					break
			}

			getArea({
					pid: pid
				})
				.then(res => {
					const {
						code,
						message,
						data
					} = res
					if (data) {
						switch (type) {
							case 0:
								that.pickerValueArray = data
								break
							case 1:
								//加载市
								that.cityArr = data
								break
							case 2:
								//加载区县
								that.districtArr = data
								break
						}
					}
				})
				.catch(err => {
					const {
						code,
						message,
						data
					} = err
					this.$message.error(message)
				})
		},

		//新增/编辑收货地址
		addmemberAddress(formName) {
			this.$refs[formName].validate(valid => {
				if (valid) {
					if (this.isSend) {
						return false
					}

					if (!this.addressForm.id) {
						this.addressForm.full_address = this.$refs.province.selectedLabel + "-" + this.$refs.city.selectedLabel + "-" +
							this.$refs.district.selectedLabel
						let data = {
							name: this.addressForm.name,
							mobile: this.addressForm.mobile,
							telephone: this.addressForm.telephone,
							province_id: this.addressForm.province_id,
							city_id: this.addressForm.city_id,
							district_id: this.addressForm.district_id,
							community_id: "",
							address: this.addressForm.address,
							full_address: this.addressForm.full_address,
							longitude: this.addressForm.longitude,
							latitude: this.addressForm.latitude,
							is_default: this.addressForm.is_default,
							url: 'add'
						}

						if (!data.province_id) {
							this.$message({
								message: "请选择省",
								type: "warning"
							})
							return false
						}
						if (!data.city_id) {
							this.$message({
								message: "请选择市",
								type: "warning"
							})
							return false
						}
						if (!data.district_id) {
							this.$message({
								message: "请选择区/县",
								type: "warning"
							})
							return false
						}
						this.isSend = true

						saveAddress(data)
							.then(res => {
								const {
									code,
									message,
									data
								} = res
								if (data) {
									this.setMemberAddress(data)

									this.$message({
										message: message,
										type: "success"
									})
									this.dialogVisible = false
									this.getMemberAddress()
									this.getOrderPaymentData()
								} else {
									this.$message({
										message: message,
										type: "warning"
									})
								}
								this.isSend = false
							})
							.catch(err => {
								const {
									code,
									message,
									data
								} = err
								this.$message.error(message)
							})
					} else {
						this.addressForm.full_address = this.$refs.province.selectedLabel + "-" + this.$refs.city.selectedLabel + "-" +
							this.$refs.district.selectedLabel
						let data = this.addressForm
						if (!data.province_id) {
							this.$message({
								message: "请选择省",
								type: "warning"
							})
							return false
						}
						if (!data.city_id) {
							this.$message({
								message: "请选择市",
								type: "warning"
							})
							return false
						}
						if (!data.district_id) {
							this.$message({
								message: "请选择区/县",
								type: "warning"
							})
							return false
						}
						this.isSend = true
						this.setMemberAddress(data.id);
						data.url = "edit";
						saveAddress(data)
							.then(res => {
								const {
									code,
									message,
									data
								} = res
								if (data) {
									this.$message({
										message: message,
										type: "success"
									})
									this.dialogVisible = false
									this.getMemberAddress()
									this.getOrderPaymentData()
								} else {
									this.$message({
										message: message,
										type: "warning"
									})
								}
								this.isSend = false
							})
							.catch(err => {
								const {
									code,
									message,
									data
								} = err
								this.$message.error(message)
							})
					}
				} else {
					return false
				}
			})
		},

		//编辑收货地址
		editAddress(id) {
			addressInfo({
					id: id
				})
				.then(res => {
					const {
						code,
						message,
						data
					} = res
					this.addressForm = {
						id: data.id,
						name: data.name,
						mobile: data.mobile,
						telephone: data.telephone,
						province_id: data.province_id,
						city_id: "",
						district_id: "",
						community_id: "",
						address: data.address,
						full_address: data.full_address,
						is_default: data.is_default,
						longitude: data.longitude,
						latitude: data.latitude
					}
					this.initAddress(0)
					this.initAddress(1)
					this.addressForm.city_id = data.city_id
					this.initAddress(2)
					this.addressForm.district_id = data.district_id

					this.dialogVisible = true
				})
				.catch(err => {
					const {
						code,
						message,
						data
					} = err
					this.$message.error(message)
				})
		},

		/**
		 * 获取订单初始化数据
		 */
		getOrderPaymentData() {
			this.orderCreateData = this.orderCreateGoodsData

			if (!this.orderCreateData) {
				this.$message({
					message: "未获取到创建订单所需数据！", //提示的信息
					type: "warning",
					offset: 225,
					duration: 3000,
					onClose: () => {
						this.$router.go(-1)
						return false
					}
				})
				return
			}
			this.orderCreateData.web_city = this.city ? this.city.id : 0
			payment(this.orderCreateData)
				.then(res => {
					const {
						code,
						message,
						data
					} = res

					if (code >= 0) {
						this.orderPaymentData = data
						this.handlePaymentData()
					} else {
						this.$message({
							message: "未获取到创建订单所需数据！", //提示的信息
							type: "warning",
							offset: 225,
							duration: 3000,
							onClose: () => {
								this.$router.go(-1)
								return false
							}
						})
						return
					}
				})
				.catch(err => {
					const {
						code,
						message,
						data
					} = err
					this.$message.error(message)
				})
		},
		/**
		 * 处理结算订单数据
		 */
		handlePaymentData() {
			this.orderCreateData.delivery = {}
			this.orderCreateData.coupon = {}
			this.orderCreateData.buyer_message = {}

			this.orderCreateData.is_balance = 0
			this.orderCreateData.pay_password = ""

			var data = this.orderPaymentData
			Object.keys(data.shop_goods_list).forEach((key, index) => {
				let siteItem = data.shop_goods_list[key]

				// 店铺配送方式
				this.orderCreateData.delivery[key] = {}
				if (siteItem.express_type[0] != undefined) {
					this.orderCreateData.delivery[key].delivery_type = siteItem.express_type[0].name
					this.orderCreateData.delivery[key].delivery_type_name = siteItem.express_type[0].title
					this.orderCreateData.delivery[key].store_id = 0
					// 如果是门店配送
					if (siteItem.express_type[0].name == "store") {
						if (siteItem.express_type[0].store_list[0] != undefined) {
							this.orderCreateData.delivery[key].store_id = siteItem.express_type[0].store_list[0].store_id
						}
					}
				}

				// 店铺优惠券
				this.orderCreateData.coupon[key] = {}
				if (siteItem.coupon_list[0] != undefined) {
					this.orderCreateData.coupon[key].coupon_id = siteItem.coupon_list[0].coupon_id
					this.orderCreateData.coupon[key].coupon_money = siteItem.coupon_list[0].money

				}

				this.orderCreateData.buyer_message[key] = ""
			})

			if (this.orderPaymentData.is_virtual) {
				this.orderCreateData.member_address = {
					mobile: ""
				}
			}

			if (this.orderPaymentData.platform_coupon_list.length > 0) {
				this.orderPaymentData.platform_coupon_id = this.orderPaymentData.platform_coupon_list[0].platformcoupon_id;
				this.orderCreateData.platform_coupon_id = this.orderPaymentData.platform_coupon_list[0].platformcoupon_id;
				this.orderPaymentData.platform_coupon_money = this.orderPaymentData.platform_coupon_list[0].money;
				this.orderCreateData.platform_coupon_money = this.orderPaymentData.platform_coupon_list[0].money;
			}

			Object.assign(this.orderPaymentData, this.orderCreateData)

			this.orderCalculate()
		},

		/**
		 * 订单计算
		 */
		orderCalculate() {
			this.fullscreenLoading = true
			var data = this.$util.deepClone(this.orderCreateData)
			data.delivery = JSON.stringify(data.delivery)
			data.coupon = JSON.stringify(data.coupon)
			data.member_address = JSON.stringify(data.member_address)

			calculate(data)
				.then(res => {
					const {
						code,
						message,
						data
					} = res
					if (code >= 0) {
						this.orderPaymentData.delivery_money = res.data.delivery_money
						this.orderPaymentData.coupon_money = res.data.coupon_money
						this.orderPaymentData.invoice_money = res.data.invoice_money
						this.orderPaymentData.promotion_money = res.data.promotion_money
						this.orderPaymentData.order_money = res.data.order_money
						this.orderPaymentData.balance_money = res.data.balance_money
						this.orderPaymentData.pay_money = res.data.pay_money
						this.orderPaymentData.goods_money = res.data.goods_money

						Object.keys(res.data.shop_goods_list).forEach((key, index) => {
							let siteItem = res.data.shop_goods_list[key]

							this.orderPaymentData.shop_goods_list[key].pay_money = siteItem.pay_money
							this.orderPaymentData.shop_goods_list[key].coupon_money = siteItem.coupon_money;
							this.orderPaymentData.shop_goods_list[key].coupon_id = siteItem.coupon_id;
						})
					} else {
						this.$message({
							message: message, //提示的信息
							type: "warning",
							offset: 225,
							duration: 3000,
							onClose: () => {
								this.$router.go(-1)
								return false
							}
						})
						return
					}
					this.fullscreenLoading = false
				})
				.catch(err => {
					const {
						code,
						message,
						data
					} = err
					this.$message.error(message)
					this.fullscreenLoading = false
				})
		},

		/**
		 * 选择配送方式
		 */
		selectDeliveryType(data, siteId, deliveryData) {
			this.tempData = {
				delivery: this.$util.deepClone(this.orderPaymentData.delivery)
			}

			this.siteDelivery.site_id = siteId
			this.siteDelivery.data = deliveryData

			this.orderCreateData.delivery[siteId].delivery_type = data.name
			this.orderCreateData.delivery[siteId].delivery_type_name = data.title
			if (data.name == "store") {
				// 如果是门店配送
				let row = '';
				let that = this;
				data.store_list.forEach(function(e, i) {
					data.store_list[i]["store_address"] = e.full_address + e.address
				})

				if (!this.orderCreateData.delivery[siteId].store_id && data.store_list[0] != undefined) {
					this.orderCreateData.delivery[siteId].store_id = data.store_list[0].store_id
					this.orderCreateData.delivery[siteId].store_name = data.store_list[0].store_name
				}

				data.store_list.forEach(function(e, i) {
					if (that.orderCreateData.delivery[siteId].store_id == e.store_id) {
						row = e
					}
				})

				this.dialogStore = true
				this.storeList = data.store_list

				setTimeout(function() {
					that.setStore(row)
					that.storeRadio = row;
				}, 50)

			} else if (data.name == "local") {
				this.deliveryTime = true
				this.time = this.orderCreateData.delivery[siteId].buyer_ask_delivery_time;
			}
			Object.assign(this.orderPaymentData, this.orderCreateData)
			this.orderCalculate()
			this.$forceUpdate()
		},

		/**
		 * 设置选择自提点
		 * @param {Object} item
		 */
		setStore(row) {
			this.$refs.singleTable.setCurrentRow(row)
		},

		/**
		 * 选择自提点
		 * @param {Object} item
		 */
		selectStore(item) {
			if (!item) return;
			let store_id = item.store_id
			this.dialogStore = false
			this.orderCreateData.delivery[this.siteDelivery.site_id].store_id = store_id
			this.orderCreateData.delivery[this.siteDelivery.site_id].store_name = item.store_name
			Object.assign(this.orderPaymentData, this.orderCreateData)
			this.storeRadio = item
			this.orderCalculate()
			this.$forceUpdate()
		},

		/**
		 * 显示平台优惠券信息
		 * @param {Object} siteId
		 * @param {Object} couponData
		 */
		openPlatformCoupon() {
			let row = 0
			let that = this;
			this.dialogPlatcoupon = true

			this.orderPaymentData.platform_coupon_list.forEach(function(e, i) {
				if (e.platformcoupon_id == that.orderCreateData.platform_coupon_id) {
					row = e
				}
			})

			setTimeout(function() {
				that.setPlatformCurrent(row)
				that.platformCouponRadio = row;
			}, 50)
		},

		/**
		 * 取消选择优惠券
		 * @param {Object} item
		 */
		setPlatformCurrent(row) {
			this.$refs.platformCouponTable.setCurrentRow(row)
			if (row == undefined) {
				this.orderCalculate()
			}
		},
		/**
		 * 确认选择优惠券
		 * @param {Object} item
		 */
		savePlatformCoupon() {
			this.dialogPlatcoupon = false
			this.orderCalculate()
		},

		/**
		 * 选择优惠券
		 * @param {Object} item
		 */
		selectPlatformCoupon(item) {
			if (!item) {
				this.orderPaymentData.platform_coupon_id = 0;
				this.orderCreateData.platform_coupon_id = 0;
				this.orderPaymentData.platform_coupon_money = "0.00";
				this.orderCreateData.platform_coupon_money = "0.00";
				this.platformCouponRadio = '';
			} else {
				if (this.orderCreateData.platform_coupon_id != item.platformcoupon_id) {
					this.orderPaymentData.platform_coupon_id = item.platformcoupon_id;
					this.orderCreateData.platform_coupon_id = item.platformcoupon_id;
					this.orderPaymentData.platform_coupon_money = item.money;
					this.orderCreateData.platform_coupon_money = item.money;

					this.platformCouponRadio = item;
				} else {
					this.platformCouponRadio = '';
					this.orderPaymentData.platform_coupon_id = 0;
					this.orderCreateData.platform_coupon_id = 0;
					this.orderPaymentData.platform_coupon_money = "0.00";
					this.orderCreateData.platform_coupon_money = "0.00";
				}
			}
			Object.assign(this.orderPaymentData, this.orderCreateData);
			this.$forceUpdate()
		},

		/**
		 * 显示店铺优惠券信息
		 * @param {Object} siteId
		 * @param {Object} couponData
		 */
		openSiteCoupon(siteId, couponData) {
			this.tempData = {
				coupon: this.$util.deepClone(this.orderPaymentData.coupon)
			}
			let row = 0
			let that = this
			this.siteCoupon.site_id = siteId
			couponData.forEach(function(e, i) {
				if (e.at_least > 0) {
					couponData[i]["use"] = "满" + e.at_least + "可用"
				} else {
					couponData[i]["use"] = "任意金额可用"
				}
				couponData[i]["time"] = timeStampTurnTime(e.end_time)

				if (e.coupon_id == that.orderCreateData.coupon[that.siteCoupon.site_id].coupon_id) {
					row = e
				}
			})
			this.siteCoupon.data = couponData
			this.dialogCoupon = true

			setTimeout(function() {
				that.setCurrent(row)
				that.couponRadio = row;
			}, 50)
		},
		/**
		 * 选择优惠券
		 * @param {Object} item
		 */
		selectCoupon(item) {
			if (!item) {
				this.orderCreateData.coupon[this.siteCoupon.site_id].coupon_id = 0
				this.orderCreateData.coupon[this.siteCoupon.site_id].coupon_money = "0.00"
				this.couponRadio = '';
			} else {
				if (this.orderCreateData.coupon[this.siteCoupon.site_id].coupon_id != item.coupon_id) {
					this.orderCreateData.coupon[this.siteCoupon.site_id].coupon_id = item.coupon_id
					this.orderCreateData.coupon[this.siteCoupon.site_id].coupon_money = item.money
					this.couponRadio = item;
				} else {
					this.couponRadio = '';
					this.orderCreateData.coupon[this.siteCoupon.site_id].coupon_id = 0
					this.orderCreateData.coupon[this.siteCoupon.site_id].coupon_money = "0.00"
				}
			}
			Object.assign(this.orderPaymentData, this.orderCreateData)

			this.$forceUpdate()
		},

		/**
		 * 取消选择优惠券
		 * @param {Object} item
		 */
		setCurrent(row) {
			this.$refs.couponTable.setCurrentRow(row)
			if (row == undefined) {
				this.orderCalculate()
			}
		},
		/**
		 * 确认选择优惠券
		 * @param {Object} item
		 */
		saveCoupon() {
			this.dialogCoupon = false
			this.orderCalculate()
		},

		/**
		 * 显示店铺优惠信息
		 * @param {Object} data
		 */
		openSitePromotion(data) {
			this.sitePromotion = data
			if (this.promotionInfo) {
				this.promotionInfo = false
			} else {
				this.promotionInfo = true
			}
		},

		/**
		 * 是否使用余额
		 */
		useBalance(type) {
			if (this.orderCreateData.is_balance) this.orderCreateData.is_balance = 0
			else this.orderCreateData.is_balance = 1
			this.orderCalculate()
			this.$forceUpdate()
		},

		orderCreate() {
			if (this.verify()) {
				if (this.isSub) return
				this.isSub = true

				var loading = this.$loading({
					lock: true,
					text: "订单提交中...",
					spinner: "el-icon-loading",
					background: "rgba(0, 0, 0, 0.7)"
				})
				var data = this.$util.deepClone(this.orderCreateData)
				data.delivery = JSON.stringify(data.delivery)
				data.coupon = JSON.stringify(data.coupon)
				data.member_address = JSON.stringify(data.member_address)
				data.buyer_message = JSON.stringify(data.buyer_message)

				orderCreate(data)
					.then(res => {
						const {
							code,
							message,
							data
						} = res
						loading.close()
						if (code >= 0) {
							this.$store.dispatch("order/removeOrderCreateData", "")
							if (this.orderPaymentData.pay_money == 0) {
								this.$router.push({
									path: "/result",
									query: {
										code: data
									}
								})
							} else {
								this.$router.push({
									path: "/pay",
									query: {
										code: data
									}
								})
							}
						} else {
							this.$message({
								message: message,
								type: "warning"
							})
						}
					})
					.catch(err => {
						loading.close()
						this.isSub = false
						const {
							code,
							message,
							data
						} = err
						this.$message.error(message)
					})
			}
		},
		/**
		 * 订单验证
		 */
		verify() {
			if (this.orderPaymentData.is_virtual == 1) {
				if (!this.orderCreateData.member_address.mobile.length) {
					this.$message({
						message: "请输入您的手机号码",
						type: "warning"
					})
					return false
				}
				var reg = /^[1](([3][0-9])|([4][5-9])|([5][0-3,5-9])|([6][5,6])|([7][0-8])|([8][0-9])|([9][1,8,9]))[0-9]{8}$/
				if (!reg.test(this.orderCreateData.member_address.mobile)) {
					this.$message({
						message: "请输入正确的手机号码",
						type: "warning"
					})
					return false
				}
			}

			if (this.orderPaymentData.is_virtual == 0) {
				if (!this.orderPaymentData.member_address) {
					this.$message({
						message: "请先选择您的收货地址",
						type: "warning"
					})

					return false
				}

				let deliveryVerify = true

				for (let key in this.orderCreateData.delivery) {
					if (JSON.stringify(this.orderCreateData.delivery[key]) == "{}") {
						deliveryVerify = false
						this.$message({
							message: '店铺"' + this.orderPaymentData.shop_goods_list[key].site_name + '"未设置配送方式',
							type: "warning"
						})

						break
					}
					if (this.orderCreateData.delivery[key].delivery_type == "store" && this.orderCreateData.delivery[key].store_id ==
						0) {
						deliveryVerify = false
						this.$message({
							message: '店铺"' + this.orderPaymentData.shop_goods_list[key].site_name + '"没有可提货的门店,请选择其他配送方式',
							type: "warning"
						})

						break
					}
				}
				if (!deliveryVerify) return false
			}

			if (this.orderCreateData.is_balance == 1 && this.orderCreateData.pay_password == "") {
				this.dialogpay = true
				return false
			}
			return true
		},
		/**
		 * 支付密码输入
		 */
		input() {
			if (this.password.length == 6) {
				var loading = this.$loading({
					lock: true,
					text: "支付中",
					spinner: "el-icon-loading",
					background: "rgba(0, 0, 0, 0.7)"
				})

				checkpaypassword({
						pay_password: this.password
					})
					.then(res => {
						const {
							code,
							message,
							data
						} = res
						loading.close()
						if (code >= 0) {
							this.orderCreateData.pay_password = this.password
							this.orderCreate()
							this.dialogpay = false
						} else {
							this.$message({
								message: message,
								type: "warning"
							})
						}
					})
					.catch(err => {
						loading.close()
						const {
							code,
							message,
							data
						} = err
						this.$message.error(message)
					})
			}
		},

		textarea() {
			this.$forceUpdate()
		},
		bindTimeChange(time) {
			this.time = time
		},
		setDeliveryTime(site_id) {
			this.deliveryTime = false
			this.orderCreateData.delivery[site_id].buyer_ask_delivery_time = this.time;
		},
		imageError(index) {
			this.orderPaymentData.shop_goods_list.goods_list[index].sku_image = this.defaultGoodsImage
		},
		setPayPassword() {
			this.$router.pushToTab("/member/security");
		}

	}
}
