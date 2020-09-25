<template>
    <div v-loading="loading">
        <div class="search_bread" v-if="keyword">
            <span>搜索结果:</span>
            <span class="keyword">'{{ keyword }}'</span>
        </div>

        <!-- 排序筛选区 -->
        <div class="filters">
            <div class="sort">
                <div class="item" @click="changeSort('site_id')">
                    <div class="item-name">综合</div>
                    <i
                        v-if="filters.order === 'site_id' && filters.sort === 'desc'"
                        class="el-icon-arrow-down el-icon--down"
                    />
                    <i v-else class="el-icon-arrow-up el-icon--up" />
                </div>
                <div class="item" @click="changeSort('shop_sales')">
                    <div class="item-name">销量</div>
                    <i
                        v-if="filters.order === 'shop_sales' && filters.sort === 'desc'"
                        class="el-icon-arrow-down el-icon--down"
                    />
                    <i v-else class="el-icon-arrow-up el-icon--up" />
                </div>
                <div class="item" @click="changeSort('shop_desccredit')">
                    <div class="item-name">信用</div>
                    <i
                        v-if="filters.order === 'shop_desccredit' && filters.sort === 'desc'"
                        class="el-icon-arrow-down el-icon--down"
                    />
                    <i v-else class="el-icon-arrow-up el-icon--up" />
                </div>
            </div>
            <div class="search">
                <el-input
                    placeholder="搜索 店铺"
                    v-model="keyword"
                    maxlength="50"
                    class="input-with-select"
                    size="medium"
                    clearable
                >
                    <el-button slot="append" icon="el-icon-search" @click="getShopList"></el-button>
                </el-input>
            </div>
        </div>

        <!-- 店铺列表 -->
        <div class="shop-wrap" v-if="shopList.length">
            <div
                class="shop-item"
                v-for="item in shopList"
                :key="item.id"
                @click="toDetail(item.site_id)"
            >
                <div class="banner-wrap">
                    <img :src="$img(item.banner)" v-if="item.banner" />
                    <img v-else src="../../assets/images/shop-defaultImg.png" alt />
                </div>
                <div class="avatar-wrap">
                    <img
                        class="avatar"
                        :src="$img(item.avatar || defaultShopImage)"
                        @error="item.avatar = defaultShopImage"
                    />
                </div>
                <div class="name-wrap">
                    <div class="name">{{ item.site_name }}</div>
                </div>

                <div class="main-wrap">
                    <p>主营：</p>
                    <div class="main">{{ item.category_name }}</div>
                </div>
                <div class="shop-bottom">
                    <div class="item">
                        <p>评分：</p>
                        {{ ((parseFloat(item.shop_desccredit) + parseFloat(item.shop_servicecredit) + parseFloat(item.shop_deliverycredit)) / 3).toFixed(1) }}
                    </div>
                    <div class="item-fan">
                        {{ item.sub_num }}
                        <p>人关注</p>
                    </div>
                </div>
            </div>
        </div>
        <div v-else class="empty">
            <div class="ns-text-align">没有找到您想要的店铺。换个条件试试吧</div>
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
    </div>
</template>

<script>
import { mapGetters } from "vuex"
import BreadCrumbs from "@/components/BreadCrumbs"
import { shopList } from "@/api/shop"
export default {
    data: () => {
        return {
            shopList: [], // 店铺列表
            total: 0,
            keyword: "",
            currentPage: 1,
            pageSize: 12,
            loading: true,
            filters: {
                order: "",
                sort: "desc"
            }
        }
    },
    components: { BreadCrumbs },
    created() {
        this.keyword = this.$route.query.keyword || ""
        this.getShopList()
    },
    computed: {
        ...mapGetters(["defaultGoodsImage", "defaultShopImage"])
    },
    methods: {
        getShopList() {
            const params = {
                keyword: this.keyword,
                page: this.currentPage,
                page_size: this.pageSize,
                ...this.filters
            }
            shopList(params || {})
                .then(res => {
                    const { count, page_count, list } = res.data
                    console.log(res)
                    this.total = count
                    this.shopList = list
                    this.loading = false
                })
                .catch(err => {
                    this.loading = false
                })
        },

        handlePageSizeChange(size) {
            this.pageSize = size
            this.getShopList()
        },
        handleCurrentPageChange(page) {
            this.currentPage = page
            this.getShopList()
        },
        changeSort(type) {
            this.filters.order = type
            if (this.filters.order === type) {
                this.$set(this.filters, "sort", this.filters.sort === "desc" ? "asc" : "desc")
            } else {
                this.$set(this.filters, "order", type)
                this.$set(this.filters, "sort", "desc")
            }
            this.getShopList()
        },
        toDetail(site_id) {
            this.$router.pushToTab({
                path: "/shop-" + site_id
            })
        }
    },
    watch: {
        $route: function(curr) {
            if (this.keyword !== curr.query.keyword) {
                this.currentPage = 1
                this.keyword = curr.query.keyword
                this.getShopList()
            }
        }
    }
}
</script>

<style lang="scss" scoped>
.search_bread {
    display: inline-block;
    font-size: 14px;
    margin: 0px auto;
    width: 100%;
    padding: 10px;
}

.filters {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-top: 10px;
    .sort {
        display: flex;
        align-items: center;
        border: 1px solid #f1f1f1;

        .item {
            display: flex;
            align-items: center;
            padding: 5px 15px;
            border-right: 1px solid #f1f1f1;
            cursor: pointer;
            &:last-child {
                border: none;
            }
            &:hover {
                background: $base-color;
                color: #fff;
            }
        }
    }
}

.shop-wrap {
    display: flex;
    flex-wrap: wrap;
    .shop-item {
        border: 1px solid #f1f1f1;
        padding: 15px;
        padding-bottom: 0;
        position: relative;
        margin-right: 12px;
        margin-top: 22px;
        cursor: pointer;
        &:hover {
            border: 1px solid $base-color;
        }
        &:nth-child(4n) {
            margin-right: initial;
        }
        .banner-wrap {
            width: 260px;
            height: 166px;
            overflow: hidden;
            display: block;
            text-align: center;
            img {
                display: inline-block;
                max-height: 166px;
            }
        }
        .avatar-wrap {
            position: absolute;
            top: 140px;
            right: 50%;
            transform: translateX(50%);
            width: 80px;
            height: 80px;
            line-height: 80px;
            border-radius: 50%;
            overflow: hidden;
            display: block;
            border: 2px solid #e9e9e9;
            .avatar {
                display: inline-block;
                background-color: #fff;
            }
        }
        .name-wrap {
            display: flex;
            justify-content: center;
            .name {
                margin-top: 50px;
                font-size: $ns-font-size-lg;
                text-align: center;
                overflow: hidden;
                white-space: nowrap;
                text-overflow: ellipsis;
                width: 170px;
            }
        }
        .main-wrap {
            display: flex;
            justify-content: center;
            p {
                color: #838383;
            }
            .main {
                display: flex;
                justify-content: center;
                overflow: hidden;
                white-space: nowrap;
                text-overflow: ellipsis;
                max-width: 140px;
            }
        }
        .shop-bottom {
            display: flex;
            border-top: 1px solid #f1f1f1;
            margin-top: 10px;
            .item {
                flex: 1;
                display: flex;
                justify-content: center;
                align-items: center;
                border-right: 1px solid #f1f1f1;
                padding: 10px 0;
                p {
                    color: #838383;
                }
            }
            .item-fan {
                flex: 1;
                display: flex;
                justify-content: center;
                align-items: center;
                padding: 10px 0;
                p {
                    color: #838383;
                }
            }
        }
    }
}
.pager {
    text-align: center;
    margin-top: 30px;
}
.empty {
    line-height: 500px;
}
</style>
