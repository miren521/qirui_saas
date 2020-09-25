<template>
	<div class="box">
		<div class="null-page" v-show="yes"></div>
		
		<el-card class="box-card order-list">
			<div slot="header" class="clearfix"><span>核销记录</span></div>

			<div v-loading="loading">
				<el-tabs v-model="orderType" @tab-click="handleClick">
					<el-tab-pane v-for="(item, index) in typeList" :key="index" :label="item.name" :name="item.type"></el-tab-pane>
				</el-tabs>

				<div>
					<nav>
						<li>商品信息</li>
						<li>单价</li>
						<li>数量</li>
					</nav>

					<div class="list" v-if="verifyList.length > 0">
						<div class="item" v-for="(item, index) in verifyList" :key="index">
							<div class="head">
								<span class="create-time">{{ $util.timeStampTurnTime(item.create_time) }}</span>
								<router-link :to="'/shop-' + item.site_id" target="_blank">{{ item.site_name }}</router-link>
								<span class="order-type">{{ item.order_type_name }}</span>
								<span class="order-type">核销员：{{ item.verifier_name }}</span>
							</div>
							<ul v-for="(goodsItem, goodsIndex) in item.item_array" :key="goodsIndex">
								<li>
									<div class="img-wrap" @click="toVerifyDetail(item.verify_code)"><img :src="$img(goodsItem.img)" @error="imageError(index, goodsIndex)" /></div>
									<div class="info-wrap">
										<h5 @click="toVerifyDetail(item.verify_code)">{{ goodsItem.name }}</h5>
									</div>
								</li>
								<li>
									<span>￥{{ goodsItem.price }}</span>
								</li>
								<li>
									<span>{{ goodsItem.num }}</span>
								</li>
							</ul>
						</div>
					</div>
					<div v-else-if="!loading && verifyList.length == 0" class="empty-wrap">暂无相关订单</div>
				</div>
			</div>

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
		</el-card>
	</div>
</template>

<script>
import { mapGetters } from 'vuex';
import { getVerifyType, verifyList } from '@/api/order/verification';

export default {
	name: 'verification_list',
	components: {},
	data: () => {
		return {
			orderType: '',
			loading: true,
			typeList: [],
			verifyList: [],
			currentPage: 1,
			pageSize: 10,
			total: 0,
			yes: true
		};
	},
	created() {
		this.getVerifyType();
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
			this.getVerifyType();
		},
		/**
		 * 订单类型(自提/虚拟)
		 */
		handleClick(tab, event) {
			this.refresh();
		},
		getVerifyType() {
			getVerifyType()
				.then(res => {
					if (res.code >= 0) {
						this.typeList = [];
						this.verifyList = [];

						Object.keys(res.data).forEach(key => {
							this.typeList.push({
								type: key,
								name: res.data[key].name
							});
						});

						if (this.orderType == 0) {
							for (let i = 0; i < this.typeList.length; i++) {
								if (i == 0) {
									this.orderType = this.typeList[i].type;
								}
							}
						}

						this.getVerifyList(this.orderType);
					}
				})
				.catch(err => {});
		},
		/**
		 * 获取核销记录
		 */
		getVerifyList(type) {
			verifyList({
				verify_type: type,
				page: this.currentPage,
				page_size: this.pageSize
			})
				.then(res => {
					this.verifyList = res.data.list;
					this.total = res.data.count;
					this.loading = false;
				})
				.catch(err => {
					this.$message.error(err.message);
					this.loading = false;
				});
		},
		imageError(orderIndex, goodsIndex) {
			this.verifyList[orderIndex].item_array[goodsIndex].img = this.defaultGoodsImage;
		},
		toVerifyDetail(code) {
			this.$router.push({ path: '/member/verification_detail', query: { code: code } });
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

.order-list {
	nav {
		overflow: hidden;
		padding: 10px 0;
		background: #fff;
		margin-bottom: 10px;
		border-bottom: 1px solid #eeeeee;
		li {
			float: left;
			&:nth-child(1) {
				width: 70%;
			}
			&:nth-child(2) {
				width: 20%;
			}
			&:nth-child(3) {
				width: 10%;
			}
		}
	}
	.list {
		.item {
			margin-bottom: 20px;
			border: 1px solid #eeeeee;
			border-top: 0;
			.head {
				padding: 8px 10px;
				background: #f7f7f7;
				font-size: 12px;

				.create-time {
					margin-right: 10px;
				}

				border-bottom: 1px solid #eeeeee;
				a {
					margin: 0 10px 0 20px;
				}

				.order-type {
					margin-left: 30px;
				}
			}
		}
		ul {
			background-color: #fff;
			padding: 10px;
			overflow: hidden;
			li {
				float: left;
				line-height: 60px;
				&:nth-child(1) {
					width: 70%;
					line-height: inherit;
					.img-wrap {
						width: 60px;
						height: 60px;
						float: left;
						margin-right: 10px;
						cursor: pointer;
					}
					.info-wrap {
						margin-left: 70px;
						h5 {
							cursor: pointer;
							font-weight: normal;
							font-size: $ns-font-size-base;
							display: -webkit-box;
							-webkit-box-orient: vertical;
							-webkit-line-clamp: 2;
							overflow: hidden;
							margin-right: 10px;
							display: inline-block;
							&:hover {
								color: $base-color;
							}
						}
						span {
							font-size: $ns-font-size-sm;
							color: #9a9a9a;
						}
					}
				}
				&:nth-child(2) {
					width: 20%;
				}
				&:nth-child(3) {
					width: 10%;
				}
			}
		}
	}
	.empty-wrap {
		text-align: center;
		padding: 10px;
	}
}
</style>
