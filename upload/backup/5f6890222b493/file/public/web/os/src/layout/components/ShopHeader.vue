<template>
  <div class="header" v-loading="loading">
    <ns-header-top />
    <ns-header-mid />
    <div class="shop" v-if="shopInfo">
      <div class="shop-wrap">
        <div class="img-wrap">
          <img
            :src="shopInfo.avatar ? $img(shopInfo.avatar) : $img(defaultShopImage)"
            @error="shopInfo.avatar = defaultShopImage"
            :alt="shopInfo.site_name"
          />
        </div>
        <div class="shop-title">{{ shopInfo.site_name }}</div>
        <div class="shop-welcome">
          ————————————
          <p>{{ shopInfo.site_name }}欢迎您</p>
        </div>
      </div>
    </div>
    <div class="shop-header">
      <div class="nav">
        <div class="shop-sort" @mouseover="shopHover" @mouseleave="shopLeave">
          <!-- <router-link to="/category">所有商品分类</router-link> -->
          <router-link :to="'/shop_list?site_id='+ site_id">所有商品分类</router-link>
          <i class="iconfont iconweibiaoti35"></i>
        </div>
        <div
          class="shop-list"
          :class="forceExpand || isShopHover || isIndex ? 'shop-list-active border' : 'shadow'"
          @mouseover.stop="shopHover"
          @mouseleave.stop="shopLeave"
        >
          <div
            class="list-item"
            v-for="(item, index) in goodsCategoryTree"
            :key="index"
            @mouseover="selectedCategory = item.category_id"
            @mouseleave="selectedCategory = -1"
          >
            <router-link
              :to="{ path: '/shop_list', query: { site_id: site_id, shop_category_id: item.category_id, category_level: item.level } }"
            >{{ item.category_name }}</router-link>
            <div
              class="cate-part"
              v-if="item.child_list"
              :class="{ show: selectedCategory == item.category_id }"
            >
              <div class="cate-part-col1">
                <div class="cate-detail">
                  <dl
                    class="cate-detail-item"
                    v-for="(second_item, second_index) in item.child_list"
                    :key="second_index"
                  >
                    <dt class="cate-detail-tit">
                      <router-link
                        :to="{ path: '/shop_list', query: { site_id: site_id, shop_category_id: second_item.category_id, category_level: second_item.level } }"
                      >
                        {{ second_item.category_name }}
                        <i
                          class="el-icon-arrow-right"
                          aria-hidden="true"
                          v-if="second_item.child_list"
                        ></i>
                      </router-link>
                    </dt>
                    <dd class="cate-detail-con" v-if="second_item.child_list">
                      <router-link
                        v-for="(third_item, third_index) in second_item.child_list"
                        :key="third_index"
                        :to="{ path: '/shop_list', query: { site_id: site_id, shop_category_id: third_item.category_id, category_level: third_item.level } }"
                      >{{ third_item.category_name }}</router-link>
                    </dd>
                  </dl>
                </div>
              </div>
            </div>
          </div>
        </div>
        <nav>
          <ul>
            <li
              v-for="nav_item in navList"
              :key="nav_item.id"
              :class="nav_item.url == navSelect ? 'router-link-active' : ''"
              @click="navUrl(nav_item.id)"
            >
              <span>{{ nav_item.nav_title }}</span>
            </li>
          </ul>
        </nav>
      </div>
    </div>
  </div>
</template>

<script>
import NsHeaderTop from "./NsHeaderTop.vue";
import NsHeaderMid from "./NsHeaderMid.vue";
import { shopInfo, tree } from "@/api/shop/index";
import { mapGetters } from "vuex";
export default {
  props: {
    forceExpand: {
      type: Boolean,
      default: false
    }
  },
  data() {
    return {
      isShopHover: false,
      isIndex: false,
      thisRoute: "",
      goodsCategoryTree: [],
      selectedCategory: -1,
      navSelect: "",
      navList: [
        { nav_title: "店铺首页", id: "0" },
        { nav_title: "全部商品", id: "1" }
      ],
      site_id: "",
      shopInfo: {},
      loading: true
    };
  },
  components: {
    NsHeaderTop,
    NsHeaderMid
  },
  computed: {
    ...mapGetters(["defaultShopImage"])
  },
  beforeCreate() {},
  created() {
    if (!this.$route.query.site_id) {
      this.site_id = this.$route.path.replace("/shop-", "");
    } else {
      this.site_id = this.$route.query.site_id;
    }
    this.$store.dispatch("cart/cart_count");
    this.getTree();
    if (this.$route.path == "/" || this.$route.path == "/index")
      this.isIndex = true;
    this.getShopInfo();
  },
  mounted() {},
  watch: {
    $route(curr) {
      if (!this.$route.query.site_id) {
        this.site_id = this.$route.path.replace("/shop-", "");
      } else {
        this.site_id = this.$route.query.site_id;
      }
      this.$store.dispatch("cart/cart_count");
      this.getTree();
      if (this.$route.path == "/" || this.$route.path == "/index")
        this.isIndex = true;
      this.getShopInfo();
    }
  },
  methods: {
    getTree() {
      tree({ site_id: this.site_id })
        .then(res => {
          this.goodsCategoryTree = res.data;
        })
        .catch(err => {
          this.$message.error(err.message);
        });
    },
    //鼠标移入显示商品分类
    shopHover() {
      if (this.goodsCategoryTree.length) {
        this.isShopHover = true;
      }
    },
    //鼠标移出商品分类隐藏
    shopLeave() {
      if (this.goodsCategoryTree.length) {
        this.isShopHover = false;
      }
    },
    navUrl(id) {
      if (id == 0) {
        this.$router.push("shop-" + this.site_id);
      } else if (id == 1) {
        this.$router.push({
          path: "shop_list",
          query: { site_id: this.site_id }
        });
      }
    },
    //获取店铺信息
    getShopInfo() {
      shopInfo({ site_id: this.site_id })
        .then(res => {
          if (res.code == 0) {
            this.shopInfo = res.data;
            this.loading = false;
          }
        })
        .catch(err => {
          this.loading = false;
          this.$message.error(err.message);
        });
    }
  }
};
</script>

<style scoped lang="scss">
.header {
  width: 100%;
  background-color: #fff;
  padding: 0;
}
.header-in {
  width: $width;
  height: 109px;
  margin: 20px auto 0;
  img {
    margin: 22px 120px auto 0;
    float: left;
  }
  .in-sousuo {
    width: 550px;
    float: left;
    margin: 10px 0 0 0;
    .sousuo-tag {
      display: block;
      height: 24px;
      span {
        cursor: pointer;
        font-size: 12px;
        padding: 0 10px;
        border-right: solid 1px #e1e1e1;
        &:last-child {
          border-right: none;
        }
      }
      .span-font {
        color: $base-color;
      }
    }
    .sousuo-box {
      width: 100%;
      height: 36px;
      border: 2px solid $base-color;
      box-sizing: border-box;
      input {
        width: 400px;
        height: 22px;
        background: none;
        outline: none;
        border: none;
        float: left;
        margin: 4px;
      }
      .box-btn {
        width: 80px;
        height: 100%;
        background-color: $base-color;
        color: #fff;
        float: right;
        text-align: center;
        line-height: 32px;
        cursor: pointer;
      }
    }
    .sousuo-key {
      width: 100%;
      height: 20px;
      margin-top: 5px;
      font-size: 12px;
      span {
        float: left;
      }
      ul {
        margin: 0;
        padding: 0;
        float: left;
        color: $ns-text-color-black;
        li {
          cursor: pointer;
          list-style: none;
          float: left;
          margin-right: 10px;
          color: $ns-text-color-black;
        }
      }
    }
  }
  .car {
    float: left;
    width: 91px;
    height: 36px;
    margin-top: 34px;
    margin-left: 30px;
    padding: 0 28px 0 19px;
    border: 1px solid #dfdfdf;
    color: $base-color;
    font-size: 12px;
    span {
      cursor: pointer;
      line-height: 38px;
      margin-right: 10px;
    }
  }
}
.shop-header {
  width: 100%;
  background-color: #383838;

  .shadow {
    box-shadow: -1px 3px 12px -1px rgba(0, 0, 0, 0.3);
  }
  .border {
    border: 1px solid #f5f5f5;
  }
  .nav {
    width: 1210px;
    height: 41px;
    margin: 0 auto;
    position: relative;
    .shop-sort {
      width: 234px;
      height: 40px;
      color: #fff;
      background-color: $base-color;
      float: left;
      padding: 10px 10px 11px 10px;
      box-sizing: border-box;
      a {
        font-size: 14px;
        line-height: 20px;
        float: left;
        color: #fff;
      }
      i {
        font-size: 14px;
        float: right;
      }
    }
    .shop-list {
      width: 234px;
      height: 440px;
      position: absolute;
      left: 0;
      top: 41px;
      background-color: #fff;
      display: none;
      padding: 12px 0;
      box-sizing: border-box;
      font-size: $ns-font-size-base;
      z-index: 10;
      // box-shadow: -1px 3px 12px -1px rgba(0, 0, 0, 0.3);

      a:hover {
        color: $base-color;
      }
      .list-item {
        height: 32px;
        padding-left: 12px;
        line-height: 32px;
        &:hover {
          background-color: #d9d9d9;
          -webkit-transition: 0.2s ease-in-out;
          -moz-transition: -webkit-transform 0.2s ease-in-out;
          -moz-transition: 0.2s ease-in-out;
          transition: 0.2s ease-in-out;
        }
        .cate-part {
          display: none;
          position: absolute;
          left: 210px;
          top: 0;
          z-index: auto;
          // width: 998px;
          width: 758px;
          min-height: 436px;
          border: 1px solid #f7f7f7;
          background-color: #fff;
          -webkit-box-shadow: 2px 0 5px rgba(0, 0, 0, 0.3);
          -moz-box-shadow: 2px 0 5px rgba(0, 0, 0, 0.3);
          box-shadow: 2px 0 5px rgba(0, 0, 0, 0.3);
          -webkit-transition: top 0.25s ease;
          -o-transition: top 0.25s ease;
          -moz-transition: top 0.25s ease;
          transition: top 0.25s ease;
          &.show {
            display: block;
          }
        }
        .cate-part-col1 {
          float: left;
          width: 740px;
          padding: 20px 0 10px;
          .cate-detail-item {
            position: relative;
            min-height: 36px;
            padding-left: 80px;
            .cate-detail-tit {
              position: absolute;
              top: 4px;
              left: 0;
              overflow: hidden;
              width: 70px;
              text-align: right;
              white-space: nowrap;
              text-overflow: ellipsis;
              font-weight: 700;
            }
            .cate-detail-con {
              overflow: hidden;
              padding: 6px 0;
              border-top: 1px solid #eee;
              a {
                height: 16px;
                float: left;
                margin: 4px 0;
                padding: 0 10px;
                white-space: nowrap;
                border-left: 1px solid #e0e0e0;
                line-height: 16px;
              }
            }
            &:first-child .cate-detail-con {
              border-top: none;
            }
          }
        }
      }
    }
    .shop-list-active {
      display: block;
    }
    nav {
      width: 934px;
      height: 36px;
      float: left;
      font-size: 14px;
      color: #fff;
      ul {
        margin: 0;
        padding: 0;
        width: 100%;
        height: 100%;
        li {
          cursor: pointer;
          list-style: none;
          float: left;
          padding: 8px 40px;
          a {
            &:hover {
              color: $base-color;
            }
          }
        }
        li:hover {
          color: $base-color;
          font-weight: bold;
        }
        .router-link-active {
          color: $base-color;
          font-weight: bold;
        }
      }
    }
  }
}
.shop {
  background: #f7f8fb;
  .shop-wrap {
    width: $width;
    margin: 0 auto;
    display: flex;
    align-items: center;
  }
  .img-wrap {
    width: 116px;
    height: 73px;
    margin: 39px 0 38px 110px;
    display: block;
    overflow: hidden;
    img {
      display: inline-block;
      max-height: 73px;
    }
  }

  .shop-title {
    font-size: 28px;
    color: #000000;
    font-weight: 600;
    margin-left: 44px;
  }
  .shop-welcome {
    display: flex;
    margin-left: 320px;
    align-items: center;
    p {
      font-weight: 400;
      font-size: 18px;
      color: #000000;
      margin-left: 31px;
    }
  }
}
</style>
