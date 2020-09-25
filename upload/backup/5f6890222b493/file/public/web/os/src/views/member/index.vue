<template>
	<div class="box">
		<div class="null-page" v-show="yes"></div>

		<div class="member-index" v-loading="loading">
			<div class="member-top">
				<div class="info-wrap">
					<div class="info-top">
						<div class="avtar">
							<router-link to="/member/info">
								<img v-if="member.headimg" :src="$img(member.headimg)" @error="member.headimg = defaultHeadImage" />
								<img v-else :src="$img(defaultHeadImage)" />
							</router-link>
						</div>
						<div class="member-wrap">
							<template v-if="member">
								<div class="name member-name" v-if="member.nickname">
									<router-link to="/member/info">{{ member.nickname }}</router-link>
								</div>
								<div class="level" v-if="member.member_level_name">{{ member.member_level_name }}</div>
								<div class="growth">
									成长值：
									<el-progress :text-inside="true" :stroke-width="10" :percentage="progress" :show-text="false"></el-progress>
									<div></div>
								</div>
							</template>
							<div class="no-login name" v-else>未登录</div>
						</div>
					</div>
					<div class="account">
						<div class="content">
							<div class="item">
								<router-link to="/member/my_coupon" class="item-content">
									<img src="@/assets/images/coupon.png" alt />
									<div class="name">优惠券</div>
									<div class="num" v-if="member.member_id && couponNum">{{ couponNum }}</div>
									<div class="num" v-else>0</div>
								</router-link>
							</div>

							<div class="item">
								<router-link to="/member/my_point" class="item-content">
									<img src="@/assets/images/point.png" alt />
									<div class="name">积分</div>
									<div class="num" v-if="member.point">{{ member.point }}</div>
									<div class="num" v-else>0</div>
								</router-link>
							</div>
							<div class="item">
								<router-link to="/member/account" class="item-content">
									<img src="@/assets/images/balance.png" alt />
									<div class="name">余额</div>
									<div class="num" v-if="member.balance || member.balance_money">
										{{ (parseFloat(member.balance) + parseFloat(member.balance_money)).toFixed(2) }}
									</div>
									<div class="num" v-else>0</div>
								</router-link>
							</div>
						</div>
					</div>
				</div>
				<div class="collection">
					<div class="title">我的关注</div>
					<div class="xian"></div>
					<div class="item-wrap">
						<div class="item">
							<div class="num">{{ shopTotal }}</div>
							<div class="collect">店铺关注</div>
						</div>
						<div class="item">
							<div class="num">{{ goodsTotal }}</div>
							<div class="collect">商品关注</div>
						</div>
					</div>
				</div>
			</div>
			<div class="member-bottom">
				<div class="my-order">
					<div class="order-title">我的订单</div>
					<div class="xian"></div>
					<div class="order-item">
						<router-link to="/member/order_list?status=waitpay" class="item">
							<i class="iconfont icondaifukuan"></i>
							<div v-if="orderNum.waitPay" class="order-num">{{ orderNum.waitPay }}</div>
							<div class="name">待付款</div>
						</router-link>
						<router-link to="/member/order_list?status=waitsend" class="item">
							<i class="iconfont icondaifahuo"></i>
							<div v-if="orderNum.readyDelivery" class="order-num">{{ orderNum.readyDelivery }}</div>
							<div class="name">待发货</div>
						</router-link>
						<router-link to="/member/order_list?status=waitconfirm" class="item">
							<i class="iconfont icontubiaolunkuo-"></i>
							<div v-if="orderNum.waitDelivery" class="order-num">{{ orderNum.waitDelivery }}</div>
							<div class="name">待收货</div>
						</router-link>
						<router-link to="/member/order_list?status=waitrate" class="item">
							<i class="iconfont icondaipingjia"></i>
							<div v-if="orderNum.waitEvaluate" class="order-num">{{ orderNum.waitEvaluate }}</div>
							<div class="name">待评价</div>
						</router-link>
						<router-link to="/member/activist" class="item">
							<i class="iconfont iconshouhou"></i>
							<div v-if="orderNum.refunding" class="order-num">{{ orderNum.refunding }}</div>
							<div class="name">退款/售后</div>
						</router-link>
					</div>
					<div v-if="orderList.length">
						<div class="order-goods-wrap" v-for="(orderItem, orderIndex) in orderList" :key="orderIndex">
							<div class="order-goods" v-for="(goodsItem, goodsIndex) in orderItem.order_goods" :key="goodsIndex">
								<div class="goods-item">
									<div class="goods-img" @click="$router.pushToTab({ path: '/sku-' + goodsItem.sku_id })">
										<img :src="$img(goodsItem.sku_image, { size: 'mid' })" @error="imageErrorOrder(orderIndex, goodsIndex)" />
									</div>
									<div class="info-wrap">
										<div class="goods-name" @click="$router.pushToTab({ path: '/sku-' + goodsItem.sku_id })">{{ goodsItem.sku_name }}</div>
										<div class="price">￥{{ goodsItem.price }}</div>
									</div>
									<div class="payment">{{ orderItem.order_status_name }}</div>
									<div class="goods-detail" @click="orderDetail(orderItem)"><p>查看详情</p></div>
								</div>
							</div>
						</div>
					</div>

					<div class="empty" v-else>
						<img src="@/assets/images/member-empty.png" alt />
						<div>您买的东西太少了，这里都空空的，快去挑选合适的商品吧！</div>
					</div>
				</div>
				<div class="bottom-right">
					<div class="my-foot">
						<div class="title">我的足迹</div>
						<div class="xian"></div>
						<div class="foot-content" v-for="(item, index) in footList" :key="item.goods_id">
							<div class="foot-item" @click="$router.pushToTab({ path: '/sku-' + item.sku_id })">
								<div class="foot-img"><img :src="$img(item.sku_image, { size: 'mid' })" @error="imageErrorFoot(index)" /></div>
								<div class="foot-info">
									<div class="foot-name">{{ item.goods_name }}</div>
									<div class="foot-price">￥{{ item.discount_price }}</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</template>

<script>
import { orderNum, couponNum, footprint, levelList } from '@/api/member/index';
import { shopCollect, goodsCollect } from '@/api/member/collection';
import { apiOrderList } from '@/api/order/order';
import { mapGetters } from 'vuex';
export default {
	name: 'member',
	components: {},
	data: () => {
		return {
			couponNum: 0,
			orderNum: {
				waitPay: 0, //待付款
				readyDelivery: 0, //待发货
				waitDelivery: 0, //待收货
				refunding: 0 // 退款中
			},
			orderList: [],
			orderStatus: 'all',
			footInfo: {
				page: 1,
				page_size: 6
			},
			total: 0,
			footList: [],
			currentPage: 1,
			loading: true,
			goodsTotal: 0,
			shopTotal: 0,
			state: '',
			growth: '',
			levelList: [],
			member_level: {},
			progress: 0,
			yes: true
		};
	},
	created() {
		this.getCouponNum();
		this.getOrderNum();
		this.getOrderList();
		this.getFootprint();
		this.getGoodsCollect();
		this.getShopCollect();
	},
	computed: {
		...mapGetters(['defaultHeadImage', 'defaultGoodsImage', 'member'])
	},
	watch: {
		member: {
			handler() {
				if (this.member) this.getLevelList();
			},
			immediate: true,
			deep: true
		}
	},
	mounted() {
		let self = this;
		setTimeout(function() {
			self.yes = false;
		}, 300);
	},
	methods: {
		getLevelList() {
			levelList()
				.then(res => {
					if (res.data && res.code == 0) {
						this.levelList = res.data;
						let listIndex = this.levelList.findIndex(item => item.level_id == this.member.member_level);

						const max = this.levelList.length;
						if (max > listIndex + 1) {
							if (this.member.growth > this.levelList[listIndex + 1].growth) {
								this.progress = 100;
							} else {
								this.progress = (this.member.growth / this.levelList[listIndex + 1].growth) * 100;
							}
						} else {
							this.progress = 100;
						}
					} else {
						this.$message.error(err.message);
					}
				})
				.catch(err => {
					console.log(err.message);
				});
		},
		//获取优惠券数量
		getCouponNum() {
			couponNum()
				.then(res => {
					this.couponNum = res.data;
				})
				.catch(err => {
					console.log(err.message);
				});
		},
		//获取订单数量
		getOrderNum() {
			orderNum({
				order_status: 'waitpay,waitsend,waitconfirm,waitrate,refunding'
			})
				.then(res => {
					if (res.code == 0) {
						this.orderNum.waitPay = res.data.waitpay;
						this.orderNum.readyDelivery = res.data.waitsend;
						this.orderNum.waitDelivery = res.data.waitconfirm;
						this.orderNum.waitEvaluate = res.data.waitrate;
						this.orderNum.refunding = res.data.refunding;
					}
				})
				.catch(err => {
					console.log(err.message);
				});
		},
		//获取订单列表
		getOrderList() {
			apiOrderList({
				order_status: this.orderStatus,
				page: 1,
				page_size: 3
			})
				.then(res => {
					if (res.code == 0 && res.data) {
						this.orderList = res.data.list;
					}
					this.loading = false;
				})
				.catch(err => {
					this.loading = false;
					this.$message.error(err.message);
				});
		},
		//获取我的足迹
		getFootprint() {
			footprint(this.footInfo)
				.then(res => {
					if (res.code == 0 && res.data) {
						this.footList = res.data.list;
						this.total = res.data.count;
					}
				})
				.catch(err => {
					console.log(err.message);
				});
		},
		orderDetail(data) {
			switch (parseInt(data.order_type)) {
				case 2:
					// 自提订单
					this.$router.push({
						path: '/member/order_detail_pickup',
						query: { order_id: data.order_id }
					});
					break;
				case 3:
					// 本地配送订单
					this.$router.push({
						path: '/member/order_detail_local_delivery',
						query: { order_id: data.order_id }
					});
					break;
				case 4:
					// 虚拟订单
					this.$router.push({
						path: '/member/order_detail_virtual',
						query: { order_id: data.order_id }
					});
					break;
				default:
					this.$router.push({
						path: '/member/order_detail',
						query: { order_id: data.order_id }
					});
					break;
			}
		},
		imageErrorOrder(orderIndex, goodsIndex) {
			this.orderList[orderIndex].order_goods[goodsIndex].sku_image = this.defaultGoodsImage;
		},
		imageErrorFoot(index) {
			this.footList[index].sku_image = this.defaultGoodsImage;
		},
		getGoodsCollect() {
			goodsCollect()
				.then(res => {
					this.goodsTotal = res.data.count;
				})
				.catch(err => {
					this.loading = false;
					console.log(err.message);
				});
		},
		getShopCollect() {
			shopCollect()
				.then(res => {
					this.shopTotal = res.data.count;
				})
				.catch(err => {
					this.loading = false;
					console.log(err.message);
				});
		}
	}
};
</script>
<style lang="scss" scoped>
.box {
	width: 100%;
	position: relative;
}

.null-page {
	width: 100%;
	height: 730px;
	background-color: #ffffff;
	position: absolute;
	top: 0;
	left: 0;
	z-index: 9;
}

.member-index {
	.member-top {
		width: 100%;
		display: flex;
		.info-wrap {
			width: 75%;
			height: 160px;
			border: 1px solid #e9e9e9;
			background: #ffffff;
			display: flex;
			.info-top {
				display: flex;
				align-items: center;
				margin: 22px;
				margin-right: 0;
				width: 300px;
				border-right: 1px solid #e9e9e9;
				.avtar {
					width: 84px;
					height: 84px;
					margin: 20px 0 10px 0;
					border: 1px solid #e9e9e9;
					border-radius: 50%;
					overflow: hidden;
					display: block;
					cursor: pointer;
					margin-left: 21px;
					text-align: center;
					line-height: 84px;

					img {
						display: inline-block;
					}
				}
				.member-wrap {
					margin-left: 20px;
					.name {
						font-size: 18px;
						font-weight: 600;
						cursor: pointer;
						overflow: hidden;
						text-overflow: ellipsis;
						white-space: nowrap;
					}
					.growth {
						display: flex;
						align-items: center;
					}
					.el-progress {
						width: 100px;
					}
					.level {
						padding: 3px 4px;
						line-height: 1;
						color: #ffffc1;
						margin: 6px 0;
						cursor: default;
						background: linear-gradient(#636362, #4e4e4d);
						border-radius: 3px;
						display: inline-block;
					}
				}
			}
			.account {
				width: 400px;
				display: flex;
				align-items: center;
				.content {
					display: flex;
					justify-content: center;
					align-items: center;
					width: 100%;
					.item {
						flex: 1;
						display: flex;
						flex-direction: column;
						justify-content: center;
						align-items: center;
						.item-content {
							display: flex;
							flex-direction: column;
							justify-content: center;
							align-items: center;
						}
						img {
							width: 50px;
							height: 50px;
						}
						.name {
							margin-top: 5px;
							color: #666666;
							&:hover {
								color: $base-color;
							}
						}
						.num {
							color: $ns-text-color-black;
						}
					}
				}
			}
		}
		.collection {
			background: #ffffff;
			margin-left: 20px;
			width: 210px;
			border: 1px solid #e9e9e9;
			padding-left: 20px;
			.title {
				padding: 10px 0;
				display: inline-block;
				border-bottom: 1px solid $base-color;
			}
			.xian {
				height: 1px;
				background: #f1f1f1;
			}
			.item-wrap {
				display: flex;
				justify-content: center;
				align-items: center;
				.item {
					flex: 1;
					margin-top: 37px;
					.num {
					}
					.collect {
						color: #666666;
					}
				}
			}
		}
	}
	.member-bottom {
		width: 100%;
		margin-top: 15px;
		height: 553px;
		display: flex;
		overflow: hidden;

		.my-order {
			width: 75%;
			background-color: #ffffff;
			.order-title {
				font-size: $ns-font-size-base;
				padding: 10px 0;
				margin-left: 15px;
				border-bottom: 1px solid $base-color;
				display: inline-block;
			}
			.xian {
				height: 1px;
				background: #f1f1f1;
				margin-left: 15px;
			}
			.order-item {
				display: flex;
				justify-content: center;
				align-content: center;

				.item {
					flex: 1;
					text-align: center;
					height: 85px;
					padding-top: 20px;
					cursor: pointer;
					position: relative;
					&:hover {
						background: #ffffff;
						box-shadow: 0 2px 12px 0 rgba(0, 0, 0, 0.1);
					}
					.order-num {
						position: absolute;
						top: 23px;
						right: 50px;
						background: $base-color;
						border-radius: 50%;
						width: 18px;
						height: 18px;
						line-height: 18px;
						color: #ffff;
					}

					i {
						font-size: 30px;
					}
					.name {
						font-size: $ns-font-size-base;
					}
				}
			}
			.order-goods {
				.goods-item {
					display: flex;
					padding: 14px;
					border-top: 1px solid #f1f1f1;
					.goods-img {
						flex: 1;
						display: flex;
						justify-content: center;
						align-items: center;
						cursor: pointer;
						img {
							width: 60px;
							height: 60px;
						}
					}
					.info-wrap {
						flex: 3;
						width: 80%;
						.goods-name {
							height: 46px;
							overflow: hidden;
							display: -webkit-box;
							-webkit-line-clamp: 2;
							-webkit-box-orient: vertical;
						}
						.price {
							color: $base-color;
						}
					}
					.payment {
						flex: 2;
						display: flex;
						align-items: center;
						justify-content: center;
						text-overflow: ellipsis;
					}
					.goods-detail {
						flex: 2;
						display: flex;
						align-items: center;
						justify-content: center;
						cursor: pointer;
						&:hover {
							color: $base-color;
						}
					}
				}
			}
		}
		.bottom-right {
			.my-foot {
				background: #ffffff;
				margin-left: 20px;
				width: 230px;
				.title {
					font-size: $ns-font-size-base;
					padding: 10px 0;
					display: inline-block;
					border-bottom: 1px solid $base-color;
					margin: 0 15px;
				}
				.xian {
					margin-left: 15px;
					background: #f1f1f1;
					height: 1px;
				}
				.foot-content {
					.foot-item {
						display: flex;
						padding: 10px 0;
						margin: 0 15px;
						.foot-img {
							width: 57px;
							height: 57px;
							cursor: pointer;
							img {
								width: 100%;
								height: 100%;
							}
						}
						.foot-info {
							margin-left: 5px;
							display: flex;
							flex-direction: column;
							justify-content: space-between;
							.foot-name {
								overflow: hidden;
								text-overflow: ellipsis;
								display: -webkit-box;
								-webkit-line-clamp: 2;
								-webkit-box-orient: vertical;
								line-height: 1;
								width: 140px;
							}
							.foot-price {
								color: $base-color;
							}
						}
					}
				}
			}
		}
	}
}
.empty {
	text-align: center;
	margin-top: 65px;
}
</style>
