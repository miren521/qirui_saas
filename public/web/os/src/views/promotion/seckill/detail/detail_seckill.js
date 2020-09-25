import {
	aftersale,
	modifyClicks,
	addGoodsbrowse,
	goodsQrcode
} from "@/api/goods/goods"
import {
	goodsSkuDetail
} from "@/api/seckill"
import {
	isCollect,
	addCollect,
	deleteCollect
} from "@/api/goods/goods_collect"
import {
	goodsEvaluateList
} from "@/api/goods/evaluate"
import {
	mapGetters
} from "vuex"
import CountDown from "vue2-countdown"
import {
	shopIsSubscribe,
	addShopSubscribe,
	deleteShopSubscribe
} from "@/api/shop"
import {
	getArea
} from "@/api/address"

export default {
	data: () => {
		return {
			id: 0,
			skuId: 0,
			loading: true,

			picZoomUrl: "",
			thumbPosition: 0,
			// 是否可以移动
			moveThumbLeft: false,
			moveThumbRight: false,

			// 商品详情
			goodsSkuDetail: {
				video_url: ""
			},
			seckillText: "距离结束仅剩",
			seckillTimeMachine: {
				currentTime: 0,
				startTime: 0,
				endTime: 0
			},
			qrcode: "",

			// 店铺详情
			shopInfo: {},
			whetherCollection: 0,
			score: 0,

			//评价
			currentPage: 1,
			pageSize: 10,
			total: 0,
			goodsEvaluateList: [],

			service: null,

			number: 1,
			tabName: "detail",
			playerOptions: {
				playbackRates: [0.5, 1.0, 1.5, 2.0, 3.0], // 可选的播放速度
				autoplay: false, // 如果为true,浏览器准备好时开始回放。
				muted: false, // 默认情况下将会消除任何音频。
				loop: false, // 是否视频一结束就重新开始。
				preload: "auto", // 建议浏览器在<video>加载元素后是否应该开始下载视频数据。auto浏览器选择最佳行为,立即开始加载视频（如果浏览器支持）
				language: "zh-CN",
				aspectRatio: "16:9", // 将播放器置于流畅模式，并在计算播放器的动态大小时使用该值。值应该代表一个比例 - 用冒号分隔的两个数字（例如"16:9"或"4:3"）
				fluid: true, // 当true时，Video.js player将拥有流体大小。换句话说，它将按比例缩放以适应其容器。
				sources: [{
					type: "video/mp4", // 类型
					src: "" // url地址
				}],
				poster: "", // 封面地址
				notSupportedMessage: "此视频暂无法播放，请稍后再试", // 允许覆盖Video.js无法播放媒体源时显示的默认信息。
				controlBar: {
					timeDivider: true, // 当前时间和持续时间的分隔符
					durationDisplay: true, // 显示持续时间
					remainingTimeDisplay: true, // 是否显示剩余时间功能
					fullscreenToggle: true // 是否显示全屏按钮
				}
			},
			switchMedia: "img",

			// 是否关注店铺
			hasFollow: false,

			// 省市区县
			provinceArr: {},
			cityArr: {},
			districtArr: {},

			// 省市区县 id
			currTabAddres: "province",
			hideRegion: false,
			selectedAddress: {}
		}
	},
	components: {
		CountDown
	},
	created() {
		this.id = this.$route.path.replace("/promotion/seckill-", "")
		if (this.addonIsExit && this.addonIsExit.seckill != 1) {
			this.$message({
				message: '秒杀插件未安装',
				type: 'warning',
				duration: 2000,
				onClose: () => {
					this.$route.push('/');
				}
			});
		} else {
			this.getGoodsSkuDetail()
		}
	},
	computed: {
		...mapGetters(["token", "defaultHeadImage", "defaultShopImage", "addonIsExit", 'locationRegion'])
	},
	watch: {
		$route(curr) {
			this.id = curr.params.pathMatch
			if (this.addonIsExit && this.addonIsExit.seckill == 1) {
				this.getGoodsSkuDetail()
			} else {
				this.$message({
					message: '秒杀插件未安装',
					type: 'warning',
					duration: 2000,
					onClose: () => {
						this.$route.push('/');
					}
				});
			}
		},
		addonIsExit() {
			if (this.addonIsExit.seckill != 1) {
				this.$message({
					message: '秒杀插件未安装',
					type: 'warning',
					duration: 2000,
					onClose: () => {
						this.$route.push('/');
					}
				});
			}
		}
	},
	methods: {
		tabChange(tab, event) {},
		bundlingChange(tab, event) {},
		getGoodsSkuDetail() {
			goodsSkuDetail({
					id: this.id
				})
				.then(res => {
					let data = res.data
					if (data.goods_sku_detail != null) {
						this.goodsSkuDetail = data.goods_sku_detail
						this.shopInfo = data.shop_info
						let num = (Number(this.shopInfo.shop_desccredit) + Number(this.shopInfo.shop_servicecredit) + Number(this.shopInfo
							.shop_deliverycredit)) / 3
						this.score = Number(num.toFixed(1));

						this.skuId = this.goodsSkuDetail.sku_id

						//秒杀倒计时
						let time = new Date(res.timestamp * 1000)
						let currentTime = time.getHours() * 60 * 60 + time.getMinutes() * 60 + time.getSeconds()
						if (this.goodsSkuDetail.seckill_start_time <= currentTime && currentTime < this.goodsSkuDetail.seckill_end_time) {
							let endTime = parseInt(time.getTime() / 1000) + (this.goodsSkuDetail.seckill_end_time - currentTime)
							this.seckillTimeMachine = {
								currentTime: res.timestamp,
								startTime: res.timestamp,
								endTime: endTime
							}
						} else if (this.goodsSkuDetail.seckill_start_time > currentTime && currentTime < this.goodsSkuDetail.seckill_end_time) {
							this.$message({
								message: '限时秒杀活动还未开始',
								type: 'warning',
								duration: 2000,
								onClose: () => {
									this.$router.push("/sku-" + this.goodsSkuDetail.sku_id)
								}
							});
						} else if (currentTime < this.goodsSkuDetail.seckill_start_time && currentTime > this.goodsSkuDetail.seckill_end_time) {
							this.$message({
								message: '限时秒杀活动已结束',
								type: 'warning',
								duration: 2000,
								onClose: () => {
									this.$router.push("/sku-" + this.goodsSkuDetail.sku_id)
								}
							});
						}

						this.goodsSkuDetail.sku_images = this.goodsSkuDetail.sku_images.split(",")

						//媒体
						if (this.goodsSkuDetail.video_url) {
							this.switchMedia = "video"
							this.playerOptions.poster = img(this.goodsSkuDetail.sku_images[0])
							this.playerOptions.sources[0].src = img(this.goodsSkuDetail.video_url)
						}

						this.picZoomUrl = this.goodsSkuDetail.sku_images[0]

						this.goodsSkuDetail.unit = this.goodsSkuDetail.unit || "件"

						// 当前商品SKU规格
						if (this.goodsSkuDetail.sku_spec_format) this.goodsSkuDetail.sku_spec_format = JSON.parse(this.goodsSkuDetail.sku_spec_format)

						// 商品属性
						if (this.goodsSkuDetail.goods_attr_format) {
							let goods_attr_format = JSON.parse(this.goodsSkuDetail.goods_attr_format)
							this.goodsSkuDetail.goods_attr_format = JSON.parse(this.goodsSkuDetail.goods_attr_format)
							this.goodsSkuDetail.goods_attr_format = util.unique(this.goodsSkuDetail.goods_attr_format, "attr_id")
							for (var i = 0; i < this.goodsSkuDetail.goods_attr_format.length; i++) {
								for (var j = 0; j < goods_attr_format.length; j++) {
									if (this.goodsSkuDetail.goods_attr_format[i].attr_id == goods_attr_format[j].attr_id && this.goodsSkuDetail.goods_attr_format[
											i].attr_value_id != goods_attr_format[j].attr_value_id) {
										this.goodsSkuDetail.goods_attr_format[i].attr_value_name += "、" + goods_attr_format[j].attr_value_name
									}
								}
							}
						}

						// 商品SKU格式
						if (this.goodsSkuDetail.goods_spec_format) this.goodsSkuDetail.goods_spec_format = JSON.parse(this.goodsSkuDetail
							.goods_spec_format)

						window.document.title = `${this.goodsSkuDetail.sku_name} - ${window.document.title}`

						this.loading = false
					} else {
						this.$router.push("/")
					}
				})
				.then(res => {
					if (this.token != "") {
						this.getWhetherCollection()
						this.isFollow()
					}
					this.getAftersale()
					this.modifyGoodsInfo()
					this.getGoodsEvaluate()
					this.getGoodsQrcode()
					this.getAddress('province', null, true);
					if (!this.locationRegion) {

						this.$store.commit("app/SET_LOCATION_REGION", {
							"level_1": {
								"id": 110000,
								"pid": 0,
								"name": "北京市",
								"shortname": "北京",
								"longitude": "116.40529",
								"latitude": "39.904987",
								"level": 1,
								"sort": 1,
								"status": 1,
								"default_data": 1
							},
							"level_2": {
								"id": 110100,
								"pid": 110000,
								"name": "北京市",
								"shortname": "北京",
								"longitude": "116.40529",
								"latitude": "39.904987",
								"level": 2,
								"sort": 1,
								"status": 1,
								"default_data": 1
							},
							"level_3": {
								"id": 110101,
								"pid": 110100,
								"name": "东城区",
								"shortname": "东城",
								"longitude": "116.418755",
								"latitude": "39.917545",
								"level": 3,
								"sort": 3,
								"status": 1,
								"default_data": 1
							}
						});
					}

					this.selectedAddress = this.locationRegion;
					this.provinceId = this.selectedAddress.level_1.id
					this.getAddress('city', null, true, () => {
						this.cityId = this.selectedAddress.level_2.id
						if (this.cityId) this.getAddress('district', null, true)
					});
				})
				.catch(res => {
					this.loading = false
					this.$router.push("/")
				})
		},
		changeThumbImg(tag) {
			if (this.goodsSkuDetail.sku_images.length < 4) return
			let page = this.goodsSkuDetail.sku_images.length % 4 // 可见数量4个
			let position = 88
			if (page == 0) page = this.goodsSkuDetail.sku_images.length - 4 // 可见数量4个
			else if (page != 0 && page != 1 && page < 2) return

			if (tag == "prev") {
				if (this.thumbPosition != 0 && Math.round(this.thumbPosition, 2) != position) {
					this.thumbPosition += position
					// this.moveThumbLeft = true;
				} else {
					// this.moveThumbLeft = false;
				}
			} else if (tag == "next") {
				if (Math.round(this.thumbPosition, 2) != -Math.round(position * page, 2)) {
					this.thumbPosition -= position
					// this.moveThumbRight = true;
				} else {
					// this.moveThumbRight = false;
				}
			}
		},
		//获取用户是否关注
		getWhetherCollection() {
			isCollect({
				goods_id: this.goodsSkuDetail.goods_id
			}).then(res => {
				this.whetherCollection = res.data
			})
		},
		editCollection() {
			//未关注添加关注
			if (this.whetherCollection == 0) {
				addCollect({
					sku_id: this.skuId,
					site_id: this.goodsSkuDetail.site_id,
					goods_id: this.goodsSkuDetail.goods_id,
					category_id: this.goodsSkuDetail.category_id,
					sku_name: this.goodsSkuDetail.sku_name,
					sku_price: this.goodsSkuDetail.discount_price,
					sku_image: this.goodsSkuDetail.sku_image
				}).then(res => {
					var data = res.data
					if (data > 0) {
						this.whetherCollection = 1
						this.goodsSkuDetail.collect_num++
					}
				})
			} else {
				//已关注取消关注
				deleteCollect({
					goods_id: this.goodsSkuDetail.goods_id
				}).then(res => {
					var data = res.data
					if (data > 0) {
						this.whetherCollection = 0
						this.goodsSkuDetail.collect_num--
					}
				})
			}
		},
		// 售后保障
		getAftersale() {
			aftersale({}).then(res => {
				if (res.code == 0 && res.data) {
					let data = res.data.content
					if (res.data.content) this.service = res.data.content
				}
			})
		},
		//更新商品信息
		modifyGoodsInfo() {
			//更新商品点击量
			modifyClicks({
				sku_id: this.skuId,
				site_id: this.goodsSkuDetail.site_id
			})

			//添加足迹
			addGoodsbrowse({
				goods_id: this.goodsSkuDetail.goods_id,
				sku_id: this.skuId,
				category_id: this.goodsSkuDetail.category_id,
				category_id_1: this.goodsSkuDetail.category_id_1,
				category_id_2: this.goodsSkuDetail.category_id_2,
				category_id_3: this.goodsSkuDetail.category_id_3,
				site_id: this.goodsSkuDetail.site_id
			})
		},
		// 商品二维码
		getGoodsQrcode() {
			goodsQrcode({
				sku_id: this.skuId
			}).then(res => {
				let data = res.data
				if (data.path.h5.img) this.qrcode = img(data.path.h5.img)
			})
		},
		// 商品评价列表
		getGoodsEvaluate() {
			goodsEvaluateList({
				page: this.currentPage,
				page_size: this.pageSize,
				goods_id: this.goodsSkuDetail.goods_id
			}).then(res => {
				let list = []
				let msg = res.message
				if (res.code == 0 && res.data) {
					list = res.data.list
					this.total = res.data.count
				}

				for (var i = 0; i < list.length; i++) {
					// 1好评2中评3差评
					if (list[i].explain_type == 1) {
						list[i].star = 5
					} else if (list[i].explain_type == 2) {
						list[i].star = 3
					} else if (list[i].explain_type == 3) {
						list[i].star = 1
					}
					if (list[i].images) {
						list[i].images = list[i].images.split(",")
						list[i].imagesFormat = []
						for (var k = 0; k < list[i].images.length; k++) {
							list[i].imagesFormat.push(img(list[i].images[k]))
						}
					}

					if (list[i].again_images) {
						list[i].again_images = list[i].again_images.split(",")
						list[i].againImagesFormat = []
						for (var j = 0; j < list[i].again_images.length; j++) {
							list[i].againImagesFormat.push(img(list[i].again_images[j]))
						}
					}
					if (list[i].is_anonymous == 1) list[i].member_name = list[i].member_name.replace(list[i].member_name.substring(1,
						list[i].member_name.length - 1), "***")
				}
				this.goodsEvaluateList = list
			})
		},
		// 图片加载失败
		imageErrorEvaluate(index) {
			this.goodsEvaluateList[index].member_headimg = this.defaultHeadImage
		},
		handlePageSizeChange(size) {
			this.pageSize = size
			this.getGoodsEvaluate()
		},
		handleCurrentPageChange(page) {
			this.currentPage = page
			this.getGoodsEvaluate()
		},
		changeNum(tag) {
			if (this.goodsSkuDetail.stock == 0) return

			var stock = this.goodsSkuDetail.stock
			var min = 1

			if (tag == "+") {
				// 加
				if (this.number < stock) {
					this.number++
				} else {
					return
				}
			} else if (tag == "-") {
				// 减
				if (this.number > min) {
					this.number -= 1
				} else {
					return
				}
			}
		},
		blur() {
			let newNumber = parseInt(this.number)
			this.number = 0
			setTimeout(() => {
				this.number = newNumber
			}, 0)
		},
		//输入数量
		keyInput(flag, callback) {
			setTimeout(() => {
				var stock = this.goodsSkuDetail.stock

				// 库存为0
				if (this.goodsSkuDetail.stock == 0) {
					this.number = 0
					return
				}

				// 防止空
				if (flag && this.number.length == 0) this.number = 1

				// 防止输入0和负数、非法输入
				if (flag && (this.number <= 0 || isNaN(this.number))) this.number = 1

				if (this.number > stock) {
					this.number = stock
				}

				if (flag) this.number = parseInt(this.number)
				if (callback) callback()
			}, 0)
		},
		// 播放回调
		onPlayerPlay(player) {},
		// 暂停回调
		onPlayerPause(player) {},
		// 视频播完回调
		onPlayerEnded(player) {},
		// DOM元素上的readyState更改导致播放停止
		onPlayerWaiting(player) {},
		// 已开始播放回调
		onPlayerPlaying(player) {},
		// 当播放器在当前播放位置下载数据时触发
		onPlayerLoadeddata(player) {},
		// 当前播放位置发生变化时触发。
		onPlayerTimeupdate(player) {},
		//媒体的readyState为HAVE_FUTURE_DATA或更高
		onPlayerCanplay(player) {},
		//媒体的readyState为HAVE_ENOUGH_DATA或更高。这意味着可以在不缓冲的情况下播放整个媒体文件。
		onPlayerCanplaythrough(player) {},
		//播放状态改变回调
		playerStateChanged(playerCurrentState) {},
		//将侦听器绑定到组件的就绪状态。与事件监听器的不同之处在于，如果ready事件已经发生，它将立即触发该函数。。
		playerReadied(player) {},
		// 立即购买
		buyNow() {
			//纠正数量
			this.keyInput(true, () => {
				if (this.goodsSkuDetail.stock == 0) {
					this.$message({
						message: "商品已售罄",
						type: "warning"
					})
					return
				}

				if (this.number.length == 0 || this.number == 0) {
					this.$message({
						message: "购买数量不能为0",
						type: "warning"
					})
					return
				}

				// 团购
				var data = {
					seckill_goods_id: this.goodsSkuDetail.id,
					num: this.number
				}

				this.$store.dispatch("order/setSeckillOrderCreateData", data)
				this.$router.push({
					path: "/promotion/seckill_payment"
				})
			})
		},
		countDownS_cb() {},
		countDownE_cb() {
			this.seckillText = "活动已结束"
			this.$message({
				message: '限时秒杀活动已结束',
				type: 'warning',
				duration: 2000,
				onClose: () => {
					this.$router.push("/sku-" + this.goodsSkuDetail.sku_id)
				}
			});
		},
		//是否关注
		isFollow() {
			shopIsSubscribe({
				site_id: this.goodsSkuDetail.site_id
			}).then(res => {
				if (res.code == 0) {
					this.hasFollow = res.data
				}
			})
		},
		follow() {
			if (this.hasFollow) {
				deleteShopSubscribe({
					site_id: this.goodsSkuDetail.site_id
				}).then(res => {
					if (res.code == 0 && res.data) {
						this.hasFollow = !this.hasFollow
						this.$message({
							message: "取消成功",
							type: "success"
						})
					}
				})
			} else {
				addShopSubscribe({
					site_id: this.goodsSkuDetail.site_id
				}).then(res => {
					if (res.code == 0 && res.data) {
						this.hasFollow = !this.hasFollow
						this.$message({
							message: "关注成功",
							type: "success"
						})
					}
				})
			}
		},
		// 图片加载失败
		imageErrorSpec(index) {
			this.goodsSkuDetail.sku_images[index] = this.defaultGoodsImage
			this.picZoomUrl = this.defaultGoodsImage
		},
		/**
		 * 获取地址
		 * @param {Object} type
		 * @param {Object} item
		 * @param {Object} first 是否第一次
		 */
		getAddress(type, item, first, callback) {
			let pid = 0
			switch (type) {
				case 'province':
					//加载省
					pid = 0
					break
				case 'city':
					//加载市
					if (item) {
						this.provinceId = item.id
					}
					pid = this.provinceId
					this.cityArr = {}
					this.districtArr = {}
					break
				case 'district':
					//加载区县
					if (item) this.cityId = item.id
					pid = this.cityId
					this.districtArr = {}
					break
			}
			if (item) {
				if (item.level <= 2) {
					let len = item.level;
					for (let i = len; i <= 3; i++) {
						delete this.selectedAddress['level_' + i];
					}
				}
				this.selectedAddress['level_' + item.level] = item;
			}

			if (!first) this.$store.commit("app/SET_LOCATION_REGION", this.selectedAddress)
			this.$forceUpdate();
			if (type == 'community') {
				this.hideRegion = true;
				setTimeout(() => {
					this.hideRegion = false;
				}, 10);
				return;
			}

			getArea({
					pid: pid
				})
				.then(res => {
					const {
						code,
						data
					} = res;
					if (data) {
						switch (type) {
							case 'province':
								//加载省
								this.provinceArr = data
								break
							case 'city':
								//加载市
								this.cityArr = data
								break
							case 'district':
								//加载区县
								this.districtArr = data
								break
						}
						this.currTabAddres = type

						if (callback) callback();
					}
				})
				.catch(err => {})
		}
	}
}
