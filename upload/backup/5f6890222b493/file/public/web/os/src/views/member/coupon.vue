<template>
	<div class="box">
		<div class="null-page" v-show="yes"></div>
		
		<el-card class="box-card member-coupon">
			<div slot="header" class="clearfix"><span>我的优惠券</span></div>

			<div>
				<el-tabs v-model="couponsource" @tab-click="handleClickSource">
					<el-tab-pane label="店铺优惠券" name="1"></el-tab-pane>
					<el-tab-pane label="平台优惠券" name="2"></el-tab-pane>
				</el-tabs>

				<div v-loading="loading">
					<el-tabs v-model="couponstatus" @tab-click="handleClickStatus">
						<el-tab-pane label="未使用" name="1"></el-tab-pane>
						<el-tab-pane label="已使用" name="2"></el-tab-pane>
						<el-tab-pane label="已过期" name="3"></el-tab-pane>
					</el-tabs>

					<div class="coupon-wrap">
						<div
							class="text item"
							:class="state == '1' ? 'coupon-not-used' : state == '2' ? 'coupon-used' : 'coupon-expire'"
							v-for="(item, index) in couponList"
							:key="index"
							@click="useCoupon(item)"
						>
							<template>
								<p class="coupon-wrap-money" v-if="item.discount == '0.00' || !item.discount">
									￥
									<span>{{ item.money }}</span>
								</p>
								<p class="coupon-wrap-money" v-else>
									<span>{{ item.discount }}</span>
									折
								</p>
							</template>
							<p class="coupon-wrap-name">{{ couponsource == '1' ? item.coupon_name : item.platformcoupon_name }}</p>
							<p class="coupon-wrap-site coupon-wrap-info" v-if="couponsource == '1'">使用店铺：{{ item.site_name }}</p>
							<template>
								<p class="coupon-wrap-least coupon-wrap-info" v-if="item.at_least > 0">满{{ item.at_least }}元可用</p>
								<p class="coupon-wrap-least coupon-wrap-info" v-else>无门槛优惠券</p>
							</template>
							<template>
								<p class="coupon-wrap-time coupon-wrap-info" v-if="item.validity_type">领取之日起{{ item.fixed_term }}日内有效</p>
								<p class="coupon-wrap-time coupon-wrap-info" v-else>有效期至{{ $timeStampTurnTime(item.end_time) }}</p>
							</template>
						</div>

						<div class="empty-text" v-if="couponList.length == 0">{{ text }}</div>
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
			</div>
		</el-card>
	</div>	
</template>

<script>
import { couponList } from '@/api/member/member';
import { mapGetters } from 'vuex';

export default {
	name: 'my_coupon',
	components: {},
	data: () => {
		return {
			total: 0,
			currentPage: 1,
			pageSize: 9,
			couponsource: '1',
			couponstatus: '1',
			couponList: [],
			type: '',
			state: 1,
			text: '您还没有优惠券哦',
			loading: true,
			yes: true
		};
	},
	created() {
		if (this.addonIsExit && this.addonIsExit.coupon != 1) {
			this.$message({
				message: '优惠券插件未安装',
				type: 'warning',
				duration: 2000,
				onClose: () => {
					this.$route.push('/member/index');
				}
			});
		} else {
			this.getListData();
		}
	},
	mounted() {
		let self = this;
		setTimeout(function() {
			self.yes = false
		}, 300)
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
						this.$route.push('/member/index');
					}
				});
			}
		}
	},
	methods: {
		/**
		 * 优惠券来源(店铺/平台)
		 */
		handleClickSource(tab, event) {
			this.refresh();
		},
		/**
		 * 优惠券状态(未使用/已使用/已过期)
		 */
		handleClickStatus(tab, event) {
			if (tab.name == '1') {
				this.state = 1;
				this.text = '您还没有优惠券哦';
			} else if (tab.name == '2') {
				this.state = 2;
				this.text = '您还没有使用过优惠券哦';
			} else {
				this.state = 3;
				this.text = '您还没有过期优惠券哦';
			}
			this.refresh();
		},
		handlePageSizeChange(size) {
			this.pageSize = size;
			this.refresh();
		},
		handleCurrentPageChange(page) {
			this.currentPage = page;
			this.refresh();
		},
		refresh() {
			this.loading = true;
			this.getListData();
		},
		// 获取优惠券列表
		getListData() {
			couponList({
				page: this.currentPage,
				page_size: this.pageSize,
				state: this.state,
				is_own: this.type,
				couponsource: this.couponsource
			})
				.then(res => {
					if (res.code >= 0) {
						this.total = res.data.count;
						this.couponList = res.data.list;
					}
					this.loading = false;
				})
				.catch(err => {
					this.loading = false;
					this.$message.error(err.message);
				});
		},
		// 去使用优惠券
		useCoupon(item) {
			if (item.state == 1) {
				if (this.couponsource == '1') {
					if (item.goods_type != 1) {
						this.$router.push({ path: '/shop_list', query: { site_id: item.site_id, couponId: item.coupon_type_id } });
					} else {
						this.$router.push({ path: '/shop_list', query: { site_id: item.site_id } });
					}
				} else {
					if (item.use_scenario != 1) {
						this.$router.push({ path: '/list', query: { platform_coupon_type: item.platformcoupon_type_id } });
					} else {
						this.$router.push('/list');
					}
				}
			}
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
	background-color: #FFFFFF;
	position: absolute;
	top: 0;
	left: 0;
	z-index: 9;
}

.el-card.is-always-shadow,
.el-card.is-hover-shadow:focus,
.el-card.is-hover-shadow:hover {
	box-shadow: unset;
}

.el-card {
	border: 0;
}

.coupon-wrap {
	display: flex;
	align-items: center;
	flex-wrap: wrap;

	.text {
		width: 32%;
		height: 140px;
		margin-right: 2%;
		border-radius: 5px;
		border: 1px dashed #fff;
		margin-bottom: 20px;
		padding: 0 15px;
		box-sizing: border-box;
		color: #ffffff;

		.coupon-wrap-money {
			span {
				font-size: 30px;
				margin-right: 5px;
			}
		}

		.coupon-wrap-info {
			font-size: 12px;
			line-height: 18px;
		}
	}

	.text:nth-child(3n) {
		margin-right: 0;
	}

	.coupon-not-used {
		background-color: $base-color;
		cursor: pointer;
	}

	.coupon-used {
		background-color: hsl(360, 50%, 70%);
	}

	.coupon-expire {
		background-color: #d0d0d0;
	}

	.coupon-wrap-info {
		font-size: 12px;
		line-height: 20px;
	}

	.empty-text {
		margin: 0 auto;
	}
}
</style>

<style lang="scss">
.member-coupon {
	.el-tabs__active-bar,
	.el-tabs__nav-wrap::after {
		/* 清除tab标签底部横线 */
		height: 0;
	}
}
</style>
