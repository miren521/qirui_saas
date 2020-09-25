<template>
	<div class="combo-detail" v-loading="loading">
		<div class="combo-title">
			<div class="title-goods">商品信息</div>
			<div class="title-orther">
				<div class="title-price">价格</div>
				<div class="title-num">数量</div>
			</div>
		</div>
		<div class="item-wrap" v-for="(item, index) in goodsList.bundling_goods" :key="item.sku_item">
			<div class="item">
				<div class="info">
					<div class="img-wrap" @click="$router.pushToTab({ path: '/sku-' + item.sku_id })">
						<img :src="$img(item.sku_image, { size: 'mid' })" @error="imageError(index)" />
					</div>
					<div class="name">
						{{ item.sku_name }}
						<p v-if="num > item.stock">库存不足，剩余：{{ item.stock }}件</p>
					</div>
				</div>
				<div class="price-wrap">
					<div class="price">{{ item.price }}</div>
					<div class="num">x1</div>
				</div>
			</div>
		</div>
		<div class="combo-bottom">
			<div class="num">
				<p>购买数量:</p>
				<el-input v-model="num" type="number" @change="changeNum(false)"></el-input>
			</div>
			<div class="bottom-right">
				<div class="price">
					<div class="save-price">为您节省:￥{{ saveThePrice }}</div>
					<div class="old-price">
						套餐价:
						<p>￥{{ packagePrice }}</p>
					</div>
				</div>
				<el-button type="primary" :disabled="isDisabled ? true : false" @click="comboBuy">立即购买</el-button>
			</div>
		</div>
	</div>
</template>

<script>
import { detail } from '@/api/combo';
import { mapGetters } from 'vuex';
export default {
	name: 'combo',
	components: {},
	data: () => {
		return {
			id: 0,
			goodsList: [],
			num: 1,
			packagePrice: 0, //套餐价
			saveThePrice: 0, //节省价格
			isDisabled: false, //按钮失效
			loading: true
		};
	},
	created() {
		this.id = this.$route.path.replace('/promotion/combo-', '');
		this.getDetail();
	},
	computed: {
		...mapGetters(['defaultGoodsImage'])
	},
	watch: {
		$route(curr) {
			this.id = curr.params.pathMatch;
			this.getDetail();
		}
	},
	methods: {
		getDetail() {
			detail({
				bl_id: this.id
			})
				.then(res => {
					if (res.data) {
						this.goodsList = res.data;
						this.changeNum();
					}
					this.loading = false;
				})
				.catch(err => {
					this.loading = false;
					this.$message.error(err.message);
				});
		},
		changeNum(flag, callback) {
			setTimeout(() => {
				var disabledCount = 0;

				// 防止空
				if (this.num.length == 0) {
					this.num = 1;
					disabledCount++;
				}

				// 防止输入0和负数、非法输入
				if (this.num <= 0 || isNaN(this.num)) {
					this.num = 1;
				}

				this.num = parseInt(this.num);

				var price = 0;
				for (var i = 0; i < this.goodsList.bundling_goods.length; i++) {
					price += parseFloat(this.goodsList.bundling_goods[i].price);
					//检测库存
					if (this.goodsList.bundling_goods[i].stock < this.num) disabledCount++;
				}
				this.isDisabled = disabledCount > 0;
				this.saveThePrice = ((price - this.goodsList.bl_price) * this.num).toFixed(2);
				this.packagePrice = (this.goodsList.bl_price * this.num).toFixed(2);
				if (callback) callback();
			}, 0);
		},
		comboBuy() {
			if (this.isDisabled) return;
			var data = {
				bl_id: this.id,
				num: this.num
			};

			this.$store.dispatch('order/setComboOrderCreateData', data);

			this.$router.push({
				path: '/promotion/combo_payment'
			});
		},
		imageError(index) {
			this.goodsList.bundling_goods[index].sku_image = this.defaultGoodsImage;
		}
	}
};
</script>
<style lang="scss" scoped>
.combo-detail {
	margin-top: 20px;
	.combo-title {
		display: flex;
		background: #ffffff;
		padding: 10px;
		margin-bottom: 20px;
		justify-content: space-between;
		.title-orther {
			display: flex;
			justify-content: space-between;
			align-items: center;
			width: 150px;
			.title-price {
				margin-right: 10px;
			}
			.title-num {
				margin-right: 10px;
			}
		}
	}
	.item-wrap {
		padding: 0 10px 10px 10px;
		background: #ffffff;
		.item {
			display: flex;
			justify-content: space-between;
			padding-top: 10px;
			.info {
				display: flex;
				.img-wrap {
					width: 80px;
					height: 80px;
					img {
						width: 100%;
						height: 100%;
					}
				}
				.name {
					margin-left: 5px;
					font-size: $ns-font-size-base;
					width: 500px;
					overflow: hidden;
					text-overflow: ellipsis;
					display: -webkit-box;
					-webkit-box-orient: vertical;
					-webkit-line-clamp: 2;
					position: relative;
					
					p {
						position: absolute;
						bottom: 0;
						color: $base-color;
					}
				}
			}
			.price-wrap {
				display: flex;
				justify-content: space-between;
				align-items: center;
				width: 150px;
				.price {
					margin-right: 10px;
					display: flex;
					justify-content: center;
					align-items: center;
				}
				.num {
					padding-right: 10px;
					display: flex;
					justify-content: center;
					align-items: center;
				}
			}
		}
	}
	.combo-bottom {
		background: #ffffff;
		padding: 10px;
		margin: 20px 0;
		display: flex;
		justify-content: space-between;
		.num {
			display: flex;
			align-items: center;
			input {
				border: none;
			}
			p {
				width: 80px;
			}
		}
		.bottom-right {
			display: flex;
			.price {
				display: flex;
				align-items: center;
				margin-right: 50px;
				.save-price {
					margin-right: 10px;
				}
				.old-price {
					margin-right: 10px;
					font-weight: 600;
					display: flex;
					p {
						color: $base-color;
						font-size: $ns-font-size-lg;
					}
				}
			}
		}
	}
}
</style>
