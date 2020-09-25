<template>
	<div class="box">
		<div class="null-page" v-show="yes"></div>
		
		<el-card class="box-card">
			<div slot="header" class="clearfix">
				<el-breadcrumb separator="/">
					<el-breadcrumb-item :to="{ path: '/member/withdrawal' }">提现记录</el-breadcrumb-item>
					<el-breadcrumb-item>提现详情</el-breadcrumb-item>
				</el-breadcrumb>
			</div>
		
			<div v-loading="loading">
				<div class="money-wrap">
					<span>-{{ detail.apply_money }}</span>
				</div>
				<div class="line-wrap">
					<span class="label">当前状态</span>
					<span class="value">{{ detail.status_name }}</span>
				</div>
				<div class="line-wrap">
					<span class="label">交易号</span>
					<span class="value">{{ detail.withdraw_no }}</span>
				</div>
				<div class="line-wrap">
					<span class="label">手续费</span>
					<span class="value">￥{{ detail.service_money }}</span>
				</div>
				<div class="line-wrap">
					<span class="label">申请时间</span>
					<span class="value">{{ $util.timeStampTurnTime(detail.apply_time) }}</span>
				</div>
				<div class="line-wrap" v-if="detail.status">
					<span class="label">审核时间</span>
					<span class="value">{{ $util.timeStampTurnTime(detail.audit_time) }}</span>
				</div>
				<div class="line-wrap" v-if="detail.bank_name">
					<span class="label">银行名称</span>
					<span class="value">{{ detail.bank_name }}</span>
				</div>
				<div class="line-wrap">
					<span class="label">收款账号</span>
					<span class="value">{{ detail.account_number }}</span>
				</div>
				<div class="line-wrap" v-if="detail.status == -1 && detail.refuse_reason">
					<span class="label">拒绝理由</span>
					<span class="value">{{ detail.refuse_reason }}</span>
				</div>
				<div class="line-wrap" v-if="detail.status == 2">
					<span class="label">转账方式名称</span>
					<span class="value">{{ detail.transfer_type_name }}</span>
				</div>
				<div class="line-wrap" v-if="detail.status == 2">
					<span class="label">转账时间</span>
					<span class="value">{{ $util.timeStampTurnTime(detail.payment_time) }}</span>
				</div>
			</div>
		</el-card>
	</div>
</template>

<script>
import { withdrawDetail } from "@/api/member/account"

export default {
	name: 'withdrawal_detail',
	components: {},
	data: () => {
		return {
			loading: true,
			id: '',
			detail: {},
			yes: true
		};
	},
	created() {
		this.getDetail();
	},
	mounted() {
		let self = this;
		setTimeout(function() {
			self.yes = false
		}, 300)
	},
	methods: {
		//获得提现详情
		getDetail() {
			this.id = this.$route.query.id
			
			withdrawDetail({
				id: this.id
			}).then(res => {
				if (res.data) {
					this.detail = res.data;
				}
				this.loading = false
			}).catch(err => {
				this.loading = false
			})
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
	
	.money-wrap {
		font-size: 20px;
		font-weight: 600;
	}
	
	.line-wrap {
		margin-top: 20px;
		
		.label {
			display: inline-block;
			width: 100px;
			color: #898989;
		}
	}
</style>
