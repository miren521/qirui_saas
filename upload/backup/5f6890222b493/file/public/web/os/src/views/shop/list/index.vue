<template>
    <div>
        <div class="goods-list" v-loading="loading">
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
            <el-input
                placeholder="搜索 商品"
                v-model="keyword"
                maxlength="50"
                class="input-with-select"
                size="medium"
                clearable
            >
                <el-button slot="append" icon="el-icon-search" @click="getGoodsList"></el-button>
            </el-input>
            <div class="shop-goods-cate" :class="{border:categoryList.length}">
                <div class="item" v-for="item in categoryList" :key="item.category_id">
                <div class="menu-name" @click="tabCate(item)">{{ item.category_name }}</div>
                <div
                    :class="filters.category_id == cate.category_id ? 'active' : 'submenu'"
                    v-for="cate in item.child_list"
                    :key="cate.category_id"
                    @click="tabCate(cate)"
                >{{ cate.category_name }}</div>
                </div>
            </div>
            </div>
            <div class="shop-right">
            <!-- 搜索关键字 -->
            <div class="search_bread" v-if="keyword">
                <span>搜索结果:</span>
                <span class="keyword">'{{ keyword }}'</span>
            </div>

            <!-- 商品列表 -->
            <!-- 排序筛选区 -->
            <div>
                <div class="sort">
                <div class="item" @click="changeSort('')">
                    <div class="item-name">综合</div>
                </div>
                <div class="item" @click="changeSort('sale_num')">
                    <div class="item-name">销量</div>
                    <i
                    v-if="filters.order === 'sale_num' && filters.sort === 'desc'"
                    class="el-icon-arrow-down el-icon--down"
                    />
                    <i v-else class="el-icon-arrow-up el-icon--up" />
                </div>
                <div class="item" @click="changeSort('discount_price')">
                    <div class="item-name">价格</div>
                    <i
                    v-if="filters.order === 'discount_price' && filters.sort === 'desc'"
                    class="el-icon-arrow-down el-icon--down"
                    />
                    <i v-else class="el-icon-arrow-up el-icon--up" />
                </div>
                <div class="item-other">
                    <el-checkbox label="包邮" v-model="is_free_shipping"></el-checkbox>
                </div>
                <div class="input-wrap">
                    <div class="price_range">
                    <el-input placeholder="最低价格" size="small" v-model="filters.min_price"></el-input>
                    <span>—</span>
                    <el-input placeholder="最高价格" size="small" v-model="filters.max_price"></el-input>
                    </div>
                    <el-button plain size="mini" @click="handlePriceRange">确定</el-button>
                </div>
                </div>
            </div>

            <!-- 商品列表 -->
            <div>
                <div class="goods-info" v-if="cateGoodsList.length">
                <div
                    class="item"
                    v-for="(item, index) in cateGoodsList"
                    :key="item.goods_id"
                    @click="$router.pushToTab({ path: '/sku-' + item.sku_id })"
                >
                    <img
                    class="img-wrap"
                    :src="$img(item.sku_image, { size: 'mid' })"
                    @error="imageError(index)"
                    />
                    <div class="price-wrap">
                    <div class="price">
                        <p>￥</p>
                        {{ item.discount_price }}
                    </div>
                    <div class="market-price">￥{{ item.market_price }}</div>
                    </div>
                    <div class="goods-name">{{ item.goods_name }}</div>
                    <div class="sale-num">
                    <p>{{ item.sale_num || 0 }}</p>人付款
                    </div>
                    <div class="saling">
                    <div class="free-shipping" v-if="item.is_free_shipping == 1">包邮</div>
                    <div class="promotion-type" v-if="item.promotion_type == 1">限时折扣</div>
                    </div>
                </div>
                </div>
                <div class="empty" v-else>
                <div class="ns-text-align">没有找到您想要的商品。换个条件试试吧</div>
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
</template>

<script>
import list from "./list";

export default {
  name: "shop_list",
  mixins: [list]
};
</script>

<style lang="scss" scoped>
.goods-list {
  padding-top: 10px;
  display: flex;
  background-color: #ffffff;
  .shop-left {
    width: 234px;
    .shop-wrap {
      width: 191px;
      float: right;
      border: 1px solid #eeeeee;
      background: #ffffff;
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
    .el-input {
      margin: 10px 0;
    }
    .border {
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
      .active {
        font-size: $ns-font-size-base;
        line-height: 2;
        border-top: 1px solid #f1f1f1;
        margin-left: 10px;
        cursor: pointer;
        color: $base-color;
      }
    }
  }
}
.shop-right {
  margin-left: 26px;
  width: 100%;
}
.el-form-item {
  margin-bottom: 0 !important;
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
      align-items: center;
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
        margin-left: 10px;
      }
    }
    .sale-num {
      display: flex;
      color: #838383;
      p {
        color: #4759a8;
      }
    }
    .saling {
      display: flex;
      font-size: $ns-font-size-sm;
      line-height: 1;
      .free-shipping {
        background: $base-color;
        color: #ffffff;
        padding: 3px 4px;
        border-radius: 2px;
        margin-right: 5px;
      }
      .promotion-type {
        color: $base-color;
        border: 1px solid $base-color;
        display: flex;
        align-items: center;
        padding: 1px;
      }
    }
  }
}
.search_bread {
  display: inline-block;
  font-size: 14px;
  margin: 0px auto 10px auto;
  width: 100%;
}

.selected_border {
  border: 1px solid $base-color;
}

.attr_filter {
  .el-tag {
    margin-right: 5px;
    margin-bottom: 10px;
    border-radius: 0;
    .title {
      color: $base-color;
      font-size: 15px;
    }
  }
}

.category {
  margin: 0 auto 10px auto;
  border: 1px solid #dcdfe6;

  .brand {
    border-bottom: 1px solid #dcdfe6;
    display: flex;
    flex-direction: row;

    .table_head {
      width: 140px;
      min-width: 140px;
      border-right: 1px solid #dcdfe6;
      padding-left: 10px;
      padding-top: 10px;
      background-color: #f2f6fc;
    }
    .table_body {
      flex-grow: 1;
      .initial {
        margin: 5px auto 10px 10px;

        span {
          border: 0;
          margin: 0;
          padding: 5px 10px;
          border: 0;
          border-radius: 0;

          &:hover {
            background-color: $base-color;
          }
        }
      }

      .content {
        display: flex;
        flex-direction: row;
        flex-wrap: wrap;
        padding: 10px;
        width: 900px;

        .el-card {
          width: 125px;
          height: 45px;

          &:hover {
            border: 1px solid $base-color;
          }
        }
      }
    }

    .table_op {
      margin-top: 5px;
    }

    .el-image {
      width: 100%;
      height: 100%;
    }
  }
}

.cargo-list {
  display: flex;
  flex-direction: row;
  flex-wrap: wrap;
  justify-content: flex-start;

  .item {
    padding: 0 2px;
  }
}

.shop-item {
  padding: 2px 2px;
}

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
    &:hover {
      background: $base-color;
      color: #ffffff;
    }
  }
  .item-other {
    display: flex;
    align-items: center;
    padding: 5px 15px;
    border-right: 1px solid #f1f1f1;
    cursor: pointer;
  }
  .input-wrap {
    display: flex;
    align-items: center;
    .price_range {
      margin-left: 60px;
    }
    span {
      padding-left: 10px;
    }
    .el-input {
      width: 150px;
      margin-left: 10px;
    }
    .el-button {
      margin: 0 17px;
    }
  }
}
.empty {
  line-height: 500px;
}
.pager {
  text-align: center;
  margin-top: 30px;
}
</style>
