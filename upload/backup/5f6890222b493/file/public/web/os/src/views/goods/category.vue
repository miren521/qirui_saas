<template>
    <div>
        <div class="newCategory" v-loading="loading">
            <div class="categoryLink">
                <ul id="categoryUl" :class="categoryFixed == true ? 'category-fixed' : ''">
                    <li v-for="(item, index) in goodsCategory" :key="index" :class="index == clickIndex ? 'selected' : ''" @click="changeCate(index, '#category' + index)">
                        <a>
                            <span>{{ item.category_name }}</span>
                        </a>
                    </li>
                </ul>
            </div>

            <div class="categoryCon">
                <div :id="'category' + index" :ref="'category' + index" class="items" :class="'items-' + index" v-for="(cate1, index) in goodsCategory" :key="index">
                    <h2>
                        <router-link :to="{ path: '/list', query: { category_id: cate1.category_id, level: cate1.level } }" target="_blank">{{ cate1.category_name }}</router-link>
                    </h2>

                    <dl v-for="(cate2, index) in cate1.child_list" :key="index">
                        <dt>
                            <router-link :to="{ path: '/list', query: { category_id: cate2.category_id, level: cate2.level } }" target="_blank">
                                {{ cate2.category_name }}
                            </router-link>
                        </dt>
                        <dd>
                            <router-link v-for="(cate3, index) in cate2.child_list" :key="index" :to="{ path: '/list', query: { category_id: cate3.category_id, level: cate3.level } }" target="_blank">
                                {{ cate3.category_name }}
                            </router-link>
                        </dd>
                    </dl>
                </div>
            </div>
			
			<div class="empty-wrap" v-if="goodsCategory.length <= 0"><div class="ns-text-align">暂无商品分类</div></div>
        </div>
    </div>
</template>

<script>
    import { tree } from "@/api/goods/goodscategory"

    export default {
        name: "category",
        components: {},
        data: () => {
            return {
                goodsCategory: [],
                categoryFixed: false,
                clickIndex: 0,
                loading: true
            }
        },
        created() {
            this.getGoodsCategory()
        },
        mounted() {
            window.addEventListener("scroll", this.handleScroll)
        },
        methods: {
            // 分类列表
            getGoodsCategory() {
                tree({
                    level: 3,
                    template: 2
                })
                    .then(res => {
                        if (res.code == 0) {
                            this.goodsCategory = res.data
                        }
                        this.loading = false
                    })
                    .catch(err => {
                        this.$message.error(err.message)
                        this.loading = false
                    })
            },
            // 监听滚动条
            handleScroll() {
                var scrollTop = window.pageYOffset || document.documentElement.scrollTop || document.body.scrollTop
                var offsetTop = document.querySelector(".newCategory").offsetTop

                if (scrollTop > offsetTop) {
                    this.categoryFixed = true
                } else {
                    this.categoryFixed = false
                }

                var divTopArr = []
                for (let i = 0; i < this.goodsCategory.length; i++) {
                    var _top = this.$refs["category" + i][0].offsetTop

                    divTopArr.push(_top)
                    var _offset = scrollTop - offsetTop
                    if (_offset < divTopArr[divTopArr.length - 1]) {
                        if (_offset >= divTopArr[i] && _offset < divTopArr[i + 1]) {
                            this.clickIndex = i
                        }
                    } else {
                        this.clickIndex = divTopArr.length - 1
                    }
                }
            },
            // 点击左侧分类
            changeCate(index, obj) {
                this.clickIndex = index
                document.querySelector(obj).scrollIntoView(true)
            }
        },
        destroyed() {
            // 离开该页面需要移除这个监听的事件，不然会报错
            window.removeEventListener("scroll", this.handleScroll)
        }
    }
</script>
<style lang="scss" scoped>
    .newCategory {
        width: $width;
        margin: 0 auto;
        overflow: hidden;
        background-color: #fff;

        .categoryLink {
            padding-top: 10px;
            float: left;
            position: relative;
            width: 210px;

            ul {
                width: 210px;
                padding-top: 20px;
				background-color: #FFFFFF;

                li {
                    width: 200px;
                    height: 30px;
                    text-align: left;
                    background-color: #f5f5f5;
                    border-radius: 30px;
                    color: #333;
                    line-height: 30px;
                    overflow: hidden;
                    position: relative;
                    cursor: pointer;
                    margin: 0 auto 15px;

                    &.selected {
                        background-color: $base-color;
                        background: -moz-linear-gradient(45deg, $base-color 0%, #ffffff 100%);
                        background: -webkit-gradient(45deg, color-stop(0%, $base-color), color-stop(100%, #ffffff));
                        background: -webkit-linear-gradient(45deg, $base-color 0%, #ffffff 100%);
                        background: -o-linear-gradient(45deg, $base-color 0%, #ffffff 100%);
                        background: -ms-linear-gradient(45deg, $base-color 0%, #ffffff 100%);
                        background: linear-gradient(45deg, $base-color 0%, #ffffff 100%);

                        a {
                            color: #fff;
                        }
                    }

                    a {
                        display: block;
                        margin-left: 30px;
                    }
                }
            }
        }

        .category-fixed {
            position: fixed;
            top: 0;
			z-index: 99;
        }

        .categoryCon {
            float: left;
            padding: 0px 0 60px;
            overflow: hidden;
            width: 990px;
            position: relative;

            .items {
                padding-left: 40px;

                h2 {
                    font-size: 18px;
                    font-weight: 600;
                    line-height: 40px;
                    margin-top: 30px;
                    border-bottom: 1px solid transparent;
                }

                dl {
                    padding: 15px 0 5px;
                    border-bottom: 1px solid #efefef;
                    overflow: hidden;

                    dt {
                        padding-right: 20px;
                        width: 72px;
                        position: relative;
                        background: #fff;
                        float: left;

                        a {
                            float: left;
                            max-width: 72px;
                            overflow: hidden;
                            white-space: nowrap;
                            text-overflow: ellipsis;
                            font-weight: 600;
                        }
                    }

                    dd {
                        float: left;
                        width: 858px;

                        a {
                            color: #666;
                            float: left;
                            padding: 0 12px;
                            margin: 0 0 10px -1px;
                            border-left: 1px solid #e0e0e0;
                            white-space: nowrap;
                        }
                    }
                }
            }
        }
    }
</style>
