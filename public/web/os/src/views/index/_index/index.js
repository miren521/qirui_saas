import Config from "@/utils/config"
import {
	login
} from "@/api/auth/login"
import {
	adList
} from "@/api/website"
import {
	noticesList
} from "@/api/cms/notice"
import {
	floors,
	floatLayer,
	apiDefaultSearchWords
} from "@/api/pc"
import {
	mapGetters
} from "vuex"
import {
	couponList,
	receiveCoupon
} from "@/api/coupon"
import {
	goodsPage,
	timeList
} from "@/api/seckill"
import CountDown from "vue2-countdown"

export default {
	name: "index",
	components: {
		CountDown
	},
	data: () => {
		return {
			loadingAd: true,
			loadingFloor: true,
			adList: [],
			adLeftList: [],
			adRightList: [],
			noticeList: [],
			couponList: [],
			floorList: [],
			floatLayer: {
				is_show: 0,
				link: {
					url: ""
				}
			},
			indexFloatLayerNum: 0,
			shopApplyUrl: Config.baseUrl + "/shop/login/register",
			shopCenterUrl: Config.baseUrl + "/shop/index/index",
			storeUrl: Config.baseUrl + "/store",
			isSub: false,
			siteId: 0,
			listData: [],
			seckillTimeMachine: {
				currentTime: 0,
				startTime: 0,
				endTime: 0
			},
			seckillText: "距离结束",
			backgroundColor: "", // 顶部banner背景颜色
			// 悬浮搜索
			searchType: "goods",
			searchTypeText: "商品",
			keyword: "",
			defaultSearchWords: "",
			isShow: false
		}
	},
	watch: {
		searchType() {
			this.searchTypeText = this.searchType == "goods" ? "商品" : "店铺"
		}
	},
	created() {
		this.getAdList()
		this.getNotice()
		this.getBigAdList()
		this.getSmallAdList()
		this.getFloors()
		this.getFloatLayer()
		this.$store.dispatch("site/siteInfo") // 站点信息
		this.getDefaultSearchWords() // 默认搜索类型
		this.getTimeList()
		this.getCanReceiveCouponQuery()
	},
	mounted() {
		window.addEventListener("scroll", this.handleScroll)
	},
	computed: {
		...mapGetters(["defaultHeadImage", "addonIsExit", "defaultGoodsImage", "member", "siteInfo", "cartCount"]),
		optionLeft() {
			return {
				direction: 2,
				limitMoveNum: 2
			}
		}
	},
	methods: {
		countDownS_cb() {},
		countDownE_cb() {
			this.seckillText = "活动已结束"
		},
		getAdList() {
			adList({
					keyword: "NS_PC_INDEX"
				})
				.then(res => {
					this.adList = res.data.adv_list
					for (let i = 0; i < this.adList.length; i++) {
						if (this.adList[i].adv_url) this.adList[i].adv_url = JSON.parse(this.adList[i].adv_url)
					}
					this.backgroundColor = this.adList[0].background
					this.loadingAd = false
				})
				.catch(err => {
					this.loadingAd = false
				})
		},
		handleChange(curr, pre) {
			this.backgroundColor = this.adList[curr].background
		},
		/**
		 * 广告位大图
		 */
		getBigAdList() {
			adList({
					keyword: "NS_PC_INDEX_MID_LEFT"
				})
				.then(res => {
					this.adLeftList = res.data.adv_list
					for (let i = 0; i < this.adLeftList.length; i++) {
						if (this.adLeftList[i].adv_url) this.adLeftList[i].adv_url = JSON.parse(this.adLeftList[i].adv_url)
					}
					this.loadingAd = false
				})
				.catch(err => {
					this.loadingAd = false
				})
		},
		/**
		 * 广告位小图
		 */
		getSmallAdList() {
			adList({
					keyword: "NS_PC_INDEX_MID_RIGHT"
				})
				.then(res => {
					this.adRightList = res.data.adv_list
					for (let i = 0; i < this.adRightList.length; i++) {
						if (this.adRightList[i].adv_url) this.adRightList[i].adv_url = JSON.parse(this.adRightList[i].adv_url)
					}
					this.loadingAd = false
				})
				.catch(err => {
					this.loadingAd = false
				})
		},
		getNotice() {
			noticesList({
					page: 1,
					page_size: 5,
					receiving_type: "web"
				})
				.then(res => {
					this.noticeList = res.data.list
				})
				.catch(err => err)
		},
		//获取优惠券列表
		getCanReceiveCouponQuery() {
			couponList({
					activeName: "second",
					site_id: this.siteId
				})
				.then(res => {
					let data = res.data
					this.couponList = []
					if (data != null) {
						for (let i = 0; i < data.list.length; i++) {
							if (i < 5) {
								this.couponList.push(data.list[i])
							}
						}
						this.couponList.forEach(v => {
							v.useState = 0
						})
					}
				})
				.catch(err => {})
		},
		/**
		 * 领取优惠券
		 */
		receiveCoupon(item, index) {
			if (this.isSub) return
			this.isSub = true

			var data = {
				site_id: item.site_id,
				activeName: "second",
				platformcoupon_type_id: item.platformcoupon_type_id
			}

			receiveCoupon(data)
				.then(res => {
					var data = res.data
					let msg = res.message
					if (res.code == 0) {
						msg = "领取成功"
						this.$message({
							message: msg,
							type: "success"
						})
					} else {
						this.$message({
							message: msg,
							type: "warning"
						})
					}
					let list = this.couponList
					if (res.data.is_exist == 1) {
						for (let i = 0; i < list.length; i++) {
							if (list[i].platformcoupon_type_id == item.platformcoupon_type_id) {
								list[i].useState = 1
							}
						}
					} else {
						for (let i = 0; i < list.length; i++) {
							if (list[i].platformcoupon_type_id == item.platformcoupon_type_id) {
								list[i].useState = 2
							}
						}
					}

					this.isSub = false
					this.$forceUpdate()
				})
				.catch(err => {
					this.$message.error(err.message)
					this.isSub = false
				})
		},
		/**
		 * 点击优惠券
		 */
		couponTap(item, index) {
			if (item.useState == 0) this.receiveCoupon(item, index)
			else this.toGoodsList(item)
		},
		/**
		 * 去购买
		 */
		toGoodsList(item) {
			if (item.use_scenario != 1) {
				this.$router.push({
					path: "/list",
					query: {
						platformcouponTypeId: item.platformcoupon_type_id
					}
				})
			} else {
				this.$router.push("/list")
			}
		},
		/**
		 * 限时秒杀
		 */
		getTimeList() {
			timeList()
				.then(res => {
					if (res.code == 0 && res.data) {
						let time = new Date(res.timestamp * 1000)
						let currentTimes = time.getHours() * 60 * 60 + time.getMinutes() * 60 + time.getSeconds()

						res.data.list.forEach((v, k) => {
							if (v.seckill_start_time <= currentTimes && currentTimes < v.seckill_end_time) {
								let seckillId = v.seckill_id
								this.getGoodsList(seckillId)

								let endTime = parseInt(time.getTime() / 1000) + (v.seckill_end_time - currentTimes)
								this.seckillTimeMachine = {
									currentTime: res.timestamp,
									startTime: res.timestamp,
									endTime: endTime
								}
							}
						})
					}
				})
				.catch(err => {
					this.$message.error(err.message)
				})
		},
		/**
		 * 秒杀商品
		 */
		getGoodsList(id) {
			goodsPage({
					page_size: 0,
					seckill_id: id,
					site_id: this.siteId
				})
				.then(res => {
					if (res.code == 0 && res.data.list) {
						this.listData = res.data.list
					}
				})
				.catch(err => {})
		},
		/**
		 * 图片加载失败
		 */
		imageError(index) {
			this.listData[index].sku_image = this.defaultGoodsImage
		},
		/**
		 * 图片加载失败
		 */
		adLeftImageError(index) {
			this.adLeftList[index].adv_image = this.defaultGoodsImage
		},
		/**
		 * 图片加载失败
		 */
		adRightImageError(index) {
			this.adRightList[index].adv_image = this.defaultGoodsImage
		},
		getFloors() {
			floors()
				.then(res => {
					this.floorList = res.data;
				})
				.catch(err => {
					console.log(err)
				})
		},
		getFloatLayer() {
			floatLayer()
				.then(res => {
					if (res.code == 0 && res.data) {
						this.floatLayer = res.data
						this.floatLayer.link = JSON.parse(this.floatLayer.url)
						if (this.$store.state.app.indexFloatLayerNum >= this.floatLayer.number) {
							this.floatLayer.is_show = !this.floatLayer.is_show
						}
					}
				})
				.catch(err => err)
		},
		closeFloat() {
			this.floatLayer.is_show = !this.floatLayer.is_show
			this.indexFloatLayerNum = this.$store.state.app.indexFloatLayerNum
			this.indexFloatLayerNum++
			this.$store.commit("app/SET_FLOAT_LAYER", this.indexFloatLayerNum)
		},
		// 监听滚动条
		handleScroll() {
			var scrollTop = window.pageYOffset || document.documentElement.scrollTop || document.body.scrollTop

			if (scrollTop >= 680) {
				this.isShow = true
			} else {
				this.isShow = false
			}
		},
		destroyed() {
			// 离开该页面需要移除这个监听的事件，不然会报错
			window.removeEventListener("scroll", this.handleScroll)
		},
		/**
		 * 默认搜索类型（goods/shop）
		 */
		getDefaultSearchWords() {
			apiDefaultSearchWords({}).then(res => {
				if (res.code == 0 && res.data.words) {
					this.defaultSearchWords = res.data.words
				}
			})
		},
		handleCommand(command) {
			this.searchType = command
		},
		search() {
			if (this.searchType == "goods") this.$router.push({
				path: "/list",
				query: {
					keyword: this.keyword
				}
			})
			else this.$router.push({
				path: "/street",
				query: {
					keyword: this.keyword
				}
			})
		}
	}
}
