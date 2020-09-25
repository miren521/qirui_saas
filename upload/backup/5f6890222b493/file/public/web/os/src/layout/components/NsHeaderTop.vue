<template>
    <div>
        <div class="header-top">
            <div class="top-content">
                <div class="top-left" v-if="addonIsExit.city">
                    <span>
                        <i class="el-icon-location"></i>
                        <template v-if="city && city.title">
                            {{ city.title }}
                        </template>
                        <template v-else>
                            全国
                        </template>
                    </span>
                    <el-tag size="mini" effect="plain" type="info" class="change-city" @click="$router.push('/change_city')">切换城市</el-tag>
                </div>
                <div class="top-right">
                    <router-link v-if="!logined" to="/login" class="ns-text-color">您好，请登录</router-link>
                    <div v-if="logined" class="member-info">
                        <router-link to="/member/index">{{ member.nickname || member.username }}</router-link>
                        <span @click="logout">退出</span>
                    </div>
                    <router-link v-if="!logined" to="/register">免费注册</router-link>
                    <router-link to="/member/order_list">我的订单</router-link>
					<router-link to="/member/footprint">我的足迹</router-link>
					<router-link to="/member/collection">我的关注</router-link>
					
                    <router-link to="/cms/help">帮助中心</router-link>
					<router-link to="/cms/notice">公告</router-link>
                    <el-dropdown v-if="qrcode">
                        <span class="el-dropdown-link">
                            手机商城
                            <i class="el-icon-arrow-down el-icon--right"></i>
                        </span>
                        <el-dropdown-menu slot="dropdown">
                            <el-dropdown-item>
                                <div class="mobile-qrcode"><img :src="$img(qrcode)" /></div>
                            </el-dropdown-item>
                        </el-dropdown-menu>
                    </el-dropdown>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    import { mapGetters } from "vuex"
    import { getToken } from "@/utils/auth"

    export default {
        props: {},
        data() {
            return {}
        },
        created() {
            this.$store.dispatch("site/qrCodes")
            this.$store.dispatch("member/member_detail")
            this.$store.dispatch("site/defaultFiles")
            this.$store.dispatch("site/addons")
        },
        mounted() {},
        watch: {},
        methods: {
            logout() {
                this.$store.dispatch("member/logout")
            }
        },
        components: {},
        computed: {
            ...mapGetters(["wapQrcode", "member", "addonIsExit", "city"]),
            qrcode: function() {
                return this.wapQrcode === "" ? "" : this.wapQrcode.path.h5.img
            },
            logined: function() {
                return this.member !== undefined && this.member !== "" && this.member !== {}
            }
        }
    }
</script>

<style scoped lang="scss">
    .header-top {
        width: 100%;
        height: 31px;
        font-size: 12px;
        background-color: #f5f5f5;

        .el-dropdown {
            font-size: $ns-font-size-sm;
        }
        .top-content {
            width: $width;
            height: 100%;
            margin: 0 auto;
            .top-left {
                height: 100%;
                float: left;
                height: 100%;
                line-height: 31px;
                .change-city {
                    cursor: pointer;
                    margin-left: 5px;
                    &:hover {
                        color: $base-color;
                        border-color: $base-color;
                    }
                }
            }
            .top-right {
                height: 100%;
                float: right;
                font-size: $ns-font-size-sm;
                color: $ns-text-color-black;
                a {
                    float: left;
                    line-height: 31px;
                    padding: 0 10px;
                    &:hover {
                        color: $base-color;
                    }
                }
                div {
                    float: left;
                    height: 100%;
                    margin-left: 10px;
                    cursor: pointer;
                    line-height: 31px;
                    padding: 0 5px;

                    &.member-info {
                        margin-left: 0;
                        span:first-child {
                            margin-right: 10px;
                        }
                    }
                    span:hover {
                        color: $base-color;
                    }
                    &.el-dropdown:hover {
                        background-color: #fff;
                    }
                }
            }
        }
    }
    .mobile-qrcode {
        padding: 10px 0;
    }
</style>
