<template>
    <aside class="main-sidebar clearfix">
        <div class="main-sidebar-body">
            <ul>
                <li @click="$router.push('/cart')">
                    <div class="cart">
                        <i class="el-icon-shopping-cart-2"></i>
                        <span>购物车</span>
                        <em v-show="cartCount">{{ cartCount }}</em>
                    </div>
                </li>
                <li @click="$router.push('/member/order_list')">
                    <el-tooltip class="item" effect="dark" content="我的订单" placement="left">
                        <el-button><i class="el-icon-tickets"></i></el-button>
                    </el-tooltip>
                </li>
                <li @click="$router.push('/member/footprint')">
                    <el-tooltip class="item" effect="dark" content="我的足迹" placement="left">
                        <el-button><i class="iconfont iconzuji"></i></el-button>
                    </el-tooltip>
                </li>
                <li @click="$router.push('/member/collection')">
                    <el-tooltip class="item" effect="dark" content="我的关注" placement="left">
                        <el-button><i class="iconfont iconlike"></i></el-button>
                    </el-tooltip>
                </li>
                <li @click="$router.push('/member/my_coupon')">
                    <el-tooltip class="item" effect="dark" content="我的优惠券" placement="left">
                        <el-button><i class="iconfont iconyouhuiquan"></i></el-button>
                    </el-tooltip>
                </li>
            </ul>
            <a class="back-top" :class="{ showBtn: visible }" title="返回顶部" @click="toTop"><i class="el-icon-top"></i></a>
        </div>
        <div class="main-sidebar-right"><div id="mainSidebarHistoryProduct" class="history-product"></div></div>
    </aside>
</template>

<script>
    import { mapGetters } from "vuex"
    export default {
        props: {},
        data() {
            return {
                visible: false
            }
        },
        computed: {
            ...mapGetters(["cartCount"])
        },
        created() {},
        mounted() {
            window.addEventListener("scroll", this.handleScroll)
        },
        beforeDestroy() {
            window.removeEventListener("scroll", this.handleScroll)
        },
        watch: {},
        methods: {
            handleScroll() {
                this.visible = window.pageYOffset > 300
            },
            toTop() {
                let timer = setInterval(function() {
                    let osTop = document.documentElement.scrollTop || document.body.scrollTop
                    let ispeed = Math.floor(-osTop / 5)
                    document.documentElement.scrollTop = document.body.scrollTop = osTop + ispeed
                    this.isTop = true
                    if (osTop === 0) {
                        clearInterval(timer)
                    }
                }, 20)
            }
        },
        components: {}
    }
</script>

<style scoped lang="scss">
    .main-sidebar {
        width: 340px;
        height: 100%;
        position: fixed;
        top: 0;
        right: -300px;
        z-index: 400;
        .main-sidebar-body {
            width: 40px;
            height: 100%;
            float: left;
            background-color: #333333;
            ul {
                position: absolute;
                top: 50%;
                -webkit-transform: translateY(-50%);
                transform: translateY(-50%);
                li {
                    position: relative;
                    .cart {
                        height: auto;
                        line-height: 20px;
                        padding: 11px 0;
                        text-align: center;
						cursor: pointer;
                        span {
                            display: block;
                            padding: 5px 9px;
                            text-align: center;
                        }
                        em {
                            min-width: 17px;
                            height: 15px;
                            line-height: 15px;
                            display: inline-block;
                            padding: 0 2px;
                            color: #ffffff;
                            font-size: 10px;
                            font-style: normal;
                            text-align: center;
                            border-radius: 8px;
                            background-color: $base-color;
                        }
                        &:hover em {
                            color: $base-color;
                            background-color: #fff;
                        }
                    }
                }
            }
            a,
            .cart,
            .el-button {
                width: 40px;
                height: 40px;
                line-height: 40px;
                display: block;
                margin-bottom: 10px;
                color: #ffffff;
                text-align: center;
                -webkit-transition: background-color 0.3s;
                transition: background-color 0.3s;
                padding: 0;
                border: none;
                background: transparent;
                &:hover {
                    background-color: $base-color;
                }
            }
            .back-top {
                display: none;
                margin-bottom: 0;
                position: absolute;
                bottom: 0;
                border-top: 1px solid #888888;
            }

            .showBtn {
                display: inline;
                opacity: 1;
                cursor: pointer;
            }

            i {
                font-size: 16px;
            }
        }
    }
</style>
