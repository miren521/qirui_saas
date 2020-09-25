<template>
	<div class="floor-style-3">
		<div class="item-wrap">
			<div class="head-wrap">
				<div class="title-name" v-if="data.value.title.value.text">
					<span :style="{ backgroundColor: data.value.title.value.color }"></span>
					<h2 @click="$router.pushToTab(data.value.title.value.link.url)" :style="{ color: data.value.title.value.color }">{{ data.value.title.value.text }}</h2>
				</div>
				<div class="category-wrap">
					<li v-for="(item, index) in data.value.categoryList.value.list" :key="index">
						<router-link target="_blank" :to="{ path: '/list', query: { category_id: item.category_id, level: item.level } }">{{ item.category_name }}</router-link>
					</li>
				</div>
			</div>
			<div class="body-wrap">
				<div class="left-img-wrap">
					<img v-if="data.value.leftImg.value.url" :src="$img(data.value.leftImg.value.url)" @click="$router.pushToTab(data.value.leftImg.value.link.url)" />
				</div>
				<ul class="right-goods-wrap">
					<li v-for="(item, index) in data.value.rightGoodsList.value.list" :key="index" @click="goSku(item.sku_id)">
						<h4>{{ item.goods_name }}</h4>
						<p class="ns-text-color">{{ item.introduction }}</p>
						<div class="img-wrap"><img alt="商品图片" :src="$img(item.sku_image)" @error="imageErrorRight(index)" /></div>
					</li>
				</ul>
				<ul class="bottom-goods-wrap">
					<li v-for="(item, index) in data.value.bottomGoodsList.value.list" :key="index" @click="goSku(item.sku_id)">
						<div class="info-wrap">
							<h4>{{ item.goods_name }}</h4>
							<p class="ns-text-color">{{ item.introduction }}</p>
						</div>
						<div class="img-wrap"><img alt="商品图片" :src="$img(item.sku_image)" @error="imageErrorBottom(index)" /></div>
					</li>
				</ul>

				<ul class="brand-wrap">
					<li v-for="(item, index) in data.value.brandList.value.list" :key="index" @click="$router.pushToTab({ path: '/list', query: { brand_id: item.brand_id } })">
						<img alt="品牌图片" :src="$img(item.image_url)" />
					</li>
				</ul>
			</div>
		</div>
	</div>
</template>

<script>
import { mapGetters } from 'vuex';
export default {
	name: 'floor-style-3',
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
		imageErrorRight(index) {
			this.data.value.rightGoodsList.value.list[index].sku_image = this.defaultGoodsImage;
		},
		imageErrorBottom(index) {
			this.data.value.bottomGoodsList.value.list[index].sku_image = this.defaultGoodsImage;
		}
	}
};
</script>

<style lang="scss">
.floor-style-3 {
	overflow: hidden;

	.item-wrap .head-wrap {
		height: 50px;
		line-height: 50px;
		.title-name {
			display: inline-block;
			span {
				float: left;
				width: 5px;
				height: 21px;
				margin-top: 15px;
			}
			h2 {
				float: left;
				margin-left: 10px;
				font-weight: bold;
				font-size: 20px;
			}
		}
		.category-wrap {
			float: right;
			display: flex;
			li {
				margin-right: 10px;
			}
		}
	}
	.body-wrap {
		.left-img-wrap {
			width: 190px;
			height: 360px;
			float: left;
			cursor: pointer;
			img {
				max-width: 100%;
				max-height: 100%;
			}
		}
		.right-goods-wrap {
			margin-left: 190px;
			text-align: center;
			overflow: hidden;
			li {
				float: left;
				width: 19.9%;
				background: #ffff;
				border-width: 0 0 1px 1px;
				border-color: #f9f9f9;
				border-style: solid;
				cursor: pointer;
				h4 {
					font-size: 14px;
					margin: 10px 20px 5px;
					overflow: hidden;
					white-space: nowrap;
					text-overflow: ellipsis;
					font-weight: normal;
				}
				p {
					font-size: 12px;
					overflow: hidden;
					white-space: nowrap;
					text-overflow: ellipsis;
					margin: 4px 30px;
					height: 20px;
				}
			}

			.img-wrap {
				width: 105px;
				height: 105px;
				line-height: 105px;
				display: inline-block;
				margin-bottom: 10px;
				img {
					max-width: 100%;
					max-height: 100%;
				}
			}
		}
	}
	.bottom-goods-wrap {
		overflow: hidden;
		display: flex;
		li {
			flex: 1;
			background: #fff;
			border-width: 0 0 1px 1px;
			border-color: #f9f9f9;
			border-style: solid;
			cursor: pointer;
			&:first-child {
				border-left: 0;
			}

			.info-wrap {
				display: inline-block;
				vertical-align: middle;
				text-align: center;
				h4 {
					font-size: 14px;
					margin: 0 10px 5px;
					overflow: hidden;
					white-space: nowrap;
					text-overflow: ellipsis;
					width: 90px;
					font-weight: normal;
				}
				p {
					font-size: 12px;
					overflow: hidden;
					white-space: nowrap;
					text-overflow: ellipsis;
					margin: 0 20px;
					width: 70px;
				}
			}
			.img-wrap {
				width: 70px;
				height: 70px;
				line-height: 70px;
				display: inline-block;
				vertical-align: middle;
				text-align: center;
				padding: 10px;
				img {
					max-width: 100%;
					max-height: 100%;
				}
			}
		}
	}

	.brand-wrap {
		display: flex;
		background: #fff;
		li {
			flex: 1;
			height: 50px;
			cursor: pointer;
			line-height: 50px;
			text-align: center;
			background: #fff;
			img {
				max-width: 100%;
				max-height: 100%;
			}
		}
	}
}
</style>
