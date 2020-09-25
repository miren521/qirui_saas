<template>
	<div v-loading="fullscreenLoading">
		<div class="pay">
			<div class="pay-icon"><i class=" ns-text-color" :class="payInfo.pay_status ? 'el-icon-circle-check' : 'el-icon-circle-close\n'"></i></div>
			<div class="pay-text">{{ payInfo.pay_status ? '支付成功' : '支付失败' }}</div>
			<div class="pay-money">支付金额：￥{{ payInfo.pay_money }}</div>
			<div class="pay-footer">
				<router-link to="/member/index"><el-button type="primary">会员中心</el-button></router-link>
				<router-link to="/index" class="pay-button"><el-button>回到首页</el-button></router-link>
			</div>
		</div>
	</div>
</template>

<script>
import { getPayInfo } from '@/api/pay';

export default {
	name: 'pay_result',
	components: {},
	data: () => {
		return {
			payInfo: {},
			outTradeNo: '',
			fullscreenLoading: true
		};
	},
	created() {
		if (!this.$route.query.code) {
			this.$router.push({ path: '/' });
			return;
		}
		this.outTradeNo = this.$route.query.code;
		this.getPayInfo();
	},
	methods: {
		getPayInfo() {
			getPayInfo({ out_trade_no: this.outTradeNo, forceLogin: true })
				.then(res => {
					const { code, message, data } = res;
					if (code >= 0 && data) {
						this.payInfo = res.data;
					} else {
						this.$message({
							message: '未获取到支付信息',
							type: 'warning',
							duration: 2000,
							onClose: () => {
								this.$router.push({ path: '/member/order_list' });
							}
						});
					}
					this.fullscreenLoading = false;
				})
				.catch(err => {
					this.fullscreenLoading = false;
					this.$message.error({
						message: err.message,
						duration: 2000,
						onClose: () => {
							this.$router.push({ path: '/member/order_list' });
						}
					});
				});
		}
	}
};
</script>
<style lang="scss" scoped>
.pay {
	padding: 40px 15px;
	margin: 10px 0;
	border-radius: 0;
	border: none;
	background: #ffffff;
	.pay-icon {
		text-align: center;
		i {
			font-size: 65px;
		}
	}
	.pay-text {
		text-align: center;
		font-size: 16px;
		margin-top: 10px;
	}
	.pay-money {
		text-align: center;
		color: $base-color;
		font-size: $ns-font-size-lg;
	}
	.pay-footer {
		text-align: center;
		margin-top: 30px;
		.pay-button {
			margin-left: 15px;
		}
	}
}
</style>
