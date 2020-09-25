<template>
    <div @click="handleClick">
        <el-card :body-style="{ padding: '0px' }" shadow="hover">
            <el-image lazy class="image" :src="img">
                <div slot="error" class="image-slot">
                    <i class="el-icon-picture-outline"></i>
                </div>
            </el-image>

            <div class="cargo-wrap">
                <div class="title clearfix">{{title}}</div>
                <div class="subtitle clearfix">{{subtitle}}</div>
            </div>
        </el-card>
    </div>
</template>

<script>
export default {
    props: {
        // 图片地址
        img: {
            type: String,
            default: "https://shadow.elemecdn.com/app/element/hamburger.9cf7b091-55e9-11e9-a976-7f4d0b07eef6.png",
            required: true
        },
        // 商品标题
        title: {
            type: String,
            default: "商品标题",
            required: true
        },
        // 商品子标题
        subTitle: {
            type: String,
            default: ""
        },
        // 跳转路径
        path: {
            type: String,
            default: "",
            required: true
        },
        // 是否打开新窗口
        newWindow: {
            type: Boolean,
            default: true
        }
    },

    data() {
        return {}
    },

    methods: {
        // 处理点击跳转
        handleClick: () => {
            // 在新窗口打开页面
            if (this.newWindow) {
                this.$router.pushToTab(this.path)
            } else {
                this.$router.push(this.path)
            }
        }
    }
}
</script>

<style lang="scss" scoped>
.image {
    width: 100%;
    display: block;
}

.title {
    font-size: 14px;
    color: #ff6900;
}

.subtitle {
    font-size: 14px;
    color: #999999;
}

.cargo-wrap {
    padding: 14px;
}

.clearfix:before,
.clearfix:after {
    display: table;
    content: "";
}

.clearfix:after {
    clear: both;
}
</style>