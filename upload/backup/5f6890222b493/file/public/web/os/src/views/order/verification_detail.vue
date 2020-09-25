<template>
	<el-card class="box-card order-list">
		<div slot="header" class="clearfix">
			<el-breadcrumb separator="/">
				<el-breadcrumb-item :to="{ path: '/member/verification_list' }">核销记录</el-breadcrumb-item>
				<el-breadcrumb-item>核销验证</el-breadcrumb-item>
			</el-breadcrumb>
		</div>
		<div class="ns-verification" v-loading="loading">
			<div class="ns-verification-order">
				<p class="ns-site-name">{{ verifyInfo.site_name }}</p>
				<div class="ns-goods-list" v-for="(item, index) in verifyInfo.item_array" :key="index">
					<div class="ns-goods-img"><el-image fit="cover" :src="$img(item.img)" @error="imageError(index)"></el-image></div>
					<div class="ns-goods-info">
						<p>{{ item.name }}</p>
						<p class="ns-goods-price ns-text-color">￥{{ item.price }}</p>
						<p>数量：{{ item.num }}</p>
					</div>
				</div>

				<div class="ns-order-info">
					<p v-for="(item, index) in verifyInfo.remark_array" :key="index">{{ item.title }}：{{ item.value }}</p>
					<p>核销类型：{{ verifyInfo.verify_type_name }}</p>

					<template v-if="verifyInfo.is_verify">
						<p>核销状态：已核销</p>
						<p v-if="verifyInfo.verify_time">核销人员：{{ verifyInfo.verifier_name }}</p>
						<p v-if="verifyInfo.verify_time">核销时间：{{ $timeStampTurnTime(verifyInfo.verify_time) }}</p>
					</template>
				</div>

				<div class="ns-btn"><el-button @click="verify" v-if="verifyInfo.is_verify == 0">确认使用</el-button></div>
			</div>
		</div>
	</el-card>
</template>

<script>
import { checkisverifier, verifyInfo, verify } from '@/api/order/verification';
import { mapGetters } from 'vuex';

export default {
	name: 'verification_detail',
	components: {},
	data: () => {
		return {
			verify_code: '',
			verifyInfo: {},
			isSub: false,
			loading: true
		};
	},
	created() {
		this.verify_code = this.$route.query.code;
		this.getVerifyInfo();
	},
	computed: {
		...mapGetters(['defaultGoodsImage'])
	},
	methods: {
		getVerifyInfo() {
			verifyInfo({
				verify_code: this.verify_code
			})
				.then(res => {
					if (res.code >= 0) {
						this.verifyInfo = res.data;
					} else {
						this.$message({ message: res.message, type: 'warning' });
						this.$router.push('/member/index');
					}
					this.loading = false;
				})
				.catch(err => {
					this.$message.error(err.message);
					this.$router.push('/member/index');
					this.loading = false;
				});
		},
		verify() {
			if (this.isSub) return;
			this.isSub = true;
			verify({
				verify_code: this.verify_code
			})
				.then(res => {
					if (res.code >= 0) {
						this.$message({
							message: res.message,
							type: 'success',
							duration: 2000,
							onClose: () => {
								this.$router.push('/member/verification_list');
							}
						});
					} else {
						this.$message({ message: res.message, type: 'warning' });
						this.isSub = false;
					}
				})
				.catch(err => {
					this.$message.error(err.message);
					this.isSub = false;
				});
		},
		/**
		 * 图片加载失败
		 */
		imageError(index) {
			this.verifyInfo.item_array[index].img = this.defaultGoodsImage;
		}
	}
};
</script>
<style lang="scss" scoped>
.ns-verification {
	.ns-verification-order {
		.ns-goods-list {
			display: flex;
			margin: 10px 0;
			.el-image {
				width: 80px;
				height: 80px;
				line-height: 80px;
				text-align: center;
				margin-right: 10px;
				border-radius: 5px;
			}
			.ns-goods-price span:first-child {
				font-weight: 600;
				font-size: 16px;
			}
		}
		.ns-order-info {
			border-top: 1px solid #eeeeee;
			padding-top: 20px;
			color: $base-color-info;
			line-height: 30px;
		}
		.ns-btn {
			text-align: right;
			.el-button {
				background: $base-color;
				color: #ffffff;
			}
		}
	}
}
</style>
