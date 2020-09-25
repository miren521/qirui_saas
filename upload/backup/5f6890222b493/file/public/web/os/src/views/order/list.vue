<template>
	<div class="box">
		<div class="null-page" v-show="yes"></div>
		
		<el-card class="box-card order-list">
			<div slot="header" class="clearfix"><span>我的订单</span></div>

			<el-tabs v-model="orderStatus" @tab-click="handleClick">
				<el-tab-pane label="全部订单" name="all"></el-tab-pane>
				<el-tab-pane label="待付款" name="waitpay"></el-tab-pane>
				<el-tab-pane label="待发货" name="waitsend"></el-tab-pane>
				<el-tab-pane label="待收货" name="waitconfirm"></el-tab-pane>
				<el-tab-pane label="待评价" name="waitrate"></el-tab-pane>
			</el-tabs>

			<div v-loading="loading">
				<nav>
					<li>商品信息</li>
					<li>单价</li>
					<li>数量</li>
					<li>实付款</li>
					<li>订单状态</li>
					<li>操作</li>
				</nav>

				<div class="list" v-if="orderList.length > 0">
					<div class="item" v-for="(orderItem, orderIndex) in orderList" :key="orderIndex">
						<div class="head">
							<span class="create-time">{{ $util.timeStampTurnTime(orderItem.create_time) }}</span>
							<span class="order-no">订单号：{{ orderItem.order_no }}</span>
							<router-link :to="'/shop-' + orderItem.site_id" target="_blank">{{ orderItem.site_name }}</router-link>
							<span class="order-type">{{ orderItem.order_type_name }}</span>
						</div>
						<ul v-for="(goodsItem, goodsIndex) in orderItem.order_goods" :key="goodsIndex">
							<li>
								<div class="img-wrap" @click="$router.pushToTab('/sku-' + goodsItem.sku_id)">
									<img :src="$img(goodsItem.sku_image, { size: 'mid' })" @error="imageError(orderIndex, goodsIndex)" />
								</div>
								<div class="info-wrap" @click="$router.pushToTab('/sku-' + goodsItem.sku_id)">
									<h5>{{ goodsItem.sku_name }}</h5>
									<!-- <span>规格：规格值</span> -->
								</div>
							</li>
							<li>
								<span>￥{{ goodsItem.price }}</span>
							</li>
							<li>
								<span>{{ goodsItem.num }}</span>
							</li>
							<li>
								<span>￥{{ orderItem.pay_money }}</span>
							</li>
							<template v-if="goodsIndex == 0">
								<li>
									<span class="ns-text-color">{{ orderItem.order_status_name }}</span>
									<el-link :underline="false" @click="orderDetail(orderItem)">订单详情</el-link>
								</li>
								<li>
									<template v-if="orderItem.action.length > 0">
										<el-button type="primary" size="mini" plain v-if="orderItem.is_evaluate == 1" @click="operation('memberOrderEvaluation', orderItem)">
											<template v-if="orderItem.evaluate_status == 0">
												评价
											</template>
											<template v-else-if="orderItem.evaluate_status == 1">
												追评
											</template>
										</el-button>

										<el-button
											type="primary"
											size="mini"
											:plain="operationItem.action == 'orderPay' ? false : true"
											v-for="(operationItem, operationIndex) in orderItem.action"
											:key="operationIndex"
											@click="operation(operationItem.action, orderItem)"
										>
											{{ operationItem.title }}
										</el-button>
									</template>

									<template class="order-operation" v-else-if="orderItem.action.length == 0 && orderItem.is_evaluate == 1">
										<el-button type="primary" size="mini" plain v-if="orderItem.is_evaluate == 1" @click="operation('memberOrderEvaluation', orderItem)">
											<template v-if="orderItem.evaluate_status == 0">
												评价
											</template>
											<template v-else-if="orderItem.evaluate_status == 1">
												追评
											</template>
										</el-button>
									</template>
								</li>
							</template>
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
		</el-card>
	</div>
</template>

<script>
import { mapGetters } from 'vuex';
import { apiOrderList } from '@/api/order/order';
import orderMethod from '@/utils/orderMethod';
export default {
	name: 'order_list',
	components: {},
	data: () => {
		return {
			orderStatus: 'all',
			loading: true,
			orderList: [],
			currentPage: 1,
			pageSize: 10,
			total: 0,
			yes: true
		};
	},
	mixins: [orderMethod],
	created() {
		this.orderStatus = this.$route.query.status || 'all';
		this.getOrderList();
	},
	computed: {
		...mapGetters(['defaultGoodsImage'])
	},
	mounted() {
		let self = this;
		setTimeout(function() {
			self.yes = false
		}, 300)
	},
	methods: {
		handleClick(tab, event) {
			this.currentPage = 1;
			this.orderStatus = tab.name;
			this.refresh();
		},
		getOrderList() {
			apiOrderList({
				page: this.currentPage,
				page_size: this.pageSize,
				order_status: this.orderStatus
			})
				.then(res => {
					let list = [];
					if (res.code == 0 && res.data) {
						list = res.data.list;
						this.total = res.data.count;
					}
					this.orderList = list;
					this.loading = false;
				})
				.catch(res => {
					this.loading = false;
				});
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
			this.getOrderList();
		},
		operation(action, orderData) {
			let index = this.status;
			switch (action) {
				case 'orderPay': // 支付
					this.orderPay(orderData);
					break;
				case 'orderClose': //关闭
					this.orderClose(orderData.order_id, () => {
						this.refresh();
					});
					break;
				case 'memberTakeDelivery': //收货
					this.orderDelivery(orderData.order_id, () => {
						this.refresh();
					});
					break;
				case 'trace': //查看物流
					this.$router.push({ path: '/member/logistics', query: { order_id: orderData.order_id } });
					break;
				case 'memberOrderEvaluation': //评价
					this.$router.pushToTab({ path: '/evaluate', query: { order_id: orderData.order_id } });
					break;
			}
		},
		orderDetail(data) {
			switch (parseInt(data.order_type)) {
				case 2:
					// 自提订单
					this.$router.push({ path: '/member/order_detail_pickup', query: { order_id: data.order_id } });
					break;
				case 3:
					// 本地配送订单
					this.$router.push({ path: '/member/order_detail_local_delivery', query: { order_id: data.order_id } });
					break;
				case 4:
					// 虚拟订单
					this.$router.push({ path: '/member/order_detail_virtual', query: { order_id: data.order_id } });
					break;
				default:
					this.$router.push({ path: '/member/order_detail', query: { order_id: data.order_id } });
					break;
			}
		},
		imageError(orderIndex, goodsIndex) {
			this.orderList[orderIndex].order_goods[goodsIndex].sku_image = this.defaultGoodsImage;
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

.order-list {
	nav {
		overflow: hidden;
		padding: 10px 0;
		background: #fff;
		margin-bottom: 10px;
		border-bottom: 1px solid #eeeeee;
		li {
			float: left;
			&:nth-child(1) {
				width: 45%;
			}
			&:nth-child(2) {
				width: 10%;
			}
			&:nth-child(3) {
				width: 10%;
			}
			&:nth-child(4) {
				width: 10%;
			}
			&:nth-child(5) {
				width: 15%;
			}
			&:nth-child(6) {
				width: 10%;
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
					width: 45%;
					line-height: inherit;
					.img-wrap {
						width: 60px;
						height: 60px;
						float: left;
						margin-right: 10px;
						cursor: pointer;
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
							display: inline-block;
							cursor: pointer;
							&:hover {
								color: $base-color;
							}
						}
						span {
							font-size: $ns-font-size-sm;
							color: #9a9a9a;
						}
					}
				}
				&:nth-child(2) {
					width: 10%;
				}
				&:nth-child(3) {
					width: 10%;
				}
				&:nth-child(4) {
					width: 10%;
				}
				&:nth-child(5) {
					width: 15%;
					line-height: 30px;
					a {
						display: block;
					}
				}
				&:nth-child(6) {
					width: 10%;
					line-height: initial;
					button {
						margin-left: 0;
						margin-bottom: 10px;
						&:last-child {
							margin-bottom: 0;
						}
					}
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
