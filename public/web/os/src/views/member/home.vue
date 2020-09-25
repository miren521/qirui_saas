<template>
    <el-container class="content">
        <el-aside width="200px">
            <el-menu :default-active="activeIndex" class="menu" router @open="handleOpen" :default-openeds="defaultOpeneds" unique-opened>
                <el-submenu index="1" title>
                    <template slot="title">
                        <span>会员中心</span>
                    </template>
                    <el-menu-item index="index">欢迎页</el-menu-item>
                    <el-menu-item index="info">个人信息</el-menu-item>
                    <el-menu-item index="security">账户安全</el-menu-item>
                    <el-menu-item index="delivery_address">收货地址</el-menu-item>
                    <el-menu-item index="collection">我的关注</el-menu-item>
                    <el-menu-item index="footprint">我的足迹</el-menu-item>
                </el-submenu>
                <el-submenu index="2" title>
                    <template slot="title">
                        <span>交易中心</span>
                    </template>
                    <el-menu-item index="order_list">我的订单</el-menu-item>
                    <el-menu-item index="activist">退款/售后</el-menu-item>
                    <template v-if="is_verify == 1">
                        <el-menu-item index="verification">核销台</el-menu-item>
                        <el-menu-item index="verification_list">核销记录</el-menu-item>
                    </template>
                </el-submenu>
                <el-submenu index="3" title>
                    <template slot="title">
                        <span>账户中心</span>
                    </template>
                    <el-menu-item index="account">账户余额</el-menu-item>
                    <el-menu-item index="withdrawal">提现记录</el-menu-item>
                    <el-menu-item index="my_coupon">我的优惠券</el-menu-item>
                    <el-menu-item index="my_point">我的积分</el-menu-item>
					<el-menu-item index="account_list">账户列表</el-menu-item>
                    <!-- <el-menu-item index="level">会员等级</el-menu-item> -->
                </el-submenu>
            </el-menu>
        </el-aside>
        <el-main class="member">
            <transition name="slide"><router-view /></transition>
        </el-main>
    </el-container>
</template>
<script>
    import { checkisverifier } from "@/api/order/verification"
    export default {
        name: "home",
        components: {},
        data: () => {
            return {
                defaultOpeneds: ["1"],
                activeIndex: "index",
                is_verify: 1
            }
        },
        created() {
            this.activeIndex = this.$route.meta.parentRouter ||this.$route.path.replace("/member/", "")
            this.checkIsVerifier()
        },
        methods: {
            handleOpen(key, keyPath) {
                this.defaultOpeneds = keyPath
            },
            checkIsVerifier() {
                checkisverifier()
                    .then(res => {
                        if (res.data) {
                            this.is_verify = 1
                        }
                    })
                    .catch(err => {
                        this.is_verify = 0
                    })
            }
        },
        watch: {
            $route(curr) {
				this.activeIndex = curr.meta.parentRouter||this.$route.path.replace("/member/", "");
            }
        }
    }
</script>
<style lang="scss" scoped>
    .menu {
        min-height: 730px;
    }
    .content {
        display: flex !important;
        margin: 20px 0;
    }
    .member {
        margin-left: 15px;
    }
</style>
