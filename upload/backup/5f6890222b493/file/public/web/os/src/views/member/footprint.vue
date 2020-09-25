<template>
	<div class="box">
		<div class="null-page" v-show="yes"></div>
		
		<el-card class="box-card foot-print">
			<div slot="header" class="clearfix">
				<span>我的足迹</span>
			</div>

			<div v-loading="loading">
				<el-timeline>
					<el-timeline-item class="ns-time-line" v-for="(item, index) in timesArr" :timestamp="item" placement="top" :key="index">
						<el-card>
							<div class="ns-goods-list">
								<div class="ns-goods-li" v-for="(value, name) in footPrintData" :key="name" v-if="item == value.browse_time" @click="$router.pushToTab('/sku-' + value.sku_id)">
									<span class="ns-btn-del" @click.stop="deleteFootprint(value.id)"><i class="iconfont iconshanchu"></i></span>
									<el-image :src="$img(value.goods_image)" fit="contain" @error="imageError(name)"></el-image>
									<p class="goods-name" :title="value.goods_name">{{ value.goods_name }}</p>
									<span class="goods-price">￥{{ value.goods_price }}</span>
								</div>
							</div>
						</el-card>
					</el-timeline-item>
				</el-timeline>
			</div>
		</el-card>
	</div>
</template>

<script>
    import { footPrint, delFootprint } from "@/api/member/member"
    import { mapGetters } from "vuex"

    export default {
        name: "footprint",
        components: {},
        data: () => {
            return {
                timesArr: [],
                footPrintData: [],
                loading: true,
				yes: true
            }
        },
        created() {
            this.getFootPrintData()
        },
        computed: {
            ...mapGetters(["defaultGoodsImage"])
        },
		mounted() {
			let self = this;
			setTimeout(function() {
				self.yes = false
			}, 300)
		},
        methods: {
            /**
             * 商品列表
             */
            getFootPrintData() {
                footPrint({
                    page: 1,
                    page_size: 0
                })
                    .then(res => {
                        var data = res.data.list
                        this.footPrintData = []
                        this.timesArr = []

                        for (let i = 0; i < data.length; i++) {
                            var date = timeStampTurnTime(data[i].browse_time).split(" ")[0]
                            var flag = util.inArray(date, this.timesArr)

                            if (flag == -1) {
                                this.timesArr.push(date)
                            }

                            var goods = {}
                            goods.id = data[i].id
                            goods.sku_id = data[i].sku_id
                            goods.browse_time = date
                            goods.goods_image = data[i].sku_image.split(",")[0]
                            goods.goods_name = data[i].sku_name
                            goods.goods_price = data[i].discount_price

                            this.footPrintData.push(goods)
                        }
                        this.loading = false
                    })
                    .catch(err => {
                        this.loading = false
                    })
            },
            /**
             * 图片加载失败
             */
            imageError(index) {
                this.footPrintData[index].goods_image = this.defaultGoodsImage
            },
            /**
             * 删除某个足迹
             */
            deleteFootprint(id) {
                delFootprint({
                    id: id
                })
                    .then(res => {
                        this.loading = false
                        this.getFootPrintData()
                        this.$message({ message: res.message, type: "success" })
                    })
                    .catch(err => {
                        this.loading = false
                        this.$message.error(err.message)
                    })
            },
            mouseenter(scope) {
                this.$set(scope, "del", true)
            },
            mouseleave(scope) {
                this.$set(scope, "del", false)
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
	
    .el-card.is-always-shadow,
    .el-card.is-hover-shadow:focus,
    .el-card.is-hover-shadow:hover {
        box-shadow: unset;
    }

    .el-card {
        border: 0;
    }

    .ns-goods-list {
        display: flex;
        flex-wrap: wrap;

        .ns-goods-li {
            width: 32%;
            margin-right: 2%;
            margin-bottom: 20px;
            padding: 15px;
            box-sizing: border-box;
            background-color: #f1f1f1;
            border-radius: 5px;
            cursor: pointer;
            position: relative;

            .el-image {
                width: 100%;
                height: 250px;
            }

            .goods-name {
                margin-top: 10px;
                display: -webkit-box;
                -webkit-box-orient: vertical;
                -webkit-line-clamp: 2;
                overflow: hidden;
            }

            .goods-price {
                color: $base-color;
                font-size: 20px;
            }

            .ns-btn-del {
                position: absolute;
                top: -6px;
                right: -6px;
                color: #999999;
                cursor: pointer;
                display: none;
                width: 20px;
                height: 20px;
                text-align: center;
                line-height: 20px;

                i {
                    font-size: 20px;
                }
            }

            &:hover {
                .ns-btn-del {
                    display: inline-block;
                }
            }
        }

        .ns-goods-li:nth-child(3n) {
            margin-right: 0;
        }
    }
</style>
<style lang="scss">
    .foot-print .ns-time-line .el-timeline-item__timestamp.is-top {
        font-size: 18px;
        color: #333333;
    }
</style>
