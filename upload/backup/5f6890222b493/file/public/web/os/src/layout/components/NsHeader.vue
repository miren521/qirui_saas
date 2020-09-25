<template>
  <div class="header">
    <ns-header-top />
    <ns-header-mid />

    <div class="nav">
      <div class="shop-sort" @mouseover="shopHover" @mouseleave="shopLeave">
        <router-link to="/category">所有商品分类</router-link>
        <i class="iconfont iconweibiaoti35"></i>
      </div>
      <div
        class="shop-list"
        :class="forceExpand || isShopHover || isIndex ? 'shop-list-active border' : 'shadow'"
        @mouseover="shopHover"
        @mouseleave="shopLeave"
      >
        <div
          class="list-item"
          v-for="(item, index) in goodsCategoryTree"
          :key="index"
          @mouseover="selectedCategory = item.category_id"
          @mouseleave="selectedCategory = -1"
        >
          <router-link
            :to="{ path: '/list', query: { category_id: item.category_id, level: item.level } }"
            target="_blank"
          >{{ item.category_name }}</router-link>
          <div
            class="cate-part"
            v-if="item.child_list"
            :class="{ show: selectedCategory == item.category_id }"
          >
            <div class="cate-part-col1">
              <!-- <div class="cate-channel">
								<router-link class="cate-channel-lk" to="/brand" target="_blank">
									手机城
									<i class="el-icon-arrow-right" aria-hidden="true"></i>
								</router-link>
              </div>-->
              <div class="cate-detail">
                <dl
                  class="cate-detail-item"
                  v-for="(second_item, second_index) in item.child_list"
                  :key="second_index"
                >
                  <dt class="cate-detail-tit">
                    <router-link
                      target="_blank"
                      :to="{ path: '/list', query: { category_id: second_item.category_id, level: second_item.level } }"
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
                      target="_blank"
                      :to="{ path: '/list', query: { category_id: third_item.category_id, level: third_item.level } }"
                    >{{ third_item.category_name }}</router-link>
                  </dd>
                </dl>
              </div>
            </div>
            <!-- <div class="sub-class-right">
							<div class="adv-promotions">
								<router-link title="" to="" target="_blank">
									<img src="" />
								</router-link>
							</div>
            </div>-->
          </div>
        </div>
      </div>
      <nav>
        <ul>
          <li
            v-for="(nav_item, nav_index) in navList"
            :key="nav_index"
            :class="nav_item.url == navSelect ? 'router-link-active' : ''"
            @click="navUrl(nav_item.url, nav_item.is_blank)"
          >
            <span>{{ nav_item.nav_title }}</span>
          </li>
        </ul>
      </nav>
    </div>
  </div>
</template>

<script>
import NsHeaderTop from "./NsHeaderTop";
import NsHeaderMid from "./NsHeaderMid";
import { tree } from "@/api/goods/goodscategory";
import { navList } from "@/api/website";
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
      navList: [],
      navSelect: ""
    };
  },
  components: {
    NsHeaderTop,
    NsHeaderMid
  },
  computed: {},
  beforeCreate() {},
  created() {
    this.$store.dispatch("cart/cart_count");
    this.getTree();
    this.nav();
    if (this.$route.path == "/" || this.$route.path == "/index")
      this.isIndex = true;
  },
  mounted() {},
  watch: {
    $route: function(curr) {
      this.initNav(curr.path);
      if (curr.path == "/" || curr.path == "/index") this.isIndex = true;
      else this.isIndex = false;

      if (curr.path == "/list") {
        this.navSelect = ""
      }
    }
  },
  methods: {
    getTree() {
      tree({
        level: 3,
        template: 2
      })
        .then(res => {
          if (res.code == 0 && res.data) {
            this.goodsCategoryTree = res.data;
          }
        })
        .catch(err => {
          this.$message.error(err.message);
        });
    },
    nav() {
      navList({})
        .then(res => {
          if (res.code == 0 && res.data) {
            this.navList = res.data.list;
            for (let i in this.navList) {
              this.navList[i]["url"] = JSON.parse(
                this.navList[i]["nav_url"]
              ).url;
            }
            this.initNav(this.$route.path);
          }
        })
        .catch(err => {
          this.$message.error(err.message);
        });
    },
    navUrl(url, target) {
      if (!url) return;
      if (url.indexOf("http") == -1) {
        if (target) this.$router.pushToTab({ path: url });
        else this.$router.push({ path: url });
      } else {
        if (target) window.open(url);
        else window.location.href = url;
      }
    },
    initNav(path) {
      for (let i in this.navList) {
        if (this.navList[i]["url"] == path) {
          this.navSelect = path;
          continue;
        }
      }
    },
    //鼠标移入显示商品分类
    shopHover() {
      this.isShopHover = true;
    },
    //鼠标移出商品分类隐藏
    shopLeave() {
      this.isShopHover = false;
    }
  }
};
</script>

<style scoped lang="scss">
.header {
  width: 100%;
  height: 180px;
  background-color: #fff;

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
      width: 210px;
      height: 41px;
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
      width: 210px;
      height: 430px;
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
          left: 209px;
          top: 0;
          z-index: auto;
          // width: 998px;
          width: 758px;
          min-height: 426px;
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
          // .cate-channel {
          // 	display: block;
          // 	overflow: hidden;
          // 	padding-left: 20px;
          // 	height: 24px;
          // 	.cate-channel-lk {
          // 		height: 24px;
          // 		float: left;
          // 		padding: 0 10px;
          // 		margin-right: 10px;
          // 		background-color: #7c7171;
          // 		line-height: 24px;
          // 		color: #fff;
          // 	}
          // }
          .cate-detail-item {
            position: relative;
            min-height: 36px;
            padding-left: 110px;
            .cate-detail-tit {
              position: absolute;
              top: 4px;
              left: 0;
              overflow: hidden;
              width: 100px;
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

        // .sub-class-right {
        // 	display: block;
        // 	width: 240px;
        // 	height: 439px;
        // 	float: right;
        // 	border-left: solid 1px #e6e6e6;
        // 	overflow: hidden;
        // 	.adv-promotions {
        // 		display: block;
        // 		height: 441px;
        // 		margin: -1px 0;
        // 		a {
        // 			background: #fff;
        // 			display: block;
        // 			width: 240px;
        // 			height: 146px;
        // 			border-top: solid 1px #e6e6e6;
        // 			img {
        // 				background: #d3d3d3;
        // 				width: 240px;
        // 				height: 146px;
        // 			}
        // 		}
        // 	}
        // }
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
      ul {
        margin: 0;
        padding: 0;
        width: 100%;
        height: 100%;
        li {
          cursor: pointer;
          list-style: none;
          float: left;
          padding: 8px 24px;
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
</style>
