<template>
    <div>
        <div class="shop-index" v-loading="loading">
            <div class="shop-left">
            <div class="shop-wrap">
                <div class="head-wrap">
                <div class="img-wrap">
                    <img
                    class="img-responsive center-block"
                    :src="shopInfo.avatar ? $img(shopInfo.avatar) : $img(defaultShopImage)"
                    @error="shopInfo.avatar = defaultShopImage"
                    :alt="shopInfo.site_name"
                    />
                </div>
                <h5>
                    <span class="ns-text-color">{{ shopInfo.site_name }}</span>
                    <el-tag class="tag" size="small" v-if="shopInfo.is_own == 1">自营</el-tag>
                </h5>
                </div>
                <div class="info-wrap">
                <div class="item">
                    描述相符
                    <b>{{ shopInfo.shop_desccredit }}</b>分
                </div>
                <div class="item">
                    服务态度
                    <b>{{ shopInfo.shop_servicecredit }}</b>分
                </div>
                <div class="item">
                    发货速度
                    <b>{{ shopInfo.shop_deliverycredit }}</b>分
                </div>
                </div>
                <div class="other-info">
                <div class="tell" v-if="shopInfo.telephone">
                    <div class="tell-name">电话:</div>
                    <div class="tell-detail">{{shopInfo.telephone}}</div>
                </div>
                <div class="item-info" v-if="shopInfo.full_address">
                    <div class="item-name">地址:</div>
                    <div class="item-detail">{{shopInfo.full_address}}{{shopInfo.address}}</div>
                </div>
                </div>
                <div class="operation">
                <el-button size="medium" @click="getIsFollow" v-if="hasFollow">取消关注</el-button>
                <el-button size="medium" @click="getIsFollow" v-else>关注店铺</el-button>
                </div>
            </div>
            <div class="shop-goods-search">
                <el-input
                placeholder="搜索 商品"
                v-model="keyword"
                class="input-with-select"
                size="medium"
                maxlength="50"
                clearable
                >
                <el-button slot="append" icon="el-icon-search" @click="goGoodsList"></el-button>
                </el-input>
            </div>
            <div class="shop-goods-cate" :class="{border:categoryList.length}">
                <div class="item" v-for="item in categoryList" :key="item.category_id">
                <div
                    class="menu-name"
                    @click="$router.push({ path: '/shop_list', query: { site_id: id, shop_category_id: item.category_id } })"
                >{{ item.category_name }}</div>
                <div
                    class="submenu"
                    v-for="cate in item.child_list"
                    :key="cate.category_id"
                    @click="$router.push({ path: '/shop_list', query: { site_id: id, shop_category_id: item.category_id} })"
                >{{ cate.category_name }}</div>
                </div>
            </div>
            </div>
            <div class="shop-right">
            <div class="content">
                <div class="shop-banner">
                <el-carousel height="406px">
                    <el-carousel-item v-for="item in adList" :key="item.adv_id">
                    <el-image :src="$img(item.adv_image)" fit="cover" @click="$router.pushToTab(item.adv_url.url)" />
                    </el-carousel-item>
                </el-carousel>
                </div>
                <div class="tj" v-if="goodsList.length">
                <h5>本店推荐</h5>
                </div>
                <div class="goods-info">
                <div
                    class="item"
                    v-for="(item, index) in goodsList"
                    :key="item.goods_id"
                    @click="$router.pushToTab({ path: '/sku-' + item.sku_id })"
                >
                    <img
                    class="img-wrap"
                    :src="$img(item.sku_image, { size: 'mid' })"
                    @error="imageError(index)"
                    />

                    <div class="goods-name">{{ item.goods_name }}</div>
                    <div class="price-wrap">
                    <div class="price">
                        <p>￥</p>
                        {{ item.discount_price }}
                    </div>
                    <div class="market-price">￥{{ item.market_price }}</div>
                    </div>
                    <div class="promotion_type" v-if="item.promotion_type == 1">限时折扣</div>
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
            </div>
            </div>
        </div>
    </div>
</template>

<script>
import { shopInfo, isFollow, unFollow, follow,tree } from "@/api/shop/index";
import { goodsSkuPage } from "@/api/goods/goods";
import { mapGetters } from "vuex";
import { adList } from "@/api/website";
export default {
  name: "shop_index",
  components: {},
  data: () => {
    return {
      id: 0,
      currentPage: 1,
      pageSize: 12,
      shopInfo: {}, //店铺信息
      hasFollow: false, //是否关注
      searchContent: "",
      categoryList: [],
      goodsList: [],
      total: 0,
      loading: true,
      keyword: "",
      adList: []
    };
  },
  computed: {
    ...mapGetters([
      "token",
      "defaultShopImage",
      "addonIsExit",
      "defaultGoodsImage"
    ])
  },
  created() {
    this.id = this.$route.path.replace("/shop-", "");
    this.getAdList();
    this.getShopInfo();
    this.getCategoryList();
    this.getGoodsList();
    this.getfollow();
  },
  watch: {
    $route(curr) {
      this.id = curr.params.pathMatch;
      this.getAdList();
      this.getShopInfo();
      this.getCategoryList();
      this.getGoodsList();
      this.getfollow();
    }
  },
  methods: {
    getAdList() {
      adList({ keyword: "NS_PC_SHOP_INDEX" })
        .then(res => {
          this.adList = res.data.adv_list;
          for (let i = 0; i < this.adList.length; i++) {
            if (this.adList[i].adv_url)
              this.adList[i].adv_url = JSON.parse(this.adList[i].adv_url);
          }
        })
        .catch(err => err);
    },
    //获取店铺信息
    getShopInfo() {
      shopInfo({ site_id: this.id })
        .then(res => {
          if (res.code == 0) {
            this.shopInfo = res.data;
          }
        })
        .catch(err => {
          this.loading = false;
          this.$message.error(err.message);
        });
    },
    //获取店铺商品分类列表
    getCategoryList() {
      tree({ site_id: this.id })
        .then(res => {
          this.categoryList = res.data;
        })
        .catch(err => {
          this.loading = false;
          this.$message.error(err.message);
        });
    },
    //获取店铺商品列表
    getGoodsList() {
      const params = {
        page: this.currentPage,
        page_size: this.pageSize,
        site_id: this.id,
        keyword: this.keyword,
        sort: "desc"
      };
      goodsSkuPage(params || {})
        .then(res => {
          if (res.code == 0 && res.data) {
            let data = res.data;
            this.goodsList = data.list;
            this.total = res.data.count;
            this.loading = false;
          }
        })
        .catch(err => {
          this.loading = false;
          this.$message.error(err.message);
        });
    },
    //获取店铺关注信息
    getfollow() {
      isFollow({
        site_id: this.id
      })
        .then(res => {
          this.hasFollow = res.data;
        })
        .catch(err => {
        });
    },
    //关注与取消
    getIsFollow() {
      if (this.hasFollow) {
        unFollow({ site_id: this.id }).then(res => {
          if (res.code == 0 && res.data) {
            this.hasFollow = !this.hasFollow;
            this.$message({
              message: "取消关注成功",
              type: "success"
            });
          }
        });
      } else {
        follow({ site_id: this.id }).then(res => {
          if (res.code == 0 && res.data) {
            this.hasFollow = !this.hasFollow;
            this.$message({
              message: "关注成功",
              type: "success"
            });
          }
        });
      }
    },
    goGoodsList() {
      this.$router.push({
        path: "/shop_list",
        query: { site_id: this.id, keyword: this.keyword }
      });
    },
    handlePageSizeChange(num) {
      this.pageSize = num;
      this.getGoodsList();
    },
    handleCurrentPageChange(page) {
      this.currentPage = page;
      this.getGoodsList();
    },
    imageError(index) {
      this.goodsList[index].sku_image = this.defaultGoodsImage;
    }
  }
};
</script>
<style lang="scss" scoped>
.shop-index {
  padding-top: 10px;
  display: flex;
  background-color: #ffffff;
  .shop-left {
    width: 234px;
    .shop-wrap {
      width: 191px;
      float: right;
      border: 1px solid #eeeeee;
      padding: 0 21px;
      .head-wrap {
        text-align: center;
        padding: 10px 0;
        border-bottom: 1px solid #eeeeee;
        .img-wrap {
          width: 60px;
          height: 60px;
          line-height: 60px;
          display: inline-block;
        }
        .tag {
          margin-left: 5px;
        }
      }
      .info-wrap {
        padding: 10px;
        color: #838383;
        b {
          color: #383838;
        }
        border-bottom: 1px solid #eeeeee;
      }
      .other-info {
        padding: 10px;
        color: #838383;
        .item-info {
          display: flex;
          .item-name {
            width: 75px;
          }
        }
        .tell {
          display: flex;
          .tell-name {
            width: 35px;
          }
        }
      }
      .operation {
        border-top: 1px solid #eeeeee;
        padding: 10px 0;
        text-align: center;
      }
    }
    .shop-goods-search {
      .el-input {
        margin: 10px 0;
      }
    }
    .border{
      border: 1px solid #f1f1ff;
    }
    .shop-goods-cate {
      width: 100%;
      .menu-name {
        padding-left: 16px;
        background: #f8f8f8;
        font-size: $ns-font-size-base;
        height: 40px;
        cursor: pointer;
        color: #666666;
        display: flex;
        align-items: center;
      }
      .submenu {
        font-size: $ns-font-size-base;
        line-height: 2;
        border-top: 1px solid #f1f1f1;
        padding-left: 26px;
        cursor: pointer;
        display: flex;
        align-items: center;
        height: 40px;
        background: #ffffff;
        color: #666666;
      }
    }
  }
  .shop-right {
    margin-left: 26px;
    width: 100%;
    .shop-banner {
      width: 100%;
    }
    .tj {
      text-align: center;
      font-size: 30px;
      margin-top: 10px;
    }
    .goods-info {
      margin-top: 5px;
      display: flex;
      flex-wrap: wrap;
      .item {
        width: 200px;
        margin: 10px 20px 0 0;
        border: 1px solid #eeeeee;
        padding: 10px;
        position: relative;
        &:nth-child(4n) {
          margin-right: initial !important;
        }
        &:hover {
          border: 1px solid $base-color;
        }
        .img-wrap {
          width: 198px;
          height: 198px;
          cursor: pointer;
        }
        .goods-name {
          overflow: hidden;
          text-overflow: ellipsis;
          white-space: nowrap;
          cursor: pointer;
          &:hover {
            color: $base-color;
          }
        }
        .price-wrap {
          display: flex;
          .price {
            display: flex;
            color: $base-color;
            font-size: $ns-font-size-lg;
            p {
              font-size: $ns-font-size-base;
              display: flex;
              align-items: flex-end;
            }
          }
          .market-price {
            color: #838383;
            text-decoration: line-through;
            margin-left: 15px;
          }
        }
        .promotion_type {
          position: absolute;
          top: 0;
          right: 0;
          color: #ffffff;
          background: $base-color;
          border-bottom-left-radius: 10px;
          padding: 3px 8px;
          line-height: 1;
          font-size: $ns-font-size-sm;
        }
      }
    }
    .page {
      display: flex;
      justify-content: center;
      align-content: center;
      padding-top: 20px;
    }
  }
}
</style>
