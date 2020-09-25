<template>
	<div class="goods-recommend" v-loading="loading">
		<h4>商品精选</h4>
		<ul v-if="list.length">
			<li v-for="(item, index) in list" :key="index"  @click="$router.pushToTab({ path: '/sku-' + item.sku_id })">
				<div class="img-wrap"><img :src="$img(item['sku_image'], { size: 'mid' })" @error="imageError(index)" /></div>
				<div class="price">￥{{ item.discount_price }}</div>
				<p class="sku-name">{{ item.goods_name }}</p>
				<div class="info-wrap"></div>
			</li>
		</ul>
	</div>
</template>

<script>
import { mapGetters } from 'vuex';
import { goodsRecommend } from '../api/goods/goods';
export default {
	name: 'goods_recommend',
	props: {
		page: {
			type: [Number, String],
			default: 1
		},
		pageSize: {
			type: [Number, String],
			default: 5
		}
	},
	data: () => {
		return {
			loading: true,
			list: []
		};
	},
	created() {
		this.getGoodsRecommend();
	},
	computed: {
		...mapGetters(['defaultGoodsImage'])
	},
	methods: {
		getGoodsRecommend() {
			goodsRecommend({
				page: this.page,
				page_size: this.pageSize
			})
				.then(res => {
					if (res.code == 0) this.list = res.data.list;
					this.loading = false;
				})
				.catch(res => {
					this.loading = false;
				});
		},
		imageError(index) {
			this.list[index].sku_image = this.defaultGoodsImage;
		}
	}
};
</script>

<style lang="scss" scoped>
.goods-recommend {
	border: 1px solid #eeeeee;
	h4 {
		margin: 10px;
	}
	ul {
		li {
			padding: 10px;
			cursor: pointer;
			.price {
				color: $base-color;
				font-size: 16px;
			}
			.sku-name {
				font-size: $ns-font-size-sm;
				overflow: hidden;
				text-overflow: ellipsis;
				display: -webkit-box;
				-webkit-line-clamp: 2;
				-webkit-box-orient: vertical;
			}
			&:hover {
				color: $base-color;
			}
		}
	}
}
</style>
