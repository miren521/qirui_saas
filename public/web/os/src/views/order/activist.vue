<template>
	<div class="box">
		<div class="null-page" v-show="yes"></div>
		
		<el-card class="box-card order-list">
			<div slot="header" class="clearfix"><span>退款/售后</span></div>

			<div v-loading="loading">
				<nav>
					<li>商品信息</li>
					<li>退款金额</li>
					<li>退款类型</li>
					<li>退款状态</li>
					<li>操作</li>
				</nav>

				<div class="list" v-if="refundList.length > 0">
					<div class="item" v-for="(refundItem, refundIndex) in refundList" :key="refundIndex">
						<div class="head">
							<span class="create-time">{{ $util.timeStampTurnTime(refundItem.refund_action_time) }}</span>
							<span class="order-no">退款编号：{{ refundItem.refund_no }}</span>
							<router-link :to="'/shop-' + refundItem.site_id" target="_blank">{{ refundItem.site_name }}</router-link>
							<span class="order-type">{{ refundItem.refund_status == 3 ? '退款成功' : '退款中' }}</span>
						</div>
						<ul>
							<li>
								<div class="img-wrap" @click="$router.pushToTab('/sku-' + refundItem.sku_id)"><img :src="$img(refundItem.sku_image, { size: 'mid' })" @error="imageError(refundIndex)" /></div>
								<div class="info-wrap">
									<h5 @click="$router.pushToTab('/sku-' + refundItem.sku_id)">{{ refundItem.sku_name }}</h5>
									<!-- <span>规格：规格值</span> -->
								</div>
							</li>
							<li>
								<span>￥{{ refundItem.refund_apply_money }}</span>
							</li>

							<li>
								<span>{{ refundItem.refund_type == 1 ? '退款' : '退货' }}</span>
							</li>
							<li>
								<span class="ns-text-color">{{ refundItem.refund_status_name }}</span>
								<el-link :underline="false" @click="orderDetail(refundItem)">退款详情</el-link>
							</li>
							<li>
								<template v-if="refundItem.refund_action.length > 0">
									<el-button
										type="primary"
										size="mini"
										:plain="true"
										v-for="(operationItem, operationIndex) in refundItem.refund_action"
										:key="operationIndex"
										@click="operation(operationItem.event, refundItem)"
									>
										{{ operationItem.title }}
									</el-button>
								</template>
							</li>
						</ul>
					</div>
				</div>
				<div v-else-if="!loading && refundList.length == 0" class="empty-wrap">暂无相关订单</div>
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
import { refundList, cancleRefund } from '@/api/order/refund';
export default {
	name: 'activist',
	components: {},
	data: () => {
		return {
			orderStatus: 'all',
			loading: true,
			refundList: [],
			currentPage: 1,
			pageSize: 10,
			total: 0,
			yes: true
		};
	},
	created() {
		this.orderStatus = this.$route.query.status || 'all';
		this.getrefundList();
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
		getrefundList() {
			refundList({
				page: this.currentPage,
				page_size: this.pageSize
			})
				.then(res => {
					let list = [];
					if (res.code == 0 && res.data) {
						list = res.data.list;
						this.total = res.data.count;
					}
					this.refundList = list;
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
			this.getrefundList();
		},
		operation(action, orderData) {
			switch (action) {
				case 'orderRefundCancel': // 撤销维权
					this.cancleRefund(orderData.order_goods_id);
					break;
				case 'orderRefundDelivery': // 退款发货
					this.$router.push({ path: '/member/refund_detail', query: { order_goods_id: orderData.order_goods_id, action: 'returngoods' } });
					break;
				case 'orderRefundAsk':
					this.$router.push({ path: '/member/refund?order_goods_id=' + orderData.order_goods_id });
					break;
			}
		},
		orderDetail(data) {
			this.$router.push({ path: '/member/refund_detail', query: { order_goods_id: data.order_goods_id } });
		},
		imageError(refundIndex) {
			this.refundList[refundIndex].sku_image = this.defaultGoodsImage;
		},

		cancleRefund(order_goods_id) {
			this.$confirm('撤销之后本次申请将会关闭,如后续仍有问题可再次发起申请', '提示', {
				confirmButtonText: '确认撤销',
				cancelButtonText: '暂不撤销',
				type: 'warning'
			})
				.then(() => {
					if (this.isSub) return;
					this.isSub = true;

					cancleRefund({ order_goods_id: order_goods_id })
						.then(res => {
							const { code, message, data } = res;
							if (code >= 0) {
								this.$message({
									message: '撤销成功',
									type: 'success'
								});
								this.getrefundList();
							} else {
								this.$message({ message: message, type: 'warning' });
							}
						})
						.catch(err => {
							this.$message.error(err.message);
						});
				})
				.catch(() => {});
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
				width: 15%;
			}
			&:nth-child(3) {
				width: 10%;
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
							cursor: pointer;
							display: inline-block;
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
					width: 15%;
				}
				&:nth-child(3) {
					width: 10%;
				}
				&:nth-child(4) {
					width: 14%;
					line-height: 30px;
					a {
						display: block;
					}
				}
				&:nth-child(5) {
					width: 15%;
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
