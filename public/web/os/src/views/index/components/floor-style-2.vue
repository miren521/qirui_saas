<template>
	<div class="floor-style-2">
		<div class="head-wrap" v-if="data.value.title.value.text">
			<h2 @click="$router.pushToTab(data.value.title.value.link.url)" :style="{ color: data.value.title.value.color }">{{ data.value.title.value.text }}</h2>
			<p @click="$router.pushToTab(data.value.subTitle.value.link.url)" :style="{ color: data.value.subTitle.value.color }">{{ data.value.subTitle.value.text }}</p>
		</div>
		<div class="body-wrap">
			<ul class="goods-list">
				<li v-for="(item, index) in data.value.goodsList.value.list" :key="index" :title="item.goods_name" @click="goSku(item.sku_id)">
					<div class="img-wrap"><img alt="商品图片" :src="$img(item.sku_image)" @error="imageError(index)" /></div>
					<h3>{{ item.goods_name }}</h3>
					<p class="desc">{{ item.introduction }}</p>
					<p class="price">
						<span class="num">{{ item.discount_price }}元</span>
						<del>{{ item.market_price }}元</del>
					</p>
				</li>
			</ul>
		</div>
		<div class="bottom-wrap" v-if="data.value.bottomImg.value.url">
			<img :src="$img(data.value.bottomImg.value.url)" @click="$router.pushToTab(data.value.bottomImg.value.link.url)" />
		</div>
	</div>
</template>

<script>
import { mapGetters } from 'vuex';
export default {
	name: 'floor-style-2',
	props: {
		data: {
			type: Object
		}
	},
	data() {
		return {};
	},
	created() {},
	computed: {
		...mapGetters(['defaultGoodsImage'])
	},
	methods: {
		goSku(skuId) {
			this.$router.pushToTab('/sku-' + skuId);
		},
		imageError(index) {
			this.data.value.goodsList.value.list[index].sku_image = this.defaultGoodsImage;
		}
	}
};
</script>

<style lang="scss">
.floor-style-2 {
	.head-wrap {
		text-align: center;
		h2 {
			line-height: 30px;
			color: #333;
			padding: 10px;
			font-size: 22px;
			cursor: pointer;
			overflow: hidden;
			text-overflow: ellipsis;
			white-space: nowrap;
		}
		p {
			color: #b0b0b0;
			padding: 0 10px;
			font-size: 14px;
			cursor: pointer;
			overflow: hidden;
			text-overflow: ellipsis;
			white-space: nowrap;
			margin-bottom: 20px;
		}
	}
	.body-wrap {
		.goods-list {
			display: flex;
			flex-wrap: wrap;
			li {
				width: 19%;
				margin-left: 15px;
				margin-bottom: 15px;
				background: #fff;
				cursor: pointer;
				padding: 10px 0;
				transition: all 0.2s linear;
				&:nth-child(5n + 1) {
					margin-left: 0;
				}
				&:hover {
					z-index: 2;
					-webkit-box-shadow: 0 15px 30px rgba(0, 0, 0, 0.1);
					box-shadow: 0 15px 30px rgba(0, 0, 0, 0.1);
					-webkit-transform: translate3d(0, -2px, 0);
					transform: translate3d(0, -2px, 0);
				}
				.img-wrap {
					width: 160px;
					height: 160px;
					margin: 0 auto 18px;
					text-align: center;
					line-height: 160px;
					img {
						max-width: 100%;
						max-height: 100%;
					}
				}
				h3 {
					font-size: 14px;
					text-align: center;
					text-overflow: ellipsis;
					white-space: nowrap;
					overflow: hidden;
					margin: 5px 15px;
				}
				.desc {
					margin: 0 30px 10px;
					height: 20px;
					font-size: 12px;
					color: #b0b0b0;
					text-align: center;
					text-overflow: ellipsis;
					white-space: nowrap;
					overflow: hidden;
				}
				.price {
					margin: 0 10px 14px;
					text-align: center;
					color: $base-color;
					del {
						margin-left: 0.5em;
						color: #b0b0b0;
					}
				}
			}
		}
	}
	.bottom-wrap {
		margin-top: 10px;
		width: $width;
		height: 118px;
		cursor: pointer;
		img {
			max-width: 100%;
		}
	}
}
</style>
