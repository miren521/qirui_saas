<template>
    <el-container>
        <!-- 头部 -->
        <div class="banner" v-loading="loadingAd" v-if="is_show">
            <el-carousel height="70px" arrow="hover" direction="vertical" indicator-position="none">
                <el-carousel-item v-for="item in adList" :key="item.adv_id" :style="{ backgroundColor: item.background }">
                    <el-image :src="$img(item.adv_image)" fit="cover" @click="$router.pushToTab(item.adv_url.url)" />
                </el-carousel-item>
            </el-carousel>
            <i class="el-icon-circle-close" @click="closeAd"></i>
        </div>
        <el-header><ns-header /></el-header>
        <el-main>
            <transition name="slide"><router-view /></transition>

            <!-- 右侧栏 -->
            <ns-aside />
        </el-main>
        <!-- 底部 -->
        <el-footer><ns-footer /></el-footer>
    </el-container>
</template>
<script>
    import NsHeader from "./components/NsHeader"
    import NsFooter from "./components/NsFooter"
    import NsAside from "./components/NsAside"
    import { adList } from "../api/website"

    export default {
        name: "Layout",
        components: {
            NsHeader,
            NsFooter,
            NsAside
        },
        created() {
            this.getAdList()
        },
        data: () => {
            return {
                loadingAd: true,
                adList: [],
                is_show: true,
                indexTopAdNum: 0
            }
        },
        mounted() {},
        computed: {},
        watch: {},
        methods: {
            getAdList() {
                if (this.$store.state.app.indexTopAdNum >= 3) {
                    this.loadingAd = false
                    this.is_show = false
                    return
                }
                adList({ keyword: "NS_PC_INDEX_TOP" })
                    .then(res => {
                        this.adList = res.data.adv_list
                        for (let i = 0; i < this.adList.length; i++) {
                            if (this.adList[i].adv_url) this.adList[i].adv_url = JSON.parse(this.adList[i].adv_url)
                        }
                        this.loadingAd = false
                    })
                    .catch(err => {
                        this.loadingAd = false
                    })
            },
            closeAd() {
                this.is_show = false
                this.indexTopAdNum = this.$store.state.app.indexTopAdNum
                this.indexTopAdNum++
                this.$store.commit("app/SET_INDEXTOPADNUM", this.indexTopAdNum)
            }
        }
    }
</script>
<style lang="scss" scoped>
    .banner {
        text-align: center;
        height: 70px;
        position: relative;
        i {
            font-size: 30px;
            position: absolute;
            z-index: 100;
            right: 280px;
            top: 10px;
            cursor: pointer;
            color: #d4d4d4;
        }
    }
    .el-header {
        padding: 0;
        height: 180px !important;
    }
    .el-footer {
        padding: 0;
        height: auto !important;
        background-color: #fff;
        padding-top: 45px;
    }
</style>
