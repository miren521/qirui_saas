<template>
	<div class="ns-coupon">
		<div class="ns-coupon-info">
			<div class="ns-coupon-wrap">
				<div class="coupon-name">
					<span class="ns-text-color">优惠券中心</span>
					<span class="ns-text-color-gray text12">省钱更多，领券更多</span>
				</div>
				<ul class="coupon-type clear-float">
					<li>
						<i></i>
						<span>限时抢券</span>
					</li>
					<li>
						<i></i>
						<span>叠加使用</span>
					</li>
					<li>
						<i></i>
						<span>种类多样</span>
					</li>
				</ul>
				<el-button @click="myCoupon">我的优惠券</el-button>
			</div>
			<div class="ns-coupon-img" v-loading="loadingAd">
				<el-carousel height="406px">
					<el-carousel-item v-for="item in adList" :key="item.adv_id">
						<el-image :src="$img(item.adv_image)" fit="cover" @click="$router.pushToTab(item.adv_url.url)" />
					</el-carousel-item>
				</el-carousel>
			</div>
		</div>

		<el-tabs v-model="activeName" @tab-click="handleClick">
			<el-tab-pane label="店铺优惠券" name="first"></el-tab-pane>
			<el-tab-pane label="平台优惠券" name="second"></el-tab-pane>

			<div v-loading="loading">
				<ul class="ns-coupon-list">
					<li class="ns-bg-color ns-coupon-li" v-for="(item, index) in couponList" :key="index">
						<div class="describe">
							<template>
								<span v-if="!item.discount || item.discount == '0.00'">￥{{ item.money }}</span>
								<span v-else>{{ item.discount }}折</span>
							</template>
							<span>{{ activeName == 'first' ? item.coupon_name : item.platformcoupon_name }}</span>
							<span v-if="activeName == 'first'">使用店铺：{{ item.site_name }}</span>
							<template>
								<span v-if="item.at_least == '0.00'">无门槛优惠券</span>
								<span v-else>满{{ item.at_least }}可使用</span>
							</template>

							<template>
								<span class="coupon-wrap-time" v-if="item.validity_type">领取之日起{{ item.fixed_term }}日内有效</span>
								<span class="coupon-wrap-time" v-else>有效期至{{ $timeStampTurnTime(item.end_time) }}</span>
							</template>
						</div>
						<div class="receive">
							<!-- 如果限领数为0 或者 领取数小于最大领取数 -->
							<a class="ns-text-color" @click="couponTap(item, index)">
								<span v-if="item.useState == 0">立即领取</span>
								<span v-else>去使用</span>
							</a>
						</div>
					</li>
				</ul>
				
				<div class="empty-wrap" v-if="couponList.length <= 0">
					<div class="ns-text-align">暂无优惠券</div>
				</div>

				<div class="pager">
					<el-pagination
						background 
						:pager-count="5" 
						:total="total" 
						prev-text="上一页" 
						next-text="下一页" 
						:current-page.sync="currentPage" 
						:page-size.sync="pageSize" 
						@size-change="handlePageSizeChange" 
						@current-change="handleCurrentPageChange" 
						hide-on-single-page
					></el-pagination>
				</div>
			</div>
		</el-tabs>
	</div>
</template>

<script>
import { couponList, receiveCoupon } from '@/api/coupon';
import { mapGetters } from 'vuex';
import { adList } from '@/api/website';

export default {
	name: 'coupon',
	components: {},
	data: () => {
		return {
			couponList: [],
			total: 0,
			currentPage: 1,
			pageSize: 9,
			couponBtnSwitch: false,
			activeName: 'first',
			loading: true,
			loadingAd: true,
			adList: []
		};
	},
	created() {
		if (this.addonIsExit && this.addonIsExit.coupon != 1) {
			this.$message({
				message: '优惠券插件未安装',
				type: 'warning',
				duration: 2000,
				onClose: () => {
					this.$route.push('/');
				}
			});
		} else {
			this.getAdList();
			this.getCanReceiveCouponQuery();
		}
	},
	computed: {
		...mapGetters(['addonIsExit'])
	},
	watch: {
		addonIsExit() {
			if (this.addonIsExit.coupon != 1) {
				this.$message({
					message: '优惠券插件未安装',
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
		getAdList() {
			adList({ keyword: 'NS_PC_COUPON' })
				.then(res => {
					this.adList = res.data.adv_list;
					for (let i = 0; i < this.adList.length; i++) {
						if (this.adList[i].adv_url) this.adList[i].adv_url = JSON.parse(this.adList[i].adv_url);
					}
					this.loadingAd = false;
				})
				.catch(err => {
					this.loadingAd = false;
				});
		},
		handleClick(tab, event) {
			this.loading = true;
			(this.currentPage = 1), this.getCanReceiveCouponQuery();
		},
		/**
		 * 我的优惠券
		 */
		myCoupon() {
			this.$router.pushToTab('/member/my_coupon');
		},
		/**
		 * 获取优惠券列表
		 */
		getCanReceiveCouponQuery() {
			couponList({
				page: this.currentPage,
				page_size: this.pageSize,
				activeName: this.activeName
			})
				.then(res => {
					this.couponList = res.data.list;
					this.total = res.data.count;
					this.couponList.forEach(v => {
						v.useState = 0;
					});
					this.loading = false;
				})
				.catch(err => {
					this.loading = false;
					this.$message.error(err.message);
				});
		},
		handlePageSizeChange(size) {
			this.pageSize = size;
			this.loading = true;
			this.getCanReceiveCouponQuery();
		},
		handleCurrentPageChange(page) {
			this.currentPage = page;
			this.loading = true;
			this.getCanReceiveCouponQuery();
		},
		/**
		 * 点击优惠券
		 */
		couponTap(item, index) {
			if (item.useState == 0) this.receiveCoupon(item, index);
			else this.toGoodsList(item);
		},
		/**
		 * 领取优惠券
		 */
		receiveCoupon(item, index) {
			if (this.couponBtnSwitch) return;
			this.couponBtnSwitch = true;

			var data = {
				site_id: item.site_id,
				activeName: this.activeName
			};
			if (this.activeName == 'first') {
				data.coupon_type_id = item.coupon_type_id;
			} else {
				data.platformcoupon_type_id = item.platformcoupon_type_id;
			}

			receiveCoupon(data)
				.then(res => {
					var data = res.data;
					let msg = res.message;
					if (res.code == 0) {
						msg = '领取成功';
						this.$message({ message: msg, type: 'success' });
					} else {
						this.$message({ message: msg, type: 'warning' });
					}
					let list = this.couponList;
					if (res.data.is_exist == 1) {
						for (let i = 0; i < list.length; i++) {
							if (this.activeName == 'first') {
								if (list[i].coupon_type_id == item.coupon_type_id) {
									list[i].useState = 1;
								}
							} else {
								if (list[i].platformcoupon_type_id == item.platformcoupon_type_id) {
									list[i].useState = 1;
								}
							}
						}
					} else {
						for (let i = 0; i < list.length; i++) {
							if (this.activeName == 'first') {
								if (list[i].coupon_type_id == item.coupon_type_id) {
									list[i].useState = 2;
								}
							} else {
								if (list[i].platformcoupon_type_id == item.platformcoupon_type_id) {
									list[i].useState = 2;
								}
							}
						}
					}

					this.couponBtnSwitch = false;
					this.$forceUpdate();
				})
				.catch(err => {
					if (err.message == 'token不存在') {
						this.$message.error("您尚未登录，请先进行登录");
					} else {
						this.$message.error(err.message);
					}
					
					this.couponBtnSwitch = false;
				});
		},
		/**
		 * 去购买
		 */
		toGoodsList(item) {
			if (this.activeName == 'first') {
				if (item.goods_type != 1) {
					this.$router.push({ path: '/shop_list', query: { site_id: item.site_id, couponId: item.coupon_type_id } });
				} else {
					this.$router.push({ path: '/shop_list', query: { site_id: item.site_id } });
				}
			} else {
				if (item.use_scenario != 1) {
					this.$router.push({ path: '/list', query: { platformcouponTypeId: item.platformcoupon_type_id } });
				} else {
					this.$router.push('/list');
				}
			}
		}
	}
};
</script>
<style lang="scss" scoped>
.empty-wrap {
	margin-top: 20px;
}

.ns-coupon {
	width: 100%;
	padding: 20px;
	box-sizing: border-box;
}

.ns-coupon-info {
	background: url(../../assets/images/coupon-bg.png) no-repeat;
	background-size: cover;
	width: 100%;
	height: 450px;
	display: flex;

	.ns-coupon-wrap {
		width: 320px;
		padding: 20px;
		box-sizing: border-box;
		text-align: center;

		.coupon-name {
			margin: 45px 0 50px;

			span:nth-child(1) {
				display: block;
				line-height: 45px;
				font-size: 30px;
			}

			.ns-text-color-gray {
				color: #898989 !important;
			}
			.text12 {
				font-size: 12px;
			}
		}

		.coupon-type {
			margin-left: 20px;
			li {
				float: left;
				width: 80px;
				height: 100px;

				i {
					display: block;
					width: 50px;
					height: 50px;
					margin: 8px auto;
				}
				&:nth-child(1) i {
					background: url(../../assets/images/limited_time.png) no-repeat center;
				}
				&:nth-child(2) i {
					background: url(../../assets/images/superposition.png) no-repeat center;
				}
				&:nth-child(3) i {
					background: url(../../assets/images/coupon_type.png) no-repeat center;
				}
			}
		}
		.el-button {
			width: 200px;
			background-color: $base-color;
			color: #fff;
			margin-top: 70px;
			font-size: 18px;
		}
	}

	.ns-coupon-img {
		width: 850px;
		padding: 20px;
		box-sizing: border-box;

		img {
			width: 100%;
			height: 100%;
		}

		.el-carousel__item:nth-child(2n) {
			background-color: #99a9bf;
		}

		.el-carousel__item:nth-child(2n + 1) {
			background-color: #d3dce6;
		}
	}
}

.el-tabs {
	margin-top: 20px;
}

.ns-coupon-list {
	display: flex;
	flex-wrap: wrap;
	padding: 0 20px;
	.ns-coupon-li {
		background: url(../../assets/images/list_bj.png) no-repeat;
		width: 32%;
		height: 169px;
		margin-bottom: 20px;
		margin-right: 2%;
		background-size: cover;

		.describe {
			float: left;
			width: 250px;
			height: inherit;
			text-align: center;
			color: #fff;
			span {
				display: block;
			}
			span:nth-child(1) {
				font-size: 40px;
				margin-top: 20px;
				margin-bottom: 3px;
				line-height: 50px;
			}
			span:nth-child(3),
			span:nth-child(4),
			span:nth-child(5) {
				font-size: 12px;
				margin-left: 15px;
				line-height: 20px;
			}
		}
		.receive {
			float: right;
			width: 95px;
			height: inherit;
			text-align: center;
			a {
				display: inline-block;
				width: 30px;
				height: 120px;
				line-height: 120px;
				padding: 0 5px;
				margin-top: 25px;
				background-color: #fff;
				border-radius: 15px;
				cursor: pointer;
				box-sizing: border-box;
				span {
					display: inline-block;
					line-height: 20px;
					vertical-align: middle;
				}
			}
		}

		&:nth-child(3n) {
			margin-right: 0;
		}
	}
}
</style>
