<template>
    <div class="footer">
        <el-tabs v-model="activeName" class="friendly-link" v-if="linkList.length > 0">
            <el-tab-pane label="友情链接" name="first">
                <div>
                    <div class="link-item" v-for="(link_item, link_index) in linkList" :key="link_index" :title="link_item.link_title">
                        <span @click="linkUrl(link_item.link_url, link_item.is_blank)"><img :src="$img(link_item.link_pic)"/></span>
                    </div>

                    <div class="clear"></div>
                </div>
            </el-tab-pane>
        </el-tabs>
        <!-- <div class="friendly-link" v-if="linkList.length > 0">
			<div class="link-title">友情链接</div>
			<div>
				<div class="link-item" v-for="(link_item, link_index) in linkList" :key="link_index" :title="link_item.link_title">
					<span @click="linkUrl(link_item.link_url, link_item.is_blank)"><img :src="$img(link_item.link_pic)" /></span>
				</div>

				<div class="clear"></div>
			</div>
		</div> -->
        <div class="footer-top" v-if="shopServiceList.length > 0">
            <ul class="service">
                <li v-for="(item, index) in shopServiceList" :key="index">
                    <div><i class="iconfont" :class="item.pc_icon"></i></div>
                    <p>{{ item.name }}</p>
                </li>
            </ul>
        </div>

        <div class="footer-center">
            <div class="left" v-if="siteInfo.web_phone || siteInfo.web_email">
                <div>
                    <p class="left-phone" v-if="siteInfo.web_phone">{{ siteInfo.web_phone }}</p>
                    <p class="left-email">{{ siteInfo.web_email }}</p>
                </div>
            </div>
            <div class="center" v-if="helpList.length">
                <div class="help" v-for="(item, index) in helpList" :key="index">
                    <p @click="clickToHelp(item.class_id)">{{ item.class_name }}</p>
                    <ul class="help-ul">
                        <li class="help-li" v-for="(helpItem, helpIndex) in item.child_list" :key="helpIndex" @click="clickToHelpDetail(helpItem.id)">{{ helpItem.title }}</li>
                    </ul>
                </div>
            </div>
            <div class="right">
                <el-image v-if="siteInfo.web_qrcode" :src="$img(siteInfo.web_qrcode)" @error="imageError">
					<div slot="error" class="image-slot">
						<img src="@/assets/images/wxewm.png" />
					</div>
				</el-image>
                <el-image v-else src="@/assets/images/wxewm.png"></el-image>
                <p>微信小程序</p>
            </div>
        </div>

        <div class="footer-bot"><copy-right /></div>
    </div>
</template>

<script>
    import { copyRight, shopServiceLists, friendlyLink } from "@/api/website"
    import CopyRight from "./CopyRight"
    import { mapGetters } from "vuex"
    import { helpList } from "@/api/cms/help"

    export default {
        props: {},
        data() {
            return {
                shopServiceList: [],
                linkList: [],
                helpList: [],
                ishide: false,
                activeName: "first",
            }
        },
        computed: {
            ...mapGetters(["siteInfo"])
        },
        created() {
            this.getShopServiceLists()
            this.link()
            this.getHelpList()
        },
        mounted() {},
        watch: {},
        methods: {
            getShopServiceLists() {
                shopServiceLists({}).then(res => {
                    if (res.code == 0 && res.data) {
                        this.shopServiceList = res.data
                    }
                })
            },
            link() {
                friendlyLink({})
                    .then(res => {
                        if (res.code == 0 && res.data) {
                            this.linkList = res.data.list
                        }
                    })
                    .catch(err => {
                        this.$message.error(err.message)
                    })
            },
            linkUrl(url, target) {
                if (!url) return
                if (url.indexOf("http") == -1) {
                    if (target) this.$router.pushToTab({ path: url })
                    else this.$router.push({ path: url })
                } else {
                    if (target) window.open(url)
                    else window.location.href = url
                }
            },
            /**
             * 获取帮助列表
             */
            getHelpList() {
                helpList()
                    .then(res => {
                        if (res.code == 0 && res.data) {
                            var arr = [];
                            arr = res.data.slice(0, 4)

                            for (let i=0; i<arr.length; i++) {
                                arr[i].child_list = arr[i].child_list.slice(0, 4);
                            }

                            this.helpList = arr
                        }
                    })
                    .catch(err => {})
            },
            /**
             * 图片加载失败
             */
            imageError() {
                this.siteInfo.web_qrcode = "../../assets/images/wxewm.png"
            },
            /**
             * 跳转到帮助列表
             */
            clickToHelp(id) {
                this.$router.push("/cms/help/listother-" + id)
            },
            /**
             * 跳转到帮助详情
             */
            clickToHelpDetail(id) {
                this.$router.push("/cms/help-" + id)
            }
        },
        components: { CopyRight }
    }
</script>

<style scoped lang="scss">
    .footer {
        .footer-top {
            background-color: #fff;
            .service {
                margin: 0;
                padding: 0;
                width: $width;
                margin: 0 auto;
                padding: 50px 0;
                box-sizing: border-box;
                border-bottom: 1px solid #e9e9e9;
                display: flex;
                justify-content: space-around;
                align-items: center;
                li {
                    list-style: none;
                    line-height: 50px;
                    text-align: center;
                    flex: 1;

                    div {
                        display: inline-block;
                        width: 48px;
                        height: 48px;
                        line-height: 48px;
                        i {
                            font-size: 48px;
                            color: $base-color;
                        }
                    }
                    p {
                        font-size: 16px;
                        line-height: 20px;
                        margin-top: 10px;
                    }
                }
            }
        }

        .footer-center {
            width: $width;
            margin: 0 auto;
            display: flex;
            justify-content: space-between;
            padding: 50px 0;
            .left {
                width: 300px;

                .left-phone {
                    font-size: 26px;
                    font-weight: 600;
                    color: $base-color;
                    padding-bottom: 10px;
                    border-bottom: 1px solid $base-color;
                    margin-bottom: 12px;
                }
                .left-email {
                    font-size: 18px;
                    color: #838383;
                }
            }
            .center {
                width: calc(100% - 550px);
                padding: 0 68px;
                display: flex;
                justify-content: space-between;

                p {
                    font-size: 18px;
                    font-weight: 600;
                    color: #838383;
                    margin-bottom: 10px;
                    cursor: pointer;
                }

                .help-li {
                    font-size: 14px;
                    color: #838383;
                    line-height: 30px;
                    cursor: pointer;
                }

                p:hover, .help-li:hover {
                    color: $base-color;
                }
            }
            .right {
                width: 250px;
                text-align: center;

                .el-image {
                    width: 120px;
                    height: 120px;
                    line-height: 120px;
                    text-align: center;

                    img {
                        max-width: 100%;
                        max-height: 100%;
                    }
                }

                p {
                    font-size: 18px;
                    color: #838383;
                }
            }

            .qrcode-hide {
                display: none;
            }
        }

        .footer-bot {
            background: #242424;
            color: #9D9D9D;
        }

        .friendly-link {
            width: $width;
            margin: 0 auto;
            border: 1px solid #e9e9e9;

            .link-title {
                line-height: 30px;
                padding: 10px 0 5px;
                margin: 0px 0 15px;
                border-bottom: 1px solid #e8e8e8;
            }
            .link-item {
                width: 10%;
                height: 50px;
                line-height: 47px;
                float: left;
                text-align: center;
                overflow: hidden;
                margin: 0 6px 10px 6px;
                -webkit-transition: opacity 0.3s, box-shadow 0.3s;
                transition: opacity 0.3s, box-shadow 0.3s;
                border: 1px solid #fff;
                border-left: none;
                cursor: pointer;
            }
            .link-item:hover {
                width: -webkit-calc(10% + 1px);
                width: calc(10% + 1px);
                margin-left: 4px;
                position: relative;
                opacity: 1;
                box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
                border: 1px solid #dddddd;
            }
            .clear {
                clear: both;
            }
        }
    }
</style>
<style lang="scss">
    .friendly-link {
        .el-tabs__nav-scroll {
            padding-left: 30px;
            height: 50px;
            line-height: 50px;
        }
        .el-tabs__content {
            padding: 0 20px;
        }
        .el-tabs__nav-wrap::after {
            height: 1px;
        }
        .el-tabs__nav {
            padding: 0 20px;
        }
        .el-tabs__active-bar {
            padding: 0 20px;
        }
    }
</style>
