<template>
	<el-card shadow="hover" :body-style="cardBody">
		<div class="cargo">
			<!-- 内容区 -->

			<!-- 商品图片区 -->
			<div class="img" @click="handleCargoContentClick"><el-image fit="scale-down" :src="$img(cargo.sku_image, { size: 'mid' })" lazy :alt="cargo.introduction" @error="cargo.sku_image=defaultGoodsImage"></el-image></div>

			<!-- 价格展示区 -->
			<div class="price" @click="handleCargoContentClick">
				<!-- 价格 -->
				<div>
					<span class="main_price">￥{{ cargo.price }}</span>
				</div>
				<!-- 活动 -->
				<div><!-- <el-tag type="danger" hit size="mini" effect="plain">活动名称</el-tag> --></div>
			</div>

			<!-- 商品名称 -->
			<div class="name" @click="handleCargoContentClick">
				<span>{{ cargo.sku_name || cargo.goods_name || '' }}</span>
				<span>
					<el-link type="warning">{{ cargo.introduction }}</el-link>
				</span>
			</div>

			<!-- 成交记录 -->
			<div class="saling">
				<div>
					<span class="num">{{ cargo.sale_num || 0 }}</span>
					人付款
				</div>
				<div>
					库存剩余
					<span class="num">{{ cargo.stock || 0 }}</span>
				</div>
			</div>

			<!-- 店铺名称 -->
			<div class="shop" v-if="cargo.is_own">
				<el-tag type="danger" size="mini" effect="dark">自营</el-tag>
				<div>{{ cargo.site_name || '' }}</div>
			</div>

			<!-- 特殊图标区 -->
			<div class="tags">
				<el-tag v-if="cargo.is_free_shipping" type="warning" size="mini" effect="dark">包邮</el-tag>
				<el-tag v-if="cargo.is_virtual" type="warning" size="mini" effect="dark">虚拟商品</el-tag>
			</div>
			<!-- 按钮区 -->
			<div>
				<el-button-group class="button-group">
					<!-- <el-button size="mini" icon="el-icon-edit">客服</el-button> -->
					<el-button plain size="mini" icon="el-icon-star-off" @click="addToCollection">收藏</el-button>
					<el-button plain size="mini" icon="el-icon-shopping-cart-full" class="item" @click="addToCart">加入购物车</el-button>
				</el-button-group>
			</div>
		</div>
	</el-card>
</template>

<script>
import { mapGetters } from 'vuex';
import { addCollect } from "@/api/goods/goods_collect"

export default {
	props: {
		cargo: {
			type: Object,
			required: true
		},
		path: {
			type: String,
			default: '/sku-'
		},
		newTab: {
			// 是否新窗口打开页面
			type: Boolean,
			default: true
		}
	},

	data() {
		return {
			cardBody: {
				width: '228px',
				height: '440px',
				padding: '5px'
			},

			isAdding: false // 添加操作是否进行中
		};
	},
	created() {},
	computed: {
		...mapGetters(['defaultGoodsImage'])
	},
	methods: {
		addToCart() {
			if (this.isAdding) return;
			this.isAdding = true;
			this.$store
				.dispatch('cart/add_to_cart', this.cargo)
				.then((this.isAdding = false))
				.catch(err => err);
		},

		addToCollection() {
			if (this.isAdding) return;
			this.isAdding = true;
			const { goods_id, sku_id, site_id, sku_name, sku_price, sku_image } = this.cargo;

			addCollect({ goods_id, sku_id, site_id, sku_name, sku_price, sku_image })
				.then(res => {
					this.$message({
						message: '收藏成功',
						type: 'success'
					});
				})
				.catch(err => err);
		},

		handleCargoContentClick() {
			if (this.newTab) this.$router.pushToTab(`${this.path}${this.cargo.sku_id}`);
			else this.$router.push(`${this.path}${this.cargo.sku_id}`);
		}
	},
};
</script>

<style lang="scss" scoped>
.el-card {
	display: inline-block;
}
.cargo {
	.el-button {
		padding: 7px 10px;
	}

	.img {
		width: 100%;
		height: 100%;

		.el-image {
			width: 100%;
			height: 220px;
			.el-image__error {
				width: 100%;
				height: 100%;
			}
		}
	}

	.price {
		display: flex;
		flex-direction: row;
		justify-content: space-between;
		align-items: center;

		.main_price {
			color: $base-color;
			font-size: 24px;
			font-weight: 500;
		}
	}

	.name {
		font-size: 14px;
		line-height: 1.4;
		height: 40px;
		margin-bottom: 8px;
		white-space: normal;
		overflow: hidden;
	}

	.tags {
		height: 25px;
		margin: 5px auto;
	}

	.saling {
		display: flex;
		flex-direction: row;

		.num {
			font-size: 14px;
			color: #646fb0;
			padding: auto 3px auto 0;
			font-weight: 600;
		}

		div {
			margin: 0 10px 5px 10px;
		}
	}

	.shop {
		display: flex;
		flex-direction: row;
		align-items: center;
		margin: auto;
		div {
			margin-left: 15px;
		}
	}

	.button-group {
		display: flex;
		flex-direction: row;

		.item {
			flex-grow: 1;
		}
	}
}
</style>
