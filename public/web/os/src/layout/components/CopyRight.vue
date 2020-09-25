<template>
    <div class="footer-bottom">
        <div class="site-info">
            <p v-if="siteInfo.web_phone"><i class="iconfont icondianhua"></i>{{ siteInfo.web_phone }}</p>
            <p v-if="siteInfo.web_email">
                <i class="iconfont iconyouxiang"></i>
                <el-link :href="`mailto:${siteInfo.web_email}`">{{ siteInfo.web_email }}</el-link>
            </p>
        </div>
        <p>
            {{ copyRight.copyright_desc }}
            <a v-if="copyRight.icp" class="footer-link" href="http://www.beian.miit.gov.cn" target="_blank">备案号：{{ copyRight.icp }}</a>
        </p>
        <p>
            <a v-if="copyRight.gov_record" class="footer-link" :href="copyRight.gov_url" target="_blank">
                <img src="@/assets/images/gov_record.png" alt="公安备案" />
                <span>{{ copyRight.gov_record }}</span>
            </a>
        </p>
    </div>
</template>

<script>
    import { copyRight } from "@/api/website"
    import { mapGetters } from "vuex"
    export default {
        props: {},
        data() {
            return {}
        },
        computed: {},
        created() {
            this.$store.dispatch("site/copyRight")
        },
        mounted() {},
        watch: {},
        methods: {},
        computed: {
            ...mapGetters(["copyRight", "siteInfo"])
        }
    }
</script>

<style scoped lang="scss">
    .footer-bottom {
        width: 100%;
        height: 85px;
        margin: 0 auto;
        padding: 20px 0;
        p {
            margin: 0;
            width: 100%;
            box-sizing: border-box;
            text-align: center;
            img {
                width: 20px;
                height: 20px;
                margin-right: 5px;
            }
            .footer-link, .el-link.el-link--default {
                color: #9d9d9d;
            }
			.footer-link:hover, .el-link.el-link--default:hover {
				color: $base-color;
			}
        }
        .site-info {
            display: flex;
            justify-content: center;
            align-items: center;

            p {
                width: auto;
                margin: 0 10px;

                i {
                    vertical-align: bottom;
                    margin-right: 5px;
                }
            }
        }
    }
</style>
