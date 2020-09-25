<template>
  <div class="goods-list" v-loading="loading">
    <!-- 搜索关键字 -->
    <div class="search_bread" v-if="keyword">
      <span>搜索结果：</span>
      <span class="keyword">'{{ keyword}}'</span>
    </div>
    <div class="search_bread" v-else-if="!keyword && catewords">
      <span>搜索结果:：</span>
      <span class="keyword">'{{ catewords}}'</span>
    </div>

    <!-- 品牌过滤记录区 -->
    <div class="attr_filter" v-if="catewords">
      <el-tag type="info" v-if="choosedBrand" closable @close="colseBrand" effect="plain">
        <span v-if="choosedBrand" class="title">品牌：</span>
        {{ choosedBrand.brand_name }}
      </el-tag>
      <span v-for="item in attributeList" :key="item.attr_id">
        <el-tag
          type="info"
          v-if="item.selectedValue"
          closable
          @close="colseAttr(item)"
          effect="plain"
        >
          <span class="title" v-if="item.attr_name">{{ item.attr_name }}：</span>
          {{ item.selectedValue }}
        </el-tag>
      </span>
    </div>

    <!-- 品牌属性筛选区 -->
    <template v-if="catewords">
      <div class="category" v-if="brandList.length || attributeList.length">
        <!-- 品牌 -->
        <div class="brand">
          <div class="table_head">品牌：</div>
          <div class="table_body">
            <div class="initial">
              <span type="info" effect="plain" hit @mouseover="handleChangeInitial('')">所有品牌</span>
              <span
                type="info"
                effect="plain"
                hit
                v-for="item in brandInitialList"
                :key="item"
                @mouseover="handleChangeInitial(item)"
              >{{ (item || '').toUpperCase() }}</span>
            </div>
            <div class="content">
              <el-card
                v-for="item in brandList"
                :key="item.id"
                body-style="padding: 0;height: 100%;"
                shadow="hover"
                v-show="currentInitial === '' || item.brand_initial === currentInitial"
                class="brand-item"
              >
                <el-image
                  :src="$img(item.image_url || defaultGoodsImage)"
                  :alt="item.brand_name"
                  :title="item.brand_name"
                  fit="contain"
                  @click="onChooseBrand(item)"
                />
              </el-card>
            </div>
          </div>
        </div>

        <!-- 属性 -->
        <div class="brand" v-for="item in attributeList" :key="'attr' + item.attr_id">
          <div class="table_head">{{ item.attr_name }}：</div>
          <div class="table_body">
            <div class="content">
              <span v-for="subitem in item.child" :key="subitem.attr_value_id">
                <el-checkbox
                  :label="subitem.attr_value_name"
                  v-if="item.isMuiltSelect"
                  :checked="subitem.selected"
                  @change="setAttrSelectedMuilt(item, subitem)"
                ></el-checkbox>
                <el-link
                  :underline="false"
                  v-else
                  @click="setAttrSelected(item, subitem)"
                >{{ subitem.attr_value_name }}</el-link>
              </span>
            </div>
          </div>
          <div class="table_op">
            <el-button
              size="small"
              icon="el-icon-circle-plus-outline"
              @click="setMuiltChoose(item)"
            >多选</el-button>
          </div>
        </div>
      </div>
    </template>

    <div class="list-wrap">
      <div class="goods-recommended" v-if="cargoList.length">
        <goods-recommend :page-size="cargoList.length < 5 ? 2 : 5" />
      </div>
      <div class="list-right">
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
            <div class="item-other">
              <el-checkbox label="自营" v-model="is_own"></el-checkbox>
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
        <div class="cargo-list" v-if="cargoList.length">
          <div class="goods-info">
            <div
              class="item"
              v-for="(item, index) in cargoList"
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
              <div class="shop_name">{{item.site_name}}</div>
              <div class="saling">
                <div class="free-shipping" v-if="item.is_free_shipping == 1">包邮</div>
                <div class="is_own" v-if="item.is_own == 1">自营</div>
                <div class="promotion-type" v-if="item.promotion_type == 1">限时折扣</div>
              </div>
              <!-- <div class="item-bottom">
                  <div v-if="!item.isCollection" class="collection" @click.stop="editCollection(item)">
                    <i class="iconfont iconlike"></i>
                    收藏
                  </div>
				  <div v-if="item.isCollection" class="collection" @click.stop="editCollection(item)">
                    <i class="iconfont iconlikefill ns-text-color"></i>
                    取消收藏
                  </div>
                  <div class="cart" @click.stop="addToCart(item)">
                    <i class="el-icon-shopping-cart-2"></i>
                    加入购物车
                  </div>
              </div>-->
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
        <div class="empty" v-else>
          <div class="ns-text-align">没有找到您想要的商品。换个条件试试吧</div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import BreadCrumbs from "@/components/BreadCrumbs";
import GoodsRecommend from "@/components/GoodsRecommend";
import list from "./list";

export default {
  name: "list",
  components: {
    BreadCrumbs,
    GoodsRecommend
  },
  data: () => {
    return {};
  },
  mixins: [list],
  created() {},
  methods: {}
};
</script>

<style lang="scss" scoped>
.goods-list {
  background: #fff;
  padding-top: 10px;
}
.search_bread {
  display: inline-block;
  font-size: 14px;
  margin: 0px auto;
  width: 100%;
  padding: 10px;
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
  border: 1px solid #eeeeee;

  .brand {
    border-bottom: 1px solid #eeeeee;
    display: flex;
    flex-direction: row;

    &:last-child {
      border-bottom: none;
    }

    .table_head {
      width: 140px;
      min-width: 140px;
      border-right: 1px solid #eeeeee;
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
            color: #ffffff;
          }
        }
      }

      .content {
        display: flex;
        flex-direction: row;
        flex-wrap: wrap;
        padding: 10px;
        width: 900px;
        .brand-item{
          margin-right: 10px;
          margin-top: 10px;
        }

        .el-card {
          width: 125px;
          height: 45px;

          &:hover {
            border: 1px solid $base-color;
            cursor: pointer;
          }
        }

        span {
          margin: auto 25px;
        }
      }
    }

    .table_op {
      margin-top: 5px;
      margin-right: 5px;
    }

    .el-image {
      width: 100%;
      height: 100%;
    }
  }
}

.list-wrap {
  overflow: hidden;
}
.goods-recommended {
  width: 200px;
  background-color: #fff;
  float: left;
  margin-right: 10px;
}

.sort {
  display: flex;
  align-items: center;
  
  .item {
    display: flex;
    align-items: center;
    padding: 5px 15px;
    border: 1px solid #f1f1f1;
    border-left: none;
    cursor: pointer;
    &:hover {
      background: $base-color;
      color: #fff;
    }
  }
  .item-other {
    display: flex;
    align-items: center;
    padding: 5px 15px;
    border: 1px solid #f1f1f1;
    border-left: none;
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

  >div:first-child {
    border-left: 1px solid #f1f1f1;
  }
}
.goods-info {
  margin-top: 5px;
  display: flex;
  flex-wrap: wrap;
  .item {
    width: 220px;
    margin: 10px 20px 0 0;
    border: 1px solid #eeeeee;
    padding-top: 10px;
    position: relative;
    padding-bottom: 5px;
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
      padding: 10px;
    }
    .goods-name {
      padding: 0 10px;
      overflow: hidden;
      text-overflow: ellipsis;
      display: -webkit-box;
      -webkit-box-orient: vertical;
      -webkit-line-clamp: 2;
      word-break: break-all;
      height: 50px;
      cursor: pointer;
      &:hover {
        color: $base-color;
      }
    }
    .price-wrap {
      padding: 0 10px;
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
      padding: 0 10px;
      display: flex;
      color: #838383;
       font-size: $ns-font-size-sm;
      p {
        color: #4759a8;
      }
    }
    .shop_name {
      padding: 0 10px;
      display: flex;
      color: #838383;
    }
    .saling {
      padding: 0 10px;
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
      .is_own {
        color: #ffffff;
        background: #ff850f;
        border: 1px solid;
        margin-right: 5px;
        display: flex;
        align-items: center;
        padding: 3px 4px;
        border-radius: 2px;
      }
      .promotion-type {
        color: $base-color;
        border: 1px solid $base-color;
        display: flex;
        align-items: center;
        padding: 1px 3px;
      }
    }
    .item-bottom {
      display: flex;
      margin-top: 5px;
      .collection {
        flex: 1;
        border: 1px solid #e9e9e9;
        border-right: none;
        border-left: none;
        height: 40px;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
      }
      .cart {
        flex: 2;
        border: 1px solid #e9e9e9;
        border-right: none;
        height: 40px;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
      }
    }
  }
}
.empty {
  margin-top: 45px;
}
.pager {
  text-align: center;
  margin-top: 30px;
}
</style>
