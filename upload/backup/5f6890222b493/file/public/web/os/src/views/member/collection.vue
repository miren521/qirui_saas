<template>
	<div class="box">
		<div class="null-page" v-show="yes"></div>
	
		<div class="collection" v-loading="loading">
			<el-tabs v-model="activeName" @tab-click="handleClick">
				<el-tab-pane label="宝贝" name="goods">
					<div v-if="goodsList.length">
						<div class="goods">
							<div class="goods-wrap" v-for="(item, index) in goodsList" :key="item.goods_id">
								<div class="goods-item">
									<div class="img" @click="$router.pushToTab({ path: '/sku-' + item.sku_id })">
										<img :src="$img(item.sku_image, { size: 'mid' })" @error="imageError(index)" />
										<i class="del el-icon-delete" @click.stop="deleteGoods(item.goods_id)"></i>
									</div>
									<div class="goods-name">{{ item.sku_name }}</div>
									<div class="price">￥{{ item.sku_price }}</div>
								</div>
							</div>
						</div>
						<div class="pager">
							<el-pagination 
								@size-change="handleSizeChange" 
								@current-change="handleCurrentChange" 
								:current-page="goodsInfo.page" 
								:page-size="goodsInfo.page_size" 
								background 
								:pager-count="5" 
								prev-text="上一页" 
								next-text="下一页" 
								hide-on-single-page 
								:total="goodsTotal"
							></el-pagination>
						</div>
					</div>
					<div v-else-if="!loading && !goodsList.length" class="empty">您还没有关注商品哦</div>
				</el-tab-pane>
				<el-tab-pane label="店铺" name="shop">
					<div v-if="shopList.length">
						<div class="shop">
							<div class="shop-wrap" v-for="(item, index) in shopList" :key="item.site_id">
								<div class="shop-item" @click="$router.pushToTab({ path: '/shop-' + item.site_id })">
									<div class="head-wrap">
										<i class="del el-icon-delete" @click.stop="deleteShop(item.site_id)"></i>
										<div class="img-wrap">
											<img class="img-responsive center-block" :src="$img(item.logo)" @error="imageErrorShop(index)" />
										</div>
										<h5>
											<span class="ns-text-color name">{{ item.site_name }}</span>
											<el-tag class="tag" size="small" v-if="item.is_own == 1">自营</el-tag>
										</h5>
									</div>
									<div class="info-wrap">
										<div class="info-item">商品描述：{{ item.shop_desccredit }}分</div>
										<div class="info-item">卖家服务：{{ item.shop_servicecredit }}分</div>
										<div class="info-item">发货速度：{{ item.shop_deliverycredit }}分</div>
										<div class="info-item" v-if="item.telephone">联系电话：{{ item.telephone }}</div>
									</div>
								</div>
							</div>
						</div>
						<div class="pager">
							<el-pagination 
								@size-change="sizeChange" 
								@current-change="currentChange" 
								:current-page="shopInfo.page" 
								:page-size="shopInfo.page_size"  
								:total="shopTotal" 
								background 
								:pager-count="5" 
								prev-text="上一页" 
								next-text="下一页" 
								hide-on-single-page
							></el-pagination>
						</div>
					</div>
					<div v-else-if="!loading && !shopList.length" class="empty">您还没有关注店铺哦！</div>
				</el-tab-pane>
			</el-tabs>
		</div>
	</div>
</template>

<script>
    import { shopCollect, goodsCollect, deleteGoods, deleteShop } from "@/api/member/collection"
    import { mapGetters } from "vuex"
    export default {
        name: "collection",
        components: {},
        data: () => {
            return {
                goodsInfo: {
                    page: 1,
                    page_size: 10
                },
                shopInfo: {
                    page: 1,
                    page_size: 10
                },
                activeName: "goods",
                goodsTotal: 0,
                shopTotal: 0,
                goodsList: [],
                shopList: [],
                loading: true,
				yes: true
            }
        },
        created() {
            this.getGoodsCollect()
        },
        computed: {
            ...mapGetters(["defaultGoodsImage", "defaultShopImage"])
        },
		mounted() {
			let self = this;
			setTimeout(function() {
				self.yes = false
			}, 300)
		},
        methods: {
            //获取关注商品
            getGoodsCollect() {
                goodsCollect(this.goodsInfo)
                    .then(res => {
                        this.goodsTotal = res.data.count
                        this.goodsList = res.data.list
                        this.loading = false
                    })
                    .catch(err => {
                        this.loading = false
                        this.$message.error(err.message)
                    })
            },
            //获取关注店铺
            getShopCollect() {
                shopCollect(this.shopInfo)
                    .then(res => {
                        this.shopTotal = res.data.count
                        this.shopList = res.data.list
                        this.loading = false
                    })
                    .catch(err => {
                        this.loading = false
                        this.$message.error(err.message)
                    })
            },
            //删除关注商品
            deleteGoods(id) {
                deleteGoods({ goods_id: id })
                    .then(res => {
                        if (res.code == 0) {
                            this.$message({ message: "取消关注成功", type: "success" })
                            this.getGoodsCollect()
                        }
                    })
                    .catch(err => {
                        this.$message.error(err.message)
                    })
            },
            //删除关注店铺
            deleteShop(id) {
                deleteShop({ site_id: id })
                    .then(res => {
                        if (res.code == 0) {
                            this.$message({ message: "取消关注成功", type: "success" })
                            this.getShopCollect()
                        }
                    })
                    .catch(err => {
                        this.$message.error(err.message)
                    })
            },
            handleClick(tab, event) {
                if (tab.index == "0") {
                    this.loading = true
                    this.getGoodsCollect()
                } else {
                    this.loading = true
                    this.getShopCollect()
                }
            },
            handleSizeChange(size) {
                this.goodsInfo.page_size = size
				this.loading = true
                this.getGoodsCollect()
            },
            handleCurrentChange(page) {
                this.goodsInfo.page = page
				this.loading = true
                this.getGoodsCollect()
            },
            sizeChange(size) {
                this.shopInfo.page_size = size
				this.loading = true
                this.getShopCollect()
            },
            currentChange(page) {
                this.shopInfo.page = page
				this.loading = true
                this.getShopCollect()
            },
            imageError(index) {
                this.goodsList[index].sku_image = this.defaultGoodsImage
            },
            imageErrorShop(index) {
                this.shopList[index].logo = this.defaultShopImage
            }
        }
    }
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
	
    .collection {
        background: #ffffff;
        padding: 10px 20px;
        .goods {
            display: flex;
            flex-wrap: wrap;
            .goods-wrap {
				width: 19%;
                margin-right: 1.25%;
				margin-bottom: 20px;
				
                &:nth-child(5n) {
                    margin-right: 0;
                }
                .goods-item {
					border: 1px solid #f1f1f1;
					box-sizing: border-box;
					padding: 10px;
                    .img {
                        width: 100%;
                        height: 160px;
                        cursor: pointer;
                        position: relative;
                        img {
                            width: 100%;
                            height: 100%;
                        }
                        .del {
                            font-size: 20px;
                            position: absolute;
                            top: 2px;
                            right: 2px;
                            padding: 3px;
                            background: rgba($color: #000000, $alpha: 0.3);
                            display: none;
                            color: #ffffff;
                        }
                        &:hover {
                            .del {
                                display: block;
                            }
                        }
                    }
                    .goods-name {
                        width: 100%;
						margin-top: 10px;
                        overflow: hidden;
                        display: -webkit-box;
                        -webkit-line-clamp: 2;
                        -webkit-box-orient: vertical;
                        height: 55px;
                    }
                    .price {
                        color: $base-color;
                    }
                }
            }
        }
        .shop {
            display: flex;
            flex-wrap: wrap;
            .shop-wrap {
                margin: 0 15px 20px 0;
                &:nth-child(5n) {
                    margin-right: 0;
                }
                .shop-item {
                    width: 156px;
                    height: 227px;
                    border: 1px solid #eeeeee;
                    padding: 0 10px;
                    cursor: pointer;
                    .head-wrap {
                        text-align: center;
                        padding: 10px 0;
                        border-bottom: 1px solid #eeeeee;
                        position: relative;
                        .del {
                            font-size: 20px;
                            position: absolute;
                            top: 0px;
                            right: 0px;
                            padding: 3px;
                            background: rgba($color: #000000, $alpha: 0.3);
                            display: none;
                            color: #ffffff;
                            cursor: pointer;
                        }
                        &:hover {
                            .del {
                                display: block;
                            }
                        }
                        .img-wrap {
                            width: 60px;
                            height: 60px;
                            line-height: 60px;
                            display: inline-block;
                        }
                        .name {
							display: block;
							width: 100%;
                            height: 24px;
                            line-height: 24px;
							overflow: hidden;
							text-overflow: ellipsis;
							white-space: nowrap;
                        }
                        .tag {
                            margin-left: 5px;
                        }
                    }
                    .info-wrap {
                        padding: 10px 0;
                    }
                }
            }
        }

        .empty {
            text-align: center;
        }
        .page {
            text-align: center;
        }
    }
</style>
