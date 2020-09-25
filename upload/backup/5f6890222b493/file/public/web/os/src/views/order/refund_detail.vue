<template>
	<div class="box">
		<div class="null-page" v-show="yes"></div>
		
		<div v-loading="loading">
			<!-- 退款详情start -->
			<div>
				<el-card class="box-card order-list">
					<div slot="header" class="clearfix">
						<el-breadcrumb separator="/">
							<el-breadcrumb-item :to="{ path: '/member/activist' }">退款/售后</el-breadcrumb-item>
							<el-breadcrumb-item>退款详情</el-breadcrumb-item>
						</el-breadcrumb>
					</div>

					<div>
						<div class="block-text">{{ detail.refund_status_name }}</div>

						<div class="status-wrap">
							<div class="refund-explain" v-if="detail.refund_status == 1">
								<div>如果商家拒绝，你可重新发起申请</div>
								<div>如果商家同意，将通过申请并退款给你</div>
								<div>如果商家逾期未处理，平台将自动通过申请并退款给你</div>
							</div>
							<div class="refund-explain" v-if="detail.refund_status == 5">
								<div>如果商家确认收货将会退款给你</div>
								<div>如果商家拒绝收货，该次退款将会关闭，你可以重新发起退款</div>
							</div>
						</div>
					</div>
				</el-card>

				<!--协商记录-->
				<div class="item-block">
					<div class="action-box">
						<span class="media-left">协商记录</span>
						<div class="media-right">
							<div class="el-button--text" @click="actionOpen ? (actionOpen = false) : (actionOpen = true)">
								协商记录
								<i :class="actionOpen ? 'rotate' : ''" class="el-icon-arrow-down"></i>
							</div>
						</div>
						<div class="clear"></div>
					</div>
					<div v-if="actionOpen">
						<el-timeline>
							<el-timeline-item
								:class="logItem.action_way == 1 ? 'buyer' : 'seller'"
								v-for="(logItem, logIndex) in detail.refund_log_list"
								:key="logIndex"
								:timestamp="$util.timeStampTurnTime(logItem.action_time)"
								placement="top"
							>
								<div>
									<h4>{{ logItem.action_way == 1 ? '买家' : '卖家' }}</h4>
									<p>{{ logItem.action }}</p>
								</div>
							</el-timeline-item>
						</el-timeline>
					</div>
				</div>

				<!-- 退货地址 -->
				<div class="item-block" v-if="detail.refund_status == 4">
					<div class="block-text">退货地址：{{ detail.shop_address }}</div>
				</div>

				<!--退款详情-->
				<div class="item-block">
					<div class="goods-list">
						<table>
							<tr>
								<td width="62.5%">商品</td>
								<td width="12.5%">数量</td>
								<td width="12.5%">退款金额</td>
							</tr>
						</table>
					</div>
				</div>

				<div class="item-block">
					<div class="goods-list">
						<table>
							<tr>
								<td width="62.5%">
									<div class="goods-info">
										<div class="goods-info-left">
											<router-link :to="{ path: '/sku-' + detail.sku_id }" target="_blank">
												<img class="goods-img" :src="$img(detail.sku_image)" @error="detail.sku_image = defaultGoodsImage" />
											</router-link>
										</div>
										<div class="goods-info-right">
											<router-link :to="{ path: '/sku-' + detail.sku_id }" target="_blank">
												<div class="goods-name">{{ detail.sku_name }}</div>
											</router-link>
										</div>
									</div>
								</td>
								<td width="12.5%" class="goods-num">{{ detail.num }}</td>
								<td width="12.5%" class="goods-money">￥{{ detail.refund_apply_money }}</td>
							</tr>
						</table>
					</div>
				</div>

				<div class="item-block">
					<div class="order-statistics">
						<table>
							<tr>
								<td align="right">退款方式：</td>
								<td align="left">{{ detail.refund_type == 1 ? '仅退款' : '退款退货' }}</td>
							</tr>
							<tr>
								<td align="right">退款原因：</td>
								<td align="left">{{ detail.refund_reason }}</td>
							</tr>
							<tr>
								<td align="right">退款金额：</td>
								<td align="left">￥{{ detail.refund_apply_money }}</td>
							</tr>
							<tr>
								<td align="right">退款编号：</td>
								<td align="left">{{ detail.refund_no }}</td>
							</tr>
							<tr>
								<td align="right">申请时间：</td>
								<td align="left">{{ $util.timeStampTurnTime(detail.refund_action_time) }}</td>
							</tr>
							<tr v-if="detail.refund_time">
								<td align="right">退款时间：</td>
								<td align="left">{{ $util.timeStampTurnTime(detail.refund_time) }}</td>
							</tr>
						</table>
					</div>
					<div class="clear"></div>
				</div>

				<div class="item-block" v-if="detail.refund_action.length">
					<div class="order-submit" v-for="(actionItem, actionIndex) in detail.refund_action" :key="actionIndex">
						<el-button type="primary" class="el-button--primary" @click="refundAction(actionItem.event)">{{ actionItem.title }}</el-button>
					</div>

					<div class="order-submit" v-if="detail.complain_action">
						<el-button type="primary" class="el-button--primary" @click="refundAction('complain')">平台维权</el-button>
					</div>

					<div class="clear"></div>
				</div>
			</div>
			<!-- 退款详情end -->

			<!-- 输入物流信息弹出 -->
			<el-dialog title="输入发货物流" :visible.sync="refundDeliveryDialog" width="50%">
				<el-form ref="form" :model="formData" label-width="80px">
					<el-form-item label="物流公司"><el-input v-model="formData.refund_delivery_name" placeholder="请输入物流公司"></el-input></el-form-item>

					<el-form-item label="物流单号"><el-input v-model="formData.refund_delivery_no" placeholder="请输入物流单号"></el-input></el-form-item>

					<el-form-item label="物流说明"><el-input v-model="formData.refund_delivery_remark" placeholder="选填"></el-input></el-form-item>
				</el-form>
				<span slot="footer" class="dialog-footer">
					<el-button @click="refundDeliveryDialog = false">取 消</el-button>
					<el-button type="primary" @click="refurnGoods('form')">确 定</el-button>
				</span>
			</el-dialog>
		</div>
	</div>
</template>

<script>
import { refundData, refund, detail, delivery, cancleRefund } from '@/api/order/refund';
import { mapGetters } from 'vuex';
export default {
	name: 'refund_detail',
	components: {},
	data: () => {
		return {
			orderGoodsId: '',
			isSub: false,
			detail: {
				refund_action: []
			},
			formData: {
				refund_delivery_name: '',
				refund_delivery_no: '',
				refund_delivery_remark: ''
			},
			actionOpen: false, //协商记录
			refundDeliveryDialog: false, //发货地址弹出
			loading: true,
			yes: true
		};
	},
	created() {
		if (this.$route.query.order_goods_id) this.orderGoodsId = this.$route.query.order_goods_id;
		this.getRefundDetail();
		if (this.$route.query.action && this.$route.query.action == 'returngoods') {
			this.refundDeliveryDialog = true;
		}
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
		//退款详情相关
		getRefundDetail() {
			this.loading = true;
			detail({ order_goods_id: this.orderGoodsId })
				.then(res => {
					const { code, message, data } = res;
					if (code >= 0) {
						this.detail = data;
					} else {
						this.$message({
							message: '未获取到该订单项退款信息！',
							type: 'warning',
							duration: 2000,
							onClose: () => {
								this.$router.push({ path: '/member/activist' });
							}
						});
					}
					this.loading = false;
				})
				.catch(err => {
					this.loading = false;
					this.$message.error({
						message: err.message,
						duration: 2000,
						onClose: () => {
							this.$router.push({ path: '/member/activist' });
						}
					});
				});
		},
		refundAction(event) {
			switch (event) {
				case 'orderRefundCancel':
					this.cancleRefund(this.detail.order_goods_id);
					break;
				case 'orderRefundDelivery':
					this.refundDeliveryDialog = true;
					break;
				case 'orderRefundAsk':
					this.$router.push({ path: '/member/refund?order_goods_id=' + this.detail.order_goods_id });
					break;
				case 'complain':
					this.$router.push({ path: '/member/complain?order_goods_id=' + this.detail.order_goods_id });
					break;
			}
		},
		refurnGoods() {
			if (this.formData.refund_delivery_name == '') {
				this.$message({ message: '请输入物流公司', type: 'warning' });
				return false;
			}
			if (this.formData.refund_delivery_no == '') {
				this.$message({ message: '请输入物流单号', type: 'warning' });
				return false;
			}
			this.formData.order_goods_id = this.orderGoodsId;
			if (this.isSub) return;
			this.isSub = true;

			delivery(this.formData)
				.then(res => {
					const { code, message, data } = res;
					if (code >= 0) {
						this.getRefundDetail();
						this.refundDeliveryDialog = false;
					} else {
						this.$message({
							message: '未获取到该订单项退款信息！',
							type: 'warning',
							duration: 2000,
							onClose: () => {
								this.$router.push({ path: '/member/activist' });
							}
						});
					}
				})
				.catch(err => {
					this.$message.error({
						message: err.message,
						duration: 2000,
						onClose: () => {
							this.$router.push({ path: '/member/activist' });
						}
					});
				});
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
									message: '撤销成功！',
									type: 'success',
									duration: 2000,
									onClose: () => {
										this.$router.push({ path: '/member/activist' });
									}
								});
							} else {
								this.$message({ message: message, type: 'warning' });
							}
						})
						.catch(err => {
							this.$message.error({
								message: err.message,
								duration: 2000,
								onClose: () => {
									this.$router.push({ path: '/member/activist' });
								}
							});
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

.el-card.is-always-shadow,
.el-card.is-hover-shadow:focus,
.el-card.is-hover-shadow:hover {
	box-shadow: unset;
}

.el-card {
	border: 0;
}
.clear {
	clear: both;
}
.item-block {
	padding: 0 20px 1px;
	margin: 10px 0;
	border-radius: 0;
	border: none;
	background: #ffffff;
	.block-text {
		border-color: #eeeeee;
		color: $ns-text-color-black;
		padding: 7px 0;
		border-bottom: 1px;
	}
}
.order-submit {
	float: right;
	padding: 10px;
}
.goods-list {
	padding: 15px 0;
	table {
		width: 100%;
	}
	.goods-info-left {
		width: 60px;
		height: 60px;
		float: left;
		.goods-img {
			width: 60px;
			height: 60px;
		}
	}
	.goods-info-right {
		float: left;
		height: 60px;
		margin-left: 10px;
		color: $base-color;
		width: 80%;
		.goods-name {
			line-height: 20px;
			padding-top: 10px;
			display: -webkit-box;
			-webkit-box-orient: vertical;
			-webkit-line-clamp: 2;
			overflow: hidden;
		}
		.goods-spec {
			color: #999;
		}
	}
}
.pay-type-list {
	padding: 20px 0;
}
.pay-type-item {
	display: inline-block;
	border: 2px solid #eeeeee;
	padding: 5px 20px;
	margin-right: 20px;
	cursor: pointer;
}
.pay-type-item.active {
	border-color: $base-color;
}
.status-wrap {
	color: #999;
}
.media-left {
	float: left;
}
.media-right {
	float: right;
	cursor: pointer;
	i.rotate {
		transform: rotate(180deg);
		transition: 0.3s;
	}
}
.action-box {
	padding: 10px 0;
}
.action-way {
	float: left;
	color: #999;
}
.head .time {
	float: right;
	color: #999;
}
.record-item {
	margin-bottom: 10px;
}
.order-statistics {
	float: left;
	padding: 10px;
	// color: #999;
}
</style>
