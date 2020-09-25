<template>
    <div class="error-wrap">
		<img src="@/assets/images/index/closed.png" />
        <!-- <i class="iconfont icon404"></i> -->
        <h2>{{siteInfo.close_reason || '网站已关闭'}}</h2>
    </div>
</template>

<script>
import { mapGetters } from "vuex"
export default {
    name: "closed",
    created() {
        this.$store.dispatch("site/siteInfo")
    },
    computed: {
        ...mapGetters(["siteInfo"])
    }
}
</script>
<style lang="scss" scoped>
.error-wrap {
    text-align: center;
    padding: 40px;
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    i {
        font-size: 60px;
    }
    h2 {
        margin: 0 0 30px;
    }
}
</style>
