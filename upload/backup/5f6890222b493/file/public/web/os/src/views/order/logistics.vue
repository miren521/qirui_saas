<template>
	<div class="box">
		<div class="null-page" v-show="yes"></div>
		
		<el-card class="box-card logistics">
			<div slot="header" class="clearfix">
				<el-breadcrumb separator="/">
					<el-breadcrumb-item :to="{ path: '/member/order_list' }">订单列表</el-breadcrumb-item>
					<el-breadcrumb-item>物流详情</el-breadcrumb-item>
				</el-breadcrumb>
			</div>
			<div v-loading="loading">
				<el-tabs v-model="activeParcel">
					<el-tab-pane v-for="(packageItem, packageIndex) in packageList" :key="packageIndex" :label="packageItem.package_name" :name="'parcel_' + packageIndex">
						<div class="trace" v-if="packageItem.trace.success && packageItem.trace.list.length > 0">
							<el-timeline>
								<el-timeline-item
									v-for="(traceItem, traceIndex) in packageItem.trace.list"
									:timestamp="traceItem.datetime"
									placement="top"
									:type="traceIndex == 0 ? 'primary' : ''"
									:key="traceIndex"
								>
									<p>{{ traceItem.remark }}</p>
								</el-timeline-item>
							</el-timeline>
						</div>
						<div class="trace" v-else>
							<p class="empty-wrap">{{ packageItem.trace.reason }}</p>
						</div>

						<ul class="info-wrap">
							<li>
								<label>运单号码：</label>
								<span>{{ packageItem.delivery_no }}</span>
							</li>
							<li>
								<label>物流公司：</label>
								<span>{{ packageItem.express_company_name }}</span>
							</li>
						</ul>

						<ul class="goods-wrap">
							<li v-for="(goodsItem, goodsIndex) in packageItem.goods_list" :key="goodsIndex" @click="$router.pushToTab('/sku-' + goodsItem.sku_id)">
								<div class="img-wrap"><img :src="$img(goodsItem.sku_image, { size: 'mid' })" @error="imageError(packageIndex, goodsIndex)" /></div>
								<p class="sku-name">{{ goodsItem.sku_name }} x {{ goodsItem.num }}</p>
							</li>
						</ul>
					</el-tab-pane>
				</el-tabs>
			</div>
		</el-card>
	</div>
</template>

<script>
import { apiOrderPackageInfo } from '@/api/order/order';
import { mapGetters } from 'vuex';
export default {
	name: 'logistics',
	components: {},
	data: () => {
		return {
			orderId: 0,
			loading: true,
			activeParcel: 'parcel_0',
			packageList: [],
			yes: true
		};
	},
	created() {
		this.orderId = this.$route.query.order_id;
		if (!this.orderId) this.$router.push({ path: '/member/order_list' });
		this.getOrderPackageInfo();
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
		getOrderPackageInfo() {
			apiOrderPackageInfo({
				order_id: this.orderId
			})
				.then(res => {
					if (res.code >= 0) {
						this.packageList = res.data;
						this.packageList.forEach(item => {
							if (item.trace.list) {
								item.trace.list = item.trace.list.reverse();
							}
						});
						this.loading = false;
					} else {
						this.$message({
							message: '未获取到订单包裹信息！',
							type: 'warning',
							duration: 2000,
							onClose: () => {
								this.$router.push({ path: '/member/order_list' });
							}
						});
					}
				})
				.catch(res => {
					this.loading = false;
				});
		},
		imageError(packageIndex, goodsIndex) {
			this.packageList[packageIndex].goods_list[goodsIndex].sku_image = this.defaultGoodsImage;
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

.logistics {
	.trace {
		.empty-wrap {
			padding: 10px 0;
		}
	}
	.info-wrap {
		overflow: hidden;
		display: flex;
		flex-wrap: wrap;
		li {
			flex: 0 0 33.3333%;
			margin-bottom: 10px;
			span {
				font-weight: bold;
			}
		}
	}
	.goods-wrap {
		overflow: hidden;
		margin: 10px 0;
		li {
			float: left;
			width: 130px;
			margin-right: 7px;
			cursor: pointer;
			&:nth-child(n + 7) {
				margin-right: 0;
			}

			.img-wrap {
				width: 120px;
			}
			.sku-name {
				margin-top: 5px;
				overflow: hidden;
				text-overflow: ellipsis;
				display: -webkit-box;
				-webkit-line-clamp: 2;
				-webkit-box-orient: vertical;
				font-size: $ns-font-size-sm;
			}
		}
	}
}
</style>
