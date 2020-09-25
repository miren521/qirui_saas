<template>
	<div class="box">
		<div class="null-page" v-show="yes"></div>
		
		<el-card class="box-card recharge-detail-wrap">
			<div slot="header" class="clearfix">
				<el-breadcrumb separator="/">
					<el-breadcrumb-item :to="{ path: '/member/account' }">账户余额</el-breadcrumb-item>
					<el-breadcrumb-item :to="{ path: '/member/recharge_list' }">充值套餐列表</el-breadcrumb-item>
					<el-breadcrumb-item>充值套餐详情</el-breadcrumb-item>
				</el-breadcrumb>
			</div>
			
			<div class="recharge-detail" v-loading="loading">
				<el-image :src="$img(rechargeInfo.cover_img)" fit="contain" @error="imageError"></el-image>
				<p class="recharge-money"><span class="buy-price">￥{{ rechargeInfo.buy_price }}</span><span class="face-price">￥{{ rechargeInfo.face_value }}</span></p>
				<p class="recharge-name">{{ rechargeInfo.recharge_name }}</p>
				<p class="recharge-point">额外赠送：{{ rechargeInfo.point }}积分</p>
				<p class="recharge-growth">额外赠送：{{ rechargeInfo.growth }}成长值</p>
				<div class="recharge-btn">
					<el-button @click="recharge">立即充值</el-button>
				</div>
			</div>
		</el-card>
	</div>
</template>

<script>
	import { rechargeDetail, recharge } from "@/api/member/account"
	import { mapGetters } from 'vuex';
	
	export default {
		name: "recharge-detail",
		components: {},
		data: () => {
			return {
				id: '',
				rechargeInfo: {},
				loading: true,
				isSub: false,
				yes: true
			}
		},
		created() {
			this.id = this.$route.query.id;
			this.getRechargeInfo();
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
			//获取详情
			getRechargeInfo() {
				rechargeDetail({
					recharge_id: this.id
				}).then(res => {
					if (res.code == 0 && res.data) {
						this.rechargeInfo = res.data;
					} else {
						this.$message.warning(res.message)
					}
					this.loading = false
				}).catch(err => {
					this.loading = false
				})
			},
			imageError(index) {
				this.rechargeInfo.cover_img = this.defaultGoodsImage;
			},
			recharge() {
				if (this.isSub) return
				this.isSub = true
									
				recharge({
					recharge_id: this.id
				}).then(res => {
					if (res.data && res.code == 0) {
						this.$router.push({path: '/pay', query: {code: res.data}});
					} else {
						this.$message.warning(res.message)
					}
					this.isSub = false
				}).catch(err => {
					this.isSub = false
					this.$message.error(err.message)
				})
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
	
	.recharge-detail-wrap {
	}
	.recharge-detail {
		width: 300px;
		.recharge-money {
			.buy-price {
				font-size: 25px;
				color: $base-color;
				font-weight: 600;
				margin-right: 10px;
			}
			
			.face-price {
				text-decoration: line-through;
				color: #898989;
			}
		}
		
		.recharge-name {
			margin: 10px 0;
			font-size: 16px;
		}
		
		.recharge-btn {
			margin-top: 30px;
			text-align: center;
			
			.el-button {
				width: 300px;
				background-color: $base-color;
				color: #FFFFFF;
			}
		}
	}
</style>
