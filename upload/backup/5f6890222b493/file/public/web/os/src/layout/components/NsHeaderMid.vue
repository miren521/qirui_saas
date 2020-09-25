<template>
	<div class="header-in">
		<el-row>
			<el-col :span="6">
				<router-link to="/" class="logo-wrap">
					<img v-if="siteInfo.logo" :src="$img(siteInfo.logo)" />
					<img v-else src="@/assets/images/logo.png" />
				</router-link>
			</el-col>
			<el-col :span="13">
				<div class="in-sousuo">
					<div class="sousuo-box">
						<el-dropdown @command="handleCommand" trigger="click">
							<span class="el-dropdown-link">
								{{ searchTypeText }}
								<i class="el-icon-arrow-down"></i>
							</span>
							<el-dropdown-menu slot="dropdown">
								<el-dropdown-item command="goods">商品</el-dropdown-item>
								<el-dropdown-item command="shop">店铺</el-dropdown-item>
							</el-dropdown-menu>
						</el-dropdown>
						<input type="text" :placeholder="defaultSearchWords" v-model="keyword" @keyup.enter="search" maxlength="50" />
						<el-button type="primary" size="small" @click="search">搜索</el-button>
					</div>
					<div class="hot-search-words" v-if="hotSearchWords.length">
						<span>热门搜索：</span>
						<ul>
							<li v-for="(item, index) in hotSearchWords" :key="index" @click="$router.push({ path: '/list', query: { keyword: item, search_type: 'goods' } })">
								{{ item }}
							</li>
						</ul>
					</div>
				</div>
			</el-col>
			<el-col :span="5">
				<div class="cart-wrap">
					<router-link class="cart" to="/cart">
						<span>我的购物车</span>
						<el-badge v-if="cartCount" :value="cartCount" type="primary"><i class="iconfont icongouwuche"></i></el-badge>
						<i v-else class="iconfont icongouwuche"></i>
					</router-link>

					<div class="list">
						<template v-if="cartList.length">
							<h4>最新加入的商品</h4>
							<div class="overflow-wrap">
								<ul :class="{ overflow: cartList.length > 5 }">
									<li class="item" v-for="(item, index) in cartList" :key="index">
										<div class="img-wrap"><img :src="$img(item.sku_image, { size: 'mid' })" @error="imageError(index)" :alt="item.sku_name" /></div>
										<div class="goods-name">{{ item.sku_name }}</div>
										<div class="operation">
											<p>￥{{ item.discount_price }}x{{ item.num }}</p>
											<span @click="deleteCart(index)">删除</span>
										</div>
									</li>
								</ul>
							</div>
							<div class="total">
								<span>
									共
									<strong>{{ cartList.length }}</strong>
									种商品，总金额
									<strong>{{ cartTotalPrice }}</strong>
									元
								</span>

								<el-button type="primary" size="mini" @click="$router.push('/cart')">去购物车</el-button>
							</div>
						</template>
						<div class="empty" v-else>
							<i class="iconfont icongouwuche"></i>
							<span>您的购物车是空的，赶快去逛逛，挑选商品吧！</span>
						</div>
					</div>
				</div>
			</el-col>
		</el-row>
	</div>
</template>

<script>
import { mapGetters } from 'vuex';
import { apiHotSearchWords, apiDefaultSearchWords } from '@/api/pc';
import { cartList as apiCartList } from '@/api/goods/cart';
export default {
	props: {},
	data() {
		return {
			searchType: 'goods',
			searchTypeText: '商品',
			keyword: '',
			hotSearchWords: [],
			defaultSearchWords: '',
			cartList: [],
			cartTotalPrice: 0
		};
	},
	components: {},
	computed: {
		...mapGetters(['cartCount', 'siteInfo', 'defaultGoodsImage', 'member'])
	},
	created() {
		this.keyword = this.$route.query.keyword || '';
		if (this.$route.name == 'street') this.searchType = 'shop';
		else this.searchType = 'goods';
		this.$store.dispatch('site/siteInfo');
		this.getHotSearchWords();
		this.getDefaultSearchWords();
		this.getCartList();
	},
	watch: {
		searchType() {
			this.searchTypeText = this.searchType == 'goods' ? '商品' : '店铺';
		},
		$route(curr) {
			if (this.keyword !== curr.query.keyword) {
				this.keyword = curr.query.keyword;
			}
			if (this.$route.name == 'street') this.searchType = 'shop';
			else this.searchType = 'goods';
		},
		cartCount() {
			if (this.member) this.getCartList();
		},
		member() {
			if (!this.member) {
				this.$store.commit('cart/SET_CART_COUNT', 0);
				this.cartList = [];
				this.cartTotalPrice = 0;
			}
		}
	},
	methods: {
		handleCommand(command) {
			this.searchType = command;
		},
		search() {
			if (this.searchType == 'goods') this.$router.push({ path: '/list', query: { keyword: this.keyword } });
			else this.$router.push({ path: '/street', query: { keyword: this.keyword } });
		},
		getHotSearchWords() {
			apiHotSearchWords({}).then(res => {
				if (res.code == 0 && res.data.words) {
					this.hotSearchWords = res.data.words.split('，');
				}
			});
		},
		getDefaultSearchWords() {
			apiDefaultSearchWords({}).then(res => {
				if (res.code == 0 && res.data.words) {
					this.defaultSearchWords = res.data.words;
				}
			});
		},
		// 获取购物车数据
		getCartList() {
			apiCartList({})
				.then(res => {
					if (res.code >= 0 && res.data.length) {
						this.cartList = res.data;

						this.cartList.forEach(item => {
							this.cartTotalPrice += item.discount_price * item.num;
						});
						this.cartTotalPrice = this.cartTotalPrice.toFixed(2);
					}
				})
				.catch(res => {});
		},
		imageError(index) {
			this.cartList[index].sku_image = this.defaultGoodsImage;
		},
		// 删除单个购物车商品
		deleteCart(index) {
			this.$store
				.dispatch('cart/delete_cart', {
					cart_id: this.cartList[index].cart_id.toString()
				})
				.then(res => {
					if (res.code >= 0) {
						this.cartList.splice(index, 1);
						this.$forceUpdate();
					}
				})
				.catch(err => {});
		}
	}
};
</script>

<style scoped lang="scss">
.header-in {
	width: $width;
	height: 89px;
	margin: 20px auto 0;
	.logo-wrap {
		width: 240px;
		height: 68px;
		line-height: 68px;
		display: block;
		img {
			max-width: 100%;
			max-height: 100%;
		}
	}
	.in-sousuo {
		width: 550px;
		margin-top: 10px;
		.sousuo-box {
			width: 100%;
			height: 36px;
			border: 2px solid $base-color;
			box-sizing: border-box;
			.el-dropdown {
				padding: 0 10px;
				cursor: pointer;
				&::after {
					content: '';
					border-left: 1px solid #cfcfcf;
					margin-left: 5px;
				}
			}
			input {
				width: 380px;
				height: 22px;
				background: none;
				outline: none;
				border: none;
				margin: 4px;
			}
			button {
				border-radius: 0;
				float: right;
			}
		}
		.hot-search-words {
			width: 100%;
			height: 20px;
			margin-top: 5px;
			font-size: 12px;
			span {
				float: left;
			}
			ul {
				overflow: hidden;
				margin: 0;
				height: 21px;
				padding: 0;
				color: $ns-text-color-black;
				li {
					cursor: pointer;
					list-style: none;
					float: left;
					margin-right: 10px;
					&:hover {
						color: $base-color;
					}
				}
			}
		}
	}
	.cart-wrap {
		position: relative;
		float: right;

		.cart {
			margin-top: 10px;
			width: 95px;
			height: 36px;
			padding: 0 28px 0 19px;
			border: 1px solid #dfdfdf;
			color: $base-color;
			font-size: $ns-font-size-sm;
			display: block;
			position: absolute;
			right: 0;
			z-index: 101;
			span {
				cursor: pointer;
				line-height: 38px;
				margin-right: 10px;
			}
		}
		&:hover {
			.cart {
				border-bottom: 1px solid #ffffff;
			}
			.list {
				display: block;
			}
		}
		.list {
			position: absolute;
			top: 47px;
			right: 0;
			width: 340px;
			background: #fff;
			border: 1px solid #dfdfdf;
			display: none;
			z-index: 100;
			h4 {
				height: 25px;
				padding: 6px 8px;
				line-height: 25px;
			}
			.overflow-wrap {
				width: 340px;
				overflow: hidden;
				ul {
					max-height: 335px;
					&.overflow {
						overflow: auto;
						width: 354px;
					}
					li {
						background-color: #fff;
						display: block;
						font-size: 12px;
						padding: 8px 10px;
						position: relative;
						border-bottom: 1px solid #dfdfdf;
						overflow: hidden;
						.img-wrap {
							width: 50px;
							height: 50px;
							margin-right: 5px;
							overflow: hidden;
							float: left;
							text-align: center;
							line-height: 50px;
						}
						.goods-name {
							float: left;
							width: 140px;
							display: -webkit-box;
							-webkit-box-orient: vertical;
							-webkit-line-clamp: 2;
							overflow: hidden;
							margin-right: 10px;
						}
						.operation {
							float: right;
							text-align: right;
							width: 90px;
							p {
								color: $base-color;
								overflow: hidden;
								text-overflow: ellipsis;
							}
							span {
								cursor: pointer;
								&:hover {
									color: $base-color;
								}
							}
						}
						&:last-child {
							border-bottom: 0;
						}
					}
				}
			}
			.total {
				background-color: #fff;
				display: block;
				font-size: 12px;
				padding: 8px 10px;
				position: relative;
				border-bottom: 1px solid #dfdfdf;
				overflow: hidden;
				background-color: #f0f0f0;
				border-bottom: 0;
				left: 0;
				span {
					margin-top: 5px;
					display: inline-block;
					width: 70%;
					overflow: hidden;
					text-overflow: ellipsis;
					white-space: nowrap;
					strong {
						margin: 0 2px;
						color: $base-color;
						font-size: $ns-font-size-base;
					}
				}
				button {
					float: right;
				}
			}

			.empty {
				width: auto;
				height: 70px;
				line-height: 70px;
				text-align: center;
				color: #999;
				i {
					font-size: 28px;
				}
				span {
					display: inline-block;
					font-size: 12px;
					padding-right: 20px;
					margin-left: 10px;
				}
			}
		}
	}
}
</style>
