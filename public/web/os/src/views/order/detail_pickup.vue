<template>
	<div class="box">
		<div class="null-page" v-show="yes"></div>
		
		<el-card class="box-card order-detail">
			<div slot="header" class="clearfix">
				<el-breadcrumb separator="/">
					<el-breadcrumb-item :to="{ path: '/member/order_list' }">订单列表</el-breadcrumb-item>
					<el-breadcrumb-item>订单详情</el-breadcrumb-item>
				</el-breadcrumb>
			</div>
			<div v-loading="loading">
				<template v-if="orderDetail">
					<div class="order-status">
						<h4>
							订单状态：
							<span class="ns-text-color">{{ orderDetail.order_status_name }}</span>
						</h4>
						<div v-if="orderDetail.order_status == 0" class="go-pay">
							<p>
								需付款：
								<span>￥{{ orderDetail.pay_money }}</span>
							</p>
						</div>

						<div class="operation" v-if="orderDetail.action.length > 0">
							<el-button type="primary" size="mini" plain v-if="orderDetail.is_evaluate == 1" @click="operation('memberOrderEvaluation')">
								<template v-if="orderDetail.evaluate_status == 0">
									评价
								</template>
								<template v-else-if="orderDetail.evaluate_status == 1">
									追评
								</template>
							</el-button>

							<el-button
								type="primary"
								size="mini"
								:plain="operationItem.action == 'orderPay' ? false : true"
								v-for="(operationItem, operationIndex) in orderDetail.action"
								:key="operationIndex"
								@click="operation(operationItem.action)"
							>
								{{ operationItem.title }}
							</el-button>
						</div>
						<div class="operation" v-else-if="orderDetail.action.length == 0 && orderDetail.is_evaluate == 1">
							<el-button type="primary" size="mini" plain v-if="orderDetail.is_evaluate == 1" @click="operation('memberOrderEvaluation')">
								<template v-if="orderDetail.evaluate_status == 0">
									评价
								</template>
								<template v-else-if="orderDetail.evaluate_status == 1">
									追评
								</template>
							</el-button>
						</div>
					</div>

					<div class="pickup-info" v-if="orderDetail.pay_status">
						<h4>
							自提点：
							<span class="ns-text-color">{{ orderDetail.delivery_store_name }}</span>
						</h4>
						<ul>
							<li>
								提货码：
								<span class="ns-text-color">{{ orderDetail.delivery_code }}</span>
							</li>
							<li v-if="orderDetail.delivery_store_info && orderDetail.delivery_store_info.open_date">营业时间：{{ orderDetail.delivery_store_info.open_date }}</li>
							<li v-if="orderDetail.delivery_store_info && orderDetail.delivery_store_info.telphone">联系方式：{{ orderDetail.delivery_store_info.telphone }}</li>
							<li v-if="orderDetail.delivery_store_info && orderDetail.delivery_store_info.full_address">详细地址：{{ orderDetail.delivery_store_info.full_address }}</li>
						</ul>
						<img :src="$img(orderDetail.pickup)" />
					</div>

					<nav>
						<li :class="{ 'no-operation': !orderDetail.is_enable_refund }">商品信息</li>
						<li>单价</li>
						<li>数量</li>
						<li>小计</li>
						<li v-if="orderDetail.is_enable_refund">操作</li>
					</nav>

					<div class="order-info">
						<h4>订单信息</h4>
						<ul>
							<li>
								<i class="iconfont iconmendian"></i>
								店铺：
								<router-link :to="'/shop-' + orderDetail.site_id" target="_blank">{{ orderDetail.site_name }}</router-link>
							</li>
							<li>订单类型：{{ orderDetail.order_type_name }}</li>
							<li>订单编号：{{ orderDetail.order_no }}</li>
							<li>订单交易号：{{ orderDetail.out_trade_no }}</li>
							<li>配送方式：{{ orderDetail.delivery_type_name }}</li>
							<li>创建时间：{{ $util.timeStampTurnTime(orderDetail.create_time) }}</li>
							<li v-if="orderDetail.close_time > 0">关闭时间：{{ $util.timeStampTurnTime(orderDetail.close_time) }}</li>
							<template v-if="orderDetail.pay_status > 0">
								<li>支付方式：{{ orderDetail.pay_type_name }}</li>
								<li>支付时间：{{ $util.timeStampTurnTime(orderDetail.pay_time) }}</li>
							</template>
							<li v-if="orderDetail.promotion_type_name != ''">店铺活动：{{ orderDetail.promotion_type_name }}</li>
							<li v-if="orderDetail.buyer_message != ''">买家留言：{{ orderDetail.buyer_message }}</li>
						</ul>
					</div>

					<div class="take-delivery-info">
						<h4>收货信息</h4>
						<ul>
							<li>收货人：{{ orderDetail.name }}</li>
							<li>手机号码：{{ orderDetail.mobile }}</li>
							<li>收货地址：{{ orderDetail.full_address }} {{ orderDetail.address }}</li>
						</ul>
					</div>

					<!-- 订单项·商品 -->
					<div class="list">
						<ul class="item" v-for="(goodsItem, goodsIndex) in orderDetail.order_goods" :key="goodsIndex">
							<li :class="{ 'no-operation': !orderDetail.is_enable_refund }">
								<div class="img-wrap" @click="$router.pushToTab('/sku-' + goodsItem.sku_id)">
									<img :src="$img(goodsItem.sku_image, { size: 'mid' })" @error="imageError(goodsIndex)" />
								</div>
								<div class="info-wrap">
									<h5 @click="$router.pushToTab('/sku-' + goodsItem.sku_id)">{{ goodsItem.sku_name }}</h5>
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
								<span>￥{{ (goodsItem.price * goodsItem.num).toFixed(2) }}</span>
							</li>
							<li v-if="orderDetail.is_enable_refund">
								<el-button
									type="primary"
									plain
									size="mini"
									v-if="goodsItem.refund_status == 0 || goodsItem.refund_status == -1"
									@click="$router.push({ path: '/member/refund', query: { order_goods_id: goodsItem.order_goods_id } })"
								>
									退款
								</el-button>
								<el-button
									type="primary"
									plain
									size="mini"
									v-else
									@click="$router.push({ path: '/member/refund_detail', query: { order_goods_id: goodsItem.order_goods_id } })"
								>
									查看退款
								</el-button>
							</li>
						</ul>
					</div>

					<!-- 订单总计 -->
					<ul class="total">
						<li>
							<label>商品金额：</label>
							<span>￥{{ orderDetail.goods_money }}</span>
						</li>
						<li>
							<label>运费：</label>
							<span>￥{{ orderDetail.delivery_money }}</span>
						</li>
						<li v-if="orderDetail.invoice_money > 0">
							<label>税费：</label>
							<span>￥{{ orderDetail.invoice_money }}</span>
						</li>
						<li v-if="orderDetail.adjust_money != 0">
							<label>订单调整：</label>
							<span>
								<template v-if="orderDetail.adjust_money < 0">
									-
								</template>
								<template v-else>
									+
								</template>
								￥{{ orderDetail.adjust_money | abs }}
							</span>
						</li>
						<li v-if="orderDetail.promotion_money > 0">
							<label>优惠：</label>
							<span>￥{{ orderDetail.promotion_money }}</span>
						</li>
						<li v-if="orderDetail.coupon_money > 0">
							<label>优惠券：</label>
							<span>￥{{ orderDetail.coupon_money }}</span>
						</li>
						<li v-if="orderDetail.balance_money > 0">
							<label>使用余额：</label>
							<span>￥{{ orderDetail.balance_money }}</span>
						</li>
						<li class="pay-money">
							<label>实付款：</label>
							<span>￥{{ orderDetail.pay_money }}</span>
						</li>
					</ul>
				</template>
			</div>
		</el-card>
	</div>	
</template>

<script>
import { apiOrderDetail } from '@/api/order/order';
import orderMethod from '@/utils/orderMethod';
import { mapGetters } from 'vuex';
export default {
	name: 'order_detail_pickup',
	components: {},
	mixins: [orderMethod],
	data: () => {
		return {
			orderId: 0,
			orderDetail: null,
			loading: true,
			yes: true
		};
	},
	created() {
		this.orderId = this.$route.query.order_id;
		this.getOrderDetail();
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
		getOrderDetail() {
			apiOrderDetail({
				order_id: this.orderId
			})
				.then(res => {
					if (res.code >= 0) {
						this.orderDetail = res.data;
						if (this.orderDetail.delivery_store_info != '') this.orderDetail.delivery_store_info = JSON.parse(this.orderDetail.delivery_store_info);
						this.loading = false;
					} else {
						this.$message({
							message: '未获取到订单信息',
							type: 'warning',
							duration: 2000,
							onClose: () => {
								this.$router.push({ path: '/member/order_list' });
							}
						});
					}
				})
				.catch(err => {
					this.loading = false;
					this.$message.error({
						message: err.message,
						duration: 2000,
						onClose: () => {
							this.$router.push({ path: '/member/order_list' });
						}
					});
				});
		},
		operation(action) {
			switch (action) {
				case 'orderPay': // 支付
					this.orderPay(this.orderDetail);
					break;
				case 'orderClose': //关闭
					this.orderClose(this.orderDetail.order_id, () => {
						this.getOrderDetail();
					});
					break;
				case 'memberTakeDelivery': //收货
					this.orderDelivery(this.orderDetail.order_id, () => {
						this.getOrderDetail();
					});
					break;
				case 'trace': //查看物流
					this.$router.push({ path: '/member/logistics', query: { order_id: this.orderDetail.order_id } });
					break;
				case 'memberOrderEvaluation': //评价
					this.$router.pushToTab({ path: '/evaluate', query: { order_id: this.orderDetail.order_id } });
					break;
			}
		},
		imageError(index) {
			this.orderDetail.order_goods[index].sku_image = this.defaultGoodsImage;
		}
	},
	filters: {
		abs(value) {
			return Math.abs(parseFloat(value)).toFixed(2);
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

.order-detail {
	.order-status {
		background-color: #fff;
		margin-bottom: 20px;
		h4 {
			margin: 10px 0 20px;
			border-bottom: 1px solid #eeeeee;
			padding-bottom: 10px;
		}
		.go-pay {
			p {
				display: inline-block;
				vertical-align: middle;
				span {
					font-weight: bold;
					color: $base-color;
					font-size: 18px;
				}
			}
		}
		.operation {
			margin-top: 10px;
		}
	}
	.order-info {
		background-color: #fff;
		margin-bottom: 10px;
		h4 {
			margin: 10px 0 20px;
			border-bottom: 1px solid #eeeeee;
			padding-bottom: 10px;
		}
		ul {
			display: flex;
			flex-wrap: wrap;
			li {
				flex: 0 0 33.3333%;
				margin-bottom: 10px;
				&:last-child {
					flex: initial;
				}
			}
		}
	}
	.take-delivery-info {
		background-color: #fff;
		margin-bottom: 10px;
		h4 {
			margin: 10px 0 20px;
			border-bottom: 1px solid #eeeeee;
			padding-bottom: 10px;
		}
		ul {
			display: flex;
			flex-wrap: wrap;
			li {
				flex: 0 0 33.3333%;
				margin-bottom: 10px;
				&:last-child {
					flex: initial;
				}
			}
		}
	}
	.pickup-info {
		background-color: #fff;
		margin-bottom: 10px;
		h4 {
			margin: 10px 0 20px;
			border-bottom: 1px solid #eeeeee;
			padding-bottom: 10px;
		}
		ul {
			display: flex;
			flex-wrap: wrap;
			li {
				flex: 0 0 33.3333%;
				margin-bottom: 10px;
				&:last-child {
					flex: initial;
				}
			}
		}
		img {
			width: 100px;
		}
	}
	nav {
		overflow: hidden;
		padding: 10px 0;
		background: #fff;
		border-bottom: 1px solid #eeeeee;
		li {
			float: left;
			&:nth-child(1) {
				width: 50%;
				&.no-operation {
					width: 60%;
				}
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
				width: 10%;
			}
		}
	}
	.list {
		border-bottom: 1px solid #eeeeee;
		.item {
			background-color: #fff;
			padding: 10px 0;
			overflow: hidden;
			li {
				float: left;
				line-height: 60px;
				&:nth-child(1) {
					width: 50%;
					line-height: inherit;
					&.no-operation {
						width: 60%;
					}
					.img-wrap {
						width: 60px;
						height: 60px;
						float: left;
						margin-right: 10px;
						cursor: pointer;
					}
					.info-wrap {
						margin-left: 70px;
						cursor: pointer;
						h5 {
							font-weight: normal;
							font-size: $ns-font-size-base;
							display: -webkit-box;
							-webkit-box-orient: vertical;
							-webkit-line-clamp: 2;
							overflow: hidden;
							margin-right: 10px;
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
					width: 15%;
				}
				&:nth-child(5) {
					width: 10%;
				}
			}
		}
	}
	// 总计
	.total {
		padding: 20px;
		background-color: #fff;
		text-align: right;
		li {
			span {
				width: 150px;
				display: inline-block;
			}
			&.pay-money {
				font-weight: bold;
				span {
					color: $base-color;
					font-size: 16px;
					vertical-align: middle;
				}
			}
		}
	}
}
</style>
