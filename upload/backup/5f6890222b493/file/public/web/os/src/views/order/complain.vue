<template>
	<div class="box">
		<div class="null-page" v-show="yes"></div>
		
		<div v-loading="loading">
			<!--申请退款 start-->
			<div v-if="!complainData.complain_info || action == 'apply'">
				<el-card class="box-card order-list">
					<div slot="header" class="clearfix">
						<el-breadcrumb separator="/">
							<el-breadcrumb-item :to="{ path: '/member/activist' }">退款/售后</el-breadcrumb-item>
							<el-breadcrumb-item :to="{ path: '/member/refund_detail?order_goods_id=' + orderGoodsId }">退款详情</el-breadcrumb-item>
							<el-breadcrumb-item>平台维权</el-breadcrumb-item>
						</el-breadcrumb>
					</div>
				

					<!--商品信息-->
					<div>
						<div class="goods-list">
							<table>
								<tr>
									<td width="62.5%">商品</td>
									<td width="12.5%">数量</td>
									<td width="12.5%">金额</td>
								</tr>
							</table>
						</div>
					</div>

					<div>
						<div class="goods-list">
							<table>
								<tr>
									<td width="62.5%">
										<div class="goods-info">
											<div class="goods-info-left">
												<router-link :to="{ path: '/sku-' + complainData.order_goods_info.sku_id }" target="_blank">
													<img
														class="goods-img"
														:src="$img(complainData.order_goods_info.sku_image, { size: 'mid' })"
														@error="complainData.order_goods_info.sku_image = defaultGoodsImage"
													/>
												</router-link>
											</div>
											<div class="goods-info-right">
												<router-link :to="{ path: '/sku-' + complainData.order_goods_info.sku_id }" target="_blank">
													<div class="goods-name">{{ complainData.order_goods_info.sku_name }}</div>
												</router-link>
											</div>
										</div>
									</td>
									<td width="12.5%" class="goods-num">{{ complainData.order_goods_info.num }}</td>
									<td width="12.5%" class="goods-money">￥{{ complainData.order_goods_info.goods_money }}</td>
								</tr>
							</table>
						</div>
					</div>
				</el-card>

				<!--退款填写-->
				<div class="item-block">
					<div class="block-text"></div>

					<el-form ref="form" label-width="80px" class="refund-form">
						<el-form-item label="退款金额"><el-input disabled="" :value="complainData.refund_money"></el-input></el-form-item>
						<el-form-item label="退款原因">
							<el-select placeholder="请选择" v-model="complainReason">
								<el-option v-for="(item, itemIndex) in complainData.refund_reason_type" :key="itemIndex" :label="item" :value="item"></el-option>
							</el-select>
						</el-form-item>

						<el-form-item label="退款说明">
							<el-input maxlength="140" show-word-limit resize="none" rows="5" placeholder="请输入退款说明（选填）" type="textarea" v-model="complainRemark"></el-input>
						</el-form-item>
					</el-form>
				</div>

				<div class="item-block">
					<div class="order-submit"><el-button type="primary" class="el-button--primary" @click="submit">提交</el-button></div>
					<div class="clear"></div>
				</div>
			</div>
			<!--申请退款 end-->

			<div v-else>
				<div class="item-block">
					<div class="block-text">{{ detail.complain_status_name }}</div>
				</div>

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
								:class="{ buyer: logItem.action_way == 1, seller: logItem.action_way == 2, platform: logItem.action_way == 3 }"
								v-for="(logItem, logIndex) in detail.refund_log_list"
								:key="logIndex"
								:timestamp="$util.timeStampTurnTime(logItem.action_time)"
								placement="top"
							>
								<div>
									<h4 v-if="logItem.action_way == 1">买家</h4>
									<h4 v-else-if="logItem.action_way == 2">卖家</h4>
									<h4 v-else-if="logItem.action_way == 3">平台</h4>
									<p>{{ logItem.action }}</p>
								</div>
							</el-timeline-item>
						</el-timeline>
					</div>
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
												<img class="goods-img" :src="$img(detail.sku_image, { size: 'mid' })" @error="detail.sku_image = defaultGoodsImage" />
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
								<td width="12.5%" class="goods-money">￥{{ detail.complain_apply_money }}</td>
							</tr>
						</table>
					</div>
				</div>

				<div class="item-block">
					<div class="order-statistics">
						<table>
							<tr>
								<td align="right">退款原因：</td>
								<td align="left">{{ detail.complainReason }}</td>
							</tr>
							<tr>
								<td align="right">退款金额：</td>
								<td align="left">￥{{ detail.complain_apply_money }}</td>
							</tr>
							<tr>
								<td align="right">退款编号：</td>
								<td align="left">{{ detail.complain_no }}</td>
							</tr>
							<tr>
								<td align="right">申请时间：</td>
								<td align="left">{{ $util.timeStampTurnTime(detail.complain_apply_time) }}</td>
							</tr>
							<tr v-if="detail.complain_time">
								<td align="right">退款时间：</td>
								<td align="left">{{ $util.timeStampTurnTime(detail.complain_time) }}</td>
							</tr>
						</table>
					</div>
					<div class="clear"></div>
				</div>

				<div class="item-block" v-if="detail.complain_action.length">
					<div class="order-submit" v-for="(actionItem, actionIndex) in detail.complain_action" :key="actionIndex">
						<el-button type="primary" class="el-button--primary" @click="refundAction(actionItem.event)">{{ actionItem.title }}</el-button>
					</div>

					<div class="clear"></div>
				</div>
			</div>
		</div>
	</div>
</template>

<script>
import { mapGetters } from 'vuex';
import { complainData, complain, complainCancel } from '@/api/order/refund';

export default {
	name: 'refund',
	components: {},
	data: () => {
		return {
			orderGoodsId: 0,
			complainData: {
				order_goods_info: {
					sku_image: '',
					sku_name: ''
				}
			},
			detail: {
				sku_image: ''
			},
			complainReason: '',
			complainRemark: '',
			action: '',
			actionOpen: false,
			loading: false,
			yes: true
		};
	},
	created() {
		this.loading = true;
		if (this.$route.query.order_goods_id) this.orderGoodsId = this.$route.query.order_goods_id;
		this.getComplainData();
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
		/**
		 * 选择退款方式
		 * @param {Object} type
		 */
		selectRefundType(type) {
			this.refund_type = type;
		},
		getComplainData() {
			this.loading = true;
			complainData({ order_goods_id: this.orderGoodsId })
				.then(res => {
					const { code, message, data } = res;
					if (code >= 0) {
						this.complainData = res.data;
						this.detail = this.complainData.complain_info;
						this.loading = false;
					} else {
						this.$message({
							message: '未获取到该订单项退款信息',
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
		submit() {
			if (this.verify()) {
				if (this.isSub) return;
				this.isSub = true;

				let submit_data = {
					order_goods_id: this.orderGoodsId,
					complain_reason: this.complainReason,
					complain_remark: this.complainRemark
				};

				complain(submit_data)
					.then(res => {
						const { code, message, data } = res;
						if (code >= 0) {
							this.$message({
								message: message,
								type: 'success'
							});

							this.getComplainData();
							this.$forceUpdate();
							this.action = '';
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
								this.$router.push({ path: '/member/order_list' });
							}
						});
					});
			}
		},
		verify() {
			if (this.complainReason == '') {
				this.$message({ message: '请选择退款原因', type: 'warning' });
				return false;
			}
			return true;
		},

		refundAction(event) {
			switch (event) {
				case 'complainCancel':
					this.cancleRefund(this.detail.order_goods_id);
					break;
				case 'complainApply':
					this.action = 'apply';
					break;
			}
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

					complainCancel({ order_goods_id: order_goods_id })
						.then(res => {
							const { code, message, data } = res;
							if (code >= 0) {
								this.$message({
									message: '撤销成功',
									type: 'success',
									duration: 2000,
									onClose: () => {
										this.$router.push({ path: '/member/order_list' });
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
									this.$router.push({ path: '/member/order_list' });
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
