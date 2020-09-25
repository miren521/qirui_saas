import {
	checkpaypassword
} from "@/api/order/payment"
import {
	addressList,
	saveAddress,
	setDefault,
	deleteAddress,
	addressInfo
} from "@/api/member/member"
import {
	payment,
	calculate,
	orderCreate
} from "@/api/seckill"
import {
	getArea
} from "@/api/address"
import {
	mapGetters
} from "vuex"

export default {
	name: "seckill_payment",
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
			memberAddress: {}, //收货地址列表
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
				pay_password: ""
			},
			orderPaymentData: {
				goods_money: 0,
				pay_money: 0,
				shop_goods_list: {
					site_name: "",
					express_type: [],
					coupon_list: []
				},
				seckill_info: {
					name: ""
				},
				member_account: {
					balance: 0,
					is_pay_password: 0
				}
			},
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
			storeRadio:false,
		}
	},
	computed: {
		...mapGetters(["seckillOrderCreateData", "defaultGoodsImage", "city"])
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
			this.$nextTick(() => {
				this.$refs.form.resetFields()
			})
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
		saveAddress(formName) {
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
							url: "add"
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

						addMemberAddress(data)
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
			this.orderCreateData = this.seckillOrderCreateData

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
						this.orderPaymentData = res.data
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
			this.orderCreateData.is_balance = 0
			this.orderCreateData.pay_password = ""

			var data = this.orderPaymentData

			let h = new Date().getHours().toString()
			let m = new Date().getMinutes().toString()
			if (h.length == 1) {
				h = "0" + h
			}
			if (m.length == 1) {
				m = "0" + m
			}
			let nowTime = h + ":" + m

			if (data.shop_goods_list.local_config) {
				if (data.shop_goods_list.local_config.info && data.shop_goods_list.local_config.info.time_is_open == 1) {
					this.orderCreateData.delivery.showTimeBar = true
					this.orderCreateData.delivery.buyer_ask_delivery_time = nowTime
				} else {
					this.orderCreateData.delivery.showTimeBar = false
				}
			}

			if (data.shop_goods_list.express_type != undefined && data.shop_goods_list.express_type[0] != undefined) {
				var express_type = data.shop_goods_list.express_type
				this.orderCreateData.delivery.delivery_type = express_type[0].name
				this.orderCreateData.delivery.delivery_type_name = express_type[0].title
				this.orderCreateData.delivery.store_id = 0
				// 如果默认配送方式是门店配送
				if (express_type[0].name == "store") {
					if (express_type[0].store_list[0] != undefined) {
						this.orderCreateData.delivery.store_id = express_type[0].store_list[0].store_id
					}
				}
			}

			if (data.shop_goods_list.coupon_list != undefined && data.shop_goods_list.coupon_list[0] != undefined) {
				var coupon_list = data.shop_goods_list.coupon_list
				this.orderCreateData.coupon.coupon_id = coupon_list[0].coupon_id
				this.orderCreateData.coupon.coupon_money = coupon_list[0].money
			}

			if (this.orderPaymentData.is_virtual) {
				this.orderCreateData.member_address = {
					mobile: ""
				}
			}

			Object.assign(this.orderPaymentData, this.orderCreateData)
			this.orderCalculate()
		},

		/**
		 * 订单计算
		 */
		orderCalculate() {
			this.fullscreenLoading = true

			let siteId = this.orderPaymentData.shop_goods_list.site_id

			var deliveryObj = {}
			deliveryObj[siteId] = this.orderCreateData.delivery

			var messageObj = {}
			messageObj[siteId] = this.orderCreateData.buyer_message

			var data = this.$util.deepClone(this.orderCreateData)
			data.delivery = JSON.stringify(deliveryObj)
			data.buyer_message = JSON.stringify(messageObj)
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
		selectDeliveryType(data) {
			this.orderCreateData.delivery.delivery_type = data.name
			this.orderCreateData.delivery.delivery_type_name = data.title
			// 如果是门店配送
			if (data.name == "store") {
				data.store_list.forEach(function(e, i) {
					data.store_list[i]["store_address"] = e.full_address + e.address
				})

				if (data.store_list[0] != undefined) {
					this.orderCreateData.delivery.store_id = data.store_list[0].store_id
				}
				this.dialogStore = true
				this.storeList = data.store_list
			} else if (data.name == "local") {
				this.deliveryTime = true
			}
			Object.assign(this.orderPaymentData, this.orderCreateData)
			this.orderCalculate()
			this.$forceUpdate()
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

				let siteId = this.orderPaymentData.shop_goods_list.site_id

				var deliveryObj = {}
				deliveryObj[siteId] = this.orderCreateData.delivery

				var messageObj = {}
				messageObj[siteId] = this.orderCreateData.buyer_message

				var data = this.$util.deepClone(this.orderCreateData)
				data.delivery = JSON.stringify(deliveryObj)
				data.buyer_message = JSON.stringify(messageObj)
				data.member_address = JSON.stringify(data.member_address)

				orderCreate(data)
					.then(res => {
						const {
							code,
							message,
							data
						} = res
						loading.close()
						if (code >= 0) {
							this.$store.dispatch("order/removeSeckillOrderCreateData", "")
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
							this.$message.error(message)
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
			this.orderCreateData.delivery.buyer_ask_delivery_time = this.time
		},
		setDeliveryTime() {
			this.deliveryTime = false
			this.orderCreateData.delivery.buyer_ask_delivery_time = this.time
		},
		imageError(index) {
			this.orderPaymentData.shop_goods_list.goods_list[index].sku_image = this.defaultGoodsImage
		},
		setPayPassword(){
			this.$router.pushToTab("/member/security");
		}
	}
}
