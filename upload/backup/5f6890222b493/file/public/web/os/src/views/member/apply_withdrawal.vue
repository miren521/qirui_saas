<template>
	<div class="box">
		<div class="null-page" v-show="yes"></div>
		
		<el-card class="box-card">
			<div slot="header" class="clearfix">
				<el-breadcrumb separator="/">
					<el-breadcrumb-item :to="{ path: '/member/account' }">账户余额</el-breadcrumb-item>
					<el-breadcrumb-item>提现申请</el-breadcrumb-item>
				</el-breadcrumb>
			</div>
			
			<div class="apply-withdrawal" v-loading="loading">
				<div class="apply-wrap">
					<div class="apply-account" v-if="bankAccountInfo.withdraw_type" @click="goAccount()">
						<span class="ns-width">提现到：</span>
						<div class="apply-account-info">
							<!-- <span v-if="bankAccountInfo.withdraw_type == 'wechatpay'">{{ bankAccountInfo.mobile }}</span> -->
							<span v-if="bankAccountInfo.withdraw_type == 'wechatpay'">暂不支持微信提现，请选择支付宝</span>
							<span v-else>{{ bankAccountInfo.bank_account }}</span>
							<el-image v-if="bankAccountInfo.withdraw_type == 'alipay'" :src="$img('upload/uniapp/member/apply_withdrawal/alipay.png')" fit="contain"></el-image>
							<el-image v-else-if="bankAccountInfo.withdraw_type == 'bank'" :src="$img('upload/uniapp/member/apply_withdrawal/bank.png')" fit="contain"></el-image>
							<!-- <el-image v-else-if="bankAccountInfo.withdraw_type == 'wechatpay'" :src="$img('upload/uniapp/member/apply_withdrawal/wechatpay.png')" fit="contain"></el-image> -->
							<i class="iconfont iconarrow-right"></i>
						</div>
					</div>
					<div class="apply-account" v-else @click="goAccount()">
						<span class="ns-width">提现账户：</span>
						<div class="apply-account-info">
							<span class="ns-text-color">请选择提现账户</span>
						</div>
					</div>
					<div class="apply-account-money demo-input-suffix">
						<span class="ns-width">提现金额：</span>
						<el-input type="number" placeholder="0" v-model="withdrawMoney" :disabled="bankAccountInfo.withdraw_type == 'wechatpay'">
							<template slot="prepend">￥</template>
						</el-input>
					</div>
					<div class="apply-account-desc">
						<p><span class="ns-width"></span><span>可提现余额为</span><span class="balance">￥{{ withdrawInfo.member_info.balance_money | moneyFormat }}</span><span @click="allTx">全部提现</span></p>
						<p><span class="ns-width"></span>最小提现金额为￥{{ withdrawInfo.config.min | moneyFormat }}，手续费为{{ withdrawInfo.config.rate + '%' }}</p>
					</div>
					<div class="apply-account-btn">
						<span class="ns-width"></span>
						<el-button type="primary" size="medium" @click="withdraw" :class="{ disabled: withdrawMoney == '' || withdrawMoney == 0 }" :disabled="bankAccountInfo.withdraw_type == 'wechatpay'">提现</el-button>
						<!-- <router-link to="/member/withdrawal">提现记录</router-link> -->
					</div>
				</div>
			</div>
		</el-card>
	</div>
</template>

<script>
    import { withdrawInfo, accountInfo, withdraw } from "@/api/member/account"
    export default {
        name: "apply_withdrawal",
        components: {},
        data: () => {
            return {
                withdrawInfo: {
                	config: {
                		is_use: 0,
                		min: 1,
                		rate: 0
                	},
                	member_info: {
                		balance_money: 0,
                		balance_withdraw: 0,
                		balance_withdraw_apply: 0
                	}
                },
                bankAccountInfo: {},
                withdrawMoney: '',
                isSub: false,
				loading: true,
				yes: true
            }
        },
		filters: {
			/**
			 * 金额格式化输出
			 * @param {Object} money
			 */
			moneyFormat(money) {
				return parseFloat(money).toFixed(2);
			}
		},
        created() {
            this.getWithdrawInfo();
			this.getBankAccountInfo();
        },
		mounted() {
			let self = this;
			setTimeout(function() {
				self.yes = false
			}, 300)
		},
        methods: {
			/**
			 * 获取提现信息
			 */
			getWithdrawInfo() {
				withdrawInfo().then(res => {
					if (res.code >= 0 && res.data) {
						this.withdrawInfo = res.data;
						if (this.withdrawInfo.config.is_use == 0) {
							this.$router.push('/member/index');
						}
					}
					this.loading = false
				}).catch(err => {
					this.loading = false
				})
			},
			/**
			 * 银行账号信息
			 */
			getBankAccountInfo() {
				accountInfo().then(res => {
					if (res.code >= 0 && res.data) {
						this.bankAccountInfo = res.data;
					}
				}).catch(err => {})
			},
			/**
			 * 全部提现
			 */
			allTx() {
				this.withdrawMoney = this.withdrawInfo.member_info.balance_money;
			},
			/**
			 * 账户列表
			 */
			goAccount() {
				let back = "/member/apply_withdrawal"
				this.$router.push({path: "/member/account_list", query: {back: back}})
			},
			withdraw() {
				if (!this.bankAccountInfo.withdraw_type) {
					this.$message({
						message: "请先添加提现方式",
						type: "warning"
					})
					return;
				}
				if (this.withdrawMoney == '' || this.withdrawMoney == 0 || isNaN(parseFloat(this.withdrawMoney))) {
					this.$message({
						message: '请输入提现金额',
						type: "warning"
					});
					return false;
				}
				if (parseFloat(this.withdrawMoney) > parseFloat(this.withdrawInfo.member_info.balance_money)) {
					this.$message({
						message: '提现金额超出可提现金额',
						type: "warning"
					});
					return false;
				}
				if (parseFloat(this.withdrawMoney) < parseFloat(this.withdrawInfo.config.min)) {
					this.$message({
						message: '提现金额小于最低提现金额',
						type: "warning"
					});
					return false;
				}
				
				if (this.isSub) return;
				this.isSub = true;
				
				withdraw({
					apply_money: this.withdrawMoney,
					transfer_type: this.bankAccountInfo.withdraw_type, //转账提现类型
					realname: this.bankAccountInfo.realname,
					mobile: this.bankAccountInfo.mobile,
					bank_name: this.bankAccountInfo.branch_bank_name,
					account_number: this.bankAccountInfo.bank_account
				}).then(res => {
					if (res.code >= 0) {
						this.$message({
							message: '提现申请成功！',
							type: 'success',
							duration: 2000,
							onClose: () => {
								this.$router.push('/member/withdrawal');
							}
						});
					} else {
						this.isSub = false;
						this.$message({
							message: res.message,
							type: "warning"
						});
					}
				}).catch(err => {
					this.isSub = false;
					this.$message({
						message: err.message,
						type: "warning"
					});
				})
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
	
	.apply-withdrawal {
		width: 100%;
		background-color: #FFFFFF;
		
		.apply-wrap {
			display: inline-block;
			width: 500px;
			box-sizing: border-box;
			
			.apply-account {
				display: flex;
				align-items: center;
				cursor: pointer;
				
				.apply-account-info {
					display: flex;
					align-items: center;
					
					span {
						margin-right: 5px;
					}
					
					.el-image {
						width: 20px;
						height: 20px;
						margin-right: 10px;
					}
				}
			}
			
			.apply-account-money {
				display: flex;
				align-items: center;
				margin-top: 30px;
				
				span {
					flex-shrink: 0;
				}
			}
			
			.apply-account-desc {
				margin-top: 30px;
				text-align: left;
				
				p:first-child {
					span.balance {
						margin-right: 10px;
					}
					span:nth-child(4) {
						color: $base-color;
						cursor: pointer;
					}
				}
				
				p:nth-child(2) {
					color: #999999;
				}
			}
			
			.apply-account-btn {
				margin-top: 30px;
				.el-button {
					margin-right: 20px;
				}
			}
		}
		
		.ns-width {
			display: inline-block;
			width: 115px;
			text-align: right;
		}
	}
</style>
