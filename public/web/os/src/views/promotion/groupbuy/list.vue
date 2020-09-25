<template>
	<div class="ns-groupbuy">
		<el-carousel height="400px" v-loading="loadingAd">
			<el-carousel-item v-for="item in adList" :key="item.adv_id"><el-image :src="$img(item.adv_image)" fit="cover" @click="$router.pushToTab(item.adv_url.url)" /></el-carousel-item>
		</el-carousel>

		<!-- 商品列表 -->
		<div class="ns-groupbuy-box" v-loading="loading">
			<div class="ns-groupbuy-title" v-if="goodsList.length">
				<i class="iconfont icontuangou"></i>
				<span>团购进行中</span>
			</div>

			<div class="goods-list" v-if="goodsList.length">
				<div class="item" v-for="item in goodsList" :key="item.id">
					<div class="goods" @click="$router.pushToTab('/promotion/groupbuy-' + item.groupbuy_id)">
						<!-- 商品图片区 -->
						<div class="img"><el-image fit="scale-down" :src="$img(item.sku_image, { size: 'mid' })" lazy @error="imageError(index)"></el-image></div>

						<!-- 商品名称 -->
						<div class="name">
							<p :title="item.goods_name">{{ item.goods_name }}</p>
						</div>

						<!-- 价格展示区 -->
						<div class="price">
							<!-- 价格 -->
							<div>
								<p>
									<span>团购价</span>
									<span>￥</span>
									<span class="main_price">{{ item.groupbuy_price }}</span>
								</p>
								<span class="primary_price">￥{{ item.price }}</span>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div v-else><div class="ns-text-align">暂无正在进行团购的商品，去首页看看吧</div></div>

			<div class="pager">
				<el-pagination
					background
					:pager-count="5"
					:total="total"
					prev-text="上一页"
					next-text="下一页"
					:current-page.sync="currentPage"
					:page-size.sync="pageSize"
					@size-change="handlePageSizeChange"
					@current-change="handleCurrentPageChange"
					hide-on-single-page
				></el-pagination>
			</div>
		</div>
	</div>
</template>

<script>
import { goodsPage } from '@/api/groupbuy';
import { mapGetters } from 'vuex';
import { adList } from '@/api/website';

export default {
	name: 'groupbuy',
	components: {},
	data: () => {
		return {
			loading: true,
			goodsList: [],
			total: 0,
			currentPage: 1,
			pageSize: 10,
			loadingAd: true,
			adList: []
		};
	},
	created() {
		if (this.addonIsExit && this.addonIsExit.groupbuy != 1) {
			this.$message({
				message: '团购插件未安装',
				type: 'warning',
				duration: 2000,
				onClose: () => {
					this.$route.push('/');
				}
			});
		} else {
			this.getAdList();
			this.getGoodsList();
		}
	},
	computed: {
		...mapGetters(['defaultGoodsImage', 'addonIsExit'])
	},
	watch: {
		addonIsExit() {
			if (this.addonIsExit.groupbuy != 1) {
				this.$message({
					message: '团购插件未安装',
					type: 'warning',
					duration: 2000,
					onClose: () => {
						this.$route.push('/');
					}
				});
			}
		}
	},
	methods: {
		getAdList() {
			adList({ keyword: 'NS_PC_GROUPBUY' })
				.then(res => {
					this.adList = res.data.adv_list;
					for (let i = 0; i < this.adList.length; i++) {
						if (this.adList[i].adv_url) this.adList[i].adv_url = JSON.parse(this.adList[i].adv_url);
					}
					this.loadingAd = false;
				})
				.catch(err => {
					this.loadingAd = false;
				});
		},
		/**
		 * 团购商品
		 */
		getGoodsList() {
			goodsPage({
				page_size: this.pageSize,
				page: this.currentPage
			})
				.then(res => {
					this.goodsList = res.data.list;
					this.total = res.data.count;
					this.loading = false;
				})
				.catch(err => {
					this.loading = false;
					this.$message.error(err.message);
				});
		},
		handlePageSizeChange(size) {
			this.pageSize = size;
			this.refresh();
		},
		handleCurrentPageChange(page) {
			this.currentPage = page;
			this.refresh();
		},
		refresh() {
			this.loading = true;
			this.getGoodsList();
		},
		/**
		 * 图片加载失败
		 */
		imageError(index) {
			this.goodsList[index].sku_image = this.defaultGoodsImage;
		}
	}
};
</script>
<style lang="scss" scoped>
.ns-groupbuy {
	background: #ffffff;
	.ns-groupbuy-box {
		padding-top: 54px;
		width: $width;
		margin: 0 auto;

		.ns-groupbuy-title {
			width: 100%;
			border-bottom: 1px solid $base-color;
			padding-bottom: 10px;

			i {
				font-size: 32px;
				color: $base-color;
			}

			span {
				font-size: 30px;
				font-family: 'BDZongYi-A001';
				font-weight: 600;
				color: $base-color;
				margin-left: 15px;
			}
		}
	}
	.goods-list {
		display: flex;
		flex-direction: row;
		flex-wrap: wrap;
		justify-content: flex-start;
		margin-top: 45px;

		.item {
			width: 20%;
			padding: 0 6px;
			box-sizing: border-box;
			margin-bottom: 16px;

			.goods {
				width: 100%;
				border: 1px solid #e9e9e9;
				background-color: #ffffff;
				overflow: hidden;
				color: #303133;
				transition: 0.3s;
				padding: 10px;
				box-sizing: border-box;
				cursor: pointer;
			}
		}

		.img {
			width: 100%;
			height: 100%;

			.el-image {
				width: 100%;
				height: 208px;
				.el-image__error {
					width: 100%;
					height: 100%;
				}
			}
		}

		.price {
			p {
				display: flex;
				align-items: flex-end;
				height: 24px;
				color: $base-color;
				margin: 10px 0 5px;

				span:first-child {
					font-size: 20px;
					font-family: 'BDZongYi-A001';
					font-weight: 600;
					margin-right: 5px;
					line-height: 24px;
				}
				span:nth-child(2) {
					line-height: 14px;
				}
			}

			.main_price {
				color: $base-color;
				font-size: 25px;
				line-height: 24px;
				font-weight: 500;
			}

			.primary_price {
				text-decoration: line-through;
				color: $base-color-info;
				margin-left: 5px;
			}
		}

		.name {
			font-size: 14px;
			line-height: 1.4;
			margin-bottom: 5px;
			white-space: normal;
			overflow: hidden;
			
			p {
				line-height: 24px;
				display: -webkit-box;
				-webkit-box-orient: vertical;
				-webkit-line-clamp: 2;
				overflow: hidden;
				height: 50px;
			}
		}
	}
}
</style>
<style lang="scss">
.ns-groupbuy {
	.el-carousel {
		.el-image__inner {
			width: auto;
		}
	}
	.el-carousel__arrow--right{
		right: 60px;
	}
}
</style>
