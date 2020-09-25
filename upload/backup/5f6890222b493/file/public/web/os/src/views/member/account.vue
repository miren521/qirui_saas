<template>
	<div class="box">
		<div class="null-page" v-show="yes"></div>
		
		<div class="my-account">
			<div class="account-wrap">
				<div class="account-left">
					<div class="title">我的可用余额(元)</div>
					<div class="money">
						<div class="balance-money">
							<b>{{ integer }}</b>
							.
							<span>{{ decimal }}</span>
						</div>
						<div class="tx" @click="applyWithdrawal">提现</div>
						<div class="cz" @click="rechargeList">充值</div>
					</div>
				</div>
				<div class="account-right">
					<div class="item-wrap">
						<div class="item">
							<div class="iconfont iconziyuan"></div>
							<div class="title">可提现余额:</div>
							<b class="num">{{ balanceInfo.balance_money }}</b>
						</div>
						<div class="item">
							<div class="iconfont iconziyuan"></div>
							<div class="title">不可提现余额:</div>
							<b class="num">{{ balanceInfo.balance }}</b>
						</div>
					</div>
				</div>
			</div>
			<div class="detail" v-loading="loading">
				<el-table :data="accountList" border>
					<el-table-column prop="type_name" label="来源" width="150"></el-table-column>
					<el-table-column prop="account_data" label="金额" width="150"></el-table-column>
					<el-table-column prop="remark" class="detail-name" label="详细说明"></el-table-column>
					<el-table-column prop="time" label="时间" width="180"></el-table-column>
				</el-table>
			</div>
			<div class="pager">
				<el-pagination 
					background 
					:pager-count="5" 
					:total="total" 
					prev-text="上一页" 
					next-text="下一页" 
					:current-page.sync="account.page" 
					:page-size.sync="account.page_size" 
					@size-change="handlePageSizeChange" 
					@current-change="handleCurrentPageChange" 
					hide-on-single-page
				></el-pagination>
			</div>
		</div>
	</div>
</template>

<script>
    import { balance, withdrawConfig, balanceDetail } from "@/api/member/account"
    export default {
        name: "account",
        components: {},
        data: () => {
            return {
                account: {
                    page: 1,
                    page_size: 10
                },
                balanceInfo: {
                    balance: 0,
                    balance_money: 0
                },
                accountList: [],
                total: 0,
                integer: 0,
                decimal: 0,
                loading: true,
				yes: true
            }
        },
        created() {
            this.getAccount(), this.getAccountList()
        },
		mounted() {
			let self = this;
			setTimeout(function() {
				self.yes = false
			}, 300)
		},
        methods: {
            //获取余额信息
            getAccount() {
                balance({ account_type: "balance,balance_money" })
                    .then(res => {
                        if (res.code == 0 && res.data) {
                            this.balanceInfo = res.data
                            const price = (parseFloat(this.balanceInfo.balance) + parseFloat(this.balanceInfo.balance_money)).toFixed(2)
                            let priceSplit = price.split(".")
                            this.integer = priceSplit[0]
                            this.decimal = priceSplit[1]
                        }
                        this.loading = false
                    })
                    .catch(err => {
                        this.loading = false
                        this.$message.error(err.message)
                    })
            },
            //获取余额明细
            getAccountList() {
                balanceDetail({
                    page_size: this.account.page_size,
                    page: this.account.page,
                    account_type: "balance"
                })
                    .then(res => {
                        if (res.code == 0 && res.data) {
                            this.accountList = res.data.list
                            this.total = res.data.count
                            this.accountList.forEach(item => {
                                item.time = this.$util.timeStampTurnTime(item.create_time)
                            })
                        }
                    })
                    .catch(err => {
                        this.$message.error(err.message)
                    })
            },
            handlePageSizeChange(num) {
                this.account.page_size = num
                this.getAccountList()
            },
            handleCurrentPageChange(page) {
                this.account.page = page
                this.getAccountList()
            },
			applyWithdrawal() {
				this.$router.push("/member/apply_withdrawal")
			},
			rechargeList() {
				this.$router.push("/member/recharge_list")
			}
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
	
    .my-account {
        background: #ffffff;
        padding: 20px;
        .account-wrap {
            display: flex;
            margin-bottom: 10px;
            .account-left {
                flex: 1;
                .title {
                    font-size: $ns-font-size-base;
                    font-weight: 600;
                }
                .money {
                    display: flex;
                    .balance-money {
                        b {
                            font-size: 30px;
                        }
                        span {
                            font-weight: 600;
                        }
                    }
                    .tx {
                        color: $base-color;
                        margin-left: 5px;
                        margin-top: 20px;
						cursor: pointer;
                    }
                    .cz {
                        color: $base-color;
                        margin-left: 5px;
                        margin-top: 20px;
						cursor: pointer;
                    }
                }
            }
            .account-right {
                flex: 1;
                font-size: $ns-font-size-base;
                display: flex;
                align-items: center;
                .item {
                    display: flex;
                    align-items: center;
                    .title {
                        margin-left: 3px;
                    }
                    .num {
                        margin-left: 3px;
                    }
                }
            }
        }
        .page {
            display: flex;
            justify-content: center;
            align-content: center;
            padding-top: 20px;
        }
    }
</style>
