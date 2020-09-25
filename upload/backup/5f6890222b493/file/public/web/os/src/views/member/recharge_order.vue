<template>
	<div class="box">
		<div class="null-page" v-show="yes"></div>
		
		<el-card class="box-card">
			<div slot="header" class="clearfix">
				<el-breadcrumb separator="/">
					<el-breadcrumb-item :to="{ path: '/member/account' }">账户余额</el-breadcrumb-item>
					<el-breadcrumb-item :to="{ path: '/member/recharge_list' }">充值套餐列表</el-breadcrumb-item>
					<el-breadcrumb-item>充值记录</el-breadcrumb-item>
				</el-breadcrumb>
			</div>

			<div v-loading="loading">
				<div class="order-list">
					<nav>
						<li>套餐名称</li>
						<li>面值</li>
						<li>购买价格</li>
						<li>赠送积分</li>
						<li>赠送成长值</li>
					</nav>

					<div class="list" v-if="orderList.length > 0">
						<div class="item" v-for="(orderItem, orderIndex) in orderList" :key="orderIndex">
							<div class="head">
								<span class="create-time">{{ $util.timeStampTurnTime(orderItem.create_time) }}</span>
								<span class="order-no">订单号：{{ orderItem.order_no }}</span>
							</div>
							<ul>
								<li>
									<div class="img-wrap"><el-image :src="$img(orderItem.cover_img)" fit="cover" @error="imageError(orderIndex)"></el-image></div>
									<div class="info-wrap">
										<h5 :title="orderItem.recharge_name">{{ orderItem.recharge_name }}</h5>
										<!-- <span>规格：规格值</span> -->
									</div>
								</li>
								<li>
									<span>￥{{ orderItem.face_value }}</span>
								</li>
								<li>
									<span>￥{{ orderItem.buy_price }}</span>
								</li>
								<li>
									<span>{{ orderItem.point }}</span>
								</li>
								<li>
									<span>{{ orderItem.growth }}</span>
								</li>
							</ul>
						</div>
					</div>
					<div v-else-if="!loading && orderList.length == 0" class="empty-wrap">暂无相关订单</div>
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
		</el-card>
	</div>
</template>

<script>
	import { rechargeOrder } from "@/api/member/account"
	import { mapGetters } from 'vuex';

	export default {
		name: "recharge_list",
		components: {},
		data: () => {
			return {
				orderList: [],
				total: 0,
				currentPage: 1,
				pageSize: 10,
				loading: true,
				yes: true
			}
		},
		created() {
			this.getListData()
		},
		mounted() {
			let self = this;
			setTimeout(function() {
				self.yes = false
			}, 300)
		},
		computed: {
			...mapGetters(['defaultGoodsImage'])
		},
		methods: {
			handlePageSizeChange(size) {
				this.pageSize = size
				this.refresh()
			},
			handleCurrentPageChange(page) {
				this.currentPage = page
				this.refresh()
			},
			refresh() {
				this.loading = true
				this.getListData()
			},
			getListData() {
				rechargeOrder({
					page: this.currentPage,
					page_size: this.pageSize
				}).then(res => {
					if (res.code == 0 && res.data) {
						this.orderList = res.data.list;
					} else {
						this.$message.warning(res.message)
					}
					this.loading = false
				}).catch(err => {
					this.loading = false
				})
			},
			imageError(index) {
				this.orderList[index].cover_img = this.defaultGoodsImage;
			},
		}
	}
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

	.order-list {
		nav {
			overflow: hidden;
			padding: 10px;
			background: #fff;
			margin-bottom: 10px;
			border-bottom: 1px solid #eeeeee;

			li {
				float: left;

				&:nth-child(1) {
					width: 40%;
				}

				&:nth-child(2) {
					width: 15%;
				}

				&:nth-child(3) {
					width: 15%;
				}

				&:nth-child(4) {
					width: 15%;
				}

				&:nth-child(5) {
					width: 15%;
				}
			}
		}

		.list {
			.item {
				margin-bottom: 20px;
				border: 1px solid #eeeeee;
				border-top: 0;

				.head {
					padding: 8px 10px;
					background: #f7f7f7;
					font-size: 12px;

					.create-time {
						margin-right: 10px;
					}

					border-bottom: 1px solid #eeeeee;

					a {
						margin: 0 10px 0 20px;
					}

					.order-type {
						margin-left: 30px;
					}
				}
			}

			ul {
				background-color: #fff;
				padding: 10px;
				overflow: hidden;

				li {
					float: left;
					line-height: 60px;

					&:nth-child(1) {
						width: 40%;
						line-height: inherit;

						.img-wrap {
							width: 60px;
							height: 60px;
							float: left;
							margin-right: 10px;
							border-radius: 5px;
							overflow: hidden;
							
							.el-image {
								width: 100%;
								height: 100%;
							}
						}

						.info-wrap {
							margin-left: 70px;

							h5 {
								font-weight: normal;
								font-size: $ns-font-size-base;
								display: -webkit-box;
								-webkit-box-orient: vertical;
								-webkit-line-clamp: 2;
								overflow: hidden;
								margin-right: 10px;
							}
						}
					}

					&:nth-child(2) {
						width: 15%;
					}

					&:nth-child(3) {
						width: 15%;
					}

					&:nth-child(4) {
						width: 15%;
					}

					&:nth-child(5) {
						width: 15%;
					}
				}
			}
		}

		.empty-wrap {
			text-align: center;
			padding: 10px;
		}
	}
</style>
