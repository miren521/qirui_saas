<template>
	<div class="ns-brand">
		<el-carousel height="400px" v-loading="loadingAd">
			<el-carousel-item v-for="item in adList" :key="item.adv_id"><el-image :src="$img(item.adv_image)" fit="cover" @click="$router.pushToTab(item.adv_url.url)" /></el-carousel-item>
		</el-carousel>

		<!-- 品牌列表 -->
		<div class="ns-brand-box" v-loading="loading">
			<div>
				<div class="ns-brand-title-wrap ns-text-align">
					<p class="ns-brand-title">品牌专区</p>
					<img src="@/assets/images/goods/split.png" alt="" />
					<p class="ns-brand-en">Brand zone</p>
				</div>

				<div class="ns-brand-list" v-if="brandList.length > 0">
					<div class="ns-brand-li" v-for="(item, index) in brandList" :key="index" @click="$router.pushToTab({ path: '/list', query: { brand_id: item.brand_id } })">
						<div class="ns-brand-wrap">
							<el-image fit="scale-down" :src="$img(item.image_url)" lazy @error="imageError(index)"></el-image>
							<p :title="item.brand_name">{{ item.brand_name }}</p>
						</div>
					</div>
				</div>

				<div class="empty-wrap" v-if="brandList.length <= 0"><div class="ns-text-align">暂无更多品牌,去首页看看吧</div></div>

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
	</div>
</template>

<script>
import { brandList } from '@/api/goods/goods';
import { mapGetters } from 'vuex';
import { adList } from '@/api/website';

export default {
	name: 'brand',
	components: {},
	data: () => {
		return {
			total: 0,
			currentPage: 1,
			pageSize: 20,
			brandList: [],
			siteId: 0,
			loading: true,
			loadingAd: true,
			adList: []
		};
	},
	created() {
		this.getAdList();
		this.getBrandList();
	},
	computed: {
		...mapGetters(['defaultGoodsImage'])
	},
	methods: {
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
			this.getBrandList();
		},
		getBrandList() {
			brandList({
				page_size: this.pageSize,
				page: this.currentPage,
				site_id: this.siteId
			})
				.then(res => {
					this.brandList = res.data.list;
					this.total = res.data.count;
					this.loading = false;
				})
				.catch(err => {
					this.loading = false;
					this.$message.error(err.message);
				});
		},
		/**
		 * 图片加载失败
		 */
		imageError(index) {
			this.brandList[index].image_url = this.defaultGoodsImage;
		},
		getAdList() {
			adList({ keyword: 'NS_PC_BRAND' })
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
		}
	}
};
</script>
<style lang="scss" scoped>
.ns-text-align {
	text-align: center;
}

.ns-brand-box {
	width: 100%;
	background: #ffffff;
	> div {
		width: $width;
		margin: 0 auto;
	}
}
.ns-brand-title-wrap {
	padding-top: 54px;
	.ns-brand-title {
		font-size: 26px;
		font-weight: 600;
		line-height: 30px;
	}
	.ns-brand-en {
		font-size: 24px;
		font-weight: 600;
		color: #383838;
		opacity: 0.2;
		text-transform: uppercase;
		letter-spacing: 5px;
		line-height: 30px;
	}
}
.ns-brand-list {
	display: flex;
	flex-wrap: wrap;
	padding-top: 30px;

	.ns-brand-li {
		width: 20%;
		padding: 8px 6px;
		box-sizing: border-box;

		.ns-brand-wrap {
			width: 100%;
			border: 1px solid #f1f1f1;
			overflow: hidden;
			color: #303133;
			padding: 15px;
			box-sizing: border-box;
			cursor: pointer;
			text-align: center;

			.el-image {
				width: 100%;
				height: 120px;
				line-height: 120px;
			}

			p {
				font-size: 22px;
				color: #383838;
				overflow: hidden;
				text-overflow: ellipsis;
				white-space: nowrap;
			}
		}
	}
}

.empty-wrap {
	margin-top: 30px;
}
</style>
<style lang="scss">
.ns-brand {
	.el-carousel {
		.el-image__inner {
			width: auto;
		}
	}
	.el-carousel__arrow--right {
		right: 60px;
	}
}
</style>
