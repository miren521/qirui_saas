<template>
	<div class="box">
		<div class="null-page" v-show="yes"></div>
		
		<el-card class="box-card">
			<div slot="header" class="clearfix">
				<el-breadcrumb separator="/">
					<el-breadcrumb-item :to="{ path: '/member/account' }">账户余额</el-breadcrumb-item>
					<el-breadcrumb-item>充值套餐列表</el-breadcrumb-item>
				</el-breadcrumb>
			</div>

			<div class="recharge-wrap" v-loading="loading">
				<div class="account-wrap">
					<div class="account-left">
						<div class="title">我的可用余额(元)</div>
						<div class="money">
							<div class="balance-money">
								<b>{{ integer }}</b>
								.
								<span>{{ decimal }}</span>
							</div>
							<div class="tx" @click="rechargeOrder">充值记录</div>
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

				<div class="recharge-table">
					<el-table :data="rechargeList" border>
						<el-table-column label="套餐名称">
							<template slot-scope="scope">
								<div class="recharge-info">
									<el-image :src="$img(scope.row.cover_img)" fit="contain" @error="imageError(scope.$index)"></el-image>
									<p :title="scope.row.recharge_name">{{ scope.row.recharge_name }}</p>
								</div>
							</template>
						</el-table-column>
						<el-table-column label="面值" width="150">
							<template slot-scope="scope">
								<span>￥{{ scope.row.face_value }}</span>
							</template>
						</el-table-column>
						<el-table-column prop="point" label="赠送积分" width="150"></el-table-column>
						<el-table-column prop="growth" label="赠送成长值" width="150"></el-table-column>
						<el-table-column label="操作" width="150">
							<template slot-scope="scope">
								<el-button size="mini" @click="handleDetail(scope.$index, scope.row)">充值</el-button>
							</template>
						</el-table-column>
					</el-table>

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
				</div>
			</div>
		</el-card>
	</div>
</template>

<script>
	import { balance, rechargeList } from "@/api/member/account"
	import { mapGetters } from 'vuex';

	export default {
		name: "recharge_list",
		components: {},
		data: () => {
			return {
				balanceInfo: {
					balance: 0,
					balance_money: 0
				},
				integer: 0,
				decimal: 0,
				rechargeList: [],
				total: 0,
				currentPage: 1,
				pageSize: 10,
				loading: true,
				yes: true
			}
		},
		created() {
			this.getUserInfo()
			this.getData()
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
			handlePageSizeChange(size) {
				this.pageSize = size
				this.refresh()
			},
			handleCurrentPageChange(page) {
				this.currentPage = page
				this.refresh()
			},
			refresh() {
				this.loading = true
				this.getData()
			},
			/**
			 * 获取余额信息
			 */
			getUserInfo() {
				balance({
					account_type: 'balance,balance_money'
				}).then(res => {
					if (res.code == 0 && res.data) {
						this.balanceInfo = res.data
						const price = (parseFloat(this.balanceInfo.balance) + parseFloat(this.balanceInfo.balance_money)).toFixed(2)
						let priceSplit = price.split(".")
						this.integer = priceSplit[0]
						this.decimal = priceSplit[1]
					} else {
						this.$message.warning(res.message)
					}
				}).catch(err => {
					this.$message.error(err.message)
				})
			},
			/**
			 * 获取充值套餐列表
			 */
			getData() {
				rechargeList({
					page: this.currentPage,
					page_size: this.pageSize
				}).then(res => {
					if (res.code == 0 && res.data.list) {
						this.rechargeList = res.data.list
						this.total = res.data.count
					} else {
						this.$message.warning(res.message)
					}
					this.loading = false
				}).catch(err => {
					this.loading = false
					this.$message.error(err.message)
				})
			},
			imageError(index) {
				this.rechargeList[index].cover_img = this.defaultGoodsImage;
			},
			handleDetail(index, row) {
				this.$router.push({path: '/member/recharge_detail', query: {id: row.recharge_id}})
			},
			rechargeOrder() {
				this.$router.push('/member/recharge_order')
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
	
	.el-card.is-always-shadow,
	.el-card.is-hover-shadow:focus,
	.el-card.is-hover-shadow:hover {
		box-shadow: unset;
	}

	.el-card {
		border: 0;
	}

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

	.recharge-table {
		.recharge-info {
			display: flex;
			align-items: center;

			.el-image {
				width: 60px;
				height: 60px;
				margin-right: 10px;
				flex-shrink: 0;
			}

			p {
				overflow: hidden;
				text-overflow: ellipsis;
				display: -webkit-box;
				-webkit-line-clamp: 2;
				-webkit-box-orient: vertical;
			}
		}
	}
</style>
