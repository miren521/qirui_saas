<template>
    <el-container>
        <!-- 头部 -->
        <el-header height="auto" class="header"><shop-header /></el-header>
        <el-main class="content">
            <transition name="slide"><router-view /></transition
        ></el-main>
        <!-- 底部 -->
        <el-footer><ns-footer /></el-footer>
    </el-container>
</template>
<script>
    import ShopHeader from "./components/ShopHeader"
    import NsHeader from "./components/NsHeader"
    import NsFooter from "./components/NsFooter"

    export default {
        created() {},
        data: () => {
            return {}
        },
        mounted() {},

        computed: {},

        watch: {},
        components: {
            ShopHeader,
            NsFooter
        }
    }
</script>
<style lang="scss" scoped>
    .header {
        padding: 0;
    }
    .content {
        max-width: $width;
        margin: 0 auto;
        padding: 0;
    }
    .el-footer {
        padding: 0;
        height: auto !important;
        background-color: #fff;
        padding-top: 45px;
    }

    .el-main {
        border-top: none;
    }
</style>
