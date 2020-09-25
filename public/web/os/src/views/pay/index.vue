<template>
	<div v-loading="loading">
		<div class="item-block">
			<div class="payment-detail">
				<div class="payment-media">
					<el-row>
						<el-col :span="4">
							<div class="media-left"><i class="el-icon-circle-check ns-text-color"></i></div>
						</el-col>

						<el-col :span="16">
							<div class="media-body">
								<el-row>
									<el-col :span="12">
										<div class="payment-text">您的订单已提交成功，正在等待处理！</div>
										<div>
											<span>应付金额：</span>
											<span class="payment-money ns-text-color">￥{{ payInfo.pay_money }}元</span>
										</div>
									</el-col>
									<el-col :span="12"></el-col>
								</el-row>
							</div>
						</el-col>

						<el-col :span="4">
							<div class="media-right">
								<div class="el-button--text" @click="orderOpen ? (orderOpen = false) : (orderOpen = true)">
									订单信息
									<i :class="orderOpen ? 'rotate' : ''" class="el-icon-arrow-down"></i>
								</div>
							</div>
						</el-col>
					</el-row>
				</div>

				<div class="order-info" v-if="orderOpen">
					<el-row>
						<el-col :span="4" class="order-info-left"></el-col>
						<el-col :span="20">
							<div class="line"></div>
							<div class="order-item">
								<div class="item-label">交易单号：</div>
								<div class="item-value">{{ payInfo.out_trade_no }}</div>
							</div>
							<div class="order-item">
								<div class="item-label">订单内容：</div>
								<div class="item-value">{{ payInfo.pay_detail }}</div>
							</div>
							<div class="order-item">
								<div class="item-label">订单金额：</div>
								<div class="item-value">￥{{ payInfo.pay_money }}</div>
							</div>
							<div class="order-item">
								<div class="item-label">创建时间：</div>
								<div class="item-value">{{ $timeStampTurnTime(payInfo.create_time) }}</div>
							</div>
						</el-col>
					</el-row>
				</div>
			</div>
		</div>

		<div class="item-block">
			<div class="block-text">支付方式</div>
			<div class="pay-type-list">
				<div class="pay-type-item" v-for="(item, index) in payTypeList" :key="index" :class="payIndex == index ? 'active' : ''" @click="payIndex = index">
					{{ item.name }}
				</div>

				<div class="clear"></div>
			</div>
		</div>

		<div class="item-block">
			<div class="order-submit"><el-button type="primary" class="el-button--primary" @click="pay">立即支付</el-button></div>
			<div class="clear"></div>
		</div>

		<el-dialog title="请确认支付是否完成" :visible.sync="dialogVisible" width="23%" top="30vh" class="confirm-pay-wrap">
			<div class="info-wrap">
				<i class="el-message-box__status el-icon-warning"></i>
				<span>完成支付前请根据您的情况点击下面的按钮</span>
			</div>
			<span slot="footer" class="dialog-footer">
				<el-button @click="goIndex" size="small">返回首页</el-button>
				<el-button type="primary" @click="goOrderList" size="small">已完成支付</el-button>
			</span>
		</el-dialog>

		<!-- 微信支付弹框 -->
		<el-dialog title="请用微信扫码支付" :visible.sync="openQrcode" width="300px" center>
			<div class="wechatpay-box"><img :src="payQrcode" /></div>
		</el-dialog>
	</div>
</template>

<script>
import { getPayInfo, getPayType, checkPayStatus, pay } from '@/api/pay';

export default {
	name: 'pay',
	components: {},
	data: () => {
		return {
			orderOpen: false,
			outTradeNo: '',
			payInfo: {
				pay_money: 0
			},
			payIndex: 0,
			payTypeList: [
				{
					name: '支付宝支付',
					icon: 'iconzhifubaozhifu-',
					type: 'alipay'
				},
				{
					name: '微信支付',
					icon: 'iconweixinzhifu',
					type: 'wechatpay'
				}
			],
			payUrl: '',
			timer: null,
			payQrcode: '',
			openQrcode: false,
			loading: true,
			test: null,
			dialogVisible: false
		};
	},
	created() {
		if (!this.$route.query.code) {
			this.$router.push({ path: '/' });
			return;
		}
		this.outTradeNo = this.$route.query.code;
		this.getPayInfo();
		this.getPayType();
		this.checkPayStatus();
	},
	methods: {
		getPayInfo() {
			getPayInfo({ out_trade_no: this.outTradeNo, forceLogin: true })
				.then(res => {
					const { code, message, data } = res;
					if (data) {
						this.payInfo = res.data;
					}
					this.loading = false;
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
		getPayType() {
			getPayType({})
				.then(res => {
					const { code, message, data } = res;
					if (code >= 0) {
						this.payTypeList.forEach((val, key) => {
							if (res.data.pay_type.indexOf(val.type) == -1) {
								this.payTypeList.splice(key, 1);
							}
						});
					}
				})
				.catch(err => {
					this.$message.error(err.message);
				});
		},
		checkPayStatus() {
			this.timer = setInterval(() => {
				checkPayStatus({ out_trade_no: this.outTradeNo })
					.then(res => {
						const { code, message, data } = res;
						if (code >= 0) {
							if (code == 0) {
								if (data.pay_status == 2) {
									clearInterval(this.timer);
									this.dialogVisible = false;
									this.$router.push({ path: '/result?code=' + this.payInfo.out_trade_no });
								}
							} else {
								clearInterval(this.timer);
							}
						}
					})
					.catch(err => {
						clearInterval(this.timer);
						this.$router.push({ path: '/' });
					});
			}, 1000);
		},
		pay() {
			var payType = this.payTypeList[this.payIndex];
			if (!payType) return;

			if(payType.type == 'alipay') var newWindow = window.open();
			pay({ out_trade_no: this.payInfo.out_trade_no, pay_type: payType.type, app_type: 'pc' })
				.then(res => {
					const { code, message, data } = res;
					if (code >= 0) {
						switch (payType.type) {
							case 'alipay':
								this.payUrl = res.data.data;
								newWindow.location.href = this.payUrl;
								this.open();
								break;
							case 'wechatpay':
								this.payQrcode = res.data.qrcode;
								this.openQrcode = true;
								break;
						}
					} else {
						this.$message({ message: message, type: 'warning' });
					}
				})
				.catch(err => {
					this.$message.error(err.message);
				});
		},
		open() {
			this.dialogVisible = true;
		},
		goIndex() {
			clearInterval(this.timer);
			this.dialogVisible = false;
			this.$router.push({ path: '/' });
		},
		goOrderList() {
			clearInterval(this.timer);
			this.dialogVisible = false;
			this.$router.push({ path: '/member/order_list' });
		}
	}
};
</script>

<style lang="scss" scoped>
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

.media-left {
	text-align: center;
	i {
		font-size: 65px;
	}
}
.payment-detail {
	padding: 30px 0;
	transition: 2s;
}
.media-right {
	text-align: center;
	line-height: 65px;
	cursor: pointer;
	i.rotate {
		transform: rotate(180deg);
		transition: 0.3s;
	}
}
.payment-text {
	font-size: 20px;
}
.payment-time {
	font-size: 12px;
	line-height: 65px;
	color: #999;
}

//支付方式
.order-submit {
	float: right;
	padding: 10px;
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
.mobile-wrap {
	width: 300px;
}

.order-info {
	.order-item {
		padding: 1px 0;
		.item-label {
			display: inline-block;
			width: 100px;
		}
		.item-value {
			display: inline-block;
		}
	}
	.line {
		width: 100%;
		height: 1px;
		background: #f2f2f2;
		margin: 20px 0 10px 0;
	}

	.order-info-left {
		height: 1px;
	}
}

.wechatpay-box {
	text-align: center;
	img {
		width: 80%;
	}
}
.confirm-pay-wrap {
	.el-dialog__body {
		padding: 10px 15px;
	}
	.info-wrap {
		i {
			position: initial;
			vertical-align: middle;
			transform: initial;
		}
		span {
			vertical-align: middle;
			padding: 0 10px;
		}
	}
}
</style>
<style lang="scss">
.confirm-pay-wrap {
	.el-dialog__body {
		padding: 10px 15px;
	}
	.el-dialog__footer {
		padding-top: 0;
		padding-bottom: 10px;
	}
}
</style>
