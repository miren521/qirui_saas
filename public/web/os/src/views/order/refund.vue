<template>
	<div class="box">
		<div class="null-page" v-show="yes"></div>
		
		<div v-loading="loading">
			<el-card class="box-card order-list">
				<div slot="header" class="clearfix">
					<el-breadcrumb separator="/">
						<el-breadcrumb-item :to="{ path: '/member/order_list' }">我的订单</el-breadcrumb-item>
						<el-breadcrumb-item :to="{ path: '/member/order_detail?order_id=' + orderId }">订单详情</el-breadcrumb-item>
						<el-breadcrumb-item>退款</el-breadcrumb-item>
					</el-breadcrumb>
				</div>

				<!--商品信息-->
				<div class="goods-list">
					<table>
						<tr>
							<td width="62.5%">商品</td>
							<td width="12.5%">数量</td>
							<td width="12.5%">金额</td>
						</tr>
					</table>
				</div>

				<div class="goods-list">
					<table>
						<tr>
							<td width="62.5%">
								<div class="goods-info">
									<div class="goods-info-left">
										<router-link :to="{ path: '/sku-' + refundData.order_goods_info.sku_id }" target="_blank">
											<img
												class="goods-img"
												:src="$img(refundData.order_goods_info.sku_image, { size: 'mid' })"
												@error="refundData.order_goods_info.sku_image = defaultGoodsImage"
											/>
										</router-link>
									</div>
									<div class="goods-info-right">
										<router-link :to="{ path: '/sku-' + refundData.order_goods_info.sku_id }" target="_blank">
											<div class="goods-name">{{ refundData.order_goods_info.sku_name }}</div>
										</router-link>
									</div>
								</div>
							</td>
							<td width="12.5%" class="goods-num">{{ refundData.order_goods_info.num }}</td>
							<td width="12.5%" class="goods-money">￥{{ refundData.order_goods_info.goods_money }}</td>
						</tr>
					</table>
				</div>
			</el-card>

			<!--退款类型 -->
			<div class="item-block">
				<div class="block-text">退款类型</div>

				<div class="pay-type-list">
					<div class="pay-type-item" :class="refundType == 1 ? 'active' : ''" @click="selectRefundType(1)">退款无需退货</div>
					<div v-if="refundData.refund_type.length == 2" class="pay-type-item" :class="refundType == 2 ? 'active' : ''" @click="selectRefundType(2)">退货退款</div>
					<div class="clear"></div>
				</div>
			</div>

			<!--退款填写-->
			<div class="item-block">
				<div class="block-text"></div>

				<el-form ref="form" label-width="80px" class="refund-form">
					<el-form-item label="退款金额"><el-input disabled="" :value="refundData.refund_money"></el-input></el-form-item>
					<el-form-item label="退款原因">
						<el-select placeholder="请选择" v-model="refundReason">
							<el-option v-for="(item, itemIndex) in refundData.refund_reason_type" :key="itemIndex" :label="item" :value="item"></el-option>
						</el-select>
					</el-form-item>

					<el-form-item label="退款说明">
						<el-input maxlength="140" show-word-limit resize="none" rows="5" placeholder="请输入退款说明（选填）" type="textarea" v-model="refundRemark"></el-input>
					</el-form-item>
				</el-form>
			</div>

			<div class="item-block">
				<div class="order-submit"><el-button type="primary" class="el-button--primary" @click="submit">提交</el-button></div>
				<div class="clear"></div>
			</div>
		</div>
	</div>
</template>

<script>
import { refundData, refund, detail, delivery } from '@/api/order/refund';
import { mapGetters } from 'vuex';

export default {
	name: 'refund',
	components: {},
	data: () => {
		return {
			orderGoodsId: '',
			orderId: '',
			refundType: 1,
			refundReason: '',
			refundRemark: '',
			isIphoneX: false,
			refundData: {
				refund_type: [],
				order_goods_info: {
					sku_image: ''
				}
			},
			isSub: false,
			show_type: 0, //退款状态 1-待退款 2-已退款
			detail: {
				refund_action: []
			},
			loading: true,
			yes: true
		};
	},
	created() {
		if (this.$route.query.order_goods_id) this.orderGoodsId = this.$route.query.order_goods_id;
		if (this.$route.query.order_id) this.orderId = this.$route.query.order_id;
		this.getRefundData();
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
		/**
		 * 选择退款方式
		 * @param {Object} type
		 */
		selectRefundType(type) {
			this.refundType = type;
		},
		getRefundData() {
			refundData({ order_goods_id: this.orderGoodsId })
				.then(res => {
					const { code, message, data } = res;
					if (code >= 0) {
						this.refundData = data;
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
		submit() {
			if (this.verify()) {
				if (this.isSub) return;
				this.isSub = true;

				let submit_data = {
					order_goods_id: this.orderGoodsId,
					refund_type: this.refundType,
					refund_reason: this.refundReason,
					refund_remark: this.refundRemark
				};

				refund(submit_data)
					.then(res => {
						const { code, message, data } = res;
						if (code >= 0) {
							this.$message({
								message: '申请成功！',
								type: 'success',
								duration: 2000,
								onClose: () => {
									this.$router.push({ path: '/member/activist' });
								}
							});
						} else {
							this.isSub = false;
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
			}
		},
		verify() {
			if (this.refundReason == '') {
				this.$message({ message: '请选择退款原因', type: 'warning' });
				return false;
			}
			return true;
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
	padding: 0 15px 1px;
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
.refund-form {
	width: 350px;
	.el-select {
		width: 100%;
	}
}
.order-submit {
	text-align: center;
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
.el-textarea .el-input__count {
	line-height: 20px;
}
</style>
